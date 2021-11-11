<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class DeleteInstanceRequest extends FormRequest
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
            'uuid' => 'required|string|uuid',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'uuid' => (string) $this->route()->parameter('uuid'),
        ]);
    }
}
