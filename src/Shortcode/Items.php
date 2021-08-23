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
            $isFeatured = !in_array(strtolower($args['is_featured']), ['0', 'false'], true);
            $params['property'][] = [
                'property' => 'curation:featured',
                'joiner' => 'and',
                'type' => $isFeatured ? 'ex' : 'nex',
            ];
        }

        // Require module AdvancedSearch.
        if (isset($args['has_image'])) {
            $params['has_thumbnails'] = !in_array(strtolower($args['has_image']), ['0', 'false'], true);
        }

        // Require module AdvancedSearch.
        if (isset($args['has_media'])) {
            $params['has_media'] = !in_array(strtolower($args['has_media']), ['0', 'false'], true);
        }

        // "collection" is an alias of "item_set".
        if (isset($args['item_set'])) {
            $params['item_set_id'] = $args['item_set'];
        } elseif (isset($args['collection'])) {
            $params['item_set_id'] = $args['collection'];
        }

        /** @deprecated "item_type" is deprecated, use "class_label" or"class". */
        if (isset($args['class_label'])) {
            $params['resource_class_label'] = $args['class_label'];
        } elseif (isset($args['item_type'])) {
            $params['resource_class_label'] = $args['item_type'];
        }

        if (isset($args['class'])) {
            $params['resource_class_term'] = $args['class'];
        }

        if (isset($args['template'])) {
            $params['resource_template_label'] = $args['template'];
        }

        // Require module AdvancedSearch.
        if (isset($args['tags'])) {
            $params['property'][] = [
                'property' => 'curation:tags',
                'joiner' => 'and',
                'type' => 'list',
                'text' => array_map('trim', explode(',', $args['tags'])),
            ];
        }

        /** @deprecated "user" is deprecated, use "owner". */
        if (isset($args['owner'])) {
            $params['owner_id'] = $args['owner'];
        } elseif (isset($args['user'])) {
            $params['owner_id'] = $args['user'];
        }

        /** @deprecated "ids" is deprecated, use singular "id". */
        if (isset($args['id'])) {
            $params['id'] = $args['id'];
        } elseif (isset($args['ids'])) {
            $params['id'] = array_map('trim', explode(',', $args['ids']));
        }

        if (isset($args['sort'])) {
            $params['sort_by'] = $args['sort'];
        }

        if (isset($args['order'])) {
            $params['sort_order'] = in_array(strtolower($args['order']), ['d', 'desc']) ? 'desc' : 'asc';
        }

        if (isset($args['num'])) {
            $params['limit'] = $args['num'];
        } else {
            $params['limit'] = 10;
        }

        if (isset($args['site'])) {
            $params['site_id'] = $args['site'];
        } else {
            // Force the current site by default (null is skipped by api).
            $params['site_id'] = $this->currentSiteId();
        }

        $items = $this->view->api()->search('items', $params)->getContent();

        return $this->view->partial('common/shortcode/items', [
            'resources' => $items,
            'items' => $items,
        ]);
    }
}
