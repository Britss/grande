<?php
declare(strict_types=1);

namespace App\Support;

final class PublicContent
{
    public static function shared(): array
    {
        return [
            'brand' => [
                'name' => (string) Config::get('app.name', 'Grande.'),
                'tagline' => (string) Config::get('app.tagline', 'Pandesal + Coffee'),
                'headline' => 'Freshly Baked, Freshly Brewed - EVERY. SINGLE. DAY.',
                'hours' => 'Open 24/7',
            ],
            'contact' => [
                'phone' => (string) Config::get('app.phone'),
                'email' => (string) Config::get('app.site_email'),
                'address' => (string) Config::get('app.address'),
            ],
            'social_links' => [
                [
                    'label' => 'Facebook',
                    'url' => 'https://www.facebook.com/profile.php?id=61560256332744',
                ],
                [
                    'label' => 'Instagram',
                    'url' => 'https://www.instagram.com/grande.pandesalcoffee',
                ],
                [
                    'label' => 'TikTok',
                    'url' => 'https://www.tiktok.com/@grande.pandesalcoffee',
                ],
            ],
        ];
    }

    public static function home(): array
    {
        $shared = self::shared();

        return [
            'title' => 'Grande. Pandesal + Coffee',
            'description' => 'Freshly baked pandesal and freshly brewed coffee, rebuilt from the ground up.',
            'hero' => [
                'eyebrow' => 'Home',
                'title' => 'Freshly Baked, Freshly Brewed.',
                'subtitle' => $shared['brand']['headline'],
            ],
            'intro' => 'The rewrite now has a reusable structure for public pages so the existing GrandeGo experience can be rebuilt section by section instead of page-level procedural PHP.',
            'highlights' => [
                '24/7 neighborhood bakery and coffee shop',
                'Fresh pan de sal, premium coffee, and community-first atmosphere',
                'Step-by-step rebuild with design parity as the end state',
            ],
            'cta' => [
                [
                    'label' => 'View Menu',
                    'href' => url('menu'),
                ],
                [
                    'label' => 'Reserve Table',
                    'href' => url('reserve'),
                ],
            ],
        ];
    }

    public static function about(): array
    {
        return [
            'title' => 'About',
            'description' => 'About Grande. Pandesal + Coffee - Learn our story, mission, and vision for premium coffee and pan de sal.',
            'hero' => [
                'eyebrow' => 'About',
                'title' => 'About Grande.',
                'subtitle' => 'Your 24/7 Neighborhood Bakery and Coffee Shop',
            ],
            'story' => [
                'title' => 'Our Story',
                'paragraphs' => [
                    'Grande. Pandesal + Coffee is a neighborhood bakery and coffee shop in Sindalan, San Fernando, Pampanga, built around the idea that fresh pan de sal and quality coffee should be available whenever people need them.',
                    'The current site emphasizes one clear promise: freshly baked bread, freshly brewed coffee, and a welcoming space at any hour of the day. That same promise is being preserved in this rewrite.',
                    'This phase keeps the story content in a structured format so the final rebuilt page can match the existing site more closely without keeping the old implementation style.',
                ],
            ],
            'pillars' => [
                [
                    'title' => 'Our Mission',
                    'body' => 'Serve fresh pan de sal and premium coffee 24/7 at affordable prices while creating a warm space for the local community.',
                ],
                [
                    'title' => 'Our Vision',
                    'body' => 'Be the most loved 24-hour bakery and coffee shop in San Fernando, Pampanga.',
                ],
                [
                    'title' => 'Our Values',
                    'body' => 'Freshness, quality, affordability, community, and genuine Filipino hospitality.',
                ],
            ],
            'features' => [
                [
                    'title' => 'Fresh Pan De Sal Daily',
                    'body' => 'Traditional recipes, quality ingredients, and bread baked throughout the day.',
                ],
                [
                    'title' => 'Premium Coffee 24/7',
                    'body' => 'Coffee service stays central to the brand, from everyday cups to specialty drinks.',
                ],
                [
                    'title' => 'Affordable Prices',
                    'body' => 'The current site consistently positions Grande as accessible, not premium-only.',
                ],
                [
                    'title' => 'Warm Atmosphere',
                    'body' => 'The space is presented as a place to relax, connect, work, or unwind any time.',
                ],
            ],
            'team' => [
                'title' => 'Meet Our Team',
                'body' => 'A dedicated team works around the clock to prepare bread, brew coffee, and keep the space welcoming.',
            ],
            'cta' => [
                'title' => 'Experience Grande Today',
                'body' => 'Visit anytime and taste the difference that fresh pan de sal and premium coffee make.',
            ],
        ];
    }

