<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model {
    public $timestamps = false;
    protected $fillable = [
        'user_id','action','entity_type','entity_id',
        'old_values','new_values','ip_address',
    ];
    protected function casts(): array {
        return ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];
    }
    public function user() { return $this->belongsTo(User::class); }

    public static function record(
        string $action,
        Model  $entity,
        array  $oldValues = [],
        array  $newValues = []
    ): void {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'entity_type' => class_basename($entity),
            'entity_id'   => $entity->getKey(),
            'old_values'  => $oldValues ?: null,
            'new_values'  => $newValues ?: null,
            'ip_address'  => request()->ip(),
        ]);
    }
}
