<?php

class ParameterValueParserTest extends PHPUnit_Framework_TestCase {

    public function testParseNonQuotedValue() {
        $parser = new \webignition\InternetMediaType\Parameter\Parser\ValueParser();
        $parser->setAttribute('charset');
        $this->assertEquals('ISO-8859-4', $parser->parse("charset=ISO-8859-4"));
    }
    
    
    public function testParseQuotedValue() {
        $parser = new \webignition\InternetMediaType\Parameter\Parser\ValueParser();
        $parser->setAttribute('charset');
        $output = $parser->parse('charset="quoted value here"');
        
        $this->assertInstanceOf('\webignition\QuotedString\QuotedString', $output);
        $this->assertEquals('quoted value here', $output->getValue());
    } 
}