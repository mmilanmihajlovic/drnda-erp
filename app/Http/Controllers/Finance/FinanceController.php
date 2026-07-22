<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller {

    /**
     * Glavna strana Finansija — invoice ledger + dashboard statistike.
     */
    public function index(Request $request) {
        $query = Invoice::with(['funeralCase', 'activePayments'])->latest('issue_date');

        // Filter po statusu
        if ($status = $request->input('status')) {
            if ($status === 'stornirano') {
                $query->where('status', 'stornirano');
            } else {
                $query->where('status', 'aktivan')->where('payment_status', $status);
            }
        }

        // Pretraga
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('funeralCase', fn($c) => $c->where('case_number', 'like', "%{$search}%"));
            });
        }

        // Filter po datumu
        if ($from = $request->input('date_from')) {
            $query->whereDate('issue_date', '>=', $from);
        }
        if ($to = $request->input('date_to')) {
            $query->whereDate('issue_date', '<=', $to);
        }

        $invoices = $query->paginate(25)->withQueryString();

        // Dashboard stats
        $stats = [
            'total_invoiced'   => Invoice::active()->thisMonth()->sum('total'),
            'total_collected'  => Invoice::active()->thisMonth()->sum('paid_amount'),
            'unpaid_count'     => Invoice::unpaid()->count(),
            'unpaid_amount'    => Invoice::unpaid()->sum('remaining_amount'),
            'partial_count'    => Invoice::active()->where('payment_status', 'delimicno_placeno')->count(),
            'paid_count'       => Invoice::active()->where('payment_status', 'placeno')->thisMonth()->count(),
        ];

        $recentPayments = Payment::with(['invoice.funeralCase', 'createdBy'])
            ->active()
            ->latest('paid_at')
            ->take(8)
            ->get();

        return view('finance.index', compact('invoices', 'stats', 'recentPayments'));
    }

    /**
     * Detalj racuna u Finansijama (sa evidencijom uplata).
     */
    public function show(Invoice $invoice) {
        $invoice->load([
            'funeralCase',
            'items.department',
            'items.caseItem',
            'activePayments.createdBy',
            'payments' => fn($q) => $q->orderByDesc('paid_at'),
            'payments.createdBy',
            'generatedBy',
        ]);
        $byDept = $invoice->items->groupBy('department_id');

        return view('finance.show', compact('invoice', 'byDept'));
    }

    /**
     * Storniraj racun.
     * Status = 'stornirano', payment_status = 'stornirano'.
     * Ne moze se stornirati vec placeni racun bez potvrde.
     */
    public function storno(Request $request, Invoice $invoice) {
        $request->validate([
            'storno_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        if ($invoice->isStorno()) {
            return back()->with('error', 'Racun je vec storniran.');
        }

        DB::transaction(function () use ($invoice, $request): void {
            $old = $invoice->only(['status', 'payment_status']);

            $invoice->update([
                'status'         => 'stornirano',
                'payment_status' => 'stornirano',
                'storno_reason'  => $request->input('storno_reason'),
                'storno_by'      => auth()->id(),
                'storno_at'      => now(),
            ]);

            AuditLog::record('storno_invoice', $invoice, $old, [
                'storno_reason' => $request->input('storno_reason'),
            ]);

            Log::info("Invoice storno: {$invoice->invoice_number}");
        });

        return redirect()
            ->route('finance.show', $invoice)
            ->with('success', "Racun {$invoice->invoice_number} je storniran.");
    }
}
