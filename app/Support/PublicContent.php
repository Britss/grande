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
            'title' => 'Home',
            'description' => 'Freshly baked pandesal, premium coffee, and a cozy 24/7 stop in Sindalan.',
            'hero' => [
                'eyebrow' => 'Home',
                'title' => 'Freshly Baked, Freshly Brewed.',
                'subtitle' => $shared['brand']['headline'],
            ],
            'intro' => 'Fresh bread, premium coffee, and a welcoming place to pause any time of day.',
            'highlights' => [
                '24/7 neighborhood bakery and coffee shop',
                'Fresh pan de sal, premium coffee, and community-first atmosphere',
                'Dine in, take out, or reserve a table',
            ],
            'cta' => [
                [
                    'label' => 'View Menu',
                    'href' => url('menu'),
                ],
                [
                    'label' => 'Reserve a table',
                    'href' => url('reserve'),
                ],
            ],
        ];
    }

    public static function about(): array
    {
        return [
            'title' => 'About',
            'description' => 'Meet Grande. Pandesal + Coffee in Sindalan: fresh bread, premium coffee, and a warm stop that stays open all day and night.',
            'hero' => [
                'eyebrow' => 'Grande. Pan De Sal + Coffee',
                'title' => 'From morning bread to late-night coffee, Grande stays part of the neighborhood rhythm.',
                'body' => 'Some visits are quick, some turn into a quiet pause, and some happen when most places are already closed. Grande is built for all of them: bakery warmth in the morning, coffee comfort through the day, and a familiar light still on when the night runs long.',
                'framing' => '',
                'image' => [
                    'src' => url('public/images/2024-12-02.webp'),
                    'alt' => 'Grande storefront glowing in Sindalan',
                ],
                'secondary_image' => [
                    'src' => url('public/images/menu-items/grande_coffee.jpg'),
                    'alt' => 'Freshly served Grande coffee',
                ],
            ],
            'timeline_moments' => [
                [
                    'time' => 'Early Bake',
                    'title' => 'The day starts with bread still warm from the tray.',
                    'body' => 'Grande opens the routine with soft pan de sal, easy takeaway bundles, and the kind of breakfast stop people can fit in before work, school, or errands.',
                    'image' => [
                        'src' => url('public/images/menu-items/classic_pan_de_sal.png'),
                        'alt' => 'Fresh pan de sal ready for the morning crowd',
                    ],
                ],
                [
                    'time' => 'Midday Pause',
                    'title' => 'Coffee and bakery comfort hold the middle of the day together.',
                    'body' => 'By lunch and merienda, Grande turns into a practical reset: a coffee that is easy to order again, bread and pastries that travel well, and a familiar place to sit for a while.',
                    'image' => [
                        'src' => url('public/images/menu-items/grande_coffee.jpg'),
                        'alt' => 'Grande coffee served for a midday break',
                    ],
                ],
                [
                    'time' => 'Late-Night Coffee',
                    'title' => 'When the area quiets down, Grande still feels open and useful.',
                    'body' => 'That 24/7 promise matters late. There is still coffee to order, fresh bread to bring home, and a reliable stop in Sindalan when most other lights are already off.',
                    'image' => [
                        'src' => url('public/images/2024-12-02.webp'),
                        'alt' => 'Grande storefront at night in Sindalan',
                    ],
                ],
            ],
            'proof_points' => [
                'intro' => [
                    'eyebrow' => 'Why Grande Works',
                    'title' => 'The atmosphere feels warm, but the reason people return is practical.',
                    'body' => 'Grande stays easy to choose because the bakery and coffee quality are familiar, the location makes sense, and the shop is ready when the day starts early or ends late.',
                ],
                [
                    'kicker' => 'Fresh bread',
                    'title' => 'Warm pan de sal stays at the center of the visit.',
                    'body' => 'The bakery side never feels secondary. Grande keeps the bread fresh, soft, and ready for the kind of quick stop that still needs to feel worth it.',
                    'icon' => url('public/icons/bread.png'),
                    'image' => [
                        'src' => url('public/images/menu-items/pandesal_fest_bundle.jpg'),
                        'alt' => 'Grande bread bundle ready for takeaway',
                    ],
                ],
                [
                    'kicker' => 'Coffee comfort',
                    'title' => 'Premium coffee stays approachable enough for repeat visits.',
                    'body' => 'From a straightforward cup to sweeter favorites, the coffee is made for ordinary days, long catch-ups, and late resets without feeling overcomplicated.',
                    'icon' => url('public/icons/coffee.png'),
                    'image' => [
                        'src' => url('public/images/menu-items/spanish_latte.jpg'),
                        'alt' => 'Grande premium coffee for a daytime pause',
                    ],
                ],
                [
                    'kicker' => '24/7 service',
                    'title' => 'The always-open promise is part of the product, not a footnote.',
                    'body' => 'Grande is there for early starts, in-between errands, and late-night coffee runs. Being open all day and night makes the place genuinely useful, not just atmospheric.',
                    'icon' => url('public/icons/clock.png'),
                    'image' => [
                        'src' => url('public/images/2024-12-02.webp'),
                        'alt' => 'Grande storefront open at all hours',
                    ],
                ],
                [
                    'kicker' => 'Sindalan stop',
                    'title' => 'The location fits the way local routines actually move.',
                    'body' => 'Beside Puregold Sindalan and in front of St. Anthony\'s Drug Store, Grande is simple to reach before heading out, while doing errands, or on the way home.',
                    'icon' => url('public/icons/pin.png'),
                    'image' => [
                        'src' => url('public/images/menu-items/classic_ensaymada.jpg'),
                        'alt' => 'Grande bakery counter and neighborhood cafe atmosphere',
                    ],
                ],
            ],
            'closing_cta' => [
                'eyebrow' => 'Visit Any Hour',
                'title' => 'Drop by for bread, coffee, and a familiar seat whenever the day gives you time.',
                'body' => 'Browse the menu first or reserve a table, then stop in whenever you are nearby in Sindalan.',
                'actions' => [
                    [
                        'label' => 'View Menu',
                        'href' => url('menu'),
                    ],
                    [
                        'label' => 'Reserve a Table',
                        'href' => url('reserve'),
                    ],
                ],
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
                'subtitle' => 'Plan your visit, attach your cart, and let the team prepare your table before you arrive.',
            ],
            'form_intro' => 'Choose your schedule and confirm the contact details staff should use for reservation updates.',
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
                    'title' => 'Cart Required',
                    'label' => 'Before booking',
                    'body' => 'Add menu items first so your reservation can continue into checkout with the linked order.',
                ],
                [
                    'title' => 'Visit Details',
                    'label' => 'At the store',
                    'body' => 'Arrive near your selected time and keep your phone reachable in case staff needs to confirm details.',
                ],
                [
                    'title' => 'Location',
                    'label' => 'Where to go',
                    'body' => 'Beside Puregold, in front of St. Anthony\'s Drug Store, Sindalan, San Fernando, Pampanga.',
                ],
                [
                    'title' => 'Reservation Policy',
                    'label' => 'Good to know',
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
            'cta' => [
                'title' => 'Need More Items First?',
                'body' => 'Build your cart from the menu, then return here to lock in the date, time, and guest count.',
            ],
        ];
    }

    public static function feedback(): array
    {
        return [
            'title' => 'Feedback',
            'description' => 'Share your Grande. Pandesal + Coffee experience.',
            'hero' => [
                'eyebrow' => '',
                'title' => 'Share Your Feedback',
                'subtitle' => 'Help us serve you better with your comments and suggestions.',
            ],
            'form_intro' => 'Tell us what happened during your visit. Short, specific notes are easiest for the team to act on.',
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
            'examples' => [
                'title' => 'Not sure what to write?',
                'body' => 'Use one of these angles and keep it honest.',
                'items' => [
                    [
                        'label' => 'Service',
                        'body' => 'The counter was quick, but my drink took longer than expected.',
                    ],
                    [
                        'label' => 'Food',
                        'body' => 'The cheese pan de sal was fresh, soft, and still warm.',
                    ],
                    [
                        'label' => 'Order',
                        'body' => 'My reservation was easy to place, but I wanted clearer pickup timing.',
                    ],
                    [
                        'label' => 'Suggestion',
                        'body' => 'Please add more pastry bundle options for afternoon orders.',
                    ],
                ],
                'note' => 'Names and emails help us follow up, but the details matter most.',
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
