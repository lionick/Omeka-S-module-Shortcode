<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

class FeaturedItems extends Items
{
    public function render(?array $args = null): string
    {
        $args['is_featured'] = '1';

        if (!isset($args['num'])) {
            $args['num'] = '1';
        }

        $args['sort'] = 'random';

        return parent::render($args);
    }
}
