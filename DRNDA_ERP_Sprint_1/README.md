# DRNDA ERP - Sprint 1

Početni samostalni MVP bez eksternih zavisnosti. Koristi PHP 8.2+ i JSON datoteke za razvojno skladište.

## Pokretanje

```bash
php database/init.php
php -S 127.0.0.1:8080 -t public public/router.php
```

Otvorite `http://127.0.0.1:8080`.

## Prijava

- Email: `admin@drnda.local`
- Lozinka: `admin123`

## Urađeno

- prijava i odjava korisnika
- osnovna navigacija
- komandni centar
- lista i pretraga slučajeva
- kreiranje novog slučaja
- centralni detalj slučaja sa karticama odeljenja
- responsive izgled
- CSRF zaštita i password hashing

## Produkciona baza

JSON je namerno izabran samo da prototip radi odmah. Pre produkcije se uvodi PostgreSQL ili MySQL kroz repository sloj.

## Sledeći sprint

- porudžbina Pogrebnog
- zadaci Pogrebnog i Operative
- statusi zadataka
- zajednička lista otvorenih obaveza na detalju slučaja
