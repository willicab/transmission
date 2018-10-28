<?php
/**
 * Another PHP implementation of Transmission RPC Spec.
 *
 * @category  Library
 * @package   Transmission
 * @author    William Cabrera <william@linux.es>
 * @copyright 2018 William Cabrera (@willicab)
 * @license   https://opensource.org/licenses/MIT    MIT
 * @link      https://github.com/willicab/transmission
 */
namespace Willicab\Transmission;

class Client
{
    private $host = 'http://localhost';
    private $port = 9091;
    private $user;
    private $password;
    private $auth;
    private $sessionId;

    public function __construct(array $arguments = [])
    {
        if (isset($arguments['host'])) {
            $this->host = $arguments['host'];
        }
        if (isset($arguments['port'])) {
            $this->port = $arguments['port'];
        }
        if (isset($arguments['user'])) {
            $this->user = $arguments['user'];
        }
        if (isset($arguments['password'])) {
            $this->password = $arguments['password'];
        }
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
    public function torrentStart($ids = null) : object
    {
        $arguments = ['method' => 'torrent-start'];
        if ($ids) {
            $arguments['arguments'] = ['ids' => $ids];
        }
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
    public function torrentStartNow($ids = null) : object
    {
        $arguments = ['method' => 'torrent-start-now'];
        if ($ids) {
            $arguments['arguments'] = ['ids' => $ids];
        }
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
    public function torrentStop($ids = null) : object
    {
        $arguments = ['method' => 'torrent-stop'];
        if ($ids) {
            $arguments['arguments'] = ['ids' => $ids];
        }
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
    public function torrentVerify($ids = null) : object
    {
        $arguments = ['method' => 'torrent-verify'];
        if ($ids) {
            $arguments['arguments'] = ['ids' => $ids];
        }
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
    public function torrentReannounce($ids = null) : object
    {
        $arguments = ['method' => 'torrent-reannounce'];
        if ($ids) {
            $arguments['arguments'] = ['ids' => $ids];
        }
        return $this->request($arguments);
    }

    /**
    * Ask tracker for more peers, if array is null all torrents will be
    * reannounced
    *
    * string                | value type & description
    * ----------------------+-------------------------------------------------
    * "bandwidthPriority"   | number   this torrent's bandwidth tr_priority_t
    * "downloadLimit"       | number   maximum download speed (KBps)
    * "downloadLimited"     | boolean  true if "downloadLimit" is honored
    * "files-wanted"        | array    indices of file(s) to download
    * "files-unwanted"      | array    indices of file(s) to not download
    * "honorsSessionLimits" | boolean  true if session upload limits are honored
    * "location"            | string   new location of the torrent's content
    * "peer-limit"          | number   maximum number of peers
    * "priority-high"       | array    indices of high-priority file(s)
    * "priority-low"        | array    indices of low-priority file(s)
    * "priority-normal"     | array    indices of normal-priority file(s)
    * "queuePosition"       | number   position of this torrent in its queue
    * "seedIdleLimit"       | number   number of minutes of seeding inactivity
    * "seedIdleMode"        | number   which seeding inactivity to use.
    * "seedRatioLimit"      | double   torrent-level seeding ratio
    * "seedRatioMode"       | number   which ratio to use.  See tr_ratiolimit
    * "trackerAdd"          | array    strings of announce URLs to add
    * "trackerRemove"       | array    ids of trackers to remove
    * "trackerReplace"      | array    pairs of <trackerId/new announce URLs>
    * "uploadLimit"         | number   maximum upload speed (KBps)
    * "uploadLimited"       | boolean  true if "uploadLimit" is honored
    *
    * Just as an "null" ids value is shorthand for "all ids", using an empty
    * array for files-wanted, files-unwanted, priority-high, priority-low,
    * or priority-normal is shorthand for saying "all files".
    *
    * @param int|array|string ids
    *   an integer referring to a torrent id,
    *   an array of torrent id numbers, sha1 hash strings, or both or
    *   an string, "recently-active", for recently-active torrents
    * @returns none
    */
    public function torrentSet($ids = null, array $fields = []) : object
    {
        $arguments = ['method' => 'torrent-set'];
        if ($ids) {
            $arguments['arguments'] = ['ids' => $ids];
        }
        $arguments['arguments'] = isset($arguments['arguments']) ?
            array_merge($arguments['arguments'], $fields) : $fields;
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
    public function torrentGet($ids = null, array $fields = null) : object
    {
        $arguments = ['method' => 'torrent-get'];
        $arguments['arguments'] = $fields ? ['fields' => $fields] :
                                    ['fields' => ['hashString', 'id', 'name']];
        if ($ids) {
            $arguments['arguments'] =
                        array_merge($arguments['arguments'], ['ids' => $ids]);
        }
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
    public function addFilename(string $filename, array $fields = null) : object
    {
        $arguments = ['method' => 'torrent-add'];
        $arguments['arguments'] = ['filename' => $filename];
        if ($fields) {
            $arguments['arguments'] =
                                array_merge($arguments['arguments'], $fields);
        }
        return $this->request($arguments);
    }

    /**
    * Add a torrent from a metainfo
    *
    * Fields:
    *  key                  | value type & description
    *  ---------------------+---------------------------------------------------
    *  "cookies"            | string  pointer to a string of one or more cookies
    *  "download-dir"       | string  path to download the torrent to
    *  "filename"           | string  filename or URL of the .torrent file
    *  "metainfo"           | string  base64-encoded .torrent content
    *  "paused"             | boolean if true, don't start the torrent
    *  "peer-limit"         | number  maximum number of peers
    *  "bandwidthPriority"  | number  torrent's bandwidth tr_priority_t
    *  "files-wanted"       | array   indices of file(s) to download
    *  "files-unwanted"     | array   indices of file(s) to not download
    *  "priority-high"      | array   indices of high-priority file(s)
    *  "priority-low"       | array   indices of low-priority file(s)
    *  "priority-normal"    | array   indices of normal-priority file(s)
    *
    * @param string metainfo base64-encoded .torrent content
    * @param array fields a array with the fields
    * @returns On success, a "torrent-added" object with the fields for id, name
    * and hashString. On failure due to a duplicate torrent existing,
    * a "torrent-duplicate" object in the same form.
    */
    public function addMetainfo(string $metainfo, array $fields = null) : object
    {
        $arguments = ['method' => 'torrent-add'];
        $arguments['arguments'] = ['metainfo' => $metainfo];
        if ($fields) {
            $arguments['arguments'] =
                                array_merge($arguments['arguments'], $fields);
        }
        return $this->request($arguments);
    }

    /**
    * Remove torrent
    *
    * @param int|array|string ids
    *   an integer referring to a torrent id,
    *   an array of torrent id numbers, sha1 hash strings, or both or
    *   an string, "recently-active", for recently-active torrents
    * @param boolean deleteLocalData delete local data. (default: false)
    * @returns none
    */
    public function torrentRemove($ids = null, bool $deleteLocalData = false)
    : object
    {
        $arguments = ['method' => 'torrent-remove'];
        $arguments['arguments'] = ['delete-local-data' => $deleteLocalData];
        if ($ids) {
            $arguments['arguments'] =
                        array_merge($arguments['arguments'], ['ids' => $ids]);
        }
        return $this->request($arguments);
    }

    /**
    * Move a torrent
    *
    * @param int|array|string ids
    *   an integer referring to a torrent id,
    *   an array of torrent id numbers, sha1 hash strings, or both or
    *   an string, "recently-active", for recently-active torrents
    * @param string location the new torrent location
    * @param boolean move if true, move from previous location, otherwise,
    * search "location" for files (default: false)
    * @returns none
    */
    public function torrentSetLocation(
        $ids = null,
        string $location = '',
        bool $move = false
    ) : object {
        $arguments = ['method' => 'torrent-set-location'];
        $arguments['arguments'] = ['location' => $location, 'move' => $move];
        if ($ids) {
            $arguments['arguments'] =
                        array_merge($arguments['arguments'], ['ids' => $ids]);
        }
        return $this->request($arguments);
    }

    /**
    * Rename a torrent
    *
    * @param int|array ids
    *   an integer referring to a torrent id,
    *   an array of torrent id number (must only be 1 torrent)
    * @param string path the path to the file or folder that will be renamed
    * @param string name the file or folder's new name
    * @returns none
    */
    public function torrentRenamePath(
        $ids = null,
        string $path = '',
        string $name = ''
    ) : object {
        $arguments = ['method' => 'torrent-rename-path'];
        $arguments['arguments'] = ['path' => $path, 'name' => $name,];
        if ($ids) {
            $arguments['arguments'] =
                        array_merge($arguments['arguments'], ['ids' => $ids]);
        }
        return $this->request($arguments);
    }

    /**
    * Get the 'X-Transmission-Session-Id' code
    *
    * @returns an array with the session arguments
    */
    public function connect() : object
    {
        if ($this->user && $this->password) {
            $this->auth = 'Authorization: Basic ' .
                            base64_encode($this->user . ':' . $this->password);
        }
        return $this->sessionStats();
    }

    /**
    * Get the session arguments
    *
    * @returns an array with the session arguments
    */
    public function sessionGet() : object
    {
        $arguments = [
            'arguments' => [],
            'method' => 'session-get'
        ];
        return $this->request($arguments);
    }

    /**
    * Get the session arguments
    *
    * @param array fields a array with the fields
    * @returns none
    */
    public function sessionSet($fields) : object
    {
        $arguments = [
            'arguments' => [$fields],
            'method' => 'session-set'
        ];
        return $this->request($arguments);
    }

    /**
    * Get the session stats
    *
    * fields return
    * ---------------------------+-------------------------------+
    * string                     | value type                    |
    * ---------------------------+-------------------------------+
    * "activeTorrentCount"       | number                        |
    * "downloadSpeed"            | number                        |
    * "pausedTorrentCount"       | number                        |
    * "torrentCount"             | number                        |
    * "uploadSpeed"              | number                        |
    * ---------------------------+-------------------------------+
    * "cumulative-stats"         | object, containing:           |
    *                            +------------------+------------+
    *                            | uploadedBytes    | number     |
    *                            | downloadedBytes  | number     |
    *                            | filesAdded       | number     |
    *                            | sessionCount     | number     |
    *                            | secondsActive    | number     |
    * ---------------------------+-------------------------------+
    * "current-stats"            | object, containing:           |
    *                            +------------------+------------+
    *                            | uploadedBytes    | number     |
    *                            | downloadedBytes  | number     |
    *                            | filesAdded       | number     |
    *                            | sessionCount     | number     |
    *                            | secondsActive    | number     |
    * ---------------------------+-------------------------------+
    *
    * @returns an array with the session stats
    */
    public function sessionStats() : object
    {
        $arguments = [
            'arguments' => [],
            'method' => 'session-stats'
        ];
        return $this->request($arguments);
    }

    /**
    * Update the blocklist size
    *
    * @returns a number "blocklist-size"
    */
    public function blocklistUpdate() : object
    {
        $arguments = [
            'arguments' => [],
            'method' => 'blocklist-update'
        ];
        return $this->request($arguments);
    }

    /**
    * This method tests to see if your incoming peer port is accessible from
    * the outside world.
    *
    * @returns a bool, "port-is-open"
    */
    public function portTest() : object
    {
        $arguments = [
            'arguments' => [],
            'method' => 'port-test'
        ];
        return $this->request($arguments);
    }

    /**
    * This method tells the transmission session to shut down.
    */
    public function sessionClose() : object
    {
        $arguments = [
            'arguments' => [],
            'method' => 'session-close'
        ];
        return $this->request($arguments);
    }

    /**
    * Move a torrent's queue
    *
    * @param int|array ids
    *   an integer referring to a torrent id,
    *   an array of torrent id number (must only be 1 torrent)
    * @param string where where move the queue (top|up|down|bottom)
    * @returns none
    */
    public function queueMove($ids = null, string $where = '')
    {
        $arguments = [
            'method' => 'queue-move-' . strtolower($where),
            'arguments' => []
        ];
        if ($ids) {
            $arguments['arguments']['ids'] = $ids;
        }
        return $this->request($arguments);
    }

    /**
    * This method tests how much free space is available in a
    * client-specified folder.
    *
    * @param string path the directory to query
    * @returns a array with the path and the size, in bytes, of the free space
    * in that directory
    */
    public function freeSpace($path)
    {
        $arguments = [
            'method' => 'free-space',
            'arguments' => ['path' => $path]
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
        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $this->host . ':' .
                    $this->port . '/transmission/rpc',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($arguments),
                CURLOPT_HTTPHEADER => [
                    (isset($this->auth) ? $this->auth : ''),
                    "Content-Type: application/json",
                    "X-Transmission-Session-Id: " . $this->sessionId
                ],
            ]
        );
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        switch ($httpCode) {
            case '200':
                return json_decode($response);
            case '409':
                preg_match_all(
                    '/X-Transmission-Session-Id: ([^<]*)/m',
                    $response,
                    $m,
                    PREG_SET_ORDER,
                    0
                );
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
                    'result' => $httpCode .
                    ': Unexpected response received from Transmission'
                ];
        }
    }
}
