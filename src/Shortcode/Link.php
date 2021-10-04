<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

class Link extends Resource
{
    public function render(?array $args = null): string
    {
        $argsValueIsUrl = array_keys($args, 'url', true);
        $viewAsUrl = in_array('view', $argsValueIsUrl)
            || array_filter($argsValueIsUrl, 'is_numeric');
        $args['view'] = $viewAsUrl ? 'url' : 'link';
        return parent::render($args);
    }
}
