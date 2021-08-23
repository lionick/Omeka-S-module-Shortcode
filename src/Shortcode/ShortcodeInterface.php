<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

use Laminas\View\Renderer\PhpRenderer;

interface ShortcodeInterface
{
    /**
     * Set the name of the shortcode.
     *
     * This method simplifies using aliases (resource, item, item set, media,
     * recent, featured, etc.), without using a factory.
     */
    public function setShortcodeName(string $shortcodeName): self;

    /**
     * Set the current view.
     */
    public function setView(PhpRenderer $view): self;

    /**
     * Render the shortcode.
     *
     * @return string The output must be cast to string to support strict types.
     */
    public function render(array $args = []): string;
}
