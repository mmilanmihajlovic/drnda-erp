<?php
namespace App\Controllers;

use App\Models\CaseModel;

final class CaseController
{
    public function index(): void
    {
        require_auth();
        $q = trim($_GET['q'] ?? '');
        view('cases/index', ['title' => 'Slučajevi', 'cases' => CaseModel::all($q), 'q' => $q]);
    }

    public function create(): void
    {
        require_auth();
        view('cases/create', ['title' => 'Novi slučaj', 'caseNumber' => CaseModel::nextNumber()]);
    }

    public function store(): void
    {
        require_auth(); verify_csrf();
        $required = ['deceased_name', 'contact_name', 'contact_phone', 'funeral_at'];
        foreach ($required as $field) {
            if (trim($_POST[$field] ?? '') === '') {
                view('cases/create', ['title' => 'Novi slučaj', 'caseNumber' => $_POST['case_number'] ?? CaseModel::nextNumber(), 'error' => 'Popunite obavezna polja.']);
                return;
            }
        }
        $id = CaseModel::create([
            'case_number' => $_POST['case_number'],
            'deceased_name' => trim($_POST['deceased_name']),
            'deceased_birth_date' => $_POST['deceased_birth_date'] ?: null,
            'deceased_death_date' => $_POST['deceased_death_date'] ?: null,
            'contact_name' => trim($_POST['contact_name']),
            'contact_phone' => trim($_POST['contact_phone']),
            'funeral_at' => $_POST['funeral_at'],
            'funeral_place' => trim($_POST['funeral_place'] ?? ''),
            'case_type' => $_POST['case_type'] ?? 'DOMACI',
            'stage' => 'NOVO',
            'notes' => trim($_POST['notes'] ?? ''),
            'created_by' => auth_user()['id'],
        ]);
        redirect('/?page=case&id=' . $id);
    }

    public function show(): void
    {
        require_auth();
        $case = CaseModel::find((int)($_GET['id'] ?? 0));
        if (!$case) { http_response_code(404); exit('Slučaj nije pronađen.'); }
        view('cases/show', ['title' => $case['case_number'], 'case' => $case]);
    }
}
