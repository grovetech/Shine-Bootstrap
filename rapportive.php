<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');

	echo htmlspecialchars(rapportive($_GET['email']));
