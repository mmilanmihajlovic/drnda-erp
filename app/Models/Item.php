<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Item extends Model {
    protected $fillable = ['name', 'description', 'price', 'unit', 'category', 'active'];
    protected function casts(): array { return ['price' => 'decimal:2']; }
}
