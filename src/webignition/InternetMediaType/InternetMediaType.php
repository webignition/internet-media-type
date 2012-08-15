<?php

namespace webignition\InternetMediaType;

use webignition\InternetMediaType\Parameter\Parameter;

/**
 * Models an Internet Media Type as defined as:
 * 
 * HTTP uses Internet Media Types [17] in the Content-Type (section 14.17) and 
 * Accept (section 14.1) header fields in order to provide open and extensible data
 * typing and type negotiation.
 * 
 * media-type     = type "/" subtype *( ";" parameter )
 * type           = token
 * subtype        = token
 * 
 * Parameters MAY follow the type/subtype in the form of attribute/value pairs
 * 
 * parameter               = attribute "=" value
 * attribute               = token
 * value                   = token | quoted-string
 * 
 * The type, subtype, and parameter attribute names are case-insensitive
 * 
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.7 
 * 
 */
class InternetMediaType {
    
    /**
     * Main media type.
     * 
     * For a 'text/html' media type, this would be 'text'
     * 
     * @var string
     */
    private $type = null;
    
    
    /**
     * Subtype, a type within a type
     * 
     * For a 'text/html' media type, this would be 'html'
     * 
     * @var string
     */
    private $subtype = null;
    
    
    /**
     * Collection of \webignition\InternetMediaType\Parameter objects
     * 
     * @var array
     */
    private $parameters = array();
    
    
    /**
     *
     * @param string $type
     * @return \webignition\InternetMediaType\InternetMediaType 
     */
    public function setType($type) {
        $this->type = strtolower($type);
        return $this;
    }
    
    
    /**
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }
    
    
    /**
     *
     * @param stirng $subtype
     * @return \webignition\InternetMediaType\InternetMediaType 
     */
    public function setSubtype($subtype) {
        $this->subtype = strtolower($subtype);
        return $this;
    }
    
    
    /**
     *
     * @return string
     */
    public function getSubtype() {
        return $this->subtype;
    }
    
    
    /**
     *
     * @param Parameter $parameter
     * @return \webignition\InternetMediaType\InternetMediaType 
     */
    public function addParameter(Parameter $parameter) {
        $this->parameters[$parameter->getAttribute()] = $parameter;
        return $this;
    }
    
    
    /**
     *
     * @param string $attribute
     * @return boolean
     */
    public function hasParameter($attribute) {
        return !is_null($this->getParameter($attribute));
    }
    
    
    /**
     *
     * @param Parameter $parameter
     * @return \webignition\InternetMediaType\InternetMediaType 
     */
    public function removeParameter(Parameter $parameter) {
        if ($this->hasParameter($parameter->getAttribute())) {
            unset($this->parameters[$parameter->getAttribute()]);
        }
        
        return $this;
    }
    
    
    /**
     *
     * @param string $attribute
     * @return \webignition\InternetMediaType\InternetMediaType|null
     */
    public function getParameter($attribute) {
        $attribute = trim(strtolower($attribute));
        return isset($this->parameters[$attribute]) ? $this->parameters[$attribute] : null;
    }
    
    
    /**
     * Get collection of Parameter objects
     * 
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }
    
}
