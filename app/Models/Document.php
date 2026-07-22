<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Document extends Model {
    protected $fillable = ['case_id','department_id','name','type','file_path','note'];
    public function funeralCase()    { return $this->belongsTo(FuneralCase::class, 'case_id'); }
    public function caseDepartment() { return $this->belongsTo(CaseDepartment::class, 'department_id'); }
}
