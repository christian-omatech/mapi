<?php declare(strict_types=1);

namespace Tests\Editora\Controllers\Database;

use Tests\Editora\EditoraTestCase;
use Tests\Editora\ObjectMother\NewsMother;

final class EditoraControllerTest extends EditoraTestCase
{
    /** @test */
    public function test(): void
    {
        dd($this->get('es/nice-url/products/uuid'));
    }
}
