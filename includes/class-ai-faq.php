<?php
// includes/class-ai-faq.php
// Single responsibility: handle FAQ-related logic using the AI API.

if ( ! defined( 'ABSPATH' ) ) exit;

class AI_FAQ {

    private AI_API $api;

    public function __construct() {
        $this->api = new AI_API();
        add_action('init',function(){
            register_post_type('faq_entry',[
                'label'   =>'Baza Wiedzy',
                'public'  =>false,  // not a frontend URL
                'show_ui' =>true,   // visible in WP Admin
                'supports'=>['title','editor'],
                'menu_icon'=>'dashicons-book-alt',
            ]);
        });
        // add_action( 'init',               [ $this, 'start_session' ] );
        add_action( 'rest_api_init',      [ $this, 'register_endpoints' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_shortcode( 'ai_faq_bot',   [ $this, 'render_chat' ] );
        
    }

    // ── REST Endpoint ────────────────────────────────────────────────────
    public function register_endpoints(): void {
        register_rest_route( 'ai-faq-bot/v1', '/chat', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'handle_chat' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public function handle_chat(WP_REST_Request $request): WP_REST_Response {

        $data = $request->get_json_params();
        $user_message = sanitize_text_field($data['message'] ?? '');
        if (empty($user_message)) {
            return new WP_REST_Response(['error' => 'Message is empty.'], 400);
        }

        // Prepare messages for AI API
        $messages = [
            ['role' => 'user', 'content' => $user_message]
        ];
        $system_prompt = $this->get_faq_system_prompt();

        // Send to AI API
        $reply = $this->api->send_messages($messages, $system_prompt);
        if (is_wp_error($reply)) {
            return new WP_REST_Response(['error' => $reply->get_error_message()], 500);
        }

        // Log unanswered questions
        $this->log_unanswered($user_message, $reply);

        return new WP_REST_Response(['reply' => $reply], 200);
    }

    /**
     * Load all knowledge base entries into system prompt.
     * @return string  Knowledge base text
     */
    private function get_knowledge_base():string{
        $entries=get_posts(['post_type'=>'faq_entry','posts_per_page'=>-1,'post_status'=>'publish']);
        if(empty($entries))return 'Knowledge base is empty.';
        $kb = "=== KNOWLEDGE BASE ===\n";
        foreach($entries as $e){
            $kb.="\n## {$e->post_title}\n".wp_strip_all_tags($e->post_content)."\n";
        }
        return $kb.'=== END ===';
    }
    
    /**
     * Get system prompt for AI API (strictly knowledge base only).
     * @return string  System prompt text
     */
    private function get_faq_system_prompt():string{
        $kb     =$this->get_knowledge_base();
        $contact=get_option('faq_contact','kontakt@firma.pl');
        return $kb."\n\n" .
            'Answer ONLY from the knowledge base. ' .
            'If not covered: "Nie mam informacji. Kontakt: '.$contact.'" ' .
            'Respond in Polish.';
    }
    
    /**
     * Log unanswered questions for admin review.
     * @param string $q     User question
     * @param string $reply AI reply
     */
    private function log_unanswered(string $q,string $reply):void{
        if(stripos($reply,'nie mam informacji')===false)return;
        $log=get_option('faq_unanswered_log',[]);
        $log[]=['q'=>$q,'date'=>current_time('Y-m-d H:i')];
        update_option('faq_unanswered_log',array_slice($log,-100));
    }


    // ── Assets ───────────────────────────────────────────────────────────
    public function enqueue_assets(): void {
        global $post;
        if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'ai_faq_bot' ) ) {
            return;
        }

        wp_enqueue_style( 'ai-faq-chat', AI_FAQ_BOT_URL . 'assets/css/chat.css', [], AI_FAQ_BOT_VERSION );
        wp_enqueue_script( 'ai-faq-chat', AI_FAQ_BOT_URL . 'assets/js/chat.js', [], AI_FAQ_BOT_VERSION, true );
        wp_localize_script( 'ai-faq-chat', 'AiChatConfig', [
            'apiUrl' => rest_url( 'ai-faq-bot/v1/chat' ),
            'nonce'  => wp_create_nonce( 'wp_rest' ),
        ] );
    }
 
    // ── Shortcode ─────────────────────────────────────────────────────────
    // Usage: add [ai_faq_bot] to any WordPress post or page.
    public function render_chat(): string {
        // ob_start/ob_get_clean: capture HTML output as a string.
        ob_start(); ?>
        <div id='ai-chat-widget'>
            <div id='ai-chat-messages'></div>
            <div id='ai-chat-input-area'>
                <input type='text' id='ai-chat-input' placeholder='Napisz wiadomość...' />
                <button id='ai-chat-send'>Wyślij</button>
            </div>
        </div>
        <?php return ob_get_clean();
    }
}
 