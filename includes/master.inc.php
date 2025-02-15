<?PHP
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

	date_default_timezone_set('America/Chicago');

    // Application flag
    define('SPF', true);
	define('DEFAULT_IPN_URL', 'https://www.paypal.com/cgi-bin/webscr?');
	define('SANDBOX_IPN_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr?');

    // Determine our absolute document root
    define('DOC_ROOT', realpath(dirname(__FILE__) . '/../'));

    // Global include files
    require DOC_ROOT . '/includes/functions.inc.php'; // __autoload() is contained in this file
    require DOC_ROOT . '/includes/class.error.php';
    require DOC_ROOT . '/includes/class.dbobject.php';
    require DOC_ROOT . '/includes/class.objects.php';
    require DOC_ROOT . '/includes/markdown.inc.php';
    require DOC_ROOT . '/includes/Postmark.php';

    // Fix magic quotes
    if(get_magic_quotes_gpc())
    {
        $_POST    = fix_slashes($_POST);
        $_GET     = fix_slashes($_GET);
        $_REQUEST = fix_slashes($_REQUEST);
        $_COOKIE  = fix_slashes($_COOKIE);
    }

    // Load our config settings
    $Config = Config::getConfig();

    // Store session info in the database?
//    if($Config->useDBSessions === true)
//        DBSession::register();

    // Initialize our session
	session_name('spfs');
    session_start();

    // Initialize current user
    $Auth = Auth::getAuth();

    // Object for tracking and displaying error messages
    $Error = ErrorApp::getError();

    $nav = '';
