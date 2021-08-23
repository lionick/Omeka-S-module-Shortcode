<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

class Resources extends AbstractShortcode
{
    /**
     * @link https://github.com/omeka/Omeka/blob/master/application/views/helpers/Shortcodes.php
     *
     * {@inheritDoc}
     * @see \Shortcode\Shortcode\AbstractShortcode::render()
     */
    public function render(?array $args = null): string
    {
        // It's not possible to search resources for now, so use items.
        $shortcodeToResources = [
            'featured_collections' => 'item_sets',
            'featured_item_sets' => 'item_sets',
            'featured_items' => 'items',
            'featured_media' => 'media',
            'featured_medias' => 'media',
            'featured_resources' => 'items',
            'collections' => 'item_sets',
            'items' => 'items',
            'item_sets' => 'item_sets',
            'media' => 'media',
            'medias' => 'media',
            'recent_collections' => 'item_sets',
            'recent_item_sets' => 'item_sets',
            'recent_items' => 'items',
            'recent_media' => 'media',
            'recent_medias' => 'media',
            'recent_resources' => 'items',
            'resources' => 'items',
        ];

        $resourceType = $shortcodeToResources[$this->shortcodeName];

        $recents = [
            'recent_collections',
            'recent_item_sets',
            'recent_items',
            'recent_media',
            'recent_medias',
            'recent_resources',
        ];
        $featureds = [
            'featured_collections',
            'featured_item_sets',
            'featured_items',
            'featured_media',
            'featured_medias',
            'featured_resources',
        ];

        if (in_array($this->shortcodeName, $recents)) {
            if (!isset($args['num'])) {
                $args['num'] = '5';
            }
            $args['sort'] = 'created';
            $args['order'] = 'desc';
        } elseif (in_array($this->shortcodeName, $featureds)) {
            $args['is_featured'] = '1';
            if (!isset($args['num'])) {
                $args['num'] = '1';
            }
            $args['sort'] = 'random';
        }

        // By default the ten oldest resources.
        if (empty($args)) {
            $args = [
                'sort' => 'created',
                'order' => 'asc',
            ];
        }

        // Don't check or cast data here but in api.

        $params = [];

        if (isset($args['is_featured'])) {
            $isFeatured = $this->boolean($args['is_featured']);
            $params['property'][] = [
                'property' => 'curation:featured',
                'joiner' => 'and',
                'type' => $isFeatured ? 'ex' : 'nex',
            ];
        }

        // Require module AdvancedSearch.
        if (isset($args['has_image'])) {
            $params['has_thumbnails'] = $this->boolean($args['has_image']);
        }

        // Require module AdvancedSearch.
        if (isset($args['has_media'])) {
            $params['has_media'] = $this->boolean($args['has_media']);
        }

        // "collection" is an alias of "item_set".
        if (isset($args['item_set'])) {
            $params['item_set_id'] = $this->listIds($args['item_set']);
        } elseif (isset($args['collection'])) {
            $params['item_set_id'] = $this->listIds($args['collection']);
        }

        if (isset($args['class'])) {
            $params['resource_class_term'] = $this->listTermsOrIds($args['class']);
        }

        /** @deprecated "item_type" is deprecated, use "class_label" or"class". */
        if (isset($args['class_label'])) {
            $params['resource_class_label'] = $args['class_label'];
        } elseif (isset($args['item_type'])) {
            $params['resource_class_label'] = $args['item_type'];
        }

        if (isset($args['template'])) {
            $params['resource_template_id'] = $this->listIds($args['template']);
        }

        if (isset($args['template_label'])) {
            $params['resource_template_label'] = $args['template_label'];
        }

        // Require module AdvancedSearch.
        if (isset($args['tag'])) {
            $params['property'][] = [
                'property' => 'curation:tag',
                'joiner' => 'and',
                'type' => 'list',
                'text' => array_map('trim', explode(',', $args['tag'])),
            ];
        } elseif (isset($args['tags'])) {
            $params['property'][] = [
                'property' => 'curation:tag',
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
            $params['id'] = $this->listIds($args['id']);
        } elseif (isset($args['ids'])) {
            $params['id'] = $this->listIds($args['ids']);
        }

        if (isset($args['sort'])) {
            $params['sort_by'] = $args['sort'] === 'added' ? 'created' : $args['sort'];
        }

        if (isset($args['order'])) {
            $params['sort_order'] = in_array(strtolower($args['order']), ['d', 'desc']) ? 'desc' : 'asc';
        }

        if (isset($args['num'])) {
            $limit = (int) $args['num'];
            // Unlike Omeka classic, the results are unlimited by default, so
            // "0" means "0".
            if ($limit > 0) {
                $params['limit'] = $limit;
            }
        } else {
            $params['limit'] = 10;
        }

        if (isset($args['site'])) {
            $params['site_id'] = $args['site'];
        } else {
            // Force the current site by default (null is skipped by api).
            $params['site_id'] = $this->currentSiteId();
        }

        $resources = $this->view->api()->search($resourceType, $params)->getContent();

        $resourceTypeTemplates = [
            'items' => 'items',
            'item_sets' => 'item-sets',
            'media' => 'medias',
            'resources' => 'resources',
        ];
        $resourceTypeVars = [
            'items' => 'items',
            'item_sets' => 'itemSets',
            'media' => 'medias',
            'resources' => 'resources',
        ];
        $resourceTypesCss = [
            'items' => 'item',
            'item_sets' => 'item-set',
            'media' => 'media',
            'resources' => 'resource',
        ];

        $partial = $this->getThemeTemplet($args) ?? 'common/shortcode/' . $resourceTypeTemplates[$resourceType];
        return $this->view->partial($partial, [
            'resources' => $resources,
            $resourceTypeVars[$resourceType] => $resources,
            'resourceType' => $resourceTypesCss[$resourceType],
        ]);
    }
}
