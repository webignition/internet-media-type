<?php

namespace webignition\Tests\InternetMediaType\Parser;

class ParseSenseOutOfInvalidContentTypeTest extends ParserTest {

    public function testParseCommaSeparatedContentTypeDuplicated() {  
        $this->parser->setAttemptToRecoverFromInvalidInternalCharacter(true);
        $internetMediaType = $this->parser->parse('application/x-javascript, application/x-javascript; charset=utf-8');
     
        $this->assertEquals('application', $internetMediaType->getType());
        $this->assertEquals('x-javascript', $internetMediaType->getSubtype());
    }
    
    public function testParseTextHtmlSpaceCharsetUtf8() {
        $this->parser->setAttemptToRecoverFromInvalidInternalCharacter(true);
        $internetMediaType = $this->parser->parse('text/html charset=UTF-8');
     
        $this->assertEquals('text', $internetMediaType->getType());
        $this->assertEquals('html', $internetMediaType->getSubtype());        
        $this->assertEquals('UTF-8', $internetMediaType->getParameter('charset')->getValue());
    }
    
    public function testParseAttributeColonValue() {
        $this->parser->setIgnoreInvalidAttributes(true);
        $this->parser->setAttemptToRecoverFromInvalidInternalCharacter(true);
        
        $this->assertEquals('text/css; charset=UTF-8', $this->parser->parse('text/css; charset: UTF-8'));
    }
}