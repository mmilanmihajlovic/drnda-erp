<?php
namespace App\Http\Requests\Settings;
use Illuminate\Foundation\Http\FormRequest;
class StoreUserRequest extends FormRequest {
    public function authorize(): bool { return \$this->user()->hasRole('administrator'); }
    public function rules(): array { return ['name' => ['required','string','max:255'], 'email' => ['required','email','unique:users,email'], 'password' => ['required','string','min:8','confirmed'], 'role' => ['nullable','string','exists:roles,name'], 'active' => ['boolean']]; }
}
