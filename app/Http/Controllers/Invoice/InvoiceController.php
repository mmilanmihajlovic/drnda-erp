<?php
namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\FuneralCase;
use App\Models\Invoice;
use App\Services\GenerateInvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller {
    public function __construct(private GenerateInvoiceService $service) {}

    /**
     * Generisi racun na zahtev (ako nije vec generisan).
     * Normalno se generise automatski kad se sva 3 odeljenja zatvore.
     */
    public function generate(FuneralCase $case) {
        try {
            $invoice = $this->service->generate($case);
            return redirect()->route('invoices.show', $invoice)
                ->with('success', "Racun {$invoice->invoice_number} je generisan.");
        } catch (\RuntimeException $e) {
            return redirect()->route('cases.show', $case)->with('error', $e->getMessage());
        }
    }

    /** Prikaz detalja racuna */
    public function show(Invoice $invoice) {
        $invoice->load([
            'funeralCase',
            'items.department',
            'items.caseItem',
            'generatedBy',
        ]);
        $byDept = $invoice->items->groupBy('department_id');
        return view('invoice.show', compact('invoice', 'byDept'));
    }

    /** Print/PDF view — bez sidebar-a, print-friendly CSS */
    public function print(Invoice $invoice) {
        $invoice->load([
            'funeralCase',
            'items.department',
            'items.caseItem',
            'generatedBy',
        ]);
        $byDept = $invoice->items->groupBy('department_id');
        return view('invoice.print', compact('invoice', 'byDept'));
    }
}
