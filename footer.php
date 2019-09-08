<div class="header">
   	<h1>Airplane reservations</h1>
   	<p class="logged"> User: <?php echo $user?> </p>
</div>	

<div class="sidebar">
	<a href="index.php">Home</a>
<?php
    if ($loggedin) {
        if ($page === "index.php")
            echo "<a href='index.php'>Update</a>"; // same as 'Home'
            if ($page !== "logout.php" && $page !== "login.php")
            echo "<a href='logout.php'>Log out</a>";
        if ($tot_myReservation > 0)
            echo "<a style='display: block' id='buy' href='index.php?buy=y'>Buy</a>";
        else echo "<a style='display: none' id='buy' href='index.php?buy=y'>Buy</a>";         
    } else if ($page === "index.php") {
        echo "<a href='login.php'>Log in</a>";
        echo "<a href='register.php'>Sign Up</a>";
    }
?>
</div>
</body>
</html>
