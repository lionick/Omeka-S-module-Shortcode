<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

class RecentItems extends Items
{
    public function render(?array $args = null): string
    {
        if (!isset($args['num'])) {
            $args['num'] = '5';
        }

        $args['sort'] = 'created';

        $args['order'] = 'desc';

        return parent::render($args);
    }
}
