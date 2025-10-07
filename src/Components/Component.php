<?php

namespace AkidoLd\SimpleComponent\Components;

use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributKeyIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentTagIsInvalidException;
use Stringable;

class Component {
    /**
     * The tag of this component
     *
     * It's the text inside the tag.
     * Example:
     * ```html
     * <tag>content</tag>
     * ```
     * @var string
     */
    protected string $tag;

    /**
     * The array of component attributes.
     *
     * Example:
     * ```html
     * <tag key="value"></tag>
     * ```
     * @var array<string, string|null>
     */
    protected array $attributes;

    /**
     * The content of the component.
     *
     * It is made up of `Stringable` elements.
     *
     * @var array<Stringable>
     */
    protected array $contents;

    /**
     * Indicates whether the component is closed.
     *
     * @var bool
     */
    protected bool $closed;

    public function __construct(string $tag, bool $closed = true){
        $this->setTag($tag);
        $this->closed = $closed;
        $this->attributes = [];
        $this->contents = [];
    }

    /**
     * Define the tag of this component.
     *
     * @param string $tag The tag to set.
     * @throws ComponentTagIsInvalidException If the tag is invalid.
     * @return Component The reference to this component.
     */
    public function setTag(string $tag): self{
        $tag = trim($tag);
        if (empty($tag)) {
            throw new ComponentTagIsInvalidException('The tag of this component is empty');
        }
        $this->tag = $tag;
        return $this;
    }

    public function getTag(): string{
        return $this->tag;
    }

    /**
     * Add an attribute to this component.
     *
     * An attribute may not have a value. In this case, only the key is stored.
     * If an attribute with the same key already exists, its value will be replaced.
     *
     * @param string $key The attribute key.
     * @param string|null $value The value of this attribute.
     * @throws ComponentAttributKeyIsInvalidException If the attribute key is invalid.
     * @return Component The reference to this component.
     */
    public function addAttribute(string $key, ?string $value = null): self{
        $key = trim($key);
        if (empty($key)) {
            throw new ComponentAttributKeyIsInvalidException("Attribute key of a Component can't be empty");
        }
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Remove an attribute from this component.
     *
     * No error occurs when removing a non-existent attribute.
     *
     * @param string $key The key of the attribute to remove.
     * @return string|null Returns the value of the removed attribute or null otherwise.
     */
    public function removeAttribute(string $key): ?string{
        if (($attribute = $this->attributes[$key] ?? null) !== null) {
            unset($this->attributes[$key]);
        }
        return $attribute;
    }

    public function getAttribute(string $key): ?string{
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set a new value for an existing attribute.
     *
     * @param string $key The attribute key.
     * @param string|null $value The new value to assign.
     * @throws ComponentAttributKeyIsInvalidException If the key does not exist.
     * @return Component The reference to this component.
     */
    public function setAttribute(string $key, ?string $value = null): self{
        if (!isset($this->attributes[$key])) {
            throw new ComponentAttributKeyIsInvalidException("The key '$key' does not exist on this component");
        }
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Check if an attribute exists in this component.
     *
     * @param string $key The attribute key to check.
     * @return bool
     */
    public function attributeExists(string $key): bool{
        return $this->getAttribute($key) !== null;
    }

    /**
     * Check if this component is closed
     * 
     * @return bool
     */
    public function isClosed(): bool{
        return $this->closed;
    }
}
