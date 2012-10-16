<?php
/*
 * pear_myException test
 *
 * $Id$
 */

$iniget = function_exists ('___ini_get') ? '___ini_get' : 'ini_get';
$iniset = function_exists ('___ini_set') ? '___ini_set' : 'ini_set';

$cwd = getcwd ();
$ccwd = basename ($cwd);
if ( $ccwd == 'tests' ) {
	$oldpath = $iniget ('include_path');
	$newpath = preg_replace ("!/{$ccwd}!", '', $cwd);
	$iniset ('include_path', $newpath . ':' . $oldpath);
}

require_once 'myException.php';

class myEX {
	function foo () {
		try {
			// mysql_connect function이 load 되지 않으면 myException으로
			// 에러 메세지를 전달한다.
			if ( ! function_exists ('mysql_connect') )
				throw new myException ('Unsupported mysql_connect function', E_USER_ERROR);

			/*
			 * mysql_connect의 argument가 없기 때문에 argument 관련 E_WARNING
			 * 이 발생하며, 기본적으로는 Exception에서 cache가 되지 않는다.
			 * PHP Error를 cache하여 myException으로 보내려면, 아래의
			 * set_error_handler line의 주석을 풀어줘야 한다.
			 */
			$c = mysql_connect ();
		} catch ( Exception $e ) {
			throw new myException ($e->getMessage (), $e->getCode (), $e);
		}
	}
}

# PHP 자체의 에러를 myException에서 제어하고 싶다면 다음 라인의 주석을 풀어준다.
#set_error_handler('myException::myErrorHandler');

$m = new myEX;

try {
	$m->foo ();
} catch ( Exception $e ) {
	echo $e->Message () . "\n";
	print_r ($e->TraceAsArray ()) . "\n";
	$e->finalize ();
}

echo "E_ERROR 또는 E_USER_ERROR 에러가 발생하면 이 라인이 보이지 않습니다.\n";
?>
