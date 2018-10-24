<?php

namespace Willicab\Transmission;

class Client
{
    private $host = '';
    private $port = 9091;
    private $user;
    private $password;
    private $auth;
    private $sessionId;

    public function __construct($arguments = [])
    {
        $this->host = isset($arguments['host']) ? $arguments['host'] : 'http://localhost';
        $this->port = isset($arguments['port']) ? $arguments['port'] : 9091 ;
        if (isset($arguments['user'])) $this->user = $arguments['user'];
        if (isset($arguments['password'])) $this->password = $arguments['password'];
    }

    /**
    * Start one or more torrents, if array is null all torrents will be started
    * 
    *
    * @param int|array|string ids
    *   an integer referring to a torrent id, 
    *   an array of torrent id numbers, sha1 hash strings, or both or
    *   an string, "recently-active", for recently-active torrents
    * @returns none
    */
    public function torrentStart($ids = null)
    {
        $arguments = ['method' => 'torrent-start'];
        if($ids) $arguments['ids'] = $ids;
        return $this->request($arguments);
    }

    /**
    * Start one or more torrents inmediatly, if array is null all torrents will
    * be started inmediatly
    *
    * @param int|array|string ids
    *   an integer referring to a torrent id, 
    *   an array of torrent id numbers, sha1 hash strings, or both or
    *   an string, "recently-active", for recently-active torrents
    * @returns none
    */
    public function torrentStartNow($ids = null)
    {
        $arguments = ['method' => 'torrent-start-now'];
        if($ids) $arguments['ids'] = $ids;
        return $this->request($arguments);
    }

    /**
    * Stop one or more torrentsif array is null all torrents will be stoped
    *
    * @param int|array|string ids
    *   an integer referring to a torrent id, 
    *   an array of torrent id numbers, sha1 hash strings, or both or
    *   an string, "recently-active", for recently-active torrents
    * @returns none
    */
    public function torrentStop($ids = null)
    {
        $arguments = ['method' => 'torrent-stop'];
        if($ids) $arguments['ids'] = $ids;
        return $this->request($arguments);
    }

    /**
    * Verify one or more torrents, if array is null all torrents will be
    * verified
    *
    * @param int|array|string ids
    *   an integer referring to a torrent id, 
    *   an array of torrent id numbers, sha1 hash strings, or both or
    *   an string, "recently-active", for recently-active torrents
    * @returns none
    */
    public function torrentVerify($ids = null)
    {
        $arguments = ['method' => 'torrent-verify'];
        if($ids) $arguments['ids'] = $ids;
        return $this->request($arguments);
    }

    /**
    * Ask tracker for more peers, if array is null all torrents will be
    * reannounced
    *
    * @param int|array|string ids
    *   an integer referring to a torrent id, 
    *   an array of torrent id numbers, sha1 hash strings, or both or
    *   an string, "recently-active", for recently-active torrents
    * @returns none
    */
    public function torrentReannounce($ids = null)
    {
        $arguments = ['method' => 'torrent-reannounce'];
        if($ids) $arguments['ids'] = $ids;
        return $this->request($arguments);
    }

    /**
    * Get the 'X-Transmission-Session-Id' code
    *
    * @returns an array with the session arguments
    */
    public function connect()
    {
        if($this->user && $this->password)
            $this->auth = 'Authorization: Basic ' . base64_encode($this->user . ':' . $this->password);
        return $this->sessionGet();
    }

    /**
    * Get the session arguments
    *
    * @returns an array with the session arguments
    */
    public function sessionGet()
    {
        $arguments = [
            'arguments' => [],
            'method' => 'session-get'
        ];
        return $this->request($arguments);
    }

    /**
    * Send a request to the server
    *
    * @param array arguments an array with the method and arguments
    * @returns an array with the response
    */
    private function request($arguments)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->host.':'.$this->port.'/transmission/rpc',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($arguments),
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
                return json_decode($response);
            case '409':
                preg_match_all('/X-Transmission-Session-Id: ([^<]*)/m', $response, $m, PREG_SET_ORDER, 0);
                $this->sessionId = $m[0][1];
                return $this->request($arguments);
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
