<?php

namespace webignition\Tests\InternetMediaType;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parameter\Parameter;

class InternetMediaTypeTest extends BaseTest
{
    /**
     * @var InternetMediaType
     */
    private $internetMediaType;

    protected function setUp()
    {
        parent::setUp();
        $this->internetMediaType = new InternetMediaType();
    }

    /**
     * @dataProvider castToStringDataProvider
     *
     * @param InternetMediaType $internetMediaType
     * @param $expectedString
     */
    public function testCastToString(InternetMediaType $internetMediaType, $expectedString)
    {
        $this->assertEquals($expectedString, (string)$internetMediaType);
    }

    /**
     * @return array
     */
    public function castToStringDataProvider()
    {
        return [
            'media type only' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                ]),
                'expectedString' => 'text/html',
            ],
            'with single parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'css',
                    'parameters' => [
                        'charset' => 'utf-8'
                    ],
                ]),
                'expectedString' => 'text/css; charset=utf-8',
            ],
            'with multiple parameters' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'css',
                    'parameters' => [
                        'charset' => 'utf-8',
                        'foo' => 'bar',
                        'fizz' => 'buzz',
                    ],
                ]),
                'expectedString' => 'text/css; charset=utf-8; foo=bar; fizz=buzz',
            ],
            'with invalid parameters' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'javascript',
                    'parameters' => [
                        'utf-8' => null,
                        'charset' => 'UTF-8',
                    ],
                ]),
                'expectedString' => 'text/javascript; utf-8; charset=UTF-8',
            ],
        ];
    }

    /**
     * @param array $properties
     *
     * @return InternetMediaType
     */
    private function createInternetMediaType($properties)
    {
        $internetMediaType = new InternetMediaType();

        if (isset($properties['type'])) {
            $internetMediaType->setType($properties['type']);
        }

        if (isset($properties['subType'])) {
            $internetMediaType->setSubType($properties['subType']);
        }

        if (isset($properties['parameters'])) {
            foreach ($properties['parameters'] as $attribute => $value) {
                $internetMediaType->addParameter(new Parameter($attribute, $value));
            }
        }

        return $internetMediaType;
    }
}
