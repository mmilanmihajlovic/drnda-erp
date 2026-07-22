<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
    protected $fillable = [
        'invoice_number', 'case_id', 'customer_name', 'issue_date',
        'total', 'paid_amount', 'remaining_amount',
        'payment_status', 'status', 'generated_at', 'generated_by',
    ];
    protected function casts(): array {
        return [
            'issue_date'    => 'date',
            'generated_at'  => 'datetime',
            'total'         => 'decimal:2',
            'paid_amount'   => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function funeralCase()  { return $this->belongsTo(FuneralCase::class, 'case_id'); }
    public function items()        { return $this->hasMany(InvoiceItem::class); }
    public function generatedBy()  { return $this->belongsTo(User::class, 'generated_by'); }
    public function payments()     { return $this->hasMany(Payment::class); }

    /** Items grupisani po odeljenju */
    public function itemsByDepartment(): \Illuminate\Support\Collection {
        return $this->items()->with(['department', 'caseItem'])->get()->groupBy('department_id');
    }

    /** Automatski status na osnovu uplata */
    public function recalculatePaymentStatus(): void {
        if ($this->paid_amount <= 0) {
            $status = 'neplaceno';
        } elseif ($this->paid_amount >= $this->total) {
            $status = 'placeno';
        } else {
            $status = 'delimicno_placeno';
        }
        $this->update([
            'payment_status'   => $status,
            'remaining_amount' => max(0, $this->total - $this->paid_amount),
        ]);
    }

    /** Auto-generiši broj računa: YYYY-NNNN */
    public static function generateInvoiceNumber(): string {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count();
        return sprintf('%d-%04d', $year, $count + 1);
    }

    public function isPaid(): bool           { return $this->payment_status === 'placeno'; }
    public function isPartiallyPaid(): bool  { return $this->payment_status === 'delimicno_placeno'; }
    public function isUnpaid(): bool         { return $this->payment_status === 'neplaceno'; }

    public function scopeActive($q)  { return $q->where('status', 'aktivan'); }
    public function scopeUnpaid($q)  { return $q->where('payment_status', 'neplaceno'); }
}
