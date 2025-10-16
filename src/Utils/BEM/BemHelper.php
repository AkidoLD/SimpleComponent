<?php

namespace AkidoLd\SimpleComponent\Utils\BEM;

use AkidoLd\SimpleComponent\Exceptions\BEM\BemHelperException;

class BemHelper
{
    /**
     * Normalize BEM parameters by trimming strings.
     */
    private static function normalizeParameters(string &$block, ?string &$element, array &$modifiers): void{
        $block = trim($block);
        $element = $element !== null ? trim($element) : null;
        $modifiers = array_map(fn($modifier) => trim($modifier), $modifiers);
    }

    /**
     * Validate BEM parameters and ensure they are consistent.
     *
     * @throws BemHelperException
     */
    private static function validateParameters(string &$block, ?string &$element, array &$modifiers): void{
        // Check if all modifiers are strings before any modification
        foreach ($modifiers as $modifier) {
            if (!is_string($modifier)) {
                $type = gettype($modifier);
                throw new BemHelperException("[BEM] Invalid modifier type: expected string, got {$type}.");
            }
        }

        // Clean parameters
        self::normalizeParameters($block, $element, $modifiers);

        // Validate content
        if ($block === '') {
            throw new BemHelperException("[BEM] Invalid block name: cannot be empty.");
        }

        if ($element !== null && $element === '') {
            throw new BemHelperException("[BEM] Invalid element name: cannot be empty if not null.");
        }

        foreach ($modifiers as $modifier) {
            if ($modifier === '') {
                throw new BemHelperException("[BEM] Invalid modifier: cannot be empty.");
            }
        }
    }

    /**
     * Generate a BEM-compliant class string.
     */
    public static function generate(string $block, ?string $element = null, array $modifiers = []): string{
        // Check and clean parameters
        self::validateParameters($block, $element, $modifiers);

        $base = $block . ($element ? "__{$element}" : "");
        $bemClasses = [$base];

        foreach ($modifiers as $modifier) {
            $bemClasses[] = "{$base}--{$modifier}";
        }

        return implode(' ', $bemClasses);
    }
}
