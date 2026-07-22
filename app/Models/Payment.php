<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
    protected $fillable = ['invoice_id','amount','method','note','paid_at','recorded_by','voided','voided_reason','voided_at','voided_by'];
    protected function casts(): array { return ['paid_at'=>'datetime','voided_at'=>'datetime','amount'=>'decimal:2']; }
    public function invoice() { return $this->belongsTo(Invoice::class); }
}
