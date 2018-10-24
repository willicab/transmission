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
        if($ids) $arguments['arguments'] = ['ids' => $ids];
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
        if($ids) $arguments['arguments'] = ['ids' => $ids];
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
        if($ids) $arguments['arguments'] = ['ids' => $ids];
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
        if($ids) $arguments['arguments'] = ['ids' => $ids];
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
        if($ids) $arguments['arguments'] = ['ids' => $ids];
        return $this->request($arguments);
    }

    /**
    * Get information of torrents
    *
    * @param int|array|string ids
    *   an integer referring to a torrent id,
    *   an array of torrent id numbers, sha1 hash strings, or both or
    *   an string, "recently-active", for recently-active torrents
    * @param array fields a list of fields, these can be activityDate,
    * addedDate, bandwidthPriority, comment, corruptEver, creator, dateCreated,
    * desiredAvailable, doneDate, downloadDir, downloadedEver, downloadLimit,
    * downloadLimited, error, errorString, eta, etaIdle, files, fileStats,
    * hashString, haveUnchecked, haveValid, honorsSessionLimits, id, isFinished,
    * isPrivate, isStalled, leftUntilDone, magnetLink, manualAnnounceTime,
    * maxConnectedPeers, metadataPercentComplete, name, peer-limit, peers,
    * peersConnected, peersFrom, peersGettingFromUs, peersSendingToUs,
    * percentDone, pieces, pieceCount, pieceSize, priorities, queuePosition,
    * rateDownload, rateUpload, recheckProgress, secondsDownloading,
    * secondsSeeding, seedIdleLimit, seedIdleMode, seedRatioLimit,
    * seedRatioMode, sizeWhenDone, startDate, status, trackers, trackerStats,
    * totalSize, torrentFile, uploadedEver, uploadLimit, uploadLimited,
    * uploadRatio, wanted, webseeds, webseedsSendingToUs
    * @returns an array with the information of torrents
    */
    public function torrentGet($ids = null, $fields = null)
    {
        $arguments = ['method' => 'torrent-get'];
        $arguments['arguments'] = $fields ? ['fields' => $fields] : ['fields' => ['hashString', 'id', 'name']];
        if($ids) $arguments['arguments'] = array_merge($arguments['arguments'], ['ids' => $ids]);
        print_r($arguments);
        return $this->request($arguments);
    }


    /**
    * Add a torrent from a filename
    *
    * Arguments:
    *  key                  | value type & description
    *  ---------------------+-------------------------------------------------
    *  "download-dir"       | string      path to download the torrent to
    *  "paused"             | boolean     if true, don't start the torrent
    *  "peer-limit"         | number      maximum number of peers
    *  "bandwidthPriority"  | number      torrent's bandwidth tr_priority_t
    *  "files-wanted"       | array       indices of file(s) to download
    *  "files-unwanted"     | array       indices of file(s) to not download
    *  "priority-high"      | array       indices of high-priority file(s)
    *  "priority-low"       | array       indices of low-priority file(s)
    *  "priority-normal"    | array       indices of normal-priority file(s)
    *
    * @param string filename filename or URL of the .torrent file
    * @returns On success, a "torrent-added" object with the fields for id, name
    * and hashString. On failure due to a duplicate torrent existing,
    * a "torrent-duplicate" object in the same form.
    */
    public function addFilename($filename, $fields = null)
    {
        $arguments = ['method' => 'torrent-add'];
        $arguments['arguments'] = ['filename' => $filename];
        if($fields) $arguments['arguments'] = array_merge($arguments['arguments'], $fields);
        return $this->request($arguments);
    }

    /**
    * Add a torrent from a metainfo
    *
    * Fields:
    *  key                  | value type & description
    *  ---------------------+-------------------------------------------------
    *  "download-dir"       | string      path to download the torrent to
    *  "paused"             | boolean     if true, don't start the torrent
    *  "peer-limit"         | number      maximum number of peers
    *  "bandwidthPriority"  | number      torrent's bandwidth tr_priority_t
    *  "files-wanted"       | array       indices of file(s) to download
    *  "files-unwanted"     | array       indices of file(s) to not download
    *  "priority-high"      | array       indices of high-priority file(s)
    *  "priority-low"       | array       indices of low-priority file(s)
    *  "priority-normal"    | array       indices of normal-priority file(s)
    *
    * @param string metainfo base64-encoded .torrent content
    * @param array fields a array with the fields
    * @returns On success, a "torrent-added" object with the fields for id, name
    * and hashString. On failure due to a duplicate torrent existing,
    * a "torrent-duplicate" object in the same form.
    */
    public function addMetainfo($metainfo, $fields = null)
    {
        $arguments = ['method' => 'torrent-add'];
        $arguments['arguments'] = ['metainfo' => $metainfo];
        if($fields) $arguments['arguments'] = array_merge($arguments['arguments'], $fields);
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
