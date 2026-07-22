<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model {
    protected $fillable = [
        'invoice_id', 'case_item_id', 'department_id',
        'description', 'quantity', 'unit_price', 'total',
    ];
    protected function casts(): array {
        return ['quantity' => 'decimal:2', 'unit_price' => 'decimal:2', 'total' => 'decimal:2'];
    }
    public function invoice()    { return $this->belongsTo(Invoice::class); }
    public function caseItem()   { return $this->belongsTo(CaseItem::class); }
    public function department() { return $this->belongsTo(Department::class); }
}
