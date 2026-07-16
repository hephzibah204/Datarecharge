# Landing Page Assets - Project Context

## Tech Stack
- PHP 8.4 (built-in server: `php -S localhost:8000 -t . router.php`)
- SQLite 3.51 (`database/providers.db`)
- MySQL dump originally, fully migrated to SQLite
- MySQL dump at `C:\Users\hephz\Downloads\xtfphfml_data (1).sql` (complete original, 289KB)

## Database
- **File**: `database/providers.db`
- **Schema**:
  - `database/schema.sqlite.sql` - Provider & Pricing system (Phase 1 tables: providers, provider_pricing, price_overrides, provider_logs, provider_performance_metrics + views)
  - Remaining 38+ tables created dynamically from MySQL dump by `database/import-mysql.php`
- **Import**: Run `php database/import-mysql.php` to rebuild from scratch

## Key Credentials (from imported data)
- Admin users: `sysusers` table (Admin pass: `Anuoluwapo11@!`, Admi pass: `admin`)
- Subscribers are in `subscribers` table (sPass values are MD5 hashes)
- Admin tool access via `sysusers` table on the management dashboard

## Endpoints
- Registration: `mobile/register/register1.php`
- Login: `mobile/home/includes/route.php` with various actions
- Admin: admin dashboard (likely `/mobile/admin/`)

## Import Script
`database/import-mysql.php` handles the full MySQL-to-SQLite conversion:
- Phase 1: Provider & Pricing system from `schema.sqlite.sql`
- Phase 2: Parse MySQL dump line-by-line, extract SQL statements
- Phase 3: Convert MySQL types to SQLite, execute all statements
- Handles: type conversion, backtick escaping, character set/collation removal, view creation

## Data Summary (after import)
- 44 total tables
- 102 subscribers, 389 transactions, 666 userlogin sessions, 195 uservisits
- All provider system tables populated
- Airtime, data, cable, electricity, exam pricing configured
- API configurations with provider endpoints (Alrahuzdata, Legitdataway, SME Plug, etc.)

## Login Fixes (2026-07-06)
Both user and admin login were broken ("Processing..." hangs). Three issues fixed:

1. **`auto_loader.php` wrong relative paths** (`mobile/home/includes/auto_loader.php` + `admin/dashboard/includes/auto_loader.php`):
   - `../../core/helpers/...` resolved to wrong directory (CWD is the script's dir in built-in server)
   - Fixed to `../../../core/helpers/...` (3 levels up to project root)
   - Also reduced redundancy in admin auto_loader

2. **SQLite PDO `rowCount()` returns 0 for SELECT queries** (all model files):
   - `PDO::rowCount()` on SELECT queries is unreliable with SQLite — always returns 0
   - Fixed 7 occurrences in `core/Models/Account.php` by checking `$result` (fetch return value) instead of `$query->rowCount() > 0`
   - Affected: `verifyAdminAccount`, `registerUser`, `loginUser`, `loginUserFingerPrint`, `recoverUserLogin`, `verifyRecoveryCode`, `checkMonnifyAccountRef`
   - ⚠️ Other model files (`SubscriberModel.php`, `ApiModel.php`, `AdminModel.php`, `ProviderModel.php`) likely have the same issue — grep for `->rowCount()` to find them

3. **PHP 8.4 deprecation warnings** (`FILTER_SANITIZE_STRING`) were being prepended to JSON responses, causing `JSON.parse()` to fail. Fixed by adding `ini_set('display_errors', 0)` and `error_reporting(E_ALL & ~E_DEPRECATED)` in both route.php files.

## Admin Dashboard Pages (2026-07-06)

### Provider Management
- **Page**: `admin/dashboard/providers.php` — wrapper that loads into admin template
- **Content**: `admin/dashboard/providers/dashboard.php` — renders provider list, add/edit forms, detail views via `ProviderController`
- **Routing**: `admin/dashboard/includes/route.php` handles `$_POST["provider-action"]` for CRUD operations
- **Sidebar**: `admin/dashboard/includes/sidebar.php` — "Providers" link (fa-plug icon) under Services
- **Controller**: `core/Controllers/ProviderController.php` — extends Controller, all CRUD + pricing/override management
- **Model**: `core/Models/ProviderModel.php` — full provider, pricing, override, log, stats queries
- **URL**: Admin → `?url=providers`

### NIN Modifications
- **Page**: `admin/dashboard/ni-modifications.php` — standalone admin page listing NIN modification requests with filter, view details, review actions
- **Controller**: `core/Controllers/NINModificationController.php` — extends AdminController, fixed broken string concatenation and missing methods, delegates to `Modification` model
- **Model**: `core/Models/Modification.php` — CRUD for `nin_modifications` table
- **Sidebar**: "NIN Modifications" link (fa-id-card icon) under Providers
- **URL**: Admin → `?url=ni-modifications`

### Route Handlers Added
- `$_POST["provider-action"]` → `ProviderController::handleProviderRequest()`
- `$_GET["update-mod-status"]` → `NINModificationController::updateModificationStatus()`
- `$_POST["submit-nin-modification"]` (user) → `Subscriber::submitNINModification()`

### Files Created/Modified
- `admin/dashboard/providers.php` (new)
- `admin/dashboard/providers/dashboard.php` (rewritten as include)
- `admin/dashboard/ni-modifications.php` (rewritten standalone)
- `core/Controllers/NINModificationController.php` (rewritten, fixed syntax)
- `admin/dashboard/includes/sidebar.php` (added providers + NIN links)
- `admin/dashboard/includes/route.php` (added provider + NIN handlers)

## NIN Fixes (2026-07-07)

### Issues Fixed
1. **`core/Models/NINModification.php`** — `$result["balance"]` referenced undefined `$result` in two methods (`createNINModificationRequest`, `createNINVerificationRequest`). Fixed by fetching user from DB with `getUserById()`.

2. **`api/nin/index.php`** — Critically broken: used `$this->respond()`, `$this->connect()`, `$this->getSiteSettings()` inside standalone functions (not class methods). Completely rewritten with proper function calls using `$ninModel` and `$controller` objects from the outer scope.

3. **`mobile/home/nin_modifications.php`** — Had full `<html><head><body>` structure but was loaded inside the mobile template (double HTML/body tags). Stripped to content fragment matching other mobile pages.

4. **Missing user modification pages** — Created 4 NIN modification form pages for users:
   - `mobile/home/name_modification.php`
   - `mobile/home/dob_modification.php`
   - `mobile/home/phone_modification.php`
   - `mobile/home/address_modification.php`

5. **Missing route handler** — Added `$_POST["submit-nin-modification"]` → `Subscriber::submitNINModification()` in mobile route.php, which calls `/api/nin/` with `modification_type`.

6. **Missing database tables** — Added `nin_modifications`, `nin_requests`, `nin_price` CREATE TABLE statements to `database/schema.sqlite.sql` with seed data for slip pricing.

### User NIN Modification Flow
- Homepage → "NIN Modify" → `/nin_modifications` (4 card grid: Name, DOB, Phone, Address)
- Each card links to a form page (e.g., `/name_modification`)
- Form collects: modification type, new value, reason
- Submits via POST with `submit-nin-modification` → `Subscriber::submitNINModification()`
- Verifies transaction PIN → calls `/api/nin/` internal API → creates request in `nin_requests` table
- Admin reviews via `?url=ni-modifications` dashboard page

### NIN API Endpoint (`/api/nin/`)
- Accepts `modification_type`, `new_value`, `reason`, `phone`, `ref` in JSON body
- Uses `NINModification` model to create and persist requests
- Returns ref, fee, new_balance on success