    public static function menu(): array
    {
        $categories = [
            [
                'name' => 'Coffee',
                'description' => 'Core espresso and latte drinks that reflect the current GrandeGo menu direction.',
                'items' => [
                    [
                        'name' => 'Americano',
                        'description' => 'Straightforward brewed coffee profile for all-day drinking.',
                        'prices' => ['12oz' => 79, '16oz' => 89],
                    ],
                    [
                        'name' => 'Cappuccino',
                        'description' => 'Balanced espresso, milk, and foam.',
                        'prices' => ['12oz' => 99, '16oz' => 109],
                    ],
                    [
                        'name' => 'Spanish Latte',
                        'description' => 'Sweeter latte-style drink that stays popular on the existing menu.',
                        'prices' => ['12oz' => 109, '16oz' => 119],
                    ],
                ],
            ],
            [
                'name' => 'Pan De Sal',
                'description' => 'Fresh bread options that anchor the brand identity.',
                'items' => [
                    [
                        'name' => 'Classic Pan De Sal',
                        'description' => 'Soft bread rolls baked fresh throughout the day.',
                        'prices' => ['piece' => 12, 'bundle' => 60],
                    ],
                    [
                        'name' => 'Cheese Pan De Sal',
                        'description' => 'Savory variation with a richer filling profile.',
                        'prices' => ['piece' => 18, 'bundle' => 90],
                    ],
                    [
                        'name' => 'Ube Cheese Pan De Sal',
                        'description' => 'Sweet and savory combination that matches the current product line.',
                        'prices' => ['piece' => 22, 'bundle' => 110],
                    ],
                ],
            ],
            [
                'name' => 'Pastries',
                'description' => 'Pastry and loaf selections that support the bakery side of the business.',
                'items' => [
                    [
                        'name' => 'Croissant',
                        'description' => 'Buttery pastry that works well with coffee service.',
                        'prices' => ['regular' => 75],
                    ],
                    [
                        'name' => 'Classic Ensaymada',
                        'description' => 'Soft, sweet, and familiar bakery staple.',
                        'prices' => ['regular' => 55],
                    ],
                    [
                        'name' => 'Dark Chocolate Loaf',
                        'description' => 'Loaf option positioned as a shareable baked item.',
                        'prices' => ['slice' => 45, 'whole' => 220],
                    ],
                ],
            ],
            [
                'name' => 'Sandwiches',
                'description' => 'Savory items for guests who want a more filling order.',
                'items' => [
                    [
                        'name' => 'Grande Burger',
                        'description' => 'Casual savory item that broadens the menu beyond drinks and bread.',
                        'prices' => ['regular' => 129],
                    ],
                    [
                        'name' => 'Cheese and Ham Pan De Sal',
                        'description' => 'A simple grab-and-go savory option.',
                        'prices' => ['regular' => 69],
                    ],
                ],
            ],
        ];

        return [
            'title' => 'Menu',
            'description' => 'Browse our menu of freshly baked pan de sal, premium coffee, pastries, and more.',
            'hero' => [
                'eyebrow' => 'Menu',
                'title' => 'Our Menu',
                'subtitle' => 'Discover our coffee, pastries, and bakery favorites.',
            ],
            'notice' => 'This phase uses a structured sample catalog in code. Database-backed menu management will replace this in a later implementation step.',
            'categories' => self::normalizeMenuCategories($categories),
        ];
    }

    public static function findMenuItem(string $code): ?array
    {
        foreach (self::menu()['categories'] as $category) {
            foreach ($category['items'] as $item) {
                if (($item['code'] ?? null) === $code) {
                    return $item + [
                        'category' => $category['name'],
                    ];
                }
            }
        }

        return null;
    }

