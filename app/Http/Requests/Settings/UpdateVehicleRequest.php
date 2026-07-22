<?php
namespace App\Http\Requests\Settings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateVehicleRequest extends FormRequest {
    public function authorize(): bool { return \$this->user()->hasAnyRole(['administrator','menadzment']); }
    public function rules(): array { return ['name' => ['required','string','max:255'], 'registration_number' => ['required','string','max:20', Rule::unique('vehicles')->ignore(\$this->route('vehicle'))], 'type' => ['required','string','max:100'], 'active' => ['boolean']]; }
}
