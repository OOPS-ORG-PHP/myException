# myException pear package

## License

Copyright (c) 2016 JoungKyun.Kim &lt;http://oops.org&gt; All rights reserved

This program is under BSD license

## Description

This is extended php exception.

## Installation

We recommand to install with pear command cause of dependency pear packages.

### 1. use pear command

```bash
[root@host ~]$ # add pear channel 'pear.oops.org'
[root@host ~]$ pear channel-discover pear.oops.org
Adding Channel "pear.oops.org" succeeded
Discovery of channel "pear.oops.org" succeeded
downloading myException-1.0.1.tgz ...
Starting to download myException-1.0.1.tgz (3,048 bytes)
...done: 3,048 bytes
install ok: channel://pear.oops.org/myException-1.0.1
[root@host ~]$
```

If you wnat to upgarde version:

```bash
[root@host ~]$ pear upgrade oops/myException
```


### 2. install by hand

Get last release at https://github.com/OOPS-ORG-PHP/myException/releases and uncompress pakcage within PHP include_path.


## Usages

Refence siste: http://pear.oops.org/docs/myException/myException.html (with Korean)

```php
<?php
require_once 'myException.php';

// If you want to manage E_ERROR with myException
#function fatal_error ($dump) {
#   echo '::: Fatal Messages' . PHP_EOL;
#   print_r ($dump);
#}
#error_reporting (E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_ERROR);
#register_shutdown_function('myException::myShutdownHandler', 'fatal_error');

# If you want to manage all Warning/Error except E_ERROR, uncomment follow line.
set_error_handler('myException::myErrorHandler');

class myEX {
    function foo () {
        try {
            if ( ! function_exists ('mysql_connect') )
                throw new myException ('Unsupported mysql_connect function', E_USER_ERROR);

			// for this warning, need set_error_handler
            $c = mysql_connect ();
        } catch ( Exception $e ) {
            throw new myException ($e->getMessage (), $e->getCode (), $e);
        }
    }
}

$m = new myEX;

try {
    $m->foo ();
} catch ( Exception $e ) {
    echo $e->Message () . "\n";
    print_r ($e->TraceAsArray ()) . "\n";
    $e->finalize ();
}

?>
```
