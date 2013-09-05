<?php

namespace webignition\InternetMediaType\Parser;

use webignition\InternetMediaType\InternetMediaType;

/**
 * Attempts to fix unparseable internet media types based purely on
 * observed invalid media type strings that, open visual observation, can
 * be translated into something sensible
 *  
 */
class TypeFixer {
    
    const COMMA_SEPARATED_TYPE_SEPARATOR = ', ';

    
    /**
     *
     * @var string
     */
    private $inputString;
    
    
    /**
     *
     * @var int
     */
    private $position;
    
    
    /**
     *
     * @var Parser 
     */
    private $parser;
    
    
    /**
     * 
     * @param string $inputString
     */
    public function setInputString($inputString) {        
        $this->inputString = $inputString;
    }
    
    
    /**
     * 
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = $position;
    }
    
    
    /**
     * 
     * @param \webignition\InternetMediaType\Parser\Parser $parser
     */
    public function setParser(Parser $parser) {
        $this->parser = $parser;
    }
    
    
    
    /**
     * 
     * @return InternetMediaType|null
     */
    public function fix() {
        $possibleFixedTypes = $this->commaSeparatedTypeFix();
        if (count($possibleFixedTypes) === 0) {
            $possibleFixedTypes = $this->spaceSeparatingTypeAndAttributeFix();
        }
        
        $bestFixIndex = null;        
        foreach ($possibleFixedTypes as $fixIndex => $possibleFixedType) {
            if ($possibleFixedType['internet-media-type'] instanceof InternetMediaType) {
                if (is_null($bestFixIndex)) {
                    $bestFixIndex = $fixIndex;
                } else {
                    if (strlen($possibleFixedType['internet-media-type']) > strlen($possibleFixedTypes[$bestFixIndex]['internet-media-type'])) {
                        $bestFixIndex = $fixIndex;
                    }
                }
            }
        }
        
        if (!is_null($bestFixIndex)) {
            return $possibleFixedTypes[$bestFixIndex]['internet-media-type'];
        }
        
        return null;
    }
    
    
    /**
     * Attempt to fix media types that are formatted as:
     * 
     * type/subtype, type/subtype
     * 
     * i.e. two media types comma-separated together
     * 
     * If of this type, go for the longest valid option     
     */
    private function commaSeparatedTypeFix() {
        if ($this->position === 0) {
            return array();
        }
        
        $separatorComparator = substr($this->inputString, $this->position - 1, 2);
        if ($separatorComparator !== self::COMMA_SEPARATED_TYPE_SEPARATOR) {
            return array();
        }
        
        $possibleTypes = array(
            array(
                'input' => substr($this->inputString, 0, $this->position - 1),
                'internet-media-type' => null
            ),
            array(
                'input' => substr($this->inputString, $this->position + 1),
                'internet-media-type' => null
            )
        );
        
        foreach ($possibleTypes as $possibleTypeIndex => $possibleType) {
            $possibleType['internet-media-type'] = $this->parser->parse($possibleType['input']);       
            $possibleTypes[$possibleTypeIndex] = $possibleType;
            
        }
        
        return $possibleTypes;
    }
    
    
    /**
     * Attempt to fix media types that are formatted as:
     * 
     * type/subtype attribute=value
     * 
     * i.e. a media type and parameters separated by a space not a semicolon  
     */    
    private function spaceSeparatingTypeAndAttributeFix() {
        if ($this->position === 0) {
            return array();
        }
        
        if ($this->inputString[$this->position] !== ' ') {
            return array();
        }
        
        $possibleTypes = array(
            array(
                'input' => substr($this->inputString, 0, $this->position) . ';' . substr($this->inputString, $this->position + 1),
                'internet-media-type' => null
            ),

        );
        
        foreach ($possibleTypes as $possibleTypeIndex => $possibleType) {
            $possibleType['internet-media-type'] = $this->parser->parse($possibleType['input']);       
            $possibleTypes[$possibleTypeIndex] = $possibleType;
            
        }
        
        return $possibleTypes;
    }

}