    public static function reserve(): array
    {
        return [
            'title' => 'Reserve',
            'description' => 'Reserve your table at Grande. Pandesal + Coffee online.',
            'hero' => [
                'eyebrow' => 'Reserve',
                'title' => 'Reserve Your Table',
                'subtitle' => 'Book your coffee experience before the full reservation workflow is wired up.',
            ],
            'form_intro' => 'The final reservation flow will require login, cart validation, and workflow enforcement. This phase rebuilds the page structure and fields first.',
            'form_fields' => [
                ['label' => 'Date', 'type' => 'date', 'name' => 'date'],
                ['label' => 'Time', 'type' => 'time', 'name' => 'time'],
                ['label' => 'Number of Guests', 'type' => 'number', 'name' => 'guests', 'min' => '1', 'max' => '20', 'value' => '1'],
                ['label' => 'First Name', 'type' => 'text', 'name' => 'first_name'],
                ['label' => 'Last Name', 'type' => 'text', 'name' => 'last_name'],
                ['label' => 'Email Address', 'type' => 'email', 'name' => 'email'],
                ['label' => 'Phone Number', 'type' => 'tel', 'name' => 'phone'],
            ],
            'sidebar_cards' => [
                [
                    'title' => 'Location',
                    'body' => 'Beside Puregold, in front of St. Anthony\'s Drug Store, Sindalan, San Fernando, Pampanga.',
                ],
                [
                    'title' => 'Contact Us',
                    'body' => 'Reach out through phone or email if you need assistance before the booking system is finished.',
                ],
                [
                    'title' => 'Operating Hours',
                    'body' => 'Open 24/7. The brand promise remains visible here because it is central to the customer experience.',
                ],
                [
                    'title' => 'Reservation Policy',
                    'body' => 'Reservations are held briefly, walk-ins remain welcome, and larger groups may need direct coordination.',
                ],
            ],
            'amenities' => [
                'Fresh pan de sal',
                'Premium coffee',
                '24/7 service',
                'Affordable pricing',
                'Warm community atmosphere',
            ],
        ];
    }

    public static function feedback(): array
    {
        return [
            'title' => 'Feedback',
            'description' => 'Share your Grande. Pandesal + Coffee experience.',
            'hero' => [
                'eyebrow' => 'Feedback',
                'title' => 'Share Your Feedback',
                'subtitle' => 'Help us serve you better with your comments and suggestions.',
            ],
            'form_intro' => 'Share your latest experience with Grande so the team can review it inside the rebuilt dashboard flow.',
            'categories' => [
                'Food Quality',
                'Coffee Quality',
                'Service Quality',
                'Ambiance and Atmosphere',
                'Cleanliness',
                'Pricing and Value',
                'Suggestion',
                'Complaint',
                'Compliment',
                'Other',
            ],
            'info_cards' => [
                [
                    'title' => 'About Your Feedback',
                    'body' => 'Every comment helps maintain Grande\'s commitment to freshness, quality, and hospitality.',
                ],
                [
                    'title' => 'Rate Your Experience',
                    'body' => 'Customers can now send ratings and comments directly into the rebuilt feedback inbox.',
                ],
                [
                    'title' => 'We\'re Listening 24/7',
                    'body' => 'Urgent concerns can still be handled through direct contact channels while the digital workflow is being reviewed by staff.',
                ],
                [
                    'title' => 'Visit Us',
                    'body' => 'The rewrite keeps the same location and always-open brand message visible on feedback pages too.',
                ],
            ],
            'cta' => [
                'title' => 'Thank You for Your Feedback',
                'body' => 'Your voice helps improve the experience every single day.',
            ],
        ];
    }

    private static function normalizeMenuCategories(array $categories): array
    {
        foreach ($categories as $categoryIndex => $category) {
            foreach ($category['items'] as $itemIndex => $item) {
                $sizes = [];

                foreach ($item['prices'] as $label => $price) {
                    $sizes[] = [
                        'label' => (string) $label,
                        'price' => (float) $price,
                        'is_default' => $label === array_key_first($item['prices']),
                    ];
                }

                $categories[$categoryIndex]['items'][$itemIndex]['code'] = self::slug(
                    $category['name'] . '-' . $item['name']
                );
                $categories[$categoryIndex]['items'][$itemIndex]['sizes'] = $sizes;
            }
        }

        return $categories;
    }

    private static function slug(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

        return trim($value, '-');
    }
}
