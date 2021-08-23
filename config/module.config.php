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
            'items' => Shortcode\Items::class,
            'noop' => Shortcode\Noop::class,
        ],
    ],
];
