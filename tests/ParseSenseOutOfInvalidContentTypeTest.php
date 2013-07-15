<?php

class ParseSenseOutOfInvalidContentTypeTest extends PHPUnit_Framework_TestCase {

    public function testParseCommaSeparatedContentTypeDuplicated() {  
        $parser = new \webignition\InternetMediaType\Parser\Parser();
        $parser->setAttemptToRecoverFromInvalidInternalCharacter(true);
        $internetMediaType = $parser->parse('application/x-javascript, application/x-javascript; charset=utf-8');
     
        $this->assertEquals('application', $internetMediaType->getType());
        $this->assertEquals('x-javascript', $internetMediaType->getSubtype());
    }

    
}