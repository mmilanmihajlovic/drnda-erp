# DRNDA ERP 3.0 - Windows Setup Script
# Pokreni iz PowerShell-a: .\setup.ps1
# Ili: powershell -ExecutionPolicy Bypass -File setup.ps1

param(
    [string]$DBHost     = "127.0.0.1",
    [string]$DBPort     = "3306",
    [string]$DBName     = "drnda_erp",
    [string]$DBUser     = "root",
    [string]$DBPassword = ""
)

$ErrorActionPreference = "Stop"

function Write-Step($msg) { Write-Host "`n[>>] $msg" -ForegroundColor Yellow }
function Write-OK($msg)   { Write-Host "  [+] $msg" -ForegroundColor Green }
function Write-Info($msg) { Write-Host "  [i] $msg" -ForegroundColor Cyan }
function Write-Fail($msg) { Write-Host "  [!] $msg" -ForegroundColor Red; exit 1 }

Clear-Host
Write-Host ""
Write-Host "================================================" -ForegroundColor DarkYellow
Write-Host "   DRNDA ERP 3.0 - Windows Setup" -ForegroundColor Yellow
Write-Host "   Laravel 12 + PHP 8.3 + MySQL 8" -ForegroundColor Yellow
Write-Host "================================================" -ForegroundColor DarkYellow
Write-Host ""

# Provjera zahteva
Write-Step "1/7  Provjera zahteva"
if (-not (Get-Command php -ErrorAction SilentlyContinue))   { Write-Fail "PHP nije pronadjen. Instaluj Laravel Herd." }
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) { Write-Fail "Composer nije pronadjen. Preuzmi sa getcomposer.org" }
if (-not (Get-Command npm -ErrorAction SilentlyContinue))   { Write-Fail "Node.js/npm nije pronadjen. Preuzmi sa nodejs.org" }
Write-OK "PHP:      $(php -r 'echo PHP_VERSION;')"
Write-OK "Composer: $(composer --version --no-ansi 2>$null | Select-String -Pattern '\d+\.\d+\.\d+' | ForEach-Object { $_.Matches[0].Value })"
Write-OK "npm:      $(npm --version)"

# MySQL podaci
Write-Step "2/7  MySQL konfiguracija"
Write-Host ""
Write-Host "  Unesi MySQL podatke (Enter = podrazumevana vrednost):" -ForegroundColor Cyan
Write-Host ""
$inputHost = Read-Host "  DB host     [$DBHost]"
if ($inputHost) { $DBHost = $inputHost }
$inputPort = Read-Host "  DB port     [$DBPort]"
if ($inputPort) { $DBPort = $inputPort }
$inputName = Read-Host "  DB naziv    [$DBName]"
if ($inputName) { $DBName = $inputName }
$inputUser = Read-Host "  DB korisnik [$DBUser]"
if ($inputUser) { $DBUser = $inputUser }
$inputPass = Read-Host "  DB lozinka  [prazno]" -AsSecureString
if ($inputPass.Length -gt 0) {
    $DBPassword = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto(
        [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($inputPass)
    )
}
Write-Host ""
Write-OK "MySQL: $DBUser@${DBHost}:${DBPort}/$DBName"

# Kreiranje baze
if (Get-Command mysql -ErrorAction SilentlyContinue) {
    Write-Info "Kreiranje baze '$DBName'..."
    $createSQL = "CREATE DATABASE IF NOT EXISTS ``$DBName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    if ($DBPassword -eq "") {
        mysql -h $DBHost -P $DBPort -u $DBUser -e $createSQL 2>$null
    } else {
        mysql -h $DBHost -P $DBPort -u $DBUser "-p$DBPassword" -e $createSQL 2>$null
    }
    Write-OK "Baza '$DBName' kreirana/postoji"
} else {
    Write-Info "mysql CLI nije pronadjen. Kreiraj bazu rucno: CREATE DATABASE $DBName;"
}

# .env konfiguracija
Write-Step "3/7  Konfiguracija .env"
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-OK ".env kreiran iz .env.example"
} else {
    Write-OK ".env vec postoji"
}
(Get-Content ".env") `
    -replace "^DB_HOST=.*", "DB_HOST=$DBHost" `
    -replace "^DB_PORT=.*", "DB_PORT=$DBPort" `
    -replace "^DB_DATABASE=.*", "DB_DATABASE=$DBName" `
    -replace "^DB_USERNAME=.*", "DB_USERNAME=$DBUser" `
    -replace "^DB_PASSWORD=.*", "DB_PASSWORD=$DBPassword" |
    Set-Content ".env"
php artisan key:generate --ansi
Write-OK ".env konfigurisan"

# Composer install
Write-Step "4/7  PHP zavisnosti (composer install)"
composer install --no-interaction --prefer-dist --optimize-autoloader
Write-OK "PHP paketi instalirani"

# npm install + build
Write-Step "5/7  JS zavisnosti i asset build"
npm install --silent
npm run build
Write-OK "Assets kompajlirani"

# Migracije + seed
Write-Step "6/7  Baza podataka"
Write-Info "Publishovanje Spatie permission tabela..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --quiet 2>$null
Write-Info "Pokretanje migracija..."
php artisan migrate --force
Write-OK "Migracije zavrsene"
Write-Info "Pokretanje seedera..."
php artisan db:seed --force
Write-OK "Seederi zavrseni"

# Optimizacija
Write-Step "7/7  Ciscenje i optimizacija"
php artisan config:clear
php artisan route:clear
php artisan view:clear
Write-OK "Kesevi obrisani"

# Finalni izvestaj
Write-Host ""
Write-Host "================================================" -ForegroundColor Green
Write-Host "   DRNDA ERP 3.0 spreman za rad!" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Green
Write-Host ""
Write-Host "  URL aplikacije:" -ForegroundColor White
Write-Host "    http://drnda-erp.test    (Laravel Herd)" -ForegroundColor Cyan
Write-Host "    http://127.0.0.1:8000   (artisan serve)" -ForegroundColor DarkCyan
Write-Host ""
Write-Host "  Kredencijali:" -ForegroundColor White
Write-Host "    Email:    admin@drnda.local" -ForegroundColor Green
Write-Host "    Lozinka:  admin123" -ForegroundColor Green
Write-Host "    Uloga:    administrator" -ForegroundColor Green
Write-Host ""
Write-Host "  Napomena za Herd:" -ForegroundColor Yellow
Write-Host "    Aplikacija je ODMAH dostupna na http://drnda-erp.test" -ForegroundColor Yellow
Write-Host "    Nije potreban 'php artisan serve'" -ForegroundColor Yellow
Write-Host ""
Write-Host "================================================" -ForegroundColor DarkGray
