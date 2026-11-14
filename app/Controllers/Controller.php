<?php
namespace App\Controllers;

/** Base controller with shared helpers. */
abstract class Controller
{
    protected function render(string $template, array $data = []): string
    {
        return view($template, $data);
    }
}
