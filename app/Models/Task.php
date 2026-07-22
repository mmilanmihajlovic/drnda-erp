<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Task extends Model {
    protected $fillable = ['case_department_id','title','description','status','priority','assigned_worker_id','assigned_vehicle_id','note','departure_location','destination','due_at'];
    protected function casts(): array { return ['due_at' => 'datetime']; }
    public function caseDepartment() { return $this->belongsTo(CaseDepartment::class); }
    public function worker()         { return $this->belongsTo(Worker::class, 'assigned_worker_id'); }
    public function vehicle()        { return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id'); }
}
