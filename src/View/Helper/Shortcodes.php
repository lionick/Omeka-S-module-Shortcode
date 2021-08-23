<?php declare(strict_types=1);

namespace Shortcode\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Shortcode\Shortcode\Manager as ShortcodeManager;

class Shortcodes extends AbstractHelper
{
    /**
     * @var \Shortcode\Shortcode\Manager
     */
    protected $shortcodeManager;

    /**
     * @param array
     */
    protected $shortcodes;

    public function __construct(ShortcodeManager $shortcodeManager)
    {
        $this->shortcodeManager = $shortcodeManager;
        $this->shortcodes = $shortcodeManager->getRegisteredNames();
    }

    /**
     * Render all shortcodes present in a string.
     *
     * @see \Omeka_View_Helper_Shortcodes::shortcodes()
     * @link https://github.com/omeka/Omeka/blob/master/application/views/helpers/Shortcodes.php
     */
    public function __invoke($string): string
    {
        // Quick check.
        if (strpos($string, '[') === false) {
            return $string;
        }

        // Get the list of shortcodes in all the string.
        $pattern = '/\[(\w+)\s*([^\]]*)\]/s';
        return preg_replace_callback($pattern, [$this, 'handleShortcode'], $string);
    }

    /**
     * Parse a detected shortcode and replace it with its actual content, or return it unchanged.
     *
     * @see \Omeka_View_Helper_Shortcodes::handleShortcode()
     * @link https://github.com/omeka/Omeka/blob/master/application/views/helpers/Shortcodes.php
     */
    protected function handleShortcode(array $matches): string
    {
        $shortcodeName = $matches[1];
        if (!in_array($shortcodeName, $this->shortcodes)) {
            return $matches[0];
        }

        $args = $this->parseShortcodeAttributes($matches[2]);
        return $this->shortcodeManager
            ->get($shortcodeName)
            ->setView($this->view)
            ->render($args);
    }

    /**
     * Parse shortcode attributes.
     *
     * @see \Omeka_View_Helper_Shortcodes::parseShortcodeAttributes()
     * @link https://github.com/omeka/Omeka/blob/master/application/views/helpers/Shortcodes.php
     */
    protected function parseShortcodeAttributes(string $attributes): array
    {
        $attributes = trim($attributes);
        if (!strlen($attributes)) {
            return [];
        }

        $args = [];
        $pattern =
            // Start by looking for attribute values in double quotes
            '/(\w+)'        // Attribute key
            . '\s*=\s*'     // Whitespace and =
            . '"([^"]*)"'   // Attrbiute value
            . '(?:\s|$)'    // Space or end of string
            . '|'           // Or look for attribute values in single quotes
            . '(\w+)'       // Attribute key
            . '\s*=\s*'     // Whitespace and =
            . '\'([^\']*)\''// Attribute value
            . '(?:\s|$)'    // Space or end of string
            . '|'           // Or look for attribute values without quotes
            . '(\w+)'       // Attribute key
            . '\s*=\s*'     // Whitespace and =
            . '([^\s\'"]+)' // Attribute value
            . '(?:\s|$)'    // Space or end of string
            . '|'           // Or look for single value
            . '"([^"]*)"'   // Attribute value alone
            . '(?:\s|$)'    // Space or end of string
            . '|'           // Or look for single value
            . '(\S+)'       // Attribute value alone
            . '(?:\s|$)/';  // Space or end of string
        $matches = [];
        if (preg_match_all($pattern, $attributes, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                if (!empty($m[1])) {
                    $args[strtolower($m[1])] = $m[2];
                } elseif (!empty($m[3])) {
                    $args[strtolower($m[3])] = $m[4];
                } elseif (!empty($m[5])) {
                    $args[strtolower($m[5])] = $m[6];
                } elseif (isset($m[7])) {
                    $args[] = $m[7];
                } elseif (isset($m[8])) {
                    $args[] = $m[8];
                }
            }
        } else {
            $args = ltrim($attributes);
        }
        return $args;
    }
}
