<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Worker extends Model {
    protected $fillable = ['user_id', 'department_id', 'name', 'active'];
    protected function casts(): array { return ['active' => 'boolean']; }
    public function user()       { return \$this->belongsTo(User::class); }
    public function department() { return \$this->belongsTo(Department::class); }
    public function scopeActive($query) { return $query->where('active', true); }
}
