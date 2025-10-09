<?php

namespace AkidoLd\SimpleComponent\Components;

use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAriaIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributeIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributKeyIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentDataIsInvalidException;
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
    
    public function __construct(string $tag = 'comp', bool $closed = true){
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
     * @param string $key The key of the attribute
     * @param string $value The value of the attribute
     * @return void
     */
    public static function cleanAttribute(string &$key, string &$value): void {
        $key = trim($key);
        $value = trim($value);
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
    
        if (!is_string($value)) {
            throw new ComponentAttributeIsInvalidException('Attribute value must be a string');
        }
    
        // Clean the key and value before further processing
        self::cleanAttribute($key, $value);
    
        // Check for empty key
        if ($key === '') {
            throw new ComponentAttributKeyIsInvalidException('Attribute key cannot be empty');
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
     * @throws ComponentAttributKeyIsInvalidException If the attribute key is empty
     * @return Component The reference to this component.
     */
    public function addAttribute(string $key, string $value = ''): self{
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
     * If in the new attributes there is an element with the key of
     * one of the attributes present, the said attributes will be 
     * overwritten by the new (check the {@see array_merge()} documentation)
     * 
     * @param array $attributes The new attributes to add
     * @throws ComponentAttributKeyIsInvalidException If an attribute key is not a string or empty
     * @throws ComponentAttributeIsInvalidException If an attribute value is not a string
     * @return Component The reference to this component.
     */
    public function addAttributes(array $attributes): self {
        $cleaned = [];
        //Check each of the attribute to add
        foreach ($attributes as $key => $value) {
            self::checkAttribute($key, $value);
            self::cleanAttribute($key, $value);
            $cleaned[$key] = $value;
        }

        //Add the new attributes to the old attribute
        $this->attributes = array_merge($this->attributes, $cleaned);
        return $this;
    }

    /**
     * Remove an attribute from this component.
     *
     * No error occurs when removing a non-existent attribute.
     *
     * @param string $key The key of the attribute to remove.
     * @return string|null Returns the value of the attribut or null if the attribute is not found.
     */
    public function removeAttribute(string $key): ?string{
        $value = $this->getAttribute($key);
        unset($this->attributes[$key]);
        return $value;
    }

    /**
     * Get an attribute of this component.
     * 
     * @param string $key The key of the attribute we want to get.
     * @return string|null Returns the value of the attribute if it's set and null otherwise.
     */
    public function getAttribute(string $key): ?string{
        return $this->attributes[$key] ?? null;
    }

    /**
     * Alias of {@see Component::addAttribute()} method
     * 
     * @param string $key The attribute key.
     * @param string $value The new value to assign.
     * @throws ComponentAttributKeyIsInvalidException If the attribute key is empty
     * @return Component The reference to this component.
     */
    public function setAttribute(string $key, string $value = ""): self{
        $this->addAttribute($key, $value);
        return $this;
    }

    /**
     * Check if an attribute exists in this component.
     *
     * @param string $key The attribute key to check.
     * @return bool Return `true` if the attribute exist, `false` otherwise
     */
    public function attributeExists(string $key): bool{
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get the attributes array of this object
     * 
     * @return array<string, string>
     */
    public function getAttributes(): array{
        return $this->attributes;
    }
    
    /**
     * Add content to this Component
     * 
     * Content is trimmed (whitespace removed from start/end).
     * Each line of content is HTML-escaped and separated by a newline.
     * The first line has no leading newline, subsequent lines are prefixed with PHP_EOL.
     * 
     * If the content is empty after trimming, it is not added.
     * 
     * @param string $content The content to add
     * @return Component The reference to this Component
     */
    public function addContent(string $content): self{
        $content = trim($content);
        if($content !== ""){
            // Add newline before content if contents is not empty (not the first line)
            if($this->contents !== ""){
                $this->contents .= PHP_EOL;
            }
            $this->contents .= htmlspecialchars($content, ENT_QUOTES);
        }
        
        return $this;
    }

    /**
     * Add several content at time
     * 
     * @param string[] $contents Array of content to add
     * @return Component The reference to this Component
     */
    public function addContents(array $contents): self{
        foreach($contents as $content){
            $this->addContent($content);
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
            //Add whitespace if is not the first attribute
            if($attributes !== ''){
                $attributes .= " ";
            }
            //
            $attributes .= "$key";
            if ($value !== '') {
                $attributes .= '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
            }
        }
        return $attributes;
    }
    
    /**
     * Render this component into HTML
     * 
     * Returns the complete HTML representation of this component,
     * with proper formatting (newlines between content lines).
     * 
     * Example:
     * ```php
     * $comp = new Component('div')
     *     ->addContent('Line 1')
     *     ->addContent('Line 2');
     * echo $comp->render();
     * ```
     * 
     * Output:
     * ```html
     * <div>
     * Line 1
     * Line 2
     * </div>
     * ```
     * 
     * @return string The HTML representation of this component
     */
    public function render(): string{
        $html = "<{$this->tag}";
        
        $attributes = $this->renderAttributes();
        if($attributes !== ""){
            $html .= " $attributes";
        }
        $html .= ">";
        
        if($this->contents !== "" && $this->isClosed()){
            $html .= PHP_EOL . $this->contents . PHP_EOL;
        }
        
        if($this->isClosed()){
            $html .= "</{$this->tag}>";
        }
        
        return $html;
    }

    //Implementation of Stringable
    public function __toString(): string{
        return $this->render();
    }

    /**
     * Class HELPERS
     * 
     * All the methods of this section abstract the manipulation of attributes table
     * 
     */
    
    /**
     * Set the Id of this component
     * 
     * @param string $id The id of this component
     * @throws ComponentAttributeIsInvalidException If the id is not valid. Read the message exception for more details
     * @return Component The reference to this Component
     */
    public function setId(string $id): self{
        $this->setAttribute('id', $id);
        return $this;
    }
    
    /**
     * Get the id of this Component
     *
     * @return string|null Return the id of this component and null if the id attribute is not set
     */
    public function getId(): ?string{
        return $this->getAttribute('id');
    }
    
    /**
     * Set the class of this Component
     * 
     * This method overwrites the previous class set.
     * 
     * @param string $class The class list you want to set to this Component
     * @return Component The reference to this Component
     */
    public function setClass(string $class): self{
        $this->setAttribute('class', $class);
        return $this;
    }
    
    /**
     * Add a new class to this component
     * 
     * This method contrary to {@see Component::setClass()} doesn't overwrite the previous class.
     * The new class is just added to the previous class
     * 
     * @param string $class The class you want to add
     * @throws ComponentAttributeIsInvalidException If the new class you want to add is empty
     * @return Component The reference to this Component
     */
    public function addClass(string $class): self{
        self::checkAttribute('class', $class);
        $current = (string) $this->getAttribute('class');
        $value = ($current === '') ? $class : $current . ' ' . $class;
        $this->setAttribute('class', $value);
        return $this;
    }
    
    /**
     * Get the class of this Component
     * 
     * @return string|null Return the class of this component and null if the class attribute is not set
     */
    public function getClass(): ?string{
        return $this->getAttribute('class');
    }
    
    /**
     * Check if this component already has the class in parameters
     * 
     * @param string $class The class to check
     * @return bool Return `true` if the class is found and `false` otherwise
     */
    public function hasClass(string $class): bool{
        $class = trim($class);
        if($class === ""){
            return false;
        }
        $current = (string) $this->getAttribute('class');
        if($current === ""){
            return false;
        }
        $classes = explode(' ', $current);
        return in_array($class, $classes);
    }
    
    /**
     * Remove a class from this Component
     * 
     * When the remove is done, if the new class string is empty
     * the class attribute will be removed automatically (useful for {@see Component::renderAttributes()})
     * 
     * @param string $class The class to remove
     * @return Component The reference to this Component
     */
    public function removeClass(string $class): self {
        $class = trim($class);
        if($class === ""){
            return $this;
        }
        
        $current = (string) $this->getAttribute('class');
        if($current === ""){
            return $this;
        }
        
        $classes = explode(' ', $current);
        $classes = array_filter($classes, fn($c) => $c !== $class);
        $updated = implode(' ', $classes);
        
        if($updated !== '') {
            $this->setAttribute('class', $updated);
        } else {
            $this->removeAttribute('class');
        }
        
        return $this;
    }
    
    /**
     * Set a data attribute to this Component
     * 
     * @param string $name The name of this data attribute (without "data-" prefix)
     * @param string $value The value of this data attribute
     * @throws ComponentDataIsInvalidException If the data name or value is empty
     * @return Component The reference to this Component
     */
    public function setData(string $name, string $value): self{
        self::cleanAttribute($name, $value);
        if($name === "" || $value === ""){
            $empty = ($name === '') ? 'name' : 'value';
            throw new ComponentDataIsInvalidException("A data $empty cannot be empty");
        }
        $this->setAttribute('data-'.$name, $value);
        return $this;
    }
    
    /**
     * Get a data attribute value from this Component
     * 
     * @param string $name The data attribute name (without "data-" prefix)
     * @return string|null Return the value of the data attribute or null if not found
     */
    public function getData(string $name): ?string{
        $name = trim($name);
        if($name === ''){
            return null;
        }
        return $this->getAttribute('data-'.$name);
    }
    
    /**
     * Remove a data attribute from this Component
     * 
     * @param string $name The data attribute name to remove (without "data-" prefix)
     * @return string|null Return the value of the removed data attribute or null if not found
     */
    public function removeData(string $name): ?string{
        $name = trim($name);
        if($name === ''){
            return null;
        }
        return $this->removeAttribute('data-'.$name);
    }
    
    /**
     * Set an aria attribute to this Component
     * 
     * If the aria attribute already exists, its value will be overwritten
     * 
     * @param string $name The name of the aria attribute (without "aria-" prefix)
     * @param string $value The value to set
     * @throws ComponentAriaIsInvalidException If the aria name or value is empty
     * @return Component The reference to this Component
     */
    public function setAria(string $name, string $value): self{
        self::cleanAttribute($name, $value);
        if($name === "" || $value === ""){
            $empty = ($name === '') ? 'name' : 'value';
            throw new ComponentAriaIsInvalidException("An aria $empty cannot be empty");
        }
        $this->setAttribute('aria-'.$name, $value);
        return $this;
    }
    
    /**
     * Get an aria attribute value from this Component
     * 
     * @param string $name The aria attribute name (without "aria-" prefix)
     * @return string|null Return the aria attribute value or null if not found
     */
    public function getAria(string $name): ?string{
        $name = trim($name);
        if($name === ''){
            return null;
        }
        return $this->getAttribute('aria-'.$name);
    }
    
    /**
     * Remove an aria attribute from this Component
     * 
     * @param string $name The aria attribute name to remove (without "aria-" prefix)
     * @return string|null Return the value of the removed aria attribute or null if not found
     */
    public function removeAria(string $name): ?string{
        $name = trim($name);
        if($name === ''){
            return null;
        }
        return $this->removeAttribute('aria-'.$name);
    }
}