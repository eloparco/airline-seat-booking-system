<?php
require_once 'header.php';
require_once 'functions.php';

if (isset($_REQUEST['user']) && isset($_REQUEST['pass'])) {
    $connection = connectMysql();
    $username = sanitizeString($_REQUEST['user'], $connection);
    $pass = $_REQUEST['pass'];
    
    $regEmail = "/^[A-Za-z0-9\-_]+(\.[A-Za-z0-9\-_]+)*@[A-Za-z0-9\-]+(\.[A-Za-z0-9\-]+)*(\.[a-z]{2,6})$/";
    $regpass = "/(?=.*[a-z])(?=.*([A-Z]|\d))/";
    
    if (!preg_match($regEmail, $username)) {
        $error = "Email must be valid. Minus sign (-), dot (.) and underscore (_) are the only special character allowed.";
    } else if (!preg_match($regpass, $pass)) {
        $error = "Password must contain at least one lower-case alphabetic character and at least one other character that is either alphabetical uppercase or numeric.";
    } else {
        $result = queryMySQL("SELECT pass 
                                FROM members 
                                WHERE user='$username'", $connection);
        
        $row = $result->fetch_assoc();
        if (!password_verify($pass, $row['pass'])) {
            $error = "Invalid login attempt";
        } else {
            $_SESSION['user'] = $username;
            $_SESSION['time'] = time();
            echo "<div class='content'><h2>Login</h2><p>You are now logged in. Please <a href='index.php'>click here</a> to continue.</p></div>";
            require_once 'footer.php';
            die();
        }
    }
    $connection->close();
} else if (isset($_REQUEST['timeout'])) {
    destroySession();
    $error = "Your session has expired. Please log in to continue.";
}
?>

<script type="text/javascript">
      function validateForm(user, pass) {
    	  var regexEmail = /^[A-Za-z0-9\-_]+(\.[A-Za-z0-9\-_]+)*@[A-Za-z0-9\-]+(\.[A-Za-z0-9\-]+)*(\.[a-z]{2,6})$/;
          var regexpass = /(?=.*[a-z])(?=.*([A-Z]|\d))/;

          if(!regexEmail.test(user)) {
            window.alert("Email must be valid. Minus sign (-), dot (.) and underscore (_) are the only special character allowed.");
            return false;
          }
          else if(!regexpass.test(pass)) {
            window.alert("Password must contain at least one lower-case alphabetic character and at least one other character that is either alphabetical uppercase or numeric.");
            return false;
          }
          else return true;
      }
</script>

<div class="content">
    <h2>Login</h2>
    <p>Enter information requested:</p>
    <form name="form" action="<?php echo  $_SERVER['PHP_SELF'];?>" method="POST" onSubmit="return validateForm(user.value,pass.value);">
        <p> Username <input type="email" name="user" placeholder="Enter email here..."> </p>
        <p> Password <input type="password" name="pass" placeholder="Enter password here..."> </p>
        <p><input type="submit" value="Submit"><input type="reset"></p>
        <p><span class="error"><?php echo $error ?></span></p>
    </form>
</div>

<?php 
    $page = basename($_SERVER['PHP_SELF']);
    require_once 'footer.php';
?>
