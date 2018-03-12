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
     * @dataProvider typeAndCastToStringDataProvider
     *
     * @param InternetMediaType $internetMediaType
     * @param string $expectedType
     * @param string $expectedSubtype
     * @param string $expectedString
     */
    public function testTypeAndCastToString(
        InternetMediaType $internetMediaType,
        $expectedType,
        $expectedSubtype,
        $expectedString
    ) {
        $this->assertEquals($expectedString, (string)$internetMediaType);

        $this->assertEquals($expectedType, $internetMediaType->getType());
        $this->assertEquals($expectedSubtype, $internetMediaType->getSubtype());
    }

    /**
     * @return array
     */
    public function typeAndCastToStringDataProvider()
    {
        return [
            'media type only; text/html' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                ]),
                'expectedType' => 'text',
                'expectedSubtype' => 'html',
                'expectedString' => 'text/html',
            ],
            'media type only; image/png' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'image',
                    'subType' => 'png',
                ]),
                'expectedType' => 'image',
                'expectedSubtype' => 'png',
                'expectedString' => 'image/png',
            ],
            'single parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'css',
                    'parameters' => [
                        'charset' => 'utf-8'
                    ],
                ]),
                'expectedType' => 'text',
                'expectedSubtype' => 'css',
                'expectedString' => 'text/css; charset=utf-8',
            ],
            'multiple parameters' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'css',
                    'parameters' => [
                        'charset' => 'utf-8',
                        'foo' => 'bar',
                        'fizz' => 'buzz',
                    ],
                ]),
                'expectedType' => 'text',
                'expectedSubtype' => 'css',
                'expectedString' => 'text/css; charset=utf-8; foo=bar; fizz=buzz',
            ],
            'invalid parameters' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'javascript',
                    'parameters' => [
                        'utf-8' => null,
                        'charset' => 'UTF-8',
                    ],
                ]),
                'expectedType' => 'text',
                'expectedSubtype' => 'javascript',
                'expectedString' => 'text/javascript; utf-8; charset=UTF-8',
            ],
            'empty parameters' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'javascript',
                    'parameters' => [
                        '' => null,
                    ],
                ]),
                'expectedType' => 'text',
                'expectedSubtype' => 'javascript',
                'expectedString' => 'text/javascript',
            ],
        ];
    }

    /**
     * @dataProvider addParameterDataProvider
     *
     * @param InternetMediaType $internetMediaType
     * @param Parameter $parameterToAdd
     * @param string[] $expectedParameters
     */
    public function testAddParameter(
        InternetMediaType $internetMediaType,
        Parameter $parameterToAdd,
        array $expectedParameters
    ) {
        $internetMediaType->addParameter($parameterToAdd);

        foreach ($internetMediaType->getParameters() as $index => $parameter) {
            $this->assertEquals($expectedParameters[$index], (string)$parameter);
        }
    }

    /**
     * @return array
     */
    public function addParameterDataProvider()
    {
        return [
            'no existing parameters' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                ]),
                'parameterToAdd' => new Parameter('foo', 'bar'),
                'expectedParameters' => [
                    'foo' => 'foo=bar',
                ],
            ],
            'add same parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ]),
                'parameterToAdd' => new Parameter('foo', 'bar'),
                'expectedParameters' => [
                    'foo' => 'foo=bar',
                ],
            ],
            'add different parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ]),
                'parameterToAdd' => new Parameter('key', 'value'),
                'expectedParameters' => [
                    'foo' => 'foo=bar',
                    'key' => 'key=value',
                ],
            ],
        ];
    }

    /**
     * @dataProvider hasParameterDataProvider
     *
     * @param InternetMediaType $internetMediaType
     * @param string $attribute
     * @param bool $expectedHasParameter
     */
    public function testHasParameter(
        InternetMediaType $internetMediaType,
        $attribute,
        $expectedHasParameter
    ) {
        $this->assertEquals($expectedHasParameter, $internetMediaType->hasParameter($attribute));
    }

    /**
     * @return array
     */
    public function hasParameterDataProvider()
    {
        return [
            'no parameters' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                ]),
                'attribute' => 'foo',
                'expectedHasParameter' => false,
            ],
            'not has parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ]),
                'attribute' => 'bar',
                'expectedHasParameter' => false,
            ],
            'has parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ]),
                'attribute' => 'foo',
                'expectedHasParameter' => true,
            ],
        ];
    }

    /**
     * @dataProvider removeParameterDataProvider
     *
     * @param InternetMediaType $internetMediaType
     * @param Parameter $parameterToRemove
     * @param string[] $expectedParameters
     */
    public function testRemoveParameter(
        InternetMediaType $internetMediaType,
        Parameter $parameterToRemove,
        array $expectedParameters
    ) {
        $internetMediaType->removeParameter($parameterToRemove);

        foreach ($internetMediaType->getParameters() as $index => $parameter) {
            $this->assertEquals($expectedParameters[$index], (string)$parameter);
        }
    }

    /**
     * @return array
     */
    public function removeParameterDataProvider()
    {
        return [
            'no existing parameters' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                ]),
                'parameterToRemove' => new Parameter('foo', 'bar'),
                'expectedParameters' => [],
            ],
            'remove only parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ]),
                'parameterToRemove' => new Parameter('foo', 'bar'),
                'expectedParameters' => [],
            ],
            'remove one parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ]),
                'parameterToRemove' => new Parameter('foo', 'bar'),
                'expectedParameters' => [
                    'key' => 'key=value',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getParameterDataProvider
     *
     * @param InternetMediaType $internetMediaType
     * @param string $attribute
     * @param Parameter|null $expectedParameter
     */
    public function testGetParameter(
        InternetMediaType $internetMediaType,
        $attribute,
        $expectedParameter
    ) {
        $parameter = $internetMediaType->getParameter($attribute);

        $this->assertEquals((string)$expectedParameter, (string)$parameter);
    }

    /**
     * @return array
     */
    public function getParameterDataProvider()
    {
        return [
            'no existing parameters' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                ]),
                'attribute' => 'foo',
                'expectedParameter' => null,
            ],
            'get only parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ]),
                'attribute' => 'foo',
                'expectedParameter' => new Parameter('foo', 'bar'),
            ],
            'get one parameter' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'text',
                    'subType' => 'html',
                    'parameters' => [
                        'foo' => 'bar',
                        'key' => 'value',
                    ],
                ]),
                'attribute' => 'key',
                'expectedParameter' => new Parameter('key', 'value'),
            ],
        ];
    }

    /**
     * @dataProvider hasTypeHasSubtypeGetSubtypeStringDataProvider
     *
     * @param InternetMediaType $internetMediaType
     * @param bool $expectedHasType
     * @param bool $expectedHasSubtype
     */
    public function testHasTypeHasSubtypeGetTypeSubtypeString(
        InternetMediaType $internetMediaType,
        $expectedHasType,
        $expectedHasSubtype,
        $expectedTypeSubtypeString
    ) {
        $this->assertEquals($expectedHasType, $internetMediaType->hasType());
        $this->assertEquals($expectedHasSubtype, $internetMediaType->hasSubtype());
        $this->assertEquals($expectedTypeSubtypeString, $internetMediaType->getTypeSubtypeString());
    }

    /**
     * @return array
     */
    public function hasTypeHasSubtypeGetSubtypeStringDataProvider()
    {
        return [
            'no type, no subtype' => [
                'internetMediaType' => new InternetMediaType(),
                'expectedHasType' => false,
                'expectedHasSubtype' => false,
                'expectedTypeSubtypeString' => '',
            ],
            'has type, no subtype' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'foo',
                ]),
                'expectedHasType' => true,
                'expectedHasSubtype' => false,
                'expectedTypeSubtypeString' => '',
            ],
            'no type, has subtype' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'subType' => 'bar',
                ]),
                'expectedHasType' => false,
                'expectedHasSubtype' => true,
                'expectedTypeSubtypeString' => '',
            ],
            'has type, has subtype' => [
                'internetMediaType' => $this->createInternetMediaType([
                    'type' => 'foo',
                    'subType' => 'bar',
                ]),
                'expectedHasType' => true,
                'expectedHasSubtype' => true,
                'expectedTypeSubtypeString' => 'foo/bar',
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
