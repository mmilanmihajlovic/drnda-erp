<?php
namespace App\Http\Requests\JNA;
use Illuminate\Foundation\Http\FormRequest;
class StoreTaskRequest extends FormRequest {
    public function authorize(): bool { return $this->user()->hasAnyRole(['administrator','menadzment','jna']); }
    public function rules(): array {
        return [
            'title'              => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:2000'],
            'assigned_worker_id' => ['nullable', 'exists:workers,id'],
            'vehicle_id'         => ['nullable', 'exists:vehicles,id'],
            'departure_location' => ['nullable', 'string', 'max:255'],
            'destination'        => ['nullable', 'string', 'max:255'],
            'due_at'             => ['nullable', 'date'],
            'note'               => ['nullable', 'string', 'max:1000'],
        ];
    }
}
