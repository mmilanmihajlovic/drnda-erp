<?php
namespace App\Http\Requests\JNA;
use Illuminate\Foundation\Http\FormRequest;
class CloseDepartmentRequest extends FormRequest {
    public function authorize(): bool { return $this->user()->hasAnyRole(['administrator','menadzment','jna']); }
    public function rules(): array { return ['no_activity' => ['boolean']]; }
}
