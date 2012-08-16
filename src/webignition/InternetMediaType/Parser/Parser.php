<?php

namespace webignition\InternetMediaType\Parser;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parser\TypeParser;
use webignition\InternetMediaType\Parser\SubtypeParser;
use webignition\InternetMediaType\Parameter\Parser\Parser as ParameterParser;
use webignition\InternetMediaType\Parameter\Parameter;



/**
 * Parses a string representation of an Internet media type into an
 * InternetMediaType object
 *  
 */
class Parser {
    
    const TYPE_SUBTYPE_SEPARATOR = '/';
    const TYPE_PARAMETER_SEPARATOR = ';';
    
    /**
     *
     * @var \webignition\InternetMediaType\Parser\TypeParser
     */
    private $typeParser = null;
    
    
    /**
     *
     * @var \webignition\InternetMediaType\Parser\SubypeParser
     */
    private $subtypeParser = null;
    
    
    /**
     *
     * @var \webignition\InternetMediaType\Parameter\Parser\Parser 
     */
    private $parameterParser = null;    
    
    
    /**
     *
     * @param string $internetMediaTypeString
     * @return \webignition\InternetMediaType\InternetMediaType
     */
    public function parse($internetMediaTypeString) {
        $inputString = trim($internetMediaTypeString);
        
        $internetMediaType = new InternetMediaType();
        $internetMediaType->setType($this->getTypeParser()->parse($inputString));
        $internetMediaType->setSubtype($this->getSubypeParser()->parse($inputString));
        
        $parameterString = $this->getParameterString($inputString, $internetMediaType->getType(), $internetMediaType->getSubtype());        
        $parameterStrings = $this->getParameterStrings($parameterString);       
        $parameters = $this->getParameters($parameterStrings);
        
        foreach ($parameters as $parameter) {
            $internetMediaType->addParameter($parameter);
        }
        
        return $internetMediaType;
    }
    
    
    /**
     *
     * @return \webignition\InternetMediaType\Parser\TypeParser
     */
    private function getTypeParser() {
        if (is_null($this->typeParser)) {
            $this->typeParser = new TypeParser();
        }
        
        return $this->typeParser;
    }
    
    
    /**
     *
     * @return \webignition\InternetMediaType\Parser\SubtypeParser
     */
    private function getSubypeParser() {
        if (is_null($this->subtypeParser)) {
            $this->subtypeParser = new SubtypeParser();
        }
        
        return $this->subtypeParser;
    }
    
    
    /**
     *
     * @return \webignition\InternetMediaType\Parameter\Parser\Parser 
     */
    private function getParameterParser() {
        if (is_null($this->parameterParser)) {
            $this->parameterParser = new ParameterParser();            
        }
        
        return $this->parameterParser;
    } 
    
    
    /**
     *
     * @param string $inputString
     * @param string $type
     * @param string $subtype
     * @return string 
     */
    private function getParameterString($inputString, $type, $subtype) {
        $parameterString = substr($inputString, strlen($type) + strlen(self::TYPE_SUBTYPE_SEPARATOR));
        $parameterString = substr($parameterString, strlen($subtype));
        
        if ($parameterString === false) {
            return '';
        }
        
        return $parameterString;
    }
    
    
    /**
     * Get collection of string representations of each parameter
     * 
     * @param string $parameterString
     * @return array
     */
    private function getParameterStrings($parameterString) {
        $rawParameterStrings = explode(self::TYPE_PARAMETER_SEPARATOR, $parameterString);
        $parameterStrings = array();
        
        foreach ($rawParameterStrings as $rawParameterString) {
            if ($rawParameterString != '') {
                $parameterStrings[] = trim($rawParameterString);
            }
        }
        
        return $parameterStrings;
    }
    
    
    /**
     * Get a collection of Parameter objects from a collection of string
     * representations of the same
     * 
     * @param array $parameterStrings
     * @return array 
     */
    private function getParameters($parameterStrings) {
        $parameters = array();
        foreach ($parameterStrings as $parameterString) {  
            $parameters[] = $this->getParameterParser()->parse($parameterString);
        }
        
        return $parameters;
    }
    
    
  
}