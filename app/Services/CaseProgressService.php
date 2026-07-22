<?php

namespace App\Services;

use App\Models\CaseDepartment;
use App\Models\FuneralCase;

/**
 * CaseProgressService — sav obracun napretka.
 *
 * Napredak se NIKADA ne unosi rucno.
 * Formula:
 *   progress = (zavrsene_obavezne_stavke / ukupne_obavezne_stavke) * 100
 *
 * U Fazi 2 (jos nema zadataka/porudzbinica):
 *   - status aktivan  → 0%
 *   - status zatvoreno / nema_aktivnosti → 100%
 *
 * Ova logika se prosiruje u Fazi 3-5 dodavanjem stvarnih stavki.
 * Kontroleri i prikazi ne smeju direktno menjati progress polje.
 */
class CaseProgressService
{
    /**
     * Izracunaj i upisi napredak za konkretno odeljenje slucaja.
     * Poziva se automatski pri svakoj promeni statusa, zadatka ili stavke.
     */
    public function recalculate(CaseDepartment $caseDepartment): void
    {
        $progress = $this->computeDepartmentProgress($caseDepartment);

        // Direktan update bez firing eventa, da ne bi doslo do rekurzije
        CaseDepartment::withoutTimestamps(function () use ($caseDepartment, $progress) {
            $caseDepartment->updateQuietly(['progress' => $progress]);
        });
    }

    /**
     * Izracunaj napredak za sva tri odeljenja slucaja.
     */
    public function recalculateAll(FuneralCase $case): void
    {
        $case->caseDepartments->each(fn ($cd) => $this->recalculate($cd));
    }

    /**
     * Vraca izracunati napredak odeljenja (0-100).
     * Logika ce se prosiriti u Fazi 3-5.
     */
    public function computeDepartmentProgress(CaseDepartment $caseDepartment): int
    {
        // Zatvoreno/nema aktivnosti = 100%
        if ($caseDepartment->isClosed()) {
            return 100;
        }

        // Faza 3-5: ovde ce se dodati obracun iz zadataka i porudzbinica
        // Za sada: aktivan bez stavki = 0%
        return 0;
    }

    /**
     * Ukupan napredak slucaja (prosek tri odeljenja).
     * Poziva se za prikaz na listi i dashboardu.
     */
    public function computeCaseProgress(FuneralCase $case): int
    {
        $depts = $case->caseDepartments;

        if ($depts->isEmpty()) {
            return 0;
        }

        return (int) round($depts->avg('progress'));
    }
}
