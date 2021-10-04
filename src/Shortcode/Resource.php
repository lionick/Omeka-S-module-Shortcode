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

        // Compatibility with Omeka Classic.
        if ($this->shortcodeName === 'file') {
            // A file is only a media.
            if ($resourceType !== 'media') {
                return '';
            }
            if (!isset($args['player'])) {
                return $this->renderMedia($resource, $args);
            }
        }

        $player = null;
        if (isset($args['player'])) {
            $args['player'] = lcfirst($args['player']);
            if ($args['player'] === 'default') {
                return $resourceType === 'media'
                    ? $this->renderMedia($resource, $args)
                    : '';
            }
            $plugins = $this->view->getHelperPluginManager();
            if ($plugins->has($args['player'])) {
                $player = $args['player'];
                unset($args['player']);
            }
        }

        $resourceTypeTemplates = [
            'annotations' => 'annotation',
            'items' => 'item',
            'item_sets' => 'item-set',
            'media' => 'media',
            'resources' => 'resource',
        ];
        $resourceTypeVars = [
            'annotations' => 'annotations',
            'items' => 'items',
            'item_sets' => 'itemSets',
            'media' => 'medias',
            'resources' => 'resources',
        ];
        $resourceTypesCss = [
            'annotations' => 'annotation',
            'items' => 'item',
            'item_sets' => 'item-set',
            'media' => 'media',
            'resources' => 'resource',
        ];

        $partial = $this->getViewTemplate($args);
        if (!$partial) {
            $partial = $player
                ? 'common/shortcode/player'
                : 'common/shortcode/' . $resourceTypeTemplates[$resourceType];
        }

        return $this->view->partial($partial, [
            'resource' => $resource,
            $resourceTypeVars[$resourceType] => $resource,
            'resourceType' => $resourceTypesCss[$resourceType],
            'options' => $args,
            'player' => $player,
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

        $partial = $this->getThemeTemplet($args) ?? 'common/shortcode/file';
        return $this->view->partial($partial, [
            'resource' => $resource,
            'media' => $resource,
            'thumbnailType' => $thumbnailType,
            'options' => $args,
        ]);
    }
}
