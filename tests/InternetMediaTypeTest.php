<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\Tests\InternetMediaType;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parameter\Parameter;
use webignition\InternetMediaTypeInterface\ParameterInterface;

class InternetMediaTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        ?string $type,
        ?string $subtype,
        array $parameters,
        ?string $expectedType,
        ?string $expectedSubtype,
        array $expectedParameterStrings
    ) {
        $internetMediaType = new InternetMediaType($type, $subtype, $parameters);

        $this->assertEquals($expectedType, $internetMediaType->getType());
        $this->assertEquals($expectedSubtype, $internetMediaType->getSubtype());

        $parameterStrings = [];

        foreach ($internetMediaType->getParameters() as $parameter) {
            $parameterStrings[] = (string)$parameter;
        }

        $this->assertEquals($expectedParameterStrings, $parameterStrings);
    }

    public function createDataProvider(): array
    {
        return [
            'null arguments' => [
                'type' => null,
                'subtype' => null,
                'parameters' => [],
                'expectedType' => null,
                'expectedSubtype' => null,
                'expectedParameterStrings' => [],
            ],
            'empty arguments' => [
                'type' => '',
                'subtype' => '',
                'parameters' => [],
                'expectedType' => null,
                'expectedSubtype' => null,
                'expectedParameterStrings' => [],
            ],
            'type only' => [
                'type' => 'text',
                'subtype' => null,
                'parameters' => [],
                'expectedType' => 'text',
                'expectedSubtype' => null,
                'expectedParameterStrings' => [],
            ],
            'subtype only' => [
                'type' => null,
                'subtype' => 'html',
                'parameters' => [],
                'expectedType' => null,
                'expectedSubtype' => 'html',
                'expectedParameterStrings' => [],
            ],
            'parameters only' => [
                'type' => null,
                'subtype' => null,
                'parameters' => [
                    new Parameter('foo', 'bar')
                ],
                'expectedType' => null,
                'expectedSubtype' => null,
                'expectedParameterStrings' => [
                    'foo=bar',
                ],
            ],
            'non-parameter parameters only' => [
                'type' => null,
                'subtype' => null,
                'parameters' => [
                    'foo',
                    'bar',
                ],
                'expectedType' => null,
                'expectedSubtype' => null,
                'expectedParameterStrings' => [],
            ],
        ];
    }

    /**
     * @dataProvider typeAndCastToStringDataProvider
     */
    public function testTypeAndCastToString(
        InternetMediaType $internetMediaType,
        string $expectedType,
        string $expectedSubtype,
        string $expectedString
    ) {
        $this->assertEquals($expectedString, (string)$internetMediaType);

        $this->assertEquals($expectedType, $internetMediaType->getType());
        $this->assertEquals($expectedSubtype, $internetMediaType->getSubtype());
    }

    public function typeAndCastToStringDataProvider(): array
    {
        return [
            'media type only; text/html' => [
                'internetMediaType' => new InternetMediaType('text', 'html'),
                'expectedType' => 'text',
                'expectedSubtype' => 'html',
                'expectedString' => 'text/html',
            ],
            'media type only; image/png' => [
                'internetMediaType' => new InternetMediaType('image', 'png'),
                'expectedType' => 'image',
                'expectedSubtype' => 'png',
                'expectedString' => 'image/png',
            ],
            'single parameter' => [
                'internetMediaType' => new InternetMediaType('text', 'css', [
                    new Parameter('charset', 'utf-8'),
                ]),
                'expectedType' => 'text',
                'expectedSubtype' => 'css',
                'expectedString' => 'text/css; charset=utf-8',
            ],
            'multiple parameters' => [
                'internetMediaType' => new InternetMediaType('text', 'css', [
                    new Parameter('charset', 'utf-8'),
                    new Parameter('foo', 'bar'),
                    new Parameter('fizz', 'buzz'),
                ]),
                'expectedType' => 'text',
                'expectedSubtype' => 'css',
                'expectedString' => 'text/css; charset=utf-8; foo=bar; fizz=buzz',
            ],
            'invalid parameters' => [
                'internetMediaType' => new InternetMediaType('text', 'javascript', [
                    new Parameter('utf-8'),
                    new Parameter('charset', 'UTF-8'),
                ]),
                'expectedType' => 'text',
                'expectedSubtype' => 'javascript',
                'expectedString' => 'text/javascript; utf-8; charset=UTF-8',
            ],
            'empty parameters' => [
                'internetMediaType' => new InternetMediaType('text', 'javascript', [
                    new Parameter(''),
                ]),
                'expectedType' => 'text',
                'expectedSubtype' => 'javascript',
                'expectedString' => 'text/javascript',
            ],
        ];
    }

    /**
     * @dataProvider addParameterDataProvider
     */
    public function testAddParameter(
        InternetMediaType $internetMediaType,
        ParameterInterface $parameterToAdd,
        array $expectedParameters
    ) {
        $internetMediaType->addParameter($parameterToAdd);

        foreach ($internetMediaType->getParameters() as $index => $parameter) {
            $this->assertEquals($expectedParameters[$index], (string)$parameter);
        }
    }

    public function addParameterDataProvider(): array
    {
        return [
            'no existing parameters' => [
                'internetMediaType' => new InternetMediaType('text', 'html'),
                'parameterToAdd' => new Parameter('foo', 'bar'),
                'expectedParameters' => [
                    'foo' => 'foo=bar',
                ],
            ],
            'add same parameter' => [
                'internetMediaType' => new InternetMediaType('text', 'html', [
                    new Parameter('foo', 'bar'),
                ]),
                'parameterToAdd' => new Parameter('foo', 'bar'),
                'expectedParameters' => [
                    'foo' => 'foo=bar',
                ],
            ],
            'add different parameter' => [
                'internetMediaType' => new InternetMediaType('text', 'html', [
                    new Parameter('foo', 'bar'),
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
     */
    public function testHasParameter(
        InternetMediaType $internetMediaType,
        string $attribute,
        bool $expectedHasParameter
    ) {
        $this->assertEquals($expectedHasParameter, $internetMediaType->hasParameter($attribute));
    }

    public function hasParameterDataProvider(): array
    {
        return [
            'no parameters' => [
                'internetMediaType' => new InternetMediaType('text', 'html'),
                'attribute' => 'foo',
                'expectedHasParameter' => false,
            ],
            'not has parameter' => [
                'internetMediaType' => new InternetMediaType('text', 'html', [
                    new Parameter('foo', 'bar'),
                ]),
                'attribute' => 'bar',
                'expectedHasParameter' => false,
            ],
            'has parameter' => [
                'internetMediaType' => new InternetMediaType('text', 'html', [
                    new Parameter('foo', 'bar'),
                ]),
                'attribute' => 'foo',
                'expectedHasParameter' => true,
            ],
        ];
    }

    /**
     * @dataProvider removeParameterDataProvider
     */
    public function testRemoveParameter(
        InternetMediaType $internetMediaType,
        ParameterInterface $parameterToRemove,
        array $expectedParametersAsStrings
    ) {
        $internetMediaType->removeParameter($parameterToRemove);

        $parametersAsStrings = [];
        foreach ($internetMediaType->getParameters() as $parameter) {
            $parametersAsStrings[] = (string)$parameter;
        }

        $this->assertEquals($expectedParametersAsStrings, $parametersAsStrings);
    }

    public function removeParameterDataProvider(): array
    {
        return [
            'no existing parameters' => [
                'internetMediaType' => new InternetMediaType('text', 'html'),
                'parameterToRemove' => new Parameter('foo', 'bar'),
                'expectedParameters' => [],
            ],
            'remove only parameter' => [
                'internetMediaType' => new InternetMediaType('text', 'html', [
                    new Parameter('foo', 'bar'),
                ]),
                'parameterToRemove' => new Parameter('foo', 'bar'),
                'expectedParameters' => [],
            ],
            'remove one parameter' => [
                'internetMediaType' => new InternetMediaType('text', 'html', [
                    new Parameter('foo', 'bar'),
                    new Parameter('key', 'value'),
                ]),
                'parameterToRemove' => new Parameter('foo', 'bar'),
                'expectedParameters' => [
                    'key=value',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getParameterDataProvider
     */
    public function testGetParameter(
        InternetMediaType $internetMediaType,
        string $attribute,
        ?ParameterInterface $expectedParameter
    ) {
        $parameter = $internetMediaType->getParameter($attribute);

        $this->assertEquals((string)$expectedParameter, (string)$parameter);
    }

    public function getParameterDataProvider(): array
    {
        return [
            'no existing parameters' => [
                'internetMediaType' => new InternetMediaType('text', 'html'),
                'attribute' => 'foo',
                'expectedParameter' => null,
            ],
            'get only parameter' => [
                'internetMediaType' => new InternetMediaType('text', 'html', [
                    new Parameter('foo', 'bar'),
                ]),
                'attribute' => 'foo',
                'expectedParameter' => new Parameter('foo', 'bar'),
            ],
            'get one parameter' => [
                'internetMediaType' => new InternetMediaType('text', 'html', [
                    new Parameter('foo', 'bar'),
                    new Parameter('key', 'value'),
                ]),
                'attribute' => 'key',
                'expectedParameter' => new Parameter('key', 'value'),
            ],
        ];
    }

    /**
     * @dataProvider hasTypeHasSubtypeGetSubtypeStringDataProvider
     */
    public function testGetTypeSubtypeString(InternetMediaType $internetMediaType, string $expectedTypeSubtypeString)
    {
        $this->assertEquals($expectedTypeSubtypeString, $internetMediaType->getTypeSubtypeString());
    }

    public function hasTypeHasSubtypeGetSubtypeStringDataProvider(): array
    {
        return [
            'no type, no subtype' => [
                'internetMediaType' => new InternetMediaType(),
                'expectedTypeSubtypeString' => '',
            ],
            'has type, no subtype' => [
                'internetMediaType' => new InternetMediaType('foo'),
                'expectedTypeSubtypeString' => '',
            ],
            'no type, has subtype' => [
                'internetMediaType' => new InternetMediaType(null, 'bar'),
                'expectedTypeSubtypeString' => '',
            ],
            'has type, has subtype' => [
                'internetMediaType' => new InternetMediaType('foo', 'bar'),
                'expectedTypeSubtypeString' => 'foo/bar',
            ],
        ];
    }
}
