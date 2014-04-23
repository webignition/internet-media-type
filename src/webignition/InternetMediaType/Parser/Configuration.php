<?php

namespace webignition\InternetMediaType\Parser;

class Configuration {     
    
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
     * Should the parser ignore issues with invalid attributes?
     * 
     * @param boolean $ignoreInvalidAttributes
     * @return \webignition\InternetMediaType\Parser\Configuration
     */
    public function setIgnoreInvalidAttributes($ignoreInvalidAttributes) {
        $this->ignoreInvalidAttributes = filter_var($ignoreInvalidAttributes, FILTER_VALIDATE_BOOLEAN);
        return $this;
    }   
    

    /**
     * Should the parser attempt to recover from cases where the given
     * input string contains invalid characters?
     * 
     * @param boolean $attemptToRecoverFromInvalidInternalCharacter
     * @return \webignition\InternetMediaType\Parser\Configuration
     */
    public function setAttemptToRecoverFromInvalidInternalCharacter($attemptToRecoverFromInvalidInternalCharacter) {
        $this->attemptToRecoverFromInvalidInternalCharacter = filter_var($attemptToRecoverFromInvalidInternalCharacter, FILTER_VALIDATE_BOOLEAN);
        return $this;
    }
    
    
}