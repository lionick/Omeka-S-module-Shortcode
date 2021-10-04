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
        $resourceNames = [
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

        $partial = $this->getViewTemplate($args);

        if (empty($args['resource']) || !isset($resourceNames[$args['resource']])) {
            if ($partial) {
                return $this->view->partial($partial, [
                    'resourceType' => null,
                    'count' => 0,
                    'options' => $args,
                ]);
            }
            return $span
                ? '<span class="' . $span . '">0</span>'
                : '0';
        }

        $resourceTypes = [
            'annotations' => 'annotation',
            'items' => 'item',
            'item_sets' => 'item-set',
            'media' => 'media',
            'resources' => 'resource',
        ];

        $resourceName = $resourceNames[$args['resource']];

        $query = $this->apiQuery($args);

        unset(
            $query['page'],
            $query['per_page'],
            $query['offset'],
            $query['limit'],
            $query['sort_by'],
            $query['sort_order'],
        );

        $total = (string) $this->view->api()->search($resourceName, $query)->getTotalResults();

        if ($partial) {
            return $this->view->partial($partial, [
                'resourceName' => $resourceName,
                'resourceType' => $resourceTypes[$resourceName],
                'count' => 0,
                'options' => $args,
            ]);
        }

        return $span
            ? '<span class="' . $span . '">' . $total . '</span>'
            : $total;
    }
}
