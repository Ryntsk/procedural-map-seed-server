<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Простой пример теста, который проверяет, что приложение перенаправляет на страницу карт.
     */
    public function test_the_application_redirects_to_maps(): void
    {
        $this->get('/')->assertRedirect(route('maps.index'));
    }
}