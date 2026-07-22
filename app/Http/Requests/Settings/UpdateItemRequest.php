<?php
namespace App\Http\Requests\Settings;
use Illuminate\Foundation\Http\FormRequest;
class UpdateItemRequest extends FormRequest {
    public function authorize(): bool { return \$this->user()->hasAnyRole(['administrator','menadzment']); }
    public function rules(): array { return ['department_id' => ['required','exists:departments,id'], 'name' => ['required','string','max:255'], 'type' => ['required','string','max:100'], 'default_price' => ['required','numeric','min:0'], 'active' => ['boolean']]; }
}
