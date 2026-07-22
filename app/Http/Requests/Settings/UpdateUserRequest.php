<?php
namespace App\Http\Requests\Settings;
use Illuminate\Foundation\Http\FormRequest;
class UpdateUserRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['name'=>'required|string|max:100','email'=>'required|email|max:150','role'=>'required|string','active'=>'boolean'];
    }
}
