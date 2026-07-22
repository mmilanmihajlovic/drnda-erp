<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CaseDepartment extends Model {
    protected $fillable = ['case_id','department_id','status','assigned_worker_id','assigned_vehicle_id','note','closed_at','closed_by'];
    protected function casts(): array { return ['closed_at' => 'datetime']; }
    public function funeralCase() { return $this->belongsTo(FuneralCase::class, 'case_id'); }
    public function department()  { return $this->belongsTo(Department::class); }
    public function worker()      { return $this->belongsTo(Worker::class, 'assigned_worker_id'); }
    public function vehicle()     { return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id'); }
    public function tasks()       { return $this->hasMany(Task::class); }
    public function documents()   { return $this->hasMany(Document::class); }
}
