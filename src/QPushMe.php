<?php

namespace Feliiz\QPushMe;


use Feliiz\QPushMe\Exceptions\AuthException;
use Feliiz\QPushMe\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;

/**
 * Class QPushMe
 * @package Feliiz\QPushMe
 */
class QPushMe
{
    /**
     * @var string
     */
    private $baseUrl = 'https://qpush.me';
    /**
     * @var string
     */
    private $pushSiteEndpoint = '/pusher/push_site/';
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
    private function send($msg)
    {
        $client = new Client($this->getBaseOption());
        $response = $client->request('POST', $this->getPushSiteEndpoint(), [
            'headers' => $this->getHeader(),
            'form_params' => [
                'name' => $this->getName(),
                'code' => $this->getCode(),
                'msg' => $msg,
                'cache' => 'false',
                'sig' => '',
            ],
        ]);
        $contents = $response->getBody()->getContents();
        return $contents;
    }

    /**
     * @return array
     */
    private function getBaseOption()
    {
        return [
            'base_uri' => $this->getBaseUrl(),
            'timeout' => $this->getTimeout(),
        ];
    }

    /**
     * @return array
     */
    private function getHeader()
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
    private function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    private function getPushSiteEndpoint()
    {
        return $this->pushSiteEndpoint;
    }

    /**
     * @return mixed
     */
    private function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @throws InvalidArgumentException
     */
    private function setName($name)
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
    private function getCode()
    {
        return $this->code;
    }


    /**
     * @param $code
     * @throws InvalidArgumentException
     */
    private function setCode($code)
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
    private function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param $timeout
     * @throws InvalidArgumentException
     */
    private function setTimeout($timeout)
    {
        if (!is_null($timeout) && is_scalar($timeout)) {
            $this->timeout = floatval($timeout);
        } else {
            throw new InvalidArgumentException('invalid timeout');
        }
    }

}