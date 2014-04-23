<?php

namespace webignition\Tests\InternetMediaType;

class InternetMediaSubtypeParserTest extends BaseTest {

    public function testParseSubtypeWithNoParameters() {
        $parser = new \webignition\InternetMediaType\Parser\SubtypeParser();
        $this->assertEquals('html', $parser->parse('text/html'));
    }

    public function testParseSubtypeWithParameters() {
        $parser = new \webignition\InternetMediaType\Parser\SubtypeParser();
        $this->assertEquals('html', $parser->parse('text/html; charset=ISO-8859-4'));
    }    
    
    
    public function testParseInvalidtype() {
        $parser = new \webignition\InternetMediaType\Parser\SubtypeParser();
        
        try {
            $parser->parse('text/h t m l; charset=ISO-8859-4');
        } catch (\webignition\InternetMediaType\Parser\SubtypeParserException $exception) {
            $this->assertEquals(1, $exception->getCode());
            return;
        }
        
        $this->fail('Invalid internal character exception not thrown');
    }
 
}