<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\StringParser\StringParser;

/**
 * Parses out the attribute name from an internet media type parameter string
 *  
 */
class AttributeParser extends StringParser {
    
    const ATTRIBUTE_VALUE_SEPARATOR = '=';
    const STATE_IN_ATTRIBUTE_NAME = 1;
    const STATE_INVALID_INTERNAL_CHARACTER = 2;
    const STATE_LEFT_ATTRIBUTE_NAME = 3;
    
    /**
     * Collection of characters not valid in an attribute name
     *  
     * @var array
     */
    private $invalidCharacters = array(
        ' ',
        '"',
        '\\'
    );
    
    /**
     *
     * @var boolean
     */
    private $ignoreInvalidAttributes = false;     
    
    /**
     *
     * @param string $inputString
     * @return string
     */
    public function parse($inputString) {
        return parent::parse(trim($inputString));
    }
    
    protected function parseCurrentCharacter() {
        switch ($this->getCurrentState()) {
            case self::STATE_UNKNOWN:
                $this->setCurrentState(self::STATE_IN_ATTRIBUTE_NAME);
                break;
            
            case self::STATE_IN_ATTRIBUTE_NAME:
                if ($this->isCurrentCharacterInvalid()) {
                    if ($this->ignoreInvalidAttributes === true) {
                        $this->incrementCurrentCharacterPointer();
                        $this->setCurrentState(self::STATE_LEFT_ATTRIBUTE_NAME);
                        $this->clearOutputString();                        
                    } else {
                        $this->setCurrentState(self::STATE_INVALID_INTERNAL_CHARACTER);
                    }
                } elseif ($this->isCurrentCharacterAttributeValueSeparator()) {
                    $this->setCurrentState(self::STATE_LEFT_ATTRIBUTE_NAME);
                } else {
                    $this->appendOutputString();
                    $this->incrementCurrentCharacterPointer();
                }
                
                break;
                
            case self::STATE_LEFT_ATTRIBUTE_NAME:
                $this->stop();
                break;
            
            case self::STATE_INVALID_INTERNAL_CHARACTER:                
                throw new AttributeParserException('Invalid internal character after at position '.$this->getCurrentCharacterPointer(), 1);
                break;
        }
    }
    
    
    /**
     *
     * @return boolean
     */
    private function isCurrentCharacterInvalid() {
        return in_array($this->getCurrentCharacter(), $this->invalidCharacters);
    }
    
    
    /**
     *
     * @return boolean
     */
    private function isCurrentCharacterAttributeValueSeparator() {
        return $this->getCurrentCharacter() == self::ATTRIBUTE_VALUE_SEPARATOR;
    }
    
    
    /**
     * 
     * @param boolean $ignoreInvalidAttributes
     */
    public function setIgnoreInvalidAttributes($ignoreInvalidAttributes) {
        $this->ignoreInvalidAttributes = filter_var($ignoreInvalidAttributes, FILTER_VALIDATE_BOOLEAN);
    }    

}