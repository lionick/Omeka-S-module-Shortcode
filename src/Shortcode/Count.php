<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

class Count extends AbstractShortcode
{
    /**
     * @link https://github.com/omeka/Omeka/blob/master/application/views/helpers/Shortcodes.php
     *
     * {@inheritDoc}
     * @see \Shortcode\Shortcode\AbstractShortcode::render()
     */
    public function render(?array $args = null): string
    {
        $resourceTypes = [
            'item' => 'items',
            'items' => 'items',
            'item_set' => 'item_sets',
            'item_sets' => 'item_sets',
            'media' => 'media',
            'medias' => 'media',
            // TODO Support count of "resources".
            // 'resource' => 'resources',
            // 'resources' => 'resources',
        ];

        $span = empty($args['span']) ? false : $this->view->escapeHtmlAttr($args['span']);

        if (empty($args['resource']) || !isset($resourceTypes[$args['resource']])) {
            return $span
                ? '<span class="' . $span . '">0</span>'
                : '0';
        }

        $resourceType = $resourceTypes[$args['resource']];

        $query = $this->apiQuery($args);

        unset(
            $query['page'],
            $query['per_page'],
            $query['offset'],
            $query['limit'],
            $query['sort_by'],
            $query['sort_order'],
        );

        $total = (string) $this->view->api()->search($resourceType, $query)->getTotalResults();
        return $span
            ? '<span class="' . $span . '">' . $total . '</span>'
            : $total;
    }
}
