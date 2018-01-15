<?php

namespace MaxBucknell\Prefab\Test\Unit\Model\Config;

use MaxBucknell\Prefab\Model\Config\Converter;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Converter
     */
    private $converter;

    public function setUp()
    {
        $this->converter = new Converter();
    }

    public function getDOMDocument($text)
    {
        $document = new \DOMDocument();
        $document->loadXml($text);

        return $document;
    }

    /**
     * @test
     */
    public function it_handles_empty_document()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>

</prefab>
XML
        );

        $expected = [
            'tasks' => [],
            'dependencies' => []
        ];
        $actual = $this->converter->convert($source);

        $this->assertEquals(
            $expected,
            $actual,
            'Config array not initialised correctly.'
        );
    }

    /**
     * @test
     */
    public function it_sets_source_as_null()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>
    <task name="foo">
    </task>
</prefab>
XML
        );

        $actual = $this->converter->convert($source);

        $this->assertNull(
            $actual['tasks']['foo']['source'],
            'source should be null if not set in configuration'
        );
    }

    /**
     * @test
     */
    public function it_sets_subtasks_as_empty()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>
    <task name="foo">
    </task>
</prefab>
XML
        );

        $actual = $this->converter->convert($source);

        $this->assertEmpty(
            $actual['tasks']['foo']['subtasks'],
            'subtasks should be empty array if not set in configuration'
        );
    }

    /**
     * @test
     */
    public function it_creates_a_source_based_task()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>
    <task name="foo">
        <source>bar.js</source>
    </task>
</prefab>
XML
        );

        $actual = $this->converter->convert($source);
        $expected = [
            'tasks' => [
                'foo' => [
                    'subtasks' => [],
                    'source' => 'bar.js'
                ]
            ],
            'dependencies' => []
        ];

        $this->assertEquals(
            $expected,
            $actual,
            'Basic task with source not captured correctly.'
        );
    }

    /**
     * @test
     */
    public function it_captures_subtasks()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>
    <task name="foo">
        <subtasks>
            <task name="bar" />
            <task name="baz" />
        </subtasks>

    </task>
</prefab>
XML
        );

        $actual = $this->converter->convert($source);
        $expected = [
            'tasks' => [
                'foo' => [
                    'subtasks' => ['bar', 'baz'],
                    'source' => null
                ]
            ],
            'dependencies' => []
        ];

        $this->assertEquals(
            $expected,
            $actual,
            'Subtasks not being collected correctly.'
        );
    }

    /**
     * @test
     */
    public function it_captures_multiple_tasks()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>
    <task name="foo"></task>
    <task name="bar"></task>
</prefab>
XML
        );

        $actual = $this->converter->convert($source);

        $this->assertArrayHasKey(
            'foo',
            $actual['tasks'],
            'First task not captured correctly'
        );

        $this->assertArrayHasKey(
            'bar',
            $actual['tasks'],
            'Second task not captured correctly'
        );
    }

    /**
     * @test
     */
    public function it_collects_a_dependency()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>
    <task name="foo">
        <dependencies>
            <package name="bar" version="^1.2.3" />
        </dependencies>
    </task>
</prefab>
XML
        );

        $actual = $this->converter->convert($source);
        $expected = [
            'bar' => '^1.2.3'
        ];

        $this->assertEquals(
            $expected,
            $actual['dependencies'],
            'Dependency not captured correctly'
        );
    }

    /**
     * @test
     */
    public function it_collects_multiple_dependencies()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>
    <task name="foo">
        <dependencies>
            <package name="bar" version="^1.2.3" />
            <package name="baz" version="^4.5.6" />
        </dependencies>
    </task>
</prefab>
XML
        );

        $actual = $this->converter->convert($source);
        $expected = [
            'bar' => '^1.2.3',
            'baz' => '^4.5.6'
        ];

        $this->assertEquals(
            $expected,
            $actual['dependencies'],
            'Multiple dependencies not captured correctly'
        );
    }

    /**
     * @test
     */
    public function it_collects_dependencies_across_tasks()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>
    <task name="foo">
        <dependencies>
            <package name="bar" version="^4.5.6" />
        </dependencies>
    </task>
    <task name="baz">
        <dependencies>
            <package name="qux" version="^1.2.3" />
        </dependencies>
    </task>
</prefab>
XML
        );

        $actual = $this->converter->convert($source);
        $expected = [
            'qux' => '^1.2.3',
            'bar' => '^4.5.6'
        ];

        $this->assertEquals(
            $expected,
            $actual['dependencies'],
            'Multiple dependencies across tasks not captured correctly'
        );
    }

    /**
     * @test
     */
    public function it_overwrites_dependencies()
    {
        $source = $this->getDOMDocument(<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<prefab>
    <task name="foo">
        <dependencies>
            <package name="bar" version="^1.2.3" />
        </dependencies>
    </task>
    <task name="baz">
        <dependencies>
            <package name="bar" version="^4.5.6" />
        </dependencies>
    </task>
</prefab>
XML
        );

        $actual = $this->converter->convert($source);
        $expected = [
            'bar' => '^4.5.6'
        ];

        $this->assertEquals(
            $expected,
            $actual['dependencies'],
            'Dependencies are not overwriting correctly.'
        );
    }
}