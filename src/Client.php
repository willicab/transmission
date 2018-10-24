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

    public function torrentStart($ids = null)
    {
        $params = ['method' => 'torrent-start'];
        if($ids) $params['ids'] = $ids;
        return $this->request($params);
    }

    public function torrentStartNow($ids = null)
    {
        $params = ['method' => 'torrent-start-now'];
        if($ids) $params['ids'] = $ids;
        return $this->request($params);
    }

    public function torrentStop($ids = null)
    {
        $params = ['method' => 'torrent-stop'];
        if($ids) $params['ids'] = $ids;
        return $this->request($params);
    }

    public function torrentVerify($ids = null)
    {
        $params = ['method' => 'torrent-verify'];
        if($ids) $params['ids'] = $ids;
        return $this->request($params);
    }

    public function torrentReannounce($ids = null)
    {
        $params = ['method' => 'torrent-reannounce'];
        if($ids) $params['ids'] = $ids;
        return $this->request($params);
    }

    public function connect()
    {
        if($this->user && $this->password)
            $this->auth = 'Authorization: Basic ' . base64_encode($this->user . ':' . $this->password);
        print $this->host;
        return $this->sessionGet();
    }

    public function sessionGet()
    {
        $params = [
            'arguments' => [],
            'method' => 'session-get'
        ];
        return $this->request($params);
    }

    private function request($params)
    private function request($params)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->host.':'.$this->port.'/transmission/rpc',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($params),
          CURLOPT_HTTPHEADER => array(
            (isset($this->auth) ? $this->auth : ''),
            "Content-Type: application/json",
            "X-Transmission-Session-Id: " . $this->sessionId
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        switch($httpCode) {
            case '200':
                return $response;
            case '409':
                preg_match_all('/X-Transmission-Session-Id: ([^<]*)/m', $response, $m, PREG_SET_ORDER, 0);
                $this->sessionId = $m[0][1];
                return json_decode($this->request($params));
            case '401':
                return [
                    'arguments' => [],
                    'result' => '401: Unauthorized User'
                ];
            default:
                return [
                    'arguments' => [],
                    'result' => $httpCode . ': Unexpected response received from Transmission'
                ];
        }
    }
}
