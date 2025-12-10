# Copilot Instructions for asp-portaal

## Project Architecture
- This is a Laravel-based web application with a modular structure: `app/` (domain logic), `resources/views/` (Blade templates), `resources/js/` (frontend logic), and `database/` (migrations, seeders).
- Major models include `Claim`, `VehicleOpposite`, `Opposite`, `Driver`, and `Company`. Data flows from controllers (e.g., `ClaimController`) to Blade views, often with JSON-encoded data for frontend JS replacement.
- Mail templates use dynamic placeholders (e.g., `[bedrijf]`, `[wederpartij_naam]`) replaced by JavaScript in `resources/js/app.js` using data injected into hidden JSON elements in Blade views.

## Developer Workflows
- **Build/Dev:** Use Laravel's artisan commands (`php artisan serve`, `php artisan migrate`, etc.). Frontend assets are managed via Vite (`npm run dev`, `npm run build`).
- **Testing:** PHPUnit is configured (`phpunit.xml`). Run tests with `php artisan test` or `vendor/bin/phpunit`.
- **Debugging:** Use Laravel's built-in error pages and logging. For mail debugging, Mailpit is recommended (see README for setup).

## Project-Specific Patterns
- **Placeholder Replacement:** Only placeholders wrapped in `[]` are replaced in mail templates. See `resources/js/app.js` for the logic. Avoid accidental replacement of normal words.
- **Data Injection:** Blade views inject JSON data for claims, contacts, vehicles, etc. into hidden `<div>`s (e.g., `#claimJson`, `#oppositeJson`). JS reads and parses these for dynamic UI and template logic.
- **Model Conventions:** Some models (e.g., `VehicleOpposite`) may not have all address/contact fields; use `Opposite` for full opponent info.
- **Controller Patterns:** Controllers often merge data from multiple models to provide complete context to views (see `ClaimController@show`).

## Integration Points
- **Mailpit:** For local email testing, install and configure Mailpit as described in the README.
- **Frontend/Backend Communication:** Data for JS logic is always injected via Blade, not fetched via API.
- **External Libraries:** Uses Laravel, Vite, and Mailpit. No SPA or API-first patterns; all dynamic behavior is server-rendered + JS-enhanced.

## Key Files/Directories
- `app/Http/Controllers/` — Main business logic, especially `ClaimController.php`.
- `resources/views/partials/claims/show/createmail.blade.php` — Mail template creation UI and data injection.
- `resources/js/app.js` — JS logic for placeholder replacement and UI interactivity.
- `database/migrations/` — Schema definitions; check for missing fields if data issues arise.
- `README.md` — Mailpit setup and general project info.

## Example Pattern: Mail Placeholder Replacement
- Placeholders like `[wederpartij_naam]` are replaced in JS using data from hidden JSON elements.
- Only keys matching `/^\[.*\]$/` are replaced to avoid accidental word changes.
- If a placeholder is not working, check both the Blade view for missing JSON and the controller for missing data fields.

---

If any section is unclear or missing, please provide feedback so this guide can be improved for future AI agents.
