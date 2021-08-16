<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateInstanceRequest extends FormRequest
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
            'classKey' => 'required|string',
            'metadata' => 'required|array',
            'metadata.key' => 'required|string',
            'metadata.publication' => 'required|array',
            'metadata.publication.startPublishingDate' => 'required|date_format:Y-m-d H:i:s',
            'attributes' => 'array',
            'relations' => 'array',
        ];
    }
}
