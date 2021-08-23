<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

class Items extends AbstractShortcode
{
    /**
     * @link https://github.com/omeka/Omeka/blob/master/application/views/helpers/Shortcodes.php
     *
     * {@inheritDoc}
     * @see \Shortcode\Shortcode\AbstractShortcode::render()
     */
    public function render(?array $args = null): string
    {
        $params = [];

        if (isset($args['is_featured'])) {
            $params['isFeatured'] = $args['is_featured'];
        }

        if (isset($args['has_image'])) {
            $params['hasImage'] = $args['has_image'];
        }

        if (isset($args['collection'])) {
            $params['item_set_id'] = $args['collection'];
        }

        if (isset($args['item_type'])) {
            $params['resource_class_label'] = $args['item_type'];
        }

        if (isset($args['tags'])) {
            $params['property'][] = [
                'property' => 'dcterms:subject',
                'joiner' => 'and',
                'type' => 'list',
                'text' => array_map('trim', explode(',', $args['tags'])),
            ];
        }

        if (isset($args['user'])) {
            $params['owner_id'] = $args['user'];
        }

        if (isset($args['ids'])) {
            $params['id'] = $args['ids'];
        }

        if (isset($args['sort'])) {
            $params['sort_by'] = $args['sort'];
        }

        if (isset($args['order'])) {
            $params['sort_order'] = $args['order'];
        }

        if (isset($args['num'])) {
            $params['limit'] = $args['num'];
        } else {
            $params['limit'] = 10;
        }

        $items = $this->view->api()->search('items', $params)->getContent();

        return $this->view->partial('common/shortcode/items', [
            'resources' => $items,
            'items' => $items,
        ]);
    }
}
