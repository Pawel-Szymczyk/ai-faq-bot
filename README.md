# AI FAQ Bot: wtyczka WordPress

Inteligentny asystent FAQ dla WordPress oparty na Claude API (Anthropic). Bot odpowiada na pytania użytkowników **wyłącznie na podstawie bazy wiedzy** którą wypełnia administrator, bez wymyślania odpowiedzi spoza zakresu.

---

## Jak to działa

```
Użytkownik pisze pytanie
    ↓
Bot pobiera wszystkie wpisy z Bazy Wiedzy
    ↓
Claude analizuje pytanie i bazę wiedzy
    ↓
Jeśli odpowiedź jest w bazie → odpowiada
Jeśli nie → "Nie mam informacji. Kontakt: kontakt@firma.pl"
    ↓
Nieodpowiedziane pytania są logowane dla admina
```

---

## Funkcje

- 💬 **Chat widget** osadzany shortcodem `[ai_faq_bot]` na dowolnej stronie
- 📚 **Baza wiedzy** zarządzana przez panel admina (Custom Post Type)
- 🔒 **Ścisłe ograniczenie** bot nie odpowiada poza zakresem bazy
- 📝 **Logi nieodpowiedzianych pytań** admin wie co dodać do bazy
- ⚡ **Aktualizacja w czasie rzeczywistym** dodaj wpis, bot od razu wie
- 🛡️ **Rate limiting** ochrona przed nadużyciami API
- 📱 **Responsywny** działa na mobile

---

## Wymagania

- WordPress 6.0+
- PHP 8.0+
- Klucz API Anthropic (console.anthropic.com)

---

## Instalacja

**1. Pobierz wtyczkę**
```bash
git clone https://github.com/TWOJ_LOGIN/ai-faq-bot.git
```

**2. Wgraj do WordPress**

Skopiuj folder `ai-faq-bot` do:
```
wp-content/plugins/ai-faq-bot/
```

**3. Aktywuj w panelu admina**

WordPress Admin → Wtyczki → AI FAQ Bot → **Aktywuj**

**4. Wpisz klucz API**

WordPress Admin → Ustawienia → AI FAQ Bot → wpisz klucz z [console.anthropic.com](https://console.anthropic.com)

---

## Konfiguracja bazy wiedzy

Po aktywacji w lewym menu admina pojawi się sekcja **Baza Wiedzy**.

**Dodawanie wpisów:**

```
Admin → Baza Wiedzy → Dodaj nowy wpis

Tytuł:  "Jak złożyć zwrot?"
Treść:  "Aby złożyć zwrot, wejdź na stronę zwroty.firma.pl
         i wypełnij formularz w ciągu 14 dni od zakupu.
         Zwrot zostanie przetworzony w ciągu 5 dni roboczych."

→ Opublikuj
```

Bot natychmiast uwzględni nowy wpis bez rekonfiguracji.

---

## Osadzanie na stronie

Dodaj shortcode w treści dowolnej strony lub posta:

```
[ai_faq_bot]
```

**Opcjonalne parametry:**
```
[ai_faq_bot height="500" placeholder="Jak mogę pomóc?"]
```

---

## Struktura plików

```
ai-faq-bot/
├── ai-faq-bot.php              # Główny plik wtyczki
├── includes/
│   ├── class-ai-api.php        # Komunikacja z Claude API
│   └── class-ai-faq.php        # Logika bota FAQ
├── admin/
│   └── settings-page.php       # Strona ustawień w adminie
└── assets/
    ├── js/chat.js              # Frontend — interfejs chatu
    └── css/chat.css            # Style widgetu
```

---

## Ustawienia admina

| Ustawienie | Opis |
|---|---|
| Klucz API Anthropic | Pobierz z console.anthropic.com |
| Nazwa asystenta | Np. "Ania" lub "Asystent FAQ" |
| Email kontaktowy | Wyświetlany gdy bot nie zna odpowiedzi |
| Dodatkowe instrukcje | Custom reguły zachowania bota |

---

## Logi nieodpowiedzianych pytań

Gdy bot odpowie "Nie mam informacji", pytanie jest automatycznie zapisywane. Admin może je przejrzeć i uzupełnić bazę wiedzy:

```
Admin → Ustawienia → AI FAQ Bot → zakładka "Nieodpowiedziane pytania"
```

---

## Bezpieczeństwo

- Klucz API przechowywany w bazie WordPress, nigdy w kodzie!
- Sanitizacja wszystkich danych wejściowych
- Rate limiting: max 20 zapytań/godzinę/IP (konfigurowalne)
- Nonce WordPress na każdym żądaniu AJAX

---

## Koszty API

Bot używa modelu **Claude Haiku** (najtańszego i najszybszego).

| Model | Koszt (przybliżony) |
|---|---|
| Claude Haiku | ~$0.25 za 1M tokenów |
| Typowe pytanie FAQ | ~500 tokenów = $0.000125 |
| 1000 pytań miesięcznie | ~$0.13 |

Przy normalnym ruchu koszty są minimalne.

---

## Dostosowanie wyglądu

Edytuj `assets/css/chat.css` - zmień kolory na brand klienta:

```css
/* Kolor marki - zmień #1E40AF na kolor klienta */
.ai-message--user  { background: #1E40AF; }
#ai-chat-send      { background: #1E40AF; }
#ai-chat-input:focus { border-color: #1E40AF; }
```

---

## Licencja

GPL v2 or later (zgodnie ze standardem WordPress).

---

## Autor

**Paweł Szymczyk** — [github.com/Pawel-Szymczyk](https://github.com/Pawel-Szymczyk)

Freelancer specjalizujący się w integracji AI z WordPress.
Zainteresowany podobnym rozwiązaniem dla swojej firmy? Napisz: [kontakt]
