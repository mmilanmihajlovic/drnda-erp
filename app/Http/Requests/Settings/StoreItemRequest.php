<?php
namespace App\Http\Requests\Settings;
use Illuminate\Foundation\Http\FormRequest;
class StoreItemRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['name'=>'required|string|max:150','description'=>'nullable|string','price'=>'required|numeric|min:0','unit'=>'required|string|max:20','category'=>'nullable|string|max:50'];
    }
}
