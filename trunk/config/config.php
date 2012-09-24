<?php  
defined( '_WAEXEC' ) or die( 'Restricted access' );
/**
* Wandering PHP Framework
*
* PHP 5
*
* @package Wandering
* @author Nowayforback<nowayforback@gmail.com>
* @copyright Copyright (c) 2012, Nowayforback, (http://nowayforback.com) 
* @license http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
* @link http://nowayforback.com
* @since Version 1.0
* @filesource
*/


/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your System root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
*/
$config['base_url']	= "http://localhost/wandering/";

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
| Remember that, all system only one index file.
|
*/
$config['index_page'] = "index.php";



/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= "english";

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
*/
$config['charset'] = "UTF-8";


/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the "hooks" feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/
$config['enable_hooks'] = FALSE;

$config['enable_cache'] = true;

$config['enable_debug'] = FALSE;


/*
|--------------------------------------------------------------------------
| Set Varials Name for some baic GET varials
|--------------------------------------------------------------------------
|
|
*/
$config['controller_variable_name'] 	= 'module';
$config['function_variable_name'] 	= 'action';
$config['prefix_input_parameter_name'] 	= 'param_';

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| If you have enabled error logging, you can set an error threshold to 
| determine what gets logged. Threshold options are:
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 1;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/logs/ folder.  Use a full server path with trailing slash.
|
*/
$config['log_path'] = '';
$config['cache_path'] = WAPATH_SYSTEM.'cache/';
$config['temp_path'] = '';

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/cache/ folder.  Use a full server path with trailing slash.
|
*/
$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class or the Sessions class with encryption
| enabled you MUST set an encryption key.  See the user guide for info.
|
*/
$config['encryption_key'] = "123";

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'session_cookie_name' = the name you want for the cookie
| 'encrypt_sess_cookie' = TRUE/FALSE (boolean).  Whether to encrypt the cookie
| 'session_expiration'  = the number of SECONDS you want the session to last.
|  by default sessions last 7200 seconds (two hours).  Set to zero for no expiration.
| 'time_to_update'		= how many seconds between CI refreshing Session Information
|
*/
$config['sess_cookie_name']		= 'pa_session';
$config['sess_expiration']		= 72000;
$config['sess_encrypt_cookie']	= false;
$config['sess_use_database']	= false;
$config['sess_table_name']		= 'dum_sessions';
$config['sess_match_ip']		= true;
$config['sess_match_useragent']	= false;
$config['sess_time_to_update'] 	= 300;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix' = Set a prefix if you need to avoid collisions
| 'cookie_domain' = Set to .your-domain.com for site-wide cookies
| 'cookie_path'   =  Typically will be a forward slash
|
*/
$config['cookie_prefix']	= "";
$config['cookie_domain']	= "";
$config['cookie_path']		= "/";




/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Enables Gzip output compression for faster page loads.  When enabled,
| the output class will test whether your server supports Gzip.
| Even if it does, however, not all browsers support compression
| so enable only if you are reasonably sure your visitors can handle it.
|
| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
| means you are prematurely outputting something to your browser. It could
| even be a line of whitespace at the end of one of your scripts.  For
| compression to work, nothing can be sent before the output buffer is called
| by the output class.  Do not "echo" any values with compression enabled.
|
*/
$config['compress_output'] = false;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are "local" or "gmt".  This pref tells the system whether to use
| your server's local time as the master "now" reference, or convert it to
| GMT.  See the "date helper" page of the user guide for information
| regarding date handling.
|
*/
$config['time_reference'] = 'local';


/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy IP
| addresses from which SYSTEM should trust the HTTP_X_FORWARDED_FOR
| header in order to properly identify the visitor's IP address.
| Comma-delimited, e.g. '10.0.1.200,10.0.1.201'
|
*/
$config['proxy_ips'] = '';


$config['library_folders'] = array('data_objects','core/libraries','userlibs','core/libraries/TemplateEngineLayer','core/libraries/element','core/packages/smarty');

/**
 * setup all classes you want auto require into system
 */
$config['auto_load_class'] = array('DataObject');


$config['template_engine'] = 'Original';
$config['template_dir'] = 'templates/default';

/*
| Controller folders, string of aray ò string
*/
$config['controler_folders'] = array('system/application/controllers','modules');

$config['view_folders'] = array('system/application/views','modules');

$config['mode_folders'] = array('system/application/modes','modules');

$config['helper_folders'] = array('system/helper');

$config['modul_folders'] = array('modules');

//language folders, global_lang_dir is language for all modul in app, cache_lang_dir if dir for cache fo all label get from translator webservice
$config['global_lang_dir'] = WAPATH_SYSTEM.'languages/';
$config['cache_lang_dir'] = WAPATH_SYSTEM.'lang_cache/';


/* End of file config.php */
/* Location: ./system/config/config.php */
