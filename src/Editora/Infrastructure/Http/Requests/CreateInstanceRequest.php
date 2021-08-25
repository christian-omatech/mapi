<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Omatech\Mapi\Editora\Infrastructure\Instance\Validator\EditoraValidator;

final class CreateInstanceRequest extends FormRequest
{
    private EditoraValidator $editoraValidator;

    public function __construct(EditoraValidator $editoraValidator)
    {
        $this->editoraValidator = $editoraValidator;
    }

    public function rules(): array
    {
        return $this->editoraValidator->create($this->input());
    }
}
