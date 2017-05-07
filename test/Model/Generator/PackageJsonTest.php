<?php

namespace Model\Generator;


use MaxBucknell\Gulp\Model\Generator\PackageJson;

class PackageJsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PackageJson
     */
    private $generator;

    public function setUp()
    {
        $this->generator = new PackageJson();
    }

    /**
     * @test
     */
    public function it_should_generate_a_package_manifest()
    {
        $actual = $this->generator->generate();
        $expected = <<<JSON
{
    "name": "@maxbucknell/magento-gulp",
    "version": "1.0.0",
    "description": "",
    "main": "gulpfile.js",
    "author": "",
    "license": "MIT",
    "dependencies": {}
}
JSON;

        $this->assertEquals(
            $expected,
            $actual,
            'Empty package.json is not correct'
        );
    }

    /**
     * @test
     */
    public function it_should_generate_a_dependency()
    {
        $config = [
            'gulp' => '^3.9.1'
        ];

        $actual = $this->generator->generate($config);
        $expected = <<<JSON
{
    "name": "@maxbucknell/magento-gulp",
    "version": "1.0.0",
    "description": "",
    "main": "gulpfile.js",
    "author": "",
    "license": "MIT",
    "dependencies": {
        "gulp": "^3.9.1"
    }
}
JSON;

        $this->assertEquals(
            $expected,
            $actual,
            'Single dependency package.json is not correct'
        );
    }

    /**
     * @test
     */
    public function it_should_generate_multiple_dependencies()
    {
        $config = [
            'gulp' => '^3.9.1',
            'glob' => '^7.1.1'
        ];

        $actual = $this->generator->generate($config);
        $expected = <<<JSON
{
    "name": "@maxbucknell/magento-gulp",
    "version": "1.0.0",
    "description": "",
    "main": "gulpfile.js",
    "author": "",
    "license": "MIT",
    "dependencies": {
        "gulp": "^3.9.1",
        "glob": "^7.1.1"
    }
}
JSON;

        $this->assertEquals(
            $expected,
            $actual,
            'Multiple dependency package.json is not correct'
        );
    }
}