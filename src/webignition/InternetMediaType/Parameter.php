<?php

namespace webignition\InternetMediaType;


/**
 * A parameter value present in an Internet media type
 * 
 * If media type == 'text/html; charset=UTF8', parameter == 'charset=UTF8'
 * 
 * Defined as:
 * 
 * parameter               = attribute "=" value
 * attribute               = token
 * value                   = token | quoted-string
 * 
 * The type, subtype, and parameter attribute names are case-insensitive
 * 
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.6
 *  
 */
class Parameter {
    
    const ATTRIBUTE_VALUE_SEPARATOR = '=';
    
    /**
     * The parameter attribute.
     * 
     * For a parameter of 'charset=UTF8', this woud be 'charset'
     * 
     * @var string
     */
    private $attribute;
    
    
    /**
     * The parameter value
     * 
     * For a parameter of 'charset=UTF8', this would be 'UTF8'
     * 
     * @var string
     */
    private $value;
    
    
    /**
     *
     * @param string $attribute
     * @return \webignition\InternetMediaType\Parameter 
     */
    public function setAttribute($attribute) {        
        $this->attribute = strtolower($attribute);
        return $this;
    }
    
    
    /**
     *
     * @return string
     */
    public function getAttribute() {
        return (is_null($this->attribute)) ? '' : $this->attribute;
    }
    
    
    /**
     *
     * @param string $value
     * @return \webignition\InternetMediaType\Parameter 
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;        
    }
    
    
    /**
     *
     * @return string
     */
    public function getValue() {
        return (string)$this->value;
    }
    
    
    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->getAttribute() . self::ATTRIBUTE_VALUE_SEPARATOR . $this->getValue();
    }
}