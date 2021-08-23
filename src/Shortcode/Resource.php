<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

use Omeka\Api\Exception\NotFoundException;

class Resource extends AbstractShortcode
{
    /**
     * @link https://github.com/omeka/Omeka/blob/master/application/views/helpers/Shortcodes.php
     *
     * {@inheritDoc}
     * @see \Shortcode\Shortcode\AbstractShortcode::render()
     */
    public function render(array $args = []): string
    {
        if (empty($args['id'])) {
            return '';
        }

        try {
            /** @var \Omeka\Api\Representation\AbstractResourceEntityRepresentation $resource */
            $resource = $this->view->api()->read('resources', ['id' => $args['id']])->getContent();
        } catch (NotFoundException $e) {
            return '';
        }

        $resourceType = $resource->resourceName();

        $resourceTypeTemplates = [
            'items' => 'item',
            'item_sets' => 'item-set',
            'media' => 'media',
            'resources' => 'resource',
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

        $partial = 'common/shortcode/' . $resourceTypeTemplates[$resourceType];
        return $this->view->partial($partial, [
            'resource' => $resource,
            $resourceTypeVars[$resourceType] => $resource,
            'resourceType' => $resourceTypesCss[$resourceType],
        ]);
    }
}
