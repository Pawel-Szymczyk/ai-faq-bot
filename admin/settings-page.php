<?php
// admin/settings-page.php
// Adds AI Assistant under WordPress Admin > Settings.

if ( ! defined( 'ABSPATH' ) ) exit;

class AI_FAQ_Admin_Settings {
 
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }
 
    public function add_menu(): void {
        add_options_page(
            'AI FAQ Settings', // browser tab <title>
            'AI FAQ Bot',          // menu item label
            'manage_options',        // capability: admin only
            'ai-faq-settings', // unique menu slug
            [ $this, 'render_page' ]
        );
    }
 
    public function register_settings(): void {
        register_setting( 'ai_opts',  'ai_assistant_api_key',  [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'ai_opts', 'faq_contact',           [ 'sanitize_callback' => 'sanitize_email' ] );
    }
 
    public function render_page(): void { ?>
        <div class='wrap'>
            <h1>AI FAQ Assistant — Ustawienia</h1>
            <form method='post' action='options.php'>
                <?php settings_fields( 'ai_opts' ); // outputs security nonce ?>
                <table class='form-table'>
                    <tr>
                        <th>Klucz API Anthropic</th>
                        <td>
                            <input type='password' name='ai_assistant_api_key'
                                   value='<?php echo esc_attr( get_option( 'ai_assistant_api_key' ) ); ?>'
                                   class='regular-text' />
                            <p class='description'>
                                Pobierz z <a href='https://console.anthropic.com' target='_blank'>console.anthropic.com</a> → API Keys → Create Key
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>Email kontaktowy</th>
                        <td>
                            <input type='email' name='faq_contact'
                                value='<?php echo esc_attr( get_option( "faq_contact", "" ) ); ?>'
                                class='regular-text' />
                            <p class='description'>Wyświetlany gdy bot nie zna odpowiedzi</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button( 'Zapisz ustawienia' ); ?>
            </form>
        </div>
    <?php }
}
