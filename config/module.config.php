<?php declare(strict_types=1);

namespace Shortcode;

return [
    'service_manager' => [
        'factories' => [
            'ShortcodeManager' => Service\Shortcode\ShortcodeManagerFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'shortcodes' => Service\ViewHelper\ShortcodesFactory::class,
        ],
    ],
    'shortcodes' => [
        'invokables' => [
            'noop' => Shortcode\Noop::class,
        ],
    ],
];
