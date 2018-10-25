# Transmission
Another PHP implementation of [Transmission RPC Spec](https://raw.githubusercontent.com/transmission/transmission/master/extras/rpc-spec.txt).

## Instalation
You can install as a [Composer](https://getcomposer.org) package:

```
    composer require willicab/transmission
```

## Usage
By default use 'http://localhost' as host and 9091 as port and not username and password
```php
<?php
require 'vendor/autoload.php';

use Willicab\Transmission\Client;

$tm = new Client();
$tm->connect();
```

If you can set the parameters you can pass as array
```php
<?php
require 'vendor/autoload.php';

use Willicab\Transmission\Client;

$arguments = [
    'host' => 'http://another.host',
    'port' => 1234,
    'user' => 'transmission',
    'password' => 'transmission'
];
$tm = new Client($arguments);
$tm->connect();
```

### Torrent Action Requests
The argument is the ids of the torrents, they can be:
* An integer referring to a torrent id.
* A list of torrent id numbers, sha1 hash strings, or both
* A string, "recently-active", for recently-active torrents
```php
$tm->torrentStart();
$tm->torrentStartNow(1);
$tm->torrentStop([1, 3, '3343d0e7c66f29d2f0ce9af951d367020eedc38c']);
$tm->torrentVerify('recently-active');
$tm->torrentReannounce('3343d0e7c66f29d2f0ce9af951d367020eedc38c');
```

### Torrent Set
You can see the list of available arguments in the [spec file](https://raw.githubusercontent.com/transmission/transmission/master/extras/rpc-spec.txt), point 3.2.
```php
$arguments = [
    'downloadLimit' => 1000,
    'downloadLimited' => true,
];
$tm->torrentSet(2, $arguments);
$tm->torrentSet(1, $arguments);
$tm->torrentSet([1, 3, '3343d0e7c66f29d2f0ce9af951d367020eedc38c'], $arguments);
$tm->torrentSet('recently-active', $arguments);
```

### Torrent Get
You can see the list of available arguments in the [spec file](https://raw.githubusercontent.com/transmission/transmission/master/extras/rpc-spec.txt), point 3.3.
```php
$arguments = ['id, 'name', 'downloadLimit', 'downloadLimited'];
$tm->torrentGet(2, $arguments);
$tm->torrentGet(1, $arguments);
$tm->torrentGet([1, 3, '3343d0e7c66f29d2f0ce9af951d367020eedc38c'], $arguments);
$tm->torrentGet('recently-active', $arguments);
```

### Adding a Torrent
You can see the list of available arguments in the [spec file](https://raw.githubusercontent.com/transmission/transmission/master/extras/rpc-spec.txt), point 3.4.

You can add a torrent with two methods:
```php
$arguments = [
    'download-dir' => '/home/user/Downloads/',
    'paused' => true,
];
$tm->addFilename('magnet:?xt=urn:btih:c39fe3eefbdb62da9c27eb6398ff4a7d2e26e7ab&dn=Big.Buck.Bunny.BDRip.XviD-MEDiC&tr=udp%3A%2F%2Ftracker.leechers-paradise.org%3A6969&tr=udp%3A%2F%2Fzer0day.ch%3A1337&tr=udp%3A%2F%2Fopen.demonii.com%3A1337&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969&tr=udp%3A%2F%2Fexodus.desync.com%3A6969', $arguments);
$tm->addFilename('https://cdimage.debian.org/debian-cd/current/amd64/bt-dvd/debian-9.5.0-amd64-DVD-1.iso.torrent', $arguments);
```

```php
$arguments = [
    'download-dir' => '/home/user/Downloads/',
    'paused' => true,
];
$tm->addMetainfo('ZDg6YW5ub3VuY2U0MTpodHRwOi8vYnR0cmFja2VyLmRlYmlhbi5vcmc6Njk ... wnJ273LLn4nZC2PNVQvHbmy89ucgL8I2QDGQZWU=', $arguments);
```

### Removing a Torrent
```php
$tm->torrentRemove(2); // remove from list
$tm->torrentRemove('3343d0e7c66f29d2f0ce9af951d367020eedc38c', true); // remove from list and delete the files
```

### Moving a Torrent
```php
$tm->torrentSetLocation(1, '/home/user/Downloaded/', true);
```

### Renaming a Torrent's Path
```php
$tm->torrentRenamePath(1, '/home/user/Downloads/The Torrent', '/home/user/Downloaded/My Torrent');
```

### Session Set
You can see the list of available arguments in the [spec file](https://raw.githubusercontent.com/transmission/transmission/master/extras/rpc-spec.txt), point 4.1.

```php
$arguments = [
    'download-dir' => '/home/user/Downloads/',
    'start-added-torrents' => true,
];
$tm->sessionSet($arguments);
```

### Session Get
```php
$tm->sessionGet();
```

### Session Statistics
```php
$tm->sessionStats();
```

### Blocklist
```php
$tm->blocklistUpdate();
```

### Port Checking
```php
$tm->portTest();
```

### Session shutdown
```php
$tm->sessionClose();
```

### Queue Movement Requests
```php
$tm->queueMove(1, 'top');
$tm->queueMove([2, 3], 'up');
$tm->queueMove('3343d0e7c66f29d2f0ce9af951d367020eedc38c', 'down');
$tm->queueMove(2, 'bottom');
```

### Free Space
```php
$tm->freeSpace('/home/user/Downloads/');
```
