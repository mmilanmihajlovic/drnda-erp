<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Vehicle extends Model {
    protected $fillable = ['name', 'registration_number', 'type', 'active'];
    protected function casts(): array { return ['active' => 'boolean']; }
    public function scopeActive($query) { return $query->where('active', true); }
}
