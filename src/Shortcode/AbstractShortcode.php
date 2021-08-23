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
}
