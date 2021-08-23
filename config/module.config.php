<?php declare(strict_types=1);

namespace Shortcode;

return [
    'service_manager' => [
        'factories' => [
            'ShortcodeManager' => Service\Shortcode\ShortcodeManagerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'shortcodes' => Service\ViewHelper\ShortcodesFactory::class,
        ],
    ],
    'shortcodes' => [
        'invokables' => [
            'featured_items' => Shortcode\FeaturedItems::class,
            'items' => Shortcode\Items::class,
            'noop' => Shortcode\Noop::class,
            'recent_items' => Shortcode\RecentItems::class,
        ],
    ],
];
