<?php
/**
 * Project: myException
 * File:    myException.php
 *
 * @category    Core
 * @package     myException
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 1997-2012 OOPS.org
 * @license     BSD License
 * @link        http://pear.oops.org/package/myException
 * @filesource
 */

/**
 * 기본 myException Class
 *
 * @category    Core
 * @package     myException
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 1997-2012 OOPS.org
 * @license     BSD License
 * @version     $Id$
 * @link        http://pear.oops.org/package/myException
 * @example     pear_myException/tests/myext.php
 */
class myException extends Exception {
	// {{{ properties
	/**#@+
	 * @access private
	 */
	/**
	 * Previous exception
	 * @var    object
	 */
	private $prev = null;
	/**
	 * PHP 버전이 5.3 이전 버전 마크. 5.3.0 이전 버전일 경우 true.
	 * @var    boolean
	 */
	private $early53 = false;
	/**#@-*/
	// }}}

	// {{{ (object) myException::__construct ($message, $code, Exception $prev= null)
	/** 
	 * myException class 초기화
	 *
	 * @access public
	 * @return object
	 * @param  string 에러 메시지
	 * @param  string 에러 코드
	 * @param  string 이전 예외 배열
	 */
	public function __construct($message, $code = 0, Exception $prev = null) {
		if ( version_compare (PHP_VERSION, '5.3.0', '>=') )
			parent::__construct($message, $code, $prev);
		else {
			$this->early53 = true;
			$this->prev = $prev;
			parent::__construct($message, $code);
		}
	}
	// }}}

	// {{{ (object) myException::Previous (void)
	/**
	 * __construct method의 3번째 인자값인 previous exception을 반환
	 *
	 * @access public
	 * @return object exception object 
	 * @param  void
	 */
	function Previous () {
		return $this->early53 ? $this->prev : $this->getPrevious ();
	}
	// }}}

	// {{{ (array) myException::Trace (void)
	/**
	 * Exception 스택을 배열로 반환
	 *
	 * @access public
	 * @return array  Exception 스택을 배열로 반환
	 * @param  void
	 */
	function Trace () {
		$r = $this->Previous ();
		if ( $r instanceof Exception )
			return $r->getTrace();

		return $this->getTrace ();
	}
	// }}}

	// {{{ (array) myException::Message (void)
	/**
	 * Exception 에러 메시지를 PHP 에러 메시지 형식으로 반환
	 *
	 * @access public
	 * @return string
	 * @param  void
	 */
	function Message () {
		$r = $this->Previous ();
		if ( $r instanceof Exception )
			$e = &$r;
		else
			$e = &$this;

		return sprintf (
			"%s: %s [%s:%d]",
			$this->errStr ($e->getCode ()),
			$e->getMessage (),
			preg_replace ('!.*/!', '', $e->getFile ()),
			$e->getLine ()
		);
	}
	// }}}

	// {{{ (string) myException::TraceAsString (void)
	/**
	 * Exception 스택 trace를 문자열로 반환
	 *
	 * @access public
	 * @return string
	 * @param  void
	 */
	function TraceAsString () {
		$r = $this->Previous ();
		if ( $r instanceof Exception )
			return $r->getTraceAsString ();

		return $this->getTraceAsString ();
	}
	// }}}

	// {{{ (array) myException::TraceAsArray (void)
	/**
	 * Exception 스택 trace를 배열로 반환
	 *
	 *
	 * @access public
	 * @return array
	 * @param  void
	 */
	function TraceAsArray () {
		$r = $this->Previous ();
		if ( $r instanceof Exception )
			$str = $r->getTraceAsString ();
		else
			$str = $this->getTraceAsString ();

		$buf = preg_split ('/[\r\n]+/', $str);
		$no = count ($buf) - 1;

		for ( $i=$no, $j=0; $i>-1; $i--,$j++ ) {
			$ret[$j] = preg_replace ('/^#[0-9]+[\s]*/', '', $buf[$i]);
		}
		return $ret;
	}
	// }}}

	// {{{ (string) myException::errStr ($errno)
	/**
	 * PHP 에러 상수를 문자열로 반환
	 *
	 * @access public
	 * @return string
	 * @param  int php error constants
	 */
	function errStr ($errno) {
		switch ($errno) {
			case 1 :
				return 'E_ERROR';
			case 2 :
				return 'E_WARNING';
			case 4 :
				return 'E_PARSE';
			case 8 :
				return 'E_NOTICE';
			case 16 :
				return 'E_CORE_ERROR';
			case 32 :
				return 'E_CORE_WARNING';
			case 64 :
				return 'E_COMPILE_ERROR';
			case 128 :
				return 'E_COMPILE_WARNING';
			case 256 :
				return 'E_USER_ERROR';
			case 512 :
				return 'E_USER_WARNING';
			case 1024 :
				return 'E_USER_NOTICE';
			case 2048 :
				return 'E_STRICT';
			case 4096 : // since php 5.2
				return 'E_RECOVERABLE_ERROR';
			case 8192 : // since php 5.3
				return 'E_DEPRECATED';
			case 16384 : // since php 5.3
				return 'E_USER_DEPRECATED';
			case 32767 :
				return 'E_ALL';
			default :
				return 'Unknown Error';
		}
	}
	// }}}

	// {{{ (void) myException::finalize (void)
	/**
	 * E_ERROR 또는 E_USER_ERROR 시에 php를 중지 시킨다.
	 *
	 * @access public
	 * @return void
	 */
	function finalize () {
		$r = $this->Previous ();
		if ( $r instanceof Exception )
			$e = &$r;
		else
			$e = &$this;

		if ( $e->getCode () === E_ERROR || $e->getCode () === E_USER_ERROR )
			exit (1);
	}
	// }}}

	// {{{ (void) myErrorHandler ($errno, $errstr, $errfile, $errline)
	/**
	 * myException을 사용하기 위한 error handler.
	 *
	 * 이 method는 static으로 선언이 되어 있으므로,
	 * myException::myErrorHandler() 과 같이 호출해야 한다.
	 *
	 * @access public
	 * @return boolean
	 * @param  int    에러 코드
	 * @param  string 에러 메시지
	 * @param  string 에러가 발생한 파일 경로
	 * @param  int    에러가 발생한 라인
	 */
	static function myErrorHandler ($errno, $errstr, $errfile, $errline) {
		if ( ! (error_reporting () & $errno) )
			return;

		switch ($errno ) {
			case E_NOTICE :
			case E_USER_NOTICE :
			case E_STRICT :
			case E_DEPRECATED :
			case E_USER_DEPRECATED :
				break;
			default :
				throw new Exception ($errstr, $errno);
		}

		return true;
	}
	// }}}
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim: set filetype=php noet sw=4 ts=4 fdm=marker:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
?>
