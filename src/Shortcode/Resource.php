<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

use Omeka\Api\Exception\NotFoundException;
use Omeka\Api\Representation\MediaRepresentation;

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

        $player = array_key_exists('player', $args) && strtolower($args['player']) === 'default';
        if ($resourceType === 'media'
            && ($player || $this->shortcodeName === 'file')
        ) {
            return $this->renderMedia($resource, $args);
        }

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

    protected function renderMedia(MediaRepresentation $resource, array $args): string
    {
        //  This is the type of thumbnail, that is rendered and converted into a
        // class in Omeka Classic.
        $thumbnailTypes = [
            null => 'medium',
            'large' => 'large',
            'medium' => 'medium',
            'square' => 'square',
            // For compatibility with Omeka Classic.
            'thumbnail' => 'medium',
            'square_thumbnail' => 'square',
            'fullsize' => 'large',
        ];

        /** @deprecated "size" is deprecated, use "thumbnail". */
        if (isset($args['thumbnail'])) {
            $thumbnailType = $thumbnailTypes[$args['thumbnail']] ?? 'medium';
        } elseif (isset($args['size'])) {
            $thumbnailType = $thumbnailTypes[$args['size']] ?? 'medium';
        } else {
            $thumbnailType = null;
        }

        unset(
            $args['thumbnail'],
            $args['size'],
            $args['player']
        );

        $args['thumbnailType'] = $thumbnailType;
        $args['link'] = $this->view->siteSetting('attachment_link_type', 'item');

        $partial = 'common/shortcode/file';
        return $this->view->partial($partial, [
            'resource' => $resource,
            'media' => $resource,
            'thumbnailType' => $thumbnailType,
            'options' => $args,
        ]);
    }
}
