<?php

namespace Willicab\Transmission;

class Client
{
    private $host = '';
    private $port = 9091;
    private $user;
    private $password;

    public function __construct($params = [])
    {
        $this->host = isset($params['host']) ? $params['host'] : 'http://localhost';
        $this->port = isset($params['port']) ? $params['port'] : 9091 ;
        if (isset($params['user'])) $this->user = $params['user'];
        if (isset($params['password'])) $this->password = $params['password'];
    }

}
