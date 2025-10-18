<?php

namespace AkidoLd\SimpleComponent\Utils\BEM;

use AkidoLd\SimpleComponent\Exceptions\BEM\BemHelperException;

/**
 * Class BemHelper
 * 
 * A utility class to generate BEM (Block-Element-Modifier) CSS class strings for your components.
 * 
 * This helper allows you to construct BEM-compliant class names easily.
 * It ensures that blocks, elements, and modifiers are properly formatted and validated.
 * 
 * Example usage:
 * ```php
 * use AkidoLd\SimpleComponent\Utils\BEM\BemHelper;
 * 
 * echo BemHelper::generate(
 *     block: "btn", 
 *     element: "label", 
 *     modifiers: ["large", "primary"]
 * );
 * // Output: btn__label btn__label--large btn__label--primary
 * ```
 * 
 * For more information about BEM methodology, see: 
 * [BEM documentation](https://getbem.com/introduction/)
 */
class BemHelper
{
    /**
     * Normalize BEM parameters by trimming whitespace from strings.
     *
     * @param string $block
     * @param string|null $element
     * @param array $modifiers
     */
    private static function normalizeParameters(string &$block, ?string &$element, array &$modifiers): void
    {
        $block = trim($block);
        $element = $element !== null ? trim($element) : null;
        $modifiers = array_map(fn($modifier) => trim($modifier), $modifiers);
    }

    /**
     * Validate BEM parameters to ensure consistency.
     *
     * @param string $block
     * @param string|null $element
     * @param array $modifiers
     * 
     * @throws BemHelperException if any parameter is invalid
     */
    private static function validateParameters(string &$block, ?string &$element, array &$modifiers): void
    {
        // Ensure all modifiers are strings
        foreach ($modifiers as $modifier) {
            if (!is_string($modifier)) {
                $type = gettype($modifier);
                throw new BemHelperException("[BEM] Invalid modifier type: expected string, got {$type}.");
            }
        }

        // Clean parameters
        self::normalizeParameters($block, $element, $modifiers);

        // Validate block
        if ($block === '') {
            throw new BemHelperException("[BEM] Invalid block name: cannot be empty.");
        }

        // Validate element
        if ($element !== null && $element === '') {
            throw new BemHelperException("[BEM] Invalid element name: cannot be empty if not null.");
        }

        // Validate modifiers
        foreach ($modifiers as $modifier) {
            if ($modifier === '') {
                throw new BemHelperException("[BEM] Invalid modifier: cannot be empty.");
            }
        }
    }

    /**
     * Generate a BEM-compliant class string.
     *
     * @param string $block The block name (required)
     * @param string|null $element The element name (optional)
     * @param array $modifiers List of modifiers (optional)
     * 
     * @return string A string of space-separated BEM classes
     * 
     * @throws BemHelperException if parameters are invalid
     */
    public static function generate(string $block, ?string $element = null, array $modifiers = []): string
    {
        // Validate and normalize parameters
        self::validateParameters($block, $element, $modifiers);

        // Base BEM class
        $base = $block . ($element ? "__{$element}" : "");
        $bemClasses = [$base];

        // Add modifier classes
        foreach ($modifiers as $modifier) {
            $bemClasses[] = "{$base}--{$modifier}";
        }

        // Return all classes as a space-separated string
        return implode(' ', $bemClasses);
    }
}
