<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateInstanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'key' => 'required|string',
            'status' => 'required|string',
            'startPublishingDate' => 'required|date_format:Y-m-d H:i:s',
            'endPublishingDate' => 'nullable|date_format:Y-m-d H:i:s',
            'attributes' => 'array',
            'relations' => 'array',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => (int) $this->route()->parameter('id'),
        ]);
    }
}
