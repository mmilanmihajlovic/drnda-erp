<?php
namespace App\Http\Requests\Cvecara;
use Illuminate\Foundation\Http\FormRequest;
class AssignWorkerRequest extends FormRequest {
    public function authorize(): bool { return $this->user()->hasAnyRole(['administrator','menadzment','cvecara']); }
    public function rules(): array { return ['worker_id' => ['nullable','exists:workers,id']]; }
}
