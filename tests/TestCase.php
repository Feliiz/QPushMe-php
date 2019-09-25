<?php


namespace Feliz\QPushMe\Tests;


use Mockery;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        Mockery::globalHelpers();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
