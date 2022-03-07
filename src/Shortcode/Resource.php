<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

use Omeka\Api\Exception\NotFoundException;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
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
        // The shortcode name can be a shortcode too, like "link".
        // The resource name is required for resources without method "resourceName()".
        $resourceNameFromShortcode = $this->resourceNames[$this->shortcodeName] ?? $this->shortcodeName;

        if (empty($args['id'])) {
            // Check if there is a numeric argument.
            if (empty($args[0])) {
                // Use current page or site.
                if ($resourceNameFromShortcode === 'site_pages') {
                    $args[0] = $this->view->params()->fromRoute('page-slug');
                    if (!$args[0]) {
                        return '';
                    }
                } elseif ($resourceNameFromShortcode === 'sites') {
                    $args[0] = $this->currentSiteId();
                    if (!$args[0]) {
                        return '';
                    }
                } else {
                    return '';
                }
            }
            if (!(int) $args[0]) {
                // May be a page or a site.
                if (!in_array($resourceNameFromShortcode, ['site_pages', 'sites'])) {
                    return '';
                }
            }
            $args['id'] = $args[0];
            unset($args[0]);
        }

        if ($resourceNameFromShortcode === 'site_pages') {
            try {
                if (empty($args['site'])) {
                    $args['site'] = $this->currentSiteId();
                }
                $queryResource = is_numeric($args['id'])
                    ? ['site' => $args['site'], 'id' => $args['id']]
                    : ['site' => $args['site'], 'slug' => $args['id']];
                /** @var \Omeka\Api\Representation\SitePageRepresentation $resource */
                $resource = $this->view->api()->read('site_pages', $queryResource)->getContent();
            } catch (NotFoundException $e) {
                return '';
            }
        } elseif ($resourceNameFromShortcode === 'sites') {
            try {
                $queryResource = is_numeric($args['id'])
                    ? ['id' => $args['id']]
                    : ['slug' => $args['id']];
                /** @var \Omeka\Api\Representation\SiteRepresentation $resource */
                $resource = $this->view->api()->read('sites', $queryResource)->getContent();
            } catch (NotFoundException $e) {
                return '';
            }
        } else {
            try {
                /** @var \Omeka\Api\Representation\AbstractResourceEntityRepresentation $resource */
                $resource = $this->view->api()->read('resources', ['id' => $args['id']])->getContent();
            } catch (NotFoundException $e) {
                return '';
            }
        }

        // The views "url" and "link" don't use any template and they can use a
        // short argument.
        $argsValueIsUrl = array_keys($args, 'url', true);
        $viewAsUrl = in_array('view', $argsValueIsUrl)
            || array_filter($argsValueIsUrl, 'is_numeric');
        if ($viewAsUrl) {
            return $this->renderUrl($resource, $args);
        }

        if ($this->shortcodeName === 'link') {
            return $this->renderLink($resource, $args);
        }

        $argsValueIsLink = array_keys($args, 'link', true);
        $viewAsLink = in_array('view', $argsValueIsLink)
            || array_filter($argsValueIsLink, 'is_numeric');
        if ($viewAsLink) {
            return $this->renderLink($resource, $args);
        }
        $resourceName = method_exists($resource, 'resourceName')
            ? $resource->resourceName()
            : $resourceNameFromShortcode;

        // Compatibility with Omeka Classic.
        if ($this->shortcodeName === 'file') {
            // A file is only a media.
            if ($resourceName !== 'media') {
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
                return $resourceName === 'media'
                    ? $this->renderMedia($resource, $args)
                    : '';
            }
            $plugins = $this->view->getHelperPluginManager();
            if ($plugins->has($args['player'])) {
                $player = $args['player'];
                unset($args['player']);
            }
        }

        // For shortcode and compatibility with Omeka classic.
        if (array_key_exists('created', $args)) {
            $args['meta'] = 'created';
        } elseif (array_key_exists('modified', $args)) {
            $args['meta'] = 'modified';
        } elseif (array_key_exists('added', $args)) {
            $args['meta'] = 'added';
        } elseif (array_key_exists('updated', $args)) {
            $args['meta'] = 'updated';
        }

        if (isset($args['meta'])) {
            return $this->renderMeta($resource, $args);
        }

        $resourceTemplates = [
            'annotations' => 'annotation',
            'items' => 'item',
            'item_sets' => 'item-set',
            'media' => 'media',
            'resources' => 'resource',
            'site_pages' => 'page',
            'sites' => 'site',
        ];

        $partial = $this->getViewTemplate($args);
        if (!$partial) {
            $partial = $player
                ? 'common/shortcode/player'
                : 'common/shortcode/' . $resourceTemplates[$resourceName];
        }

        return $this->view->partial($partial, [
            'resource' => $resource,
            $this->resourceVars[$resourceName] => $resource,
            'resourceName' => $resourceName,
            'resourceType' => $this->resourceTypes[$resourceName],
            'options' => $args,
            'player' => $player,
        ]);
    }

    protected function urlResource(AbstractResourceEntityRepresentation $resource, array $args): ?string
    {
        if ($resource instanceof MediaRepresentation && isset($args['file'])) {
            // TODO Use $resource->thumbnailDisplayUrl($type) (but not use often when we want file).
            return $args['file'] === 'original'
                ? $resource->originalUrl()
                : $resource->thumbnailUrl($args['file']);
        }
        return $resource->url(null, true);
    }

    protected function renderUrl(AbstractResourceEntityRepresentation $resource, array $args): string
    {
        $resourceUrl = $this->urlResource($resource, $args);
        if (!$resourceUrl) {
            return '';
        }
        $span = empty($args['span']) ? false : $this->view->escapeHtmlAttr($args['span']);
        return $span
            ? '<span class="' . $span . '">' . $resourceUrl . '</span>'
            : $resourceUrl;
    }

    protected function renderLink(AbstractResourceEntityRepresentation$resource, array $args): string
    {
        $resourceUrl = $this->urlResource($resource, $args);
        if (!$resourceUrl) {
            return '';
        }

        $plugins = $this->view->getHelperPluginManager();
        $escape = $plugins->get('escapeHtml');
        $hyperlink = $plugins->get('hyperlink');
        $escapeAttr = $plugins->get('escapeHtmlAttr');

        $displayTitle = method_exists($resource, 'displayTitle')
            ? $resource->displayTitle()
            : $resource->title();

        if (array_key_exists('title', $args)) {
            $title = strlen($args['title']) ? $args['title'] : $resourceUrl;
        } else {
            $title = $displayTitle;
        }

        $attributes = [
            'title' => $displayTitle,
        ];

        if ($resource instanceof MediaRepresentation && isset($args['file'])) {
            $attributes['type'] = $args['file'] === 'original'
                ? $resource->mediaType()
                : 'image/jpeg';
        }

        $link = $hyperlink->raw($escape($title), $resourceUrl, $attributes);

        $span = empty($args['span']) ? false : $escapeAttr($args['span']);
        return $span
            ? '<span class="' . $span . '">' . $link . '</span>'
            : $link;
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
