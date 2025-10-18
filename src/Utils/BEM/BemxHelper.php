<?php

namespace AkidoLd\SimpleComponent\Utils\BEM;

use AkidoLd\SimpleComponent\Exceptions\BEM\BemHelperException;
use AkidoLd\SimpleComponent\Exceptions\BEM\BemxHelperException;

/**
 * BEMX Helper - Contextual BEM generator
 * 
 * BEMX extends the traditional BEM methodology by adding context awareness.
 * It allows a block to generate class names considering its parent, without
 * affecting the parent itself.
 * 
 * Key features:
 * - Blocks remain autonomous: their own modifiers apply only to themselves.
 * - Parent blocks influence the child via context: parent__child and parent__child--parentModifier.
 * - Flexible: supports any combination of parent and child modifiers.
 * - Removes the strict "element" concept of BEM; any block can be nested.
 * 
 * Conceptual example:
 * 
 * Parent block: "menu-item"   (modifiers: ["expanded"])
 * └── Child block: "btn"       (modifiers: ["primary"])
 * 
 * Generated classes:
 * "btn btn--primary menu-item__btn menu-item__btn--expanded"
 * 
 * Usage example:
 * ```php
 * echo BemxHelper::generate(
 *     'btn',           // child block
 *     ['primary'],     // child modifiers
 *     'menu-item',     // parent block
 *     ['expanded']     // parent modifiers
 * );
 * // Output: "btn btn--primary menu-item__btn menu-item__btn--expanded"
 * ```
 * 
 * Notes:
 * - Child modifiers never modify the parent.
 * - Parent modifiers are applied only in the context of the child (parent__child--modifier).
 * - If either parent or child has no modifiers, it still generates correct BEMX classes.
 */
class BemxHelper
{
    /**
     * Generate BEMX classes considering the parent context.
     * 
     * When the parent is null, the parent modifier will been ignore
     * for the BEMX generation
     *
     * @param string $block The child block name.
     * @param string|null $parentBlock Optional parent block name.
     * @param array $modifiers Modifiers for the child block.
     * @param array $parentModifiers Optional parent modifiers.
     * @return string Space-separated BEMX class names.
     */
    public static function generate(
        string $block,
        ?string $parentBlock = null,
        array $modifiers = [],
        array $parentModifiers = []
    ): string {
        self::validateParameters($parentBlock, $parentModifiers);
        
        try{
            $childBem = BemHelper::generate($block, null, $modifiers);
        }catch(BemHelperException $e){
            throw new BemxHelperException("[BEMX] {$e->getMessage()}");
        }        

        if($parentBlock !== null){
            $parentClasses = [$parentBlock.'__'.$block];
            foreach($parentModifiers as $modifier){
                $parentClasses[] = "{$parentBlock}__{$block}--{$modifier}";
            }
            $parentBem = implode(' ', $parentClasses);
        }
        return $childBem . ($parentBlock !== null ? ' '.$parentBem : '');
    }

    private static function normalizeParameters(?string &$parentBlock, array &$parentModifiers){
        $parentBlock = $parentBlock !== null ? trim($parentBlock) : null;
        $parentModifiers = array_map(fn($modifier) => trim($modifier), $parentModifiers);;
    }

    private static function validateParameters(?string &$parentBlock, array &$parentModifiers){
        // Check if all modifiers are strings before any modification
        foreach ($parentModifiers as $modifier) {
            if (!is_string($modifier)) {
                $type = gettype($modifier);
                throw new BemxHelperException("[BEMX] Invalid modifier type: expected string, got {$type}.");
            }
        }

        //Clean parameters
        self::normalizeParameters($parentBlock, $parentModifiers);

        //Validate parameters
        if($parentBlock !== null && $parentBlock === ''){
            throw new BemxHelperException("[BEMX] Invalid parent block name: cannot be empty if not null.");
        }

        foreach ($parentModifiers as $modifier) {
            if ($modifier === '') {
                throw new BemxHelperException("[BEMX] Invalid modifier: cannot be empty.");
            }
        }
    }
}
