<noscript>
	<meta http-equiv="refresh" content="0; url=error.php?javascriptDisabled=y" />
</noscript>

<script type="text/javascript">
	document.cookie = 'cookietest=1';
    var cookiesEnabled = document.cookie.indexOf('cookietest=') !== -1;
    document.cookie = 'cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT';
    if (!cookiesEnabled)
	    window.location = "error.php?cookiesDisabled=y";
</script>

<?php
    session_start();     
       
    // redirect to https
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
        $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $location);
        die();
    }
    
    $loggedin = FALSE;
    $user = "Anonymous";
    if (isset($_SESSION['user'])) {
        $loggedin = TRUE;
        $user = $_SESSION['user'];
    }
    
    $width = 6;
    $length = 10;
    $tot_seats = $width * $length;    
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' href='styles.css' type='text/css'>
	<link rel="icon" href="data:,"> <!-- to avoid favicon request -->
	<title> Airplane reservations </title>
  </head>
  <body>
	
