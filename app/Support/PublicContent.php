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
            'description' => 'Meet Grande. Pandesal + Coffee in Sindalan: a 24/7 neighborhood bread-and-coffee shop for everyday stops.',
            'hero' => [
                'eyebrow' => 'Grande. Pan De Sal + Coffee',
                'title' => 'Bread, coffee, and a familiar stop in Sindalan.',
                'lead' => 'A 24/7 neighborhood bread-and-coffee shop for breakfast, merienda, takeout drinks, and late-night coffee runs.',
                'body' => 'Grande keeps everyday comfort close: pan de sal, ensaymada, loaf breads, coffee-based drinks, tea, and non-coffee favorites served in a compact shop built for quick visits and familiar routines.',
                'framing' => '',
                'image' => [
                    'src' => url('public/images/2024-12-02.webp'),
                    'alt' => 'Grande food and drinks on a cafe table',
                    'credit' => 'Photo by grande.',
                ],
                'secondary_image' => [
                    'src' => url('public\icons\snaptik_7422529582160448786_2_v2.jpeg'),
                    'alt' => 'Grande iced drinks served with bread inside the cafe',
                    'credit' => 'Photo by fayecuico',
                ],
                'quick_facts' => [
                    [
                        'label' => 'Open',
                        'detail' => '24/7 for early starts, errands, and late-night stops.',
                    ],
                    [
                        'label' => 'Known For',
                        'detail' => 'Pan de sal, ensaymada, loaf breads, coffee, tea, and refreshers.',
                    ],
                    [
                        'label' => 'Location',
                        'detail' => 'Sindalan, City of San Fernando, Pampanga.',
                    ],
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
                    'title' => 'Warm in mood, useful in the moments that matter.',
                    'body' => 'Grande works because it fits real routines: bread for the morning, drinks through the day, and an open door when nearby choices are limited.',
                ],
                [
                    'kicker' => 'Fresh bread',
                    'title' => 'Pastries keep the visit grounded.',
                    'body' => 'Soft bread, ensaymada, and loaf favorites make Grande an easy stop for breakfast, merienda, or something to bring home.',
                    'icon' => url('public/icons/bread.png'),
                    'image' => [
                        'src' => url('public\icons\snaptik_7422529582160448786_5_v2.jpeg'),
                        'alt' => 'Grande bread and iced drinks on a cafe table',
                        'credit' => 'Photo by fayecuico',
                    ],
                ],
                [
                    'kicker' => 'Coffee comfort',
                    'title' => 'Coffee stays simple to come back to.',
                    'body' => 'From classic cups to sweeter blends, the drinks are made for ordinary days, catch-ups, and quick resets.',
                    'icon' => url('public/icons/coffee.png'),
                    'image' => [
                        'src' => url('public/icons/snaptik_7422529582160448786_0_v2.jpeg'),
                        'alt' => 'Four Grande iced drinks lined up by the cafe window',
                        'credit' => 'Photo by fayecuico',
                    ],
                ],
                [
                    'kicker' => '24/7 service',
                    'title' => 'Open hours carry real weight.',
                    'body' => 'Being open day and night makes Grande useful for early starts, errands, night shifts, and late coffee runs.',
                    'icon' => url('public/icons/clock.png'),
                    'image' => [
                        'src' => url('public\icons\snaptik_7422529582160448786_3_v2.jpeg'),
                        'alt' => 'Grande exterior sign glowing at night',
                        'credit' => 'Photo by fayecuico',
                    ],
                ],
                [
                    'kicker' => 'Sindalan stop',
                    'title' => 'The location fits local routines.',
                    'body' => 'In Sindalan, Grande is easy to reach before heading out, during errands, or on the way home.',
                    'icon' => url('public/icons/pin.png'),
                    'image' => [
                        'src' => url('public\icons\snaptik_7422529582160448786_4_v2.jpeg'),
                        'alt' => 'Grande counter view looking toward the Sindalan street at night',
                        'credit' => 'Photo by fayecuico',
                    ],
                ],
            ],
            'closing_cta' => [
                'eyebrow' => 'Visit Any Hour',
                'title' => 'Drop by when bread or coffee fits your day.',
                'body' => 'Browse the menu first or reserve a table, then stop in when you are nearby in Sindalan.',
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
                'eyebrow' => '',
                'title' => 'Save a Spot at Grande',
                'subtitle' => 'Plan a bread-and-coffee run for breakfast, merienda, or a late-night stop in Sindalan.',
            ],
            'form_intro' => 'Pick a time and leave the contact details staff can use for quick updates.',
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
                    'title' => 'Start With Your Cart',
                    'label' => 'Before booking',
                    'body' => 'Add your pandesal, pastries, coffee, or tea first so the visit and order stay together.',
                ],
                [
                    'title' => 'Arrive Around Your Time',
                    'label' => 'At the store',
                    'body' => 'Drop by near your selected schedule and keep your phone reachable for any quick confirmation.',
                ],
                [
                    'title' => 'Location',
                    'label' => 'Where to go',
                    'body' => 'Beside Puregold, in front of St. Anthony\'s Drug Store, Sindalan, San Fernando, Pampanga.',
                ],
                [
                    'title' => 'Good to Know',
                    'label' => 'Good to know',
                    'body' => 'Walk-ins are still welcome. Larger groups may need direct coordination with the store.',
                ],
            ],
            'amenities' => [
                [
                    'title' => 'Bread Favorites',
                    'body' => 'Pandesal, ensaymada, and loaf breads for everyday cravings.',
                ],
                [
                    'title' => 'Coffee and More',
                    'body' => 'Classic coffee, teas, non-coffee drinks, and refreshers in one stop.',
                ],
                [
                    'title' => 'Open Anytime',
                    'body' => 'A neighborhood stop for morning, merienda, and late-night visits.',
                ],
                [
                    'title' => 'Easy to Find',
                    'body' => 'Located in Sindalan, beside Puregold and in front of St. Anthony\'s Drug Store.',
                ],
                [
                    'title' => 'Made for Takeout',
                    'body' => 'Quick bread-and-drink orders for everyday comfort and convenience.',
                ],
            ],
            'cta' => [
                'title' => 'We Are Happy to Have You',
                'body' => 'The store is happily waiting for your visit. Come by near your selected time and settle in with something warm, fresh, and familiar.',
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
                'title' => 'How Was Your Visit?',
                'subtitle' => 'A quick note helps us make the next bread, coffee, or pickup run smoother.',
            ],
            'form_intro' => 'Tell us what stood out. A few clear details are enough.',
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
                'title' => 'What Helps Most',
                'body' => 'Start with one moment from your visit, order, or pickup.',
                'items' => [
                    [
                        'label' => 'Service',
                        'title' => 'At the Counter',
                        'body' => 'Service was quick, but my drink needed a little more time.',
                    ],
                    [
                        'label' => 'Food',
                        'title' => 'Bread and Pastries',
                        'body' => 'The cheese pan de sal was soft and still warm.',
                    ],
                    [
                        'label' => 'Order',
                        'title' => 'Pickup Flow',
                        'body' => 'Ordering was easy. Pickup time could be clearer.',
                    ],
                    [
                        'label' => 'Suggestion',
                        'title' => 'Menu Ideas',
                        'body' => 'More pastry bundles would be nice for merienda.',
                    ],
                ],
                'footer' => 'We use your name and email only when a follow-up is needed.',
            ],
            'cta' => [
                'title' => 'Thanks for the Note',
                'body' => 'We read every message so everyday stops at Grande stay easy and familiar.',
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
