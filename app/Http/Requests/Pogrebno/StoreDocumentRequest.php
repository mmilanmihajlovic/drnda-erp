<?php
namespace App\Http\Requests\Pogrebno;
use Illuminate\Foundation\Http\FormRequest;
class StoreDocumentRequest extends FormRequest {
    public function authorize(): bool { return $this->user()->hasAnyRole(['administrator','menadzment','pogrebno']); }
    public function rules(): array {
        return [
            'file' => ['required','file','max:20480','mimes:pdf,doc,docx,jpg,jpeg,png,gif,xlsx,xls'],
            'name' => ['nullable','string','max:255'],
            'type' => ['required','string','max:100'],
        ];
    }
}
