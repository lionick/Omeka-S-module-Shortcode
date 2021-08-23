<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

use Laminas\View\Renderer\PhpRenderer;

interface ShortcodeInterface
{
    /**
     * Set the current view.
     */
    public function setView(PhpRenderer $view): self;

    /**
     * Render the shortcode.
     *
     * @return string The output must be cast to string to support strict types.
     */
    public function render(?array $args = null): string;
}
