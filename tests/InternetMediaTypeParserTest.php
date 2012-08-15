<?php

class InternetMediaTypeParserTest extends PHPUnit_Framework_TestCase {

    public function testParseValidType() {
        $parser = new \webignition\InternetMediaType\Parser\TypeParser();
        $this->assertEquals('text', $parser->parse('text/html; charset=ISO-8859-4'));
    }
    
    
    public function testParseInvalidType() {
        $parser = new \webignition\InternetMediaType\Parser\TypeParser();
        
        try {
            $parser->parse('t e x t/html; charset=ISO-8859-4');
        } catch (\webignition\InternetMediaType\Parser\TypeParserException $exception) {
            $this->assertEquals(1, $exception->getCode());
            return;
        }
        
        $this->fail('Invalid internal character exception not thrown');
    }
 
}