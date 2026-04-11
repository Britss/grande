<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Support\PublicContent;

final class HomeController extends Controller
{
    public function index(): string
    {
        $page = PublicContent::home();

        return $this->render('pages.home', [
            'pageTitle' => $page['title'],
            'metaDescription' => $page['description'],
            'page' => $page,
            'bodyClass' => 'home-page',
        ]);
    }
}
