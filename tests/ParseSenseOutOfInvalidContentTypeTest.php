<?php

class ParseSenseOutOfInvalidContentTypeTest extends PHPUnit_Framework_TestCase {

    public function testParseCommaSeparatedContentTypeDuplicated() {  
        $parser = new \webignition\InternetMediaType\Parser\Parser();
        $parser->setAttemptToRecoverFromInvalidInternalCharacter(true);
        $internetMediaType = $parser->parse('application/x-javascript, application/x-javascript; charset=utf-8');
     
        $this->assertEquals('application', $internetMediaType->getType());
        $this->assertEquals('x-javascript', $internetMediaType->getSubtype());
    }
    
    public function testParseTextHtmlSpaceCharsetUtf8() {
        $parser = new \webignition\InternetMediaType\Parser\Parser();
        $parser->setAttemptToRecoverFromInvalidInternalCharacter(true);
        $internetMediaType = $parser->parse('text/html charset=UTF-8');
     
        $this->assertEquals('text', $internetMediaType->getType());
        $this->assertEquals('html', $internetMediaType->getSubtype());        
        $this->assertEquals('UTF-8', $internetMediaType->getParameter('charset')->getValue());
    }    
}