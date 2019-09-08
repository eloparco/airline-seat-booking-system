<?php
$max_inactivity = 120; // inactivity period = 2 minutes (120 seconds)

function connectMysql() {
    $dbhost  = 'localhost';
    $dbname  = 's261072';
    $dbuser  = 's261072';
    $dbpass  = 'arifligh';
    $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($connection->connect_error)
        die("Fatal Error");
    return $connection;
}
    
function queryMysql($query, $connection) {
    $result = $connection->query($query);
    if (!$result) {
        echo("<div class='content'>Fatal Error</div>");
//         echo "<div class='content'><p>Error query failed: " . $connection->error . "</div>"; // for debugging purposes
        require_once 'footer.php';
        die();
    }
    return $result;
}

// similar to 'queryMysql', but launch exception if query fails
function queryMysqlWithException($query, $connection) {
    $result = $connection->query($query);
    if (!$result)
        throw new Exception("Internal error: unable to complete the transaction");
    return $result;
}

function destroySession() {
    $_SESSION=array();

    if (session_id() != "" || isset($_COOKIE[session_name()]))
        setcookie(session_name(), '', time()-2592000, '/');
        
    session_destroy();
}

function sanitizeString($var, $connection) {
    $var = strip_tags($var);
    $var = htmlentities($var);
    if (get_magic_quotes_gpc())
        $var = stripslashes($var);
    return $connection->real_escape_string($var);
}
?>
