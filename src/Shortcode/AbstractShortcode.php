<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

use Laminas\View\Renderer\PhpRenderer;

abstract class AbstractShortcode implements ShortcodeInterface
{
    /**
     * @var \Laminas\View\Renderer\PhpRenderer
     */
    protected $view;

    public function setView(PhpRenderer $view): self
    {
        $this->view = $view;
        return $this;
    }

    abstract public function render(?array $args = null): string;

    protected function currentSiteId(): ?int
    {
        static $siteId;
        if (is_null($siteId)) {
            $vars = $this->view->vars();
            $site = $vars->offsetGet('site');
            if (!$site) {
                $site = $this->view
                    ->getHelperPluginManager()
                    ->get('Laminas\View\Helper\ViewModel')
                    ->getRoot()
                    ->getVariable('site');
                $vars->offsetSet('site', $site);
            }
            $siteId = $site ? $site->id() : 0;
        }
        return $siteId ?: null;
    }
}
