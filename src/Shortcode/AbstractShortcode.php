<?php declare(strict_types=1);

namespace Shortcode\Shortcode;

use Laminas\View\Renderer\PhpRenderer;

abstract class AbstractShortcode implements ShortcodeInterface
{
    /**
     * @var string
     */
    protected $shortcodeName;

    /**
     * @var \Laminas\View\Renderer\PhpRenderer
     */
    protected $view;

    public function setShortcodeName(string $shortcodeName ): self
    {
        $this->shortcodeName = $shortcodeName;
        return $this;
    }

    public function setView(PhpRenderer $view): self
    {
        $this->view = $view;
        return $this;
    }

    abstract public function render(array $args = []): string;

    /**
     * Check if a value is a boolean and return "0" or "1".
     *
     * The casting is required to keep at least one character to the value, else
     * it will be managed as a null.
     */
    protected function boolean(string $value): int
    {
        return (int) !in_array(strtolower($value), ['0', 'false'], true);
    }

    /**
     * Get a list of integers from a string with comma-separated values or range.
     *
     * @return int[]
     */
    protected function listIds(string $value): array
    {
        if (strpos($value, ',') === false) {
            if (strpos($value, '-') === false) {
                return [(int) $value];
            }
            [$from, $to] = explode('-', $value);
            $from = (int) $from;
            $to = (int) $to;
            return range(min($from, $to), max($from, $to));
        }
        return array_map('intval', explode(',', $value));
    }

    /**
     * Get a list of integers from a string with comma-separated values.
     *
     * @return int[]|int A single value can be returned for perfomance.
     */
    protected function singleOrListIds(string $value)
    {
        return strpos($value, ',') === false
            ? (int) $value
            : array_map('intval', explode(',', $value));
    }

    /**
     * Get a list of terms or ids from a string with comma-separated values.
     */
    protected function listTermsOrIds(string $value): array
    {
        return strpos($value, ',') === false
            ? [$value]
            : array_map('trim', explode(',', $value));
    }

    /**
     * Get a list of terms or ids from a string with comma separated values.
     *
     * @return array|string A single value can be returned for perfomance.
     */
    protected function singleOrListTermsOrIds(string $value)
    {
        return strpos($value, ',') === false
            ? $value
            : array_map('trim', explode(',', $value));
    }

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
