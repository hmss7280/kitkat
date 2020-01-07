<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */


define('PAGENUMBER',10);
define('ABS_PATH',$_SERVER['DOCUMENT_ROOT']);

define("HNAME",        "피싱트리");
define("CNAME",        "fishingtree");
define("CKEY",        "##fishingtree$#$!");
define("DNAME",        "pims.fishingtree.com");
define("DOMAIN",        "fishingtree.com");
define("FROM",        "benjamin@fishingtree.com");
define("GOOGLE_CALENDAR_API_KEY","AIzaSyAnTv1zm3-7X6QwUVgMw0W50uuUbGjdYbY");

define("CLIENT_ID",        "474193942858-sq584cn6ra57itm6v6gmjqp4rous3r6t.apps.googleusercontent.com");
define("CLIENT_SECRET",        "knmQsT0l3AJmv69pgVnJqE5h");
define("REDIRECT_URI",        "http://pims.fishingtree.com/api/callback");

define("APPROVE_HOLIDAY_SEQ",        "236"); //신중목이사