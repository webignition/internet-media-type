<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\AttributeParser;
use webignition\InternetMediaType\Parameter\Parser\ValueParser;
use webignition\InternetMediaType\Parameter\Parameter;


/**
 * Parsers a parameter string value into a Parameter object
 * 
 * Defined as:
 * 
 * parameter               = attribute "=" value
 * attribute               = token
 * value                   = token | quoted-string
 * 
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.6
 * 
 * Linear white space (LWS) MUST NOT be used between the type and subtype, nor between an attribute and its value.
 * 
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.7
 *  
 */
class Parser {
    
    
    /**
     *
     * @var boolean
     */
    private $ignoreInvalidAttributes = false;    
    
    
    /**
     *
     * @var boolean
     */
    private $attemptToRecoverFromInvalidInternalCharacter = false;    
    
    /**
     *
     * @param string $parameterString
     * @return \webignition\InternetMediaType\Parameter 
     */
    public function parse($parameterString) {
        $inputString = trim($parameterString);
        $attribute = $this->getAttributeParser()->parse($inputString);
        
        if ($attribute == '') {
            return new Parameter();
        }
        
        $value = $this->getValueParser($attribute)->parse($parameterString);

        $parameter = new Parameter();
        $parameter->setAttribute($attribute);
        $parameter->setValue($value);
        
        return $parameter;
    }
    
    
    /**
     *
     * @return \webignition\InternetMediaType\Parameter\Parser\AttributeParser 
     */
    private function getAttributeParser() {
        $attributeParser = new AttributeParser();
        if ($this->ignoreInvalidAttributes === true) {
            $attributeParser->setIgnoreInvalidAttributes(true);
        }
        
        if ($this->attemptToRecoverFromInvalidInternalCharacter === true) {
            $attributeParser->setAttemptToRecoverFromInvalidInternalCharacter(true);
        }         
        
        return $attributeParser;
    }
    
    
    /**
     *
     * @param string $attribute
     * @return \webignition\InternetMediaType\Parameter\Parser\ValueParser
     */
    private function getValueParser($attribute) {
        $valueParser = new ValueParser();
        $valueParser->setAttribute($attribute);
        return $valueParser;
    }   
    
    
    /**
     * 
     * @param boolean $ignoreInvalidAttributes
     */
    public function setIgnoreInvalidAttributes($ignoreInvalidAttributes) {
        $this->ignoreInvalidAttributes = filter_var($ignoreInvalidAttributes, FILTER_VALIDATE_BOOLEAN);
    } 
    
    
    /**
     * 
     * @param boolean $attemptToRecoverFromInvalidInternalCharacter
     */
    public function setAttemptToRecoverFromInvalidInternalCharacter($attemptToRecoverFromInvalidInternalCharacter) {
        $this->attemptToRecoverFromInvalidInternalCharacter = true;
    }    
}