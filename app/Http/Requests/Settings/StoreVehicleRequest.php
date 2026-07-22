<?php
namespace App\Http\Requests\Settings;
use Illuminate\Foundation\Http\FormRequest;
class StoreVehicleRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['name'=>'required|string|max:100','registration_number'=>'required|string|max:20|unique:vehicles','type'=>'required|string|max:50'];
    }
}
