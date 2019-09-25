<?php

namespace Feliz\QPushMe\Tests;


use Feliz\QPushMe\Exceptions\InvalidArgumentException;
use Feliz\QPushMe\QPushMe;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Mockery\Mock;
use Psr\Http\Message\ResponseInterface;

class QPushMeTest extends TestCase
{
    public function testQPushMe()
    {
        $config = ['name'=>'name','code'=>'123456','timeout'=>5];
        $QPushMe = new QPushMe($config);
        $this->assertInstanceOf(QPushMe::class,$QPushMe);
    }

    public function testQPushMeWithInvalidName()
    {
        $config = ['name'=>['123'],'code'=>'123456','timeout'=>5];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid name');
        $QPushMe = new QPushMe($config);
    }

    public function testQPushMeWithInvalidCode()
    {
        $config = ['name'=>'name','code'=>'abc','timeout'=>5];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid code');
        $QPushMe = new QPushMe($config);
    }
    public function testQPushMeWithInvalidTimeout()
    {
        $config = ['name'=>'name','code'=>'123456','timeout'=>[0]];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid timeout');
        $QPushMe = new QPushMe($config);
    }


    public function testText()
    {
        $config = ['name'=>'name','code'=>'123456','timeout'=>5];
        $QPushMe = \Mockery::mock(QPushMe::class.'[send]', [$config])->shouldAllowMockingProtectedMethods();
        $QPushMe->shouldReceive('send')->andReturnValues(['"abc"'])->once();
        $this->assertSame('"abc"', $QPushMe->text('message'));
    }

    public function testTextWithClientException()
    {
        $config = ['name'=>'name','code'=>'123456','timeout'=>5];
        $QPushMe = \Mockery::mock(QPushMe::class.'[send]', [$config])->shouldAllowMockingProtectedMethods();
        $mockResponse = \Mockery::mock(ClientException::class);
        $QPushMe->shouldReceive('send')->andThrows($mockResponse)->once();
        $this->expectException(ClientException::class);
        $QPushMe->text('message');
    }


    public function testTextWithInvalidText()
    {
        $config = ['name'=>'name','code'=>'123456','timeout'=>5];
        $QPushMe =  new QPushMe($config);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid text : isString and len<5000');
        $QPushMe->text(['abc']);
    }
    public function testUrl()
    {
        $config = ['name'=>'name','code'=>'123456','timeout'=>5];
        $QPushMe = \Mockery::mock(QPushMe::class.'[send]', [$config])->shouldAllowMockingProtectedMethods();
        $QPushMe->shouldReceive('send')->andReturnValues(['"abc"'])->once();
        $this->assertSame('"abc"', $QPushMe->url('http://www.github.com','title'));
    }

    public function testUrlWithClientException()
    {
        $config = ['name'=>'name','code'=>'123456','timeout'=>5];
        $QPushMe = \Mockery::mock(QPushMe::class.'[send]', [$config])->shouldAllowMockingProtectedMethods();
        $mockResponse = \Mockery::mock(ClientException::class);
        $QPushMe->shouldReceive('send')->andThrows($mockResponse)->once();
        $this->expectException(ClientException::class);
        $QPushMe->url('http://www.github.com','title');
    }


    public function testUrlWithInvalidUrl()
    {
        $config = ['name'=>'name','code'=>'123456','timeout'=>5];
        $QPushMe =  new QPushMe($config);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid url : isString and len<5000');
        $QPushMe->url(['abc'],'title');
    }

    public function testUrlWithInvalidTitle()
    {
        $config = ['name'=>'name','code'=>'123456','timeout'=>5];
        $QPushMe =  new QPushMe($config);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid title : isString and len<5000');
        $QPushMe->url('http://www.github.com',['abc']);
    }

}