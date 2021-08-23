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
            'count' => Shortcode\Count::class,
            'noop' => Shortcode\Noop::class,

            'collection' => Shortcode\Resource::class,
            'item' => Shortcode\Resource::class,
            'item_set' => Shortcode\Resource::class,
            'media' => Shortcode\Resource::class,
            'resource' => Shortcode\Resource::class,

            'collections' => Shortcode\Resources::class,
            'items' => Shortcode\Resources::class,
            'item_sets' => Shortcode\Resources::class,
            'medias' => Shortcode\Resources::class,
            // TODO Support "resources".
            // 'resources' => Shortcode\Resources::class,

            // Deprecated aliases for compatibility with Omeka Classic.
            'file' => Shortcode\Resource::class,

            'featured_collections' => Shortcode\Resources::class,
            // 'featured_item_sets' => Shortcode\Resources::class,
            'featured_items' => Shortcode\Resources::class,
            // 'featured_media' => Shortcode\Resources::class,
            // 'featured_medias' => Shortcode\Resources::class,
            // 'featured_resources' => Shortcode\Resources::class,
            'recent_collections' => Shortcode\Resources::class,
            // 'recent_item_sets' => Shortcode\Resources::class,
            'recent_items' => Shortcode\Resources::class,
            // 'recent_media' => Shortcode\Resources::class,
            // 'recent_medias' => Shortcode\Resources::class,
            // 'recent_resources' => Shortcode\Resources::class,
        ],
    ],
];
