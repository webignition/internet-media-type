<?php

namespace webignition\Tests\InternetMediaType;

class ParameterAttributeParserTest extends BaseTest {

    public function testParseValidAttributeName() {
        $parser = new \webignition\InternetMediaType\Parameter\Parser\AttributeParser();
        $this->assertEquals('charset', $parser->parse("charset=ISO-8859-4"));
    }
    
    public function testParseInvalidInternalCharactersAttributeName() {
        $parser = new \webignition\InternetMediaType\Parameter\Parser\AttributeParser();
        
        try {
            $parser->parse("ch arset=ISO-8859-4");
        } catch (\webignition\InternetMediaType\Parameter\Parser\AttributeParserException $exception) {
            $this->assertEquals(1, $exception->getCode());
            return;
        }
        
        $this->fail('Invalid internal character exception not thrown');                        
    }
    
    public function testParseMissingValue() {
        $parser = new \webignition\InternetMediaType\Parameter\Parser\AttributeParser();
        $this->assertEquals('charset', $parser->parse("charset"));
        $this->assertEquals('charset', $parser->parse("charset="));
    }    
}