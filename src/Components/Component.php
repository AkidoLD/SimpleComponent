<?php

namespace AkidoLd\SimpleComponent\Components;

use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributeIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributKeyIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentTagIsInvalidException;

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
     * This string contains all the content of this `Component`
     *
     * @var string
     */
    protected string $contents;
    
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
        $this->contents = "";
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
    
    /**
     * Get the tag of this component
     * 
     * @return string
     */
    public function getTag(): string{
        return $this->tag;
    }
    
    /**
     * Check if this component is closed
     * 
     * @return bool
     */
    public function isClosed(): bool{
        return $this->closed;
    }
    
    /**
     * Set closed value
     * 
     * @param bool $closed
     * @return self The reference to this component
     */
    public function setClosed(bool $closed): self{
        $this->closed = $closed;
        return $this;
    }

    /**
     * Clean an attribute
     * 
     * This method removes empty spaces on the key and value of an attribute
     * 
     * @param string $key
     * @param string|null $value
     * @return void
     */
    public static function cleanAttribute(string &$key, ?string &$value): void {
        $key = trim($key);
        $value = $value !== null ? trim($value) : null;
    }
    
    /**
     * Check if an attribute is valid
     * 
     * Calls cleanAttribute to normalize the values
     * 
     * @param mixed $key The attribute key to check
     * @param mixed $value The attribute value to check
     * @throws ComponentAttributKeyIsInvalidException If the attribute key is not a string or empty
     * @throws ComponentAttributeIsInvalidException If the attribute value is not string, is empty and not null
     * @return void
     */
    public static function checkAttribute(mixed $key, mixed $value): void {
        if (!is_string($key)) {
            throw new ComponentAttributKeyIsInvalidException('This attribute key is not a string');
        }
    
        if (!is_string($value) && $value !== null) {
            throw new ComponentAttributeIsInvalidException('Attribute value must be a string or null');
        }
    
        // Clean the key and value before further processing
        self::cleanAttribute($key, $value);
    
        // Check for empty key
        if ($key === '') {
            throw new ComponentAttributKeyIsInvalidException('Attribute key cannot be empty');
        }
    
        // Check for invalid value (empty string is not allowed)
        if ($value !== null && $value === '') {
            throw new ComponentAttributeIsInvalidException('Attribute value cannot be an empty string');
        }
    }

    /**
     * Add an attribute to this component.
     *
     * An attribute may not have a value. In this case, only the key is stored.
     * If an attribute with the same key already exists, its value will be replaced.
     *
     * @param string $key The attribute key.
     * @param string|null $value The value of this attribute.
     * @throws ComponentAttributKeyIsInvalidException If the attribute key is not a string or empty
     * @throws ComponentAttributeIsInvalidException If the attribute value is not string, is empty and not null
     * @return Component The reference to this component.
     */
    public function addAttribute(string $key, ?string $value = null): self{
        self::checkAttribute($key, $value);
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Add many attributes at time
     * 
     * If one element of the attributes you want to add is invalid,
     * no attribute will be added to this component attributes
     * 
     * @param array $attributes The new attributes to add
     * @throws ComponentAttributKeyIsInvalidException If an attribute key is invalid
     * @throws ComponentAttributeIsInvalidException If an attribute value is invalid
     * @return Component The reference to this component.
     */
    public function addAttributes(array $attributes): self {
        // Validate all attributes before adding any
        foreach ($attributes as $key => $value) {
            self::checkAttribute($key, $value);
        }
        
        // All attributes are valid, merge them
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Remove an attribute from this component.
     *
     * No error occurs when removing a non-existent attribute.
     *
     * @param string $key The key of the attribute to remove.
     * @throws ComponentAttributKeyIsInvalidException If the attribute you want to remove doesn't exist on this component.
     * @return string|null Returns the value of the removed attribute.
     */
    public function removeAttribute(string $key): ?string{
        if($this->attributeExists($key)){
            $value = $this->getAttribute($key);
            unset($this->attributes[$key]);
            return $value;
        }
        throw new ComponentAttributKeyIsInvalidException("The attribute with key '$key' doesn't exist on this component");
    }

    /**
     * Get an attribute of this component.
     * 
     * @param string $key The key of the attribute we want to get.
     * @throws ComponentAttributKeyIsInvalidException If the attribute does not exist on this component.
     * @return string|null The attribute value.
     */
    public function getAttribute(string $key): ?string{
        if(!$this->attributeExists($key)){
            throw new ComponentAttributKeyIsInvalidException("The key '$key' does not exist on this component");
        }
        return $this->attributes[$key];
    }

    /**
     * Set a new value for an existing attribute.
     *
     * @param string $key The attribute key.
     * @param string|null $value The new value to assign.
     * @throws ComponentAttributKeyIsInvalidException If the key does not exist.
     * @throws ComponentAttributeIsInvalidException If the attribute value is invalid.
     * @return Component The reference to this component.
     */
    public function setAttribute(string $key, ?string $value = null): self{
        if (!$this->attributeExists($key)) {
            throw new ComponentAttributKeyIsInvalidException("The key '$key' does not exist on this component");
        }
        
        self::checkAttribute($key, $value);
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
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get the attributes array of this object
     * 
     * @return array<string, string|null>
     */
    public function getAttributes(): array{
        return $this->attributes;
    }

    /**
     * Add content to this Component
     * 
     * If the content is empty, the content of this component doesn't change
     * 
     * @param string $content
     * @return Component The reference to this Component
     */
    public function addContent(string $content): self{
        $content = trim($content);
        if($content !== ""){
            $this->contents .= htmlspecialchars($content, ENT_QUOTES).PHP_EOL;
        }
        
        return $this;
    }

    /**
     * Get the contents string of this Component
     * 
     * @return string
     */
    public function getContents(): string {
        return $this->contents;
    }

    /**
     * Clear all the content of this component
     * 
     * @return Component The reference to this Component
     */
    public function clearContents(): self{
        $this->contents = "";
        return $this;
    }

    /**
     * Render the attribute string of this component
     * 
     * Note: No validation is performed here because all attributes are validated
     * when they are added via addAttribute(), addAttributes(), or setAttribute().
     * Encapsulation guarantees data integrity.
     * 
     * @return string
     */
    public function renderAttributes(): string {
        if (empty($this->attributes)) {
            return "";
        }
    
        $attributes = "";
    
        foreach ($this->attributes as $key => $value) {
            $attributes .= "$key";
            if ($value !== null) {
                $attributes .= '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
            }
            $attributes .= " ";
        }
    
        return trim($attributes);
    }
}