<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseItem extends Model {
    protected $fillable = [
        'case_id','department_id','item_id','description',
        'quantity','unit_price','total','billable','note','locked_at',
    ];
    protected function casts(): array {
        return [
            'quantity'    => 'decimal:2',
            'unit_price'  => 'decimal:2',
            'total'       => 'decimal:2',
            'billable'    => 'boolean',
            'locked_at'   => 'datetime',
        ];
    }
    public function funeralCase()  { return $this->belongsTo(FuneralCase::class, 'case_id'); }
    public function department()   { return $this->belongsTo(Department::class); }
    public function item()         { return $this->belongsTo(Item::class); }
    public function isLocked(): bool { return !is_null($this->locked_at); }

    protected static function booted(): void {
        static::saving(function (self $ci) {
            $ci->total = round($ci->quantity * $ci->unit_price, 2);
        });
    }
}
