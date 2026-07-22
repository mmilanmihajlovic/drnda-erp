<?php
namespace App\Http\Requests\Settings;
use Illuminate\Foundation\Http\FormRequest;
class StoreWorkerRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['name'=>'required|string|max:100','phone'=>'nullable|string|max:30','department_id'=>'nullable|integer','active'=>'boolean'];
    }
}
