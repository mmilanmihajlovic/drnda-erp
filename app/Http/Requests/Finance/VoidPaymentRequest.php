<?php
namespace App\Http\Requests\Finance;
use Illuminate\Foundation\Http\FormRequest;
class VoidPaymentRequest extends FormRequest {
    public function authorize(): bool { return $this->user()->hasAnyRole(['administrator','menadzment','finansije']); }
    public function rules(): array { return ['void_reason' => ['required', 'string', 'min:5', 'max:1000']]; }
}
