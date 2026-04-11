<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Support\View;

abstract class Controller
{
    protected function render(string $view, array $data = []): string
    {
        return View::make($view, $data);
    }
}
