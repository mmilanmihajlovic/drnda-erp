#!/bin/bash
# ═══════════════════════════════════════════════════════════════
#  DRNDA ERP 3.0 — Automatski setup
#  Pokreni: bash setup.sh
# ═══════════════════════════════════════════════════════════════

set -e  # Zaustavi se na prvoj grešci

# ── Boje ────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; GOLD='\033[0;33m'; NC='\033[0m'; BOLD='\033[1m'

ok()   { echo -e "${GREEN}✓${NC}  $1"; }
info() { echo -e "${BLUE}→${NC}  $1"; }
warn() { echo -e "${YELLOW}⚠${NC}  $1"; }
err()  { echo -e "${RED}✗  GREŠKA: $1${NC}"; exit 1; }
step() { echo -e "\n${GOLD}${BOLD}[$1]${NC} $2"; }

# ── Header ──────────────────────────────────────────────────────
clear
echo ""
echo -e "${GOLD}${BOLD}╔═══════════════════════════════════════╗${NC}"
echo -e "${GOLD}${BOLD}║       DRNDA ERP 3.0 — Setup          ║${NC}"
echo -e "${GOLD}${BOLD}║       Laravel 12 · PHP 8.3+           ║${NC}"
echo -e "${GOLD}${BOLD}╚═══════════════════════════════════════╝${NC}"
echo ""

# ── Proveri da li smo u pravom folderu ──────────────────────────
if [ ! -f "composer.json" ]; then
  err "Nisi u drnda-erp folderu. Pokreni: cd ~/Herd/drnda-erp && bash setup.sh"
fi

PROJECT_NAME=$(basename "$(pwd)")
info "Folder: $(pwd)"
echo ""

# ── Proveri zahteve ─────────────────────────────────────────────
step "1/7" "Provera zahteva sistema"

command -v php   >/dev/null 2>&1 || err "PHP nije pronađen. Instaluj Laravel Herd."
command -v composer >/dev/null 2>&1 || err "Composer nije pronađen. Skini sa getcomposer.org"
command -v npm   >/dev/null 2>&1 || err "Node.js/npm nije pronađen. Skini sa nodejs.org"
command -v mysql >/dev/null 2>&1 || warn "MySQL CLI nije pronađen — preskačem kreiranje baze."
PHP_VER=$(php -r "echo PHP_VERSION;")
ok "PHP $PHP_VER"
ok "Composer $(composer --version --no-ansi 2>/dev/null | head -1 | awk '{print $3}')"
ok "npm $(npm --version)"
echo ""

# ── MySQL podaci ─────────────────────────────────────────────────
step "2/7" "MySQL konfiguracija"
echo ""
echo -e "${YELLOW}Unesi MySQL podatke (pritisni Enter za podrazumevane vrednosti):${NC}"
echo ""

read -p "  DB host     [127.0.0.1]: " DB_HOST;    DB_HOST=${DB_HOST:-127.0.0.1}
read -p "  DB port     [3306]:      " DB_PORT;    DB_PORT=${DB_PORT:-3306}
read -p "  DB naziv    [drnda_erp]: " DB_NAME;    DB_NAME=${DB_NAME:-drnda_erp}
read -p "  DB korisnik [root]:      " DB_USER;    DB_USER=${DB_USER:-root}
read -s -p "  DB lozinka  [prazno]:    " DB_PASS;   echo ""

echo ""
ok "MySQL: $DB_USER@$DB_HOST:$DB_PORT/$DB_NAME"

# ── Kreiranje baze ───────────────────────────────────────────────
if command -v mysql >/dev/null 2>&1; then
  info "Kreiranje baze '$DB_NAME'..."
  if [ -z "$DB_PASS" ]; then
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -e \
      "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" \
      2>/dev/null && ok "Baza '$DB_NAME' kreirana/postoji" || warn "Baza već postoji ili greška — nastavlja se"
  else
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" -e \
      "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" \
      2>/dev/null && ok "Baza '$DB_NAME' kreirana/postoji" || warn "Baza već postoji ili greška — nastavlja se"
  fi
else
  warn "Nema mysql CLI — kreiraj bazu ručno: CREATE DATABASE $DB_NAME;"
fi
echo ""

# ── .env konfiguracija ───────────────────────────────────────────
step "3/7" "Konfiguracija .env"

if [ ! -f ".env" ]; then
  cp .env.example .env
  ok ".env kreiran"
else
  ok ".env već postoji"
fi

# Upiši MySQL podatke u .env
sed -i.bak "s|^DB_HOST=.*|DB_HOST=$DB_HOST|"        .env
sed -i.bak "s|^DB_PORT=.*|DB_PORT=$DB_PORT|"        .env
sed -i.bak "s|^DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" .env
sed -i.bak "s|^DB_USERNAME=.*|DB_USERNAME=$DB_USER|" .env
sed -i.bak "s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" .env
rm -f .env.bak

# Generiši APP_KEY
php artisan key:generate --ansi
ok ".env konfigurisan"
echo ""

# ── Composer install ─────────────────────────────────────────────
step "4/7" "PHP zavisnosti (composer install)"
composer install --no-interaction --prefer-dist --optimize-autoloader
ok "PHP paketi instalirani"
echo ""

# ── npm install + build ──────────────────────────────────────────
step "5/7" "JS zavisnosti i asset build"
npm install --silent
npm run build
ok "Assets kompajlirani"
echo ""

# ── Spatie publish + migracije + seed ────────────────────────────
step "6/7" "Baza podataka — migracije i seederi"

info "Publishovanje Spatie permission tabela..."
php artisan vendor:publish \
  --provider="Spatie\Permission\PermissionServiceProvider" \
  --quiet 2>/dev/null || true

info "Pokretanje migracija..."
php artisan migrate --force
ok "Migracije završene (11 tabela)"

info "Pokretanje seedera (odeljenja, uloge, admin korisnik)..."
php artisan db:seed --force
ok "Seederi završeni"
echo ""

# ── Čišćenje keševa ─────────────────────────────────────────────
step "7/7" "Čišćenje i optimizacija"
php artisan config:clear
php artisan route:clear
php artisan view:clear
ok "Keševi obrisani"
echo ""

# ── Finalni izveštaj ─────────────────────────────────────────────
echo -e "${GOLD}${BOLD}╔═══════════════════════════════════════════╗${NC}"
echo -e "${GOLD}${BOLD}║   ✓  DRNDA ERP 3.0 spreman za rad!       ║${NC}"
echo -e "${GOLD}${BOLD}╚═══════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${BOLD}URL aplikacije:${NC}"
echo -e "    ${BLUE}http://drnda-erp.test${NC}    ${YELLOW}(Laravel Herd)${NC}"
echo -e "    ${BLUE}http://127.0.0.1:8000${NC}   ${YELLOW}(artisan serve)${NC}"
echo ""
echo -e "  ${BOLD}Kredencijali za prijavu:${NC}"
echo -e "    Email:    ${GREEN}admin@drnda.local${NC}"
echo -e "    Lozinka:  ${GREEN}admin123${NC}"
echo -e "    Uloga:    ${GREEN}administrator${NC}"
echo ""
echo -e "  ${BOLD}Lokacija projekta:${NC}"
echo -e "    ${BLUE}$(pwd)${NC}"
echo ""
echo -e "  ${YELLOW}Napomena za Herd korisnika:${NC}"
echo -e "    Aplikacija je ODMAH dostupna na ${BLUE}http://drnda-erp.test${NC}"
echo -e "    Nije potreban 'php artisan serve'"
echo ""
echo -e "${GOLD}══════════════════════════════════════════════════${NC}"
echo ""
