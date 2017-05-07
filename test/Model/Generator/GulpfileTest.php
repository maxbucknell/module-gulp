<?php

namespace MaxBucknell\Gulp\Test\Model\Generator;

use MaxBucknell\Gulp\Model\Generator\Gulpfile;

class GulpfileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @const Gulpfile
     */
    private $generator;

    public function setUp()
    {
        $this->generator = new Gulpfile();
    }

    /**
     * @test
     */
    public function it_should_generate_a_gulpfile()
    {
        $actual = $this->generator->generate();
        $expected = <<<JS
const gulp = require('gulp');
const minimist = require('minimist');

const magento = JSON.parse(minimist(
    process.argv.slice(1),
    {
        string: ['magento'],
        default: {
            magento: '{}'
        }
    }
).magento);


JS;
        $this->assertEquals(
            $expected,
            $actual,
            'Empty gulpfile is not correct'
        );
    }

    /**
     * @test
     */
    public function it_should_generate_source_based_tasks()
    {
        $config = [
            'foo' => [
                'subtasks' => [],
                'source' => 'bar.js'
            ]
        ];
        $actual = $this->generator->generate($config);
        $expected = <<<JS
const gulp = require('gulp');
const minimist = require('minimist');

const magento = JSON.parse(minimist(
    process.argv.slice(1),
    {
        string: ['magento'],
        default: {
            magento: '{}'
        }
    }
).magento);

gulp.task('foo', [], function () {
    return require('bar.js')(magento);
});
JS;

        $this->assertEquals(
            $expected,
            $actual,
            'Single task is not generated correctly.'
        );
    }

    /**
     * @test
     */
    public function it_should_generate_two_tasks()
    {
        $config = [
            'foo' => [
                'subtasks' => [],
                'source' => 'bar.js'
            ],
            'baz' => [
                'subtasks' => [],
                'source' => 'qux.js'
            ]
        ];
        $actual = $this->generator->generate($config);
        $expected = <<<JS
const gulp = require('gulp');
const minimist = require('minimist');

const magento = JSON.parse(minimist(
    process.argv.slice(1),
    {
        string: ['magento'],
        default: {
            magento: '{}'
        }
    }
).magento);

gulp.task('foo', [], function () {
    return require('bar.js')(magento);
});

gulp.task('baz', [], function () {
    return require('qux.js')(magento);
});
JS;

        $this->assertEquals(
            $expected,
            $actual,
            'Multiple tasks are not generated correctly.'
        );
    }

    /**
     * @test
     */
    public function it_should_capture_subtasks()
    {
        $config = [
            'foo' => [
                'subtasks' => ['bar', 'baz'],
                'source' => 'qux.js'
            ]
        ];
        $actual = $this->generator->generate($config);
        $expected = <<<JS
const gulp = require('gulp');
const minimist = require('minimist');

const magento = JSON.parse(minimist(
    process.argv.slice(1),
    {
        string: ['magento'],
        default: {
            magento: '{}'
        }
    }
).magento);

gulp.task('foo', ['bar', 'baz'], function () {
    return require('qux.js')(magento);
});
JS;

        $this->assertEquals(
            $expected,
            $actual,
            'Subtasks are not generated correctly.'
        );
    }

    /**
     * @test
     */
    public function it_should_handle_empty_source()
    {
        $config = [
            'foo' => [
                'subtasks' => [],
                'source' => null
            ]
        ];
        $actual = $this->generator->generate($config);
        $expected = <<<JS
const gulp = require('gulp');
const minimist = require('minimist');

const magento = JSON.parse(minimist(
    process.argv.slice(1),
    {
        string: ['magento'],
        default: {
            magento: '{}'
        }
    }
).magento);

gulp.task('foo', [], function () {
    return true;
});
JS;

        $this->assertEquals(
            $expected,
            $actual,
            'Empty source is not handled correctly.'
        );
    }
}
