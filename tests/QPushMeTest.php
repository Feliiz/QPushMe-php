<?php

namespace Feliz\QPushMe\Tests;


use Feliz\QPushMe\Exceptions\InvalidArgumentException;
use Feliz\QPushMe\QPushMe;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class QPushMeTest
 * @package Feliz\QPushMe\Tests
 */
class QPushMeTest extends TestCase
{
    public function testQPushMe()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = new QPushMe($config);
        $headers = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Referer' => 'https://qpush.me/en/push/',
            'Origin' => 'https://qpush.me',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
        ];
        $baseUrl = 'https://qpush.me';
        $pushSiteEndPoint = '/pusher/push_site/';

        $this->assertInstanceOf(QPushMe::class, $QPushMe);
        $this->assertInstanceOf(Client::class, $QPushMe->getHttpClient());
        $this->assertSame($headers, $QPushMe->getHeaders());
        $this->assertSame('name', $QPushMe->getName());
        $this->assertSame(123456, $QPushMe->getCode());
        $this->assertSame(5, $QPushMe->getTimeout());
        $this->assertSame($baseUrl, $QPushMe->getBaseUrl());
        $this->assertSame($pushSiteEndPoint, $QPushMe->getPushSiteEndpoint());


    }

    public function testQPushMeWithInvalidName()
    {
        $config = ['name' => ['123'], 'code' => '123456', 'timeout' => 5];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid name');
        $QPushMe = new QPushMe($config);
    }

    public function testQPushMeWithInvalidCode()
    {
        $config = ['name' => 'name', 'code' => 'abc', 'timeout' => 5];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid code');
        $QPushMe = new QPushMe($config);
    }

    public function testQPushMeWithInvalidTimeout()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => [0]];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid timeout');
        $QPushMe = new QPushMe($config);
    }


    public function testText()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = \Mockery::mock(QPushMe::class . '[send]', [$config])->shouldAllowMockingProtectedMethods();
        $QPushMe->shouldReceive('send')->andReturnValues(['"abc"'])->once();
        $this->assertSame('"abc"', $QPushMe->text('message'));
    }

    public function testTextWithClientException()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = \Mockery::mock(QPushMe::class . '[send]', [$config])->shouldAllowMockingProtectedMethods();
        $mockResponse = \Mockery::mock(ClientException::class);
        $QPushMe->shouldReceive('send')->andThrows($mockResponse)->once();
        $this->expectException(ClientException::class);
        $QPushMe->text('message');
    }


    public function testTextWithInvalidText()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = new QPushMe($config);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid text : isString and len<5000');
        $QPushMe->text(['abc']);
    }

    public function testUrl()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = \Mockery::mock(QPushMe::class . '[send]', [$config])->shouldAllowMockingProtectedMethods();
        $QPushMe->shouldReceive('send')->andReturnValues(['"abc"'])->once();
        $this->assertSame('"abc"', $QPushMe->url('http://www.github.com', 'title'));
    }

    public function testUrlWithClientException()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = \Mockery::mock(QPushMe::class . '[send]', [$config])->shouldAllowMockingProtectedMethods();
        $mockResponse = \Mockery::mock(ClientException::class);
        $QPushMe->shouldReceive('send')->andThrows($mockResponse)->once();
        $this->expectException(ClientException::class);
        $QPushMe->url('http://www.github.com', 'title');
    }


    public function testUrlWithInvalidUrl()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = new QPushMe($config);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid url : isString and len<5000');
        $QPushMe->url(['abc'], 'title');
    }

    public function testUrlWithInvalidTitle()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = new QPushMe($config);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid title : isString and len<5000');
        $QPushMe->url('http://www.github.com', ['abc']);
    }

    public function testSend()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = \Mockery::mock(QPushMe::class, [$config])->shouldAllowMockingProtectedMethods();

        $mockHeaders = ['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36'];

        $QPushMe->expects()->getHeaders()->andReturn($mockHeaders)->once();
        $QPushMe->expects()->getName()->andReturn('mock-name')->once();
        $QPushMe->expects()->getCode()->andReturn('mock-code')->once();
        $QPushMe->expects()->getPushSiteEndpoint()->andReturn('mock-endpoint')->once();


        $mockParams = [
            'headers' => $mockHeaders,
            'form_params' => [
                'name' => 'mock-name',
                'code' => 'mock-code',
                'msg' => 'mock-message',
                'cache' => 'false',
                'sig' => '',
            ],
        ];
        $QPushMe->expects()->httpRequest('POST', 'mock-endpoint', $mockParams)->andReturn('response-contents');
        $QPushMe->allows()->send(anyArgs())->passthru();
        $this->assertSame('response-contents', $QPushMe->send('mock-message'));

    }

    public function testHttpRequest()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = \Mockery::mock(QPushMe::class, [$config])->shouldAllowMockingProtectedMethods();
        $mockHttpClient = \Mockery::mock(Client::class);
        $mockResponse = \Mockery::mock(ResponseInterface::class);
        $mockBaseOption = ['base_uri' => 'https://mock-base-url', 'timeout' => 5];

        $QPushMe->expects()->getHttpClient($mockBaseOption)->andReturn($mockHttpClient)->once();
        $QPushMe->expects()->getBaseOption()->andReturn($mockBaseOption)->once();

        $QPushMe->expects()->responseContent($mockResponse)->andReturn('response-contents');
        $mockParams = ['mock-key' => 'mock-value'];
        $mockHttpClient->allows()->request('POST', 'mock-endpoint', $mockParams)->andReturn($mockResponse)->once();
        $QPushMe->allows()->httpRequest(anyArgs())->passthru();
        $this->assertSame('response-contents', $QPushMe->httpRequest('POST', 'mock-endpoint', $mockParams));

    }

    public function testGetBaseOption()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = \Mockery::mock(QPushMe::class, [$config])->shouldAllowMockingProtectedMethods();
        $QPushMe->expects()->getBaseUrl()->andReturn('mock-base-url')->once();
        $QPushMe->expects()->getTimeout()->andReturn('mock-base-url')->once();
        $QPushMe->allows()->getBaseOption()->passthru();;

        $this->assertSame([
            'base_uri' => 'mock-base-url',
            'timeout' => 'mock-base-url',
        ], $QPushMe->getBaseOption());
    }

    public function testResponseContent()
    {
        $config = ['name' => 'name', 'code' => '123456', 'timeout' => 5];
        $QPushMe = \Mockery::mock(QPushMe::class, [$config])->shouldAllowMockingProtectedMethods();
        $QPushMe->allows()->responseContent(anyArgs())->passthru();
        $response = new Response(200, ['content-type' => 'application/json'], 'mock-response-contents');
        $this->assertSame('mock-response-contents', $QPushMe->responseContent($response));
    }

}