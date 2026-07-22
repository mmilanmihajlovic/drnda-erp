<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Department extends Model {
    protected $fillable = ['name', 'slug', 'is_active'];
    public function caseDepartments() { return $this->hasMany(CaseDepartment::class); }
}
