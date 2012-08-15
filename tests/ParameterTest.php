<?php

class ParameterTest extends PHPUnit_Framework_TestCase {

    public function testSetAttribute() {
        $testData = array(
            'attribute' => 'attribute',
            'ATTRIBUTE' => 'attribute',
            'AttriBuTE' => 'attribute',
            null => ''
        );
        
        $parameter = new \webignition\InternetMediaType\Parameter();
        $this->assertEquals('', $parameter->getValue());
        
        foreach ($testData as $input => $expectedOutput) {
            $parameter->setAttribute($input);
            $this->assertEquals($expectedOutput, $parameter->getAttribute());
        }        
    }
    
    
    public function testSetValue() {
        $testData = array(
            'value1' => 'value1',
            'Value2' => 'Value2',
            'VALUE3' => 'VALUE3',
            0 => '0',
            123 => '123',
            null => ''
        );
        
        $parameter = new \webignition\InternetMediaType\Parameter();
        $this->assertEquals('', $parameter->getValue());
        
        foreach ($testData as $input => $expectedOutput) {
            $parameter->setValue($input);
            $this->assertEquals($expectedOutput, $parameter->getValue());
        }        
    }
    
    
    public function testToString() {
        $testData = array(
            array(
                'attribute' => 'attribute1',
                'value' => 'value1',
                'expectedOutput' => 'attribute1=value1'
            ),
            array(
                'attribute' => 'Attribute2',
                'value' => 'value2',
                'expectedOutput' => 'attribute2=value2'
            ),
            array(
                'attribute' => 'ATTribUTE3',
                'value' => 'VALUE3',
                'expectedOutput' => 'attribute3=VALUE3'
            ),
            array(
                'attribute' => '',
                'value' => '{anything}',
                'expectedOutput' => ''
            ),
            array(
                'attribute' => '{anything}',
                'value' => '',
                'expectedOutput' => ''
            )            
        );
        
        $parameter = new \webignition\InternetMediaType\Parameter();
        $this->assertEquals('', (string)$parameter);
        
        foreach ($testData as $testDataSet) {
            $parameter->setAttribute($testDataSet['attribute']);
            $parameter->setValue($testDataSet['value']);
            $this->assertEquals($testDataSet['expectedOutput'], (string)$parameter);
        } 
    }
}