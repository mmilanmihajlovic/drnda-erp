<?php
require __DIR__ . '/../app/bootstrap.php';
write_data('users', [[
    'id' => 1,
    'name' => 'Administrator',
    'email' => 'admin@drnda.local',
    'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
    'role' => 'ADMIN',
]]);
write_data('cases', [[
    'id' => 1,
    'case_number' => 'PO-' . date('Y') . '-000001',
    'deceased_name' => 'Petar Petrović',
    'deceased_birth_date' => null,
    'deceased_death_date' => date('Y-m-d'),
    'contact_name' => 'Milan Petrović',
    'contact_phone' => '064 123 4567',
    'funeral_at' => date('Y-m-d') . ' 13:00:00',
    'funeral_place' => 'Staro groblje Požarevac',
    'case_type' => 'DOMACI',
    'stage' => 'NOVO',
    'notes' => 'Primer slučaja za početni ekran.',
    'created_by' => 1,
    'created_at' => date(DATE_ATOM),
]]);
echo "Podaci su uspešno kreirani.\n";
