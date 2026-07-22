<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FlowerOrder extends Model {
    protected $fillable = ['order_number','case_id','customer_name','customer_phone','delivery_type','delivery_address','status','ribbon_text','note','total_price','ordered_at','delivered_at'];
    protected function casts(): array { return ['ordered_at'=>'datetime','delivered_at'=>'datetime','total_price'=>'decimal:2']; }
    public function funeralCase() { return $this->belongsTo(FuneralCase::class, 'case_id'); }
    public function items()       { return $this->hasMany(FlowerOrderItem::class, 'order_id'); }
}
