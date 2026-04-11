<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CartRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\MenuRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\PublicContent;
use App\Support\Session;
use App\Support\Validator;

final class PublicPageController extends Controller
{
    public function about(): string
    {
        return $this->renderPublicPage('pages.about', PublicContent::about());
    }

    public function menu(): string
    {
        $page = PublicContent::menu();
        $page['notice'] = 'Now reading from the rebuilt `grande` menu catalog imported from GrandeGo.';
        $page['categories'] = (new MenuRepository())->groupedCatalog();
        $user = Auth::user();
        $cartTotals = ['item_count' => 0, 'subtotal' => 0.0];

        if (is_array($user) && ($user['role'] ?? 'customer') === 'customer') {
            $cartTotals = (new CartRepository())->totalsForUser((int) $user['id']);
        }

        return $this->render('pages.menu', [
            'pageTitle' => $page['title'],
            'metaDescription' => $page['description'],
            'page' => $page,
            'user' => $user,
            'cartTotals' => $cartTotals,
        ]);
    }

    public function feedback(): string
    {
        $page = PublicContent::feedback();
        $page['form_intro'] = 'Share your recent experience with Grande. Feedback now goes straight into the rebuilt staff dashboard for review.';

        return $this->render('pages.feedback', [
            'pageTitle' => $page['title'],
            'metaDescription' => $page['description'],
            'page' => $page,
            'user' => Auth::user(),
        ]);
    }

    public function storeFeedback(): never
    {
        if (!Csrf::validate((string) request_input('_token'))) {
            Session::flash('error', 'Your session expired. Please try submitting your feedback again.');
            Session::flashInput($_POST);
            redirect('/feedback');
        }

        $user = Auth::user();
        $input = [
            'feedback_name' => trim((string) request_input('feedback_name')),
            'feedback_email' => strtolower(trim((string) request_input('feedback_email'))),
            'feedback_rating' => (string) request_input('feedback_rating'),
            'feedback_category' => trim((string) request_input('feedback_category')),
            'feedback_body' => trim((string) request_input('feedback_body')),
        ];

        $validator = Validator::make($input)
            ->required('feedback_name', 'Full name')
            ->min('feedback_name', 2, 'Full name')
            ->max('feedback_name', 120, 'Full name')
            ->required('feedback_email', 'Email address')
            ->email('feedback_email', 'Email address')
            ->max('feedback_email', 120, 'Email address')
            ->required('feedback_rating', 'Overall experience rating')
            ->regex('feedback_rating', '/^[1-5]$/', 'Overall experience rating must be between 1 and 5.')
            ->required('feedback_category', 'Feedback category')
            ->regex('feedback_category', '/^(service|food-quality|store-cleanliness|website-ordering|reservation-experience|suggestion)$/', 'Select a valid feedback category.')
            ->required('feedback_body', 'Your feedback')
            ->min('feedback_body', 12, 'Your feedback')
            ->max('feedback_body', 1500, 'Your feedback');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($input);
            redirect('/feedback');
        }

        (new FeedbackRepository())->create([
            'user_id' => is_array($user) ? (int) ($user['id'] ?? 0) : null,
            'name' => $input['feedback_name'],
            'email' => $input['feedback_email'],
            'rating' => (int) $input['feedback_rating'],
            'category' => $input['feedback_category'],
            'message' => $input['feedback_body'],
            'status' => 'new',
        ]);

        Session::flash('status', 'Your feedback was sent successfully. Thank you for helping improve Grande.');
        redirect('/feedback');
    }

    private function renderPublicPage(string $view, array $page): string
    {
        return $this->render($view, [
            'pageTitle' => $page['title'],
            'metaDescription' => $page['description'],
            'page' => $page,
        ]);
    }
}
