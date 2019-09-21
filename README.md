<h1 align="center">QPushMe</h1>

<p align="center">:calling: php-sdk for qpush.me,quick & easy to push notification to iPhone</p>

## install
```shell
$ composer require "feliz/qpushme"
```

## use
```php
$config = ['name'=>'your name','code'=>'your code','timeout'=>5];
$QPushMe = new \Feliiz\QPushMe\QPushMe($config);

//send text to iPhone
$QPushMe->text('new message');

//send url to iPhone
$QPushMe->url('https://www.google.com','google');
```

## License

MIT
