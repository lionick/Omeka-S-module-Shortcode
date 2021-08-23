<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

class Noop extends AbstractShortcode
{
    public function render(?array $args = null): string
    {
        return '';
    }
}
