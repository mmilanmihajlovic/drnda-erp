<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FlowerOrderItem extends Model {
    protected $fillable = ['order_id','name','description','quantity','price'];
    protected function casts(): array { return ['price'=>'decimal:2']; }
    public function order() { return $this->belongsTo(FlowerOrder::class, 'order_id'); }
}
