<?php

namespace Feliz\QPushMe;


use Feliz\QPushMe\Exceptions\AuthException;
use Feliz\QPushMe\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Class QPushMe
 * @package Feliz\QPushMe
 */
class QPushMe
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $code;
    /**
     * @var int
     */
    private $timeout = 5;

    /**
     * QPushMe constructor.
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function __construct($config = [])
    {
        $this->setName((isset($config['name'])) ? $config['name'] : null);
        $this->setCode((isset($config['code'])) ? $config['code'] : null);
        $this->setTimeout((isset($config['timeout'])) ? $config['timeout'] : $this->getTimeout());
    }

    /**
     * @param string $text
     * @return string
     * @throws AuthException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function text($text = '')
    {
        if (!(is_string($text) && mb_strlen($text) < 5000)) {
            throw  new InvalidArgumentException('invalid text : isString and len<5000');
        }
        $msg['text'] = $text;
        return $this->send($msg);
    }

    /**
     * @param string $url
     * @param string $title
     * @return string
     * @throws AuthException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function url($url = '', $title = '')
    {
        if (!(is_string($url) && mb_strlen($url) < 5000)) {
            throw  new InvalidArgumentException('invalid url : isString and len<5000');
        }
        if (!(is_string($title) && mb_strlen($title) < 5000)) {
            throw  new InvalidArgumentException('invalid title : isString and len<5000');
        }
        $msg['type'] = 'url';
        $msg['text'] = $url;
        $msg['extra']['title'] = $title;
        return $this->send($msg);

    }


    /**
     * @param $msg
     * @return string
     * @throws AuthException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function send($msg)
    {
        $params = [
            'headers' => $this->getHeaders(),
            'form_params' => [
                'name' => $this->getName(),
                'code' => $this->getCode(),
                'msg' => $msg,
                'cache' => 'false',
                'sig' => '',
            ],
        ];
        return $this->httpRequest('POST', $this->getPushSiteEndpoint(), $params);
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $params
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function httpRequest($method, $endpoint, $params = [])
    {
        return $this->responseContent($this->getHttpClient($this->getBaseOption())->request($method, $endpoint, $params));
    }

    /**
     * @param array $options
     * @return Client
     */
    public function getHttpClient($options = [])
    {
        return new Client($options);
    }

    /**
     * @param ResponseInterface $response
     * @return string
     */
    protected function responseContent(ResponseInterface $response)
    {
        return $response->getBody()->getContents();
    }

    /**
     * @return array
     */
    protected function getBaseOption()
    {
        return [
            'base_uri' => $this->getBaseUrl(),
            'timeout' => $this->getTimeout(),
        ];
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Referer' => 'https://qpush.me/en/push/',
            'Origin' => 'https://qpush.me',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
        ];
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return 'https://qpush.me';
    }

    /**
     * @return string
     */
    public function getPushSiteEndpoint()
    {
        return '/pusher/push_site/';
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @throws InvalidArgumentException
     */
    protected function setName($name)
    {
        if (!is_null($name) && is_scalar($name) && trim($name) != false) {
            $this->name = strval($name);
        } else {
            throw new InvalidArgumentException('invalid name');
        }

    }


    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * @param $code
     * @throws InvalidArgumentException
     */
    protected function setCode($code)
    {
        if (!is_null($code) && is_scalar($code) && intval($code) > 0) {
            $this->code = intval($code);
        } else {
            throw new InvalidArgumentException('invalid code');
        }
    }


    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param $timeout
     * @throws InvalidArgumentException
     */
    protected function setTimeout($timeout)
    {
        if (!is_null($timeout) && is_scalar($timeout)) {
            $this->timeout = intval($timeout);
        } else {
            throw new InvalidArgumentException('invalid timeout');
        }
    }

}