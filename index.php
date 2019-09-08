<?php 
    require_once 'header.php';
    require_once 'functions.php';

    if (isset($_SESSION['user'])) {
        $diff = time() - $_SESSION['time'];
        if ($diff > $max_inactivity) {
            header('HTTP/1.1 307 temporary redirect');
            header('Location: login.php?timeout=1');
            die();
        } else {
            $loggedin = TRUE;
            $user = $_SESSION['user'];
        }
    }
    
    if (isset($_REQUEST['buy']) && isset($_SESSION['user'])) {
        // should never happen, buy botton displayed only if at least one seat
        if (count($_SESSION['reserved']) == 0) {
            $message = "Nothing to buy...";
        } else {
            $connection = connectMysql();
            try {
                $connection->autocommit(false);
                
                $result = queryMysqlWithException("SELECT * 
                                                    FROM reservations 
                                                    WHERE UserInvolved='$user' AND Status='Reserved' FOR UPDATE", $connection);
        
                if (count($_SESSION['reserved']) === $result->num_rows) {
                    foreach($_SESSION['reserved'] as $seat=>$value) {
                        $letter = preg_split("/-/", $seat)[0];
                        $number = preg_split("/-/", $seat)[1];
                        
                        queryMysqlWithException("UPDATE reservations
                                                    SET Status = 'Purchased'
                                                    WHERE Row_letter='$letter' AND Column_number='$number'", $connection);               
                    }
                    $message = "Seat purchased successfully!";                    
                } else {
                    foreach($_SESSION['reserved'] as $seat=>$value) {
                        $letter = preg_split("/-/", $seat)[0];
                        $number = preg_split("/-/", $seat)[1];
                        
                        queryMysqlWithException("DELETE FROM reservations
                                                    WHERE UserInvolved='$user' AND Status='Reserved'", $connection);
                    }
                    $message = "Impossible to complete the purchase. Someone must have been faster than you...";
                }
                
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollback();     
                $message = $e->getMessage();
            }
            $connection->close();
        }
        
//         $message = $message . "\nmsg: " . print_r($_SESSION['reserved'], true); // for debugging purpose
        unset($_SESSION['reserved']);
    }
       
    if ($loggedin) {
?>

	<script type="text/javascript" src="jquery-1.7.2.js"></script>
	<script type="text/javascript">

    $(document).ready(function() {
    	var myReservationsCount = $("td[class='MyReservation']").length;
    	console.log(myReservationsCount);
		$("td").click(function() {
			var seat = $(this);
			var seatId = seat.text();
			var oldStatus = seat.attr('class');
			
			var seatLetter = seatId.substring(0, 1);
		    var seatNumber = seatId.substring(1, 2);
			$.post('manageReservations.php', { seat_letter: seatLetter, seat_number: seatNumber, old_status : oldStatus }, function(data) {
				try {
					var jsonData = JSON.parse(data);
					var newStatus = jsonData.new_status;
					var message = jsonData.message;
				} catch (error) {
					var newStatus = "Error";
					var message = "JSON parsing error.";
				}

				if (message === "Session expired! Please log in...") {
				    window.location = "login.php?timeout=1";
				    return;
				}
				
				document.getElementById("message").innerHTML = message;
				if (newStatus !== "Error") {
    				seat.attr("class", newStatus);    
    				if (newStatus === "MyReservation" && oldStatus !== "MyReservation")
    					myReservationsCount++;
    				if (newStatus === "Free" || newStatus === "Reserved")
    					myReservationsCount--;
					if (newStatus === "Purchased" && oldStatus === "MyReservation")
						myReservationsCount--;
				}
				console.log(myReservationsCount);
				if (myReservationsCount == 0)
					$("#buy").hide();
				else $("#buy").show();
		    });
		});
	});
    
	</script>
<?php 
    }
    $connection = connectMysql();
    $result = queryMysql("SELECT Row_letter, Column_number, Status, UserInvolved 
                            FROM reservations", $connection);
?>	
    <div class="content">
    	<h2>
    		Seats
    	</h2>
      <table id="table">
      <?php 
      $seats = array();
      $userInvolved = array();
      while ($row = $result->fetch_assoc()) {
          $index = $row[Row_letter].$row[Column_number];
          $seats[$index] = $row[Status];
          $userInvolved[$index] = $row[UserInvolved];
      }
      $connection->close();

      $number = 1;
      $tot_purchased = $tot_myReservation = $tot_reserved = $tot_free = 0;
      for ($i = 0; $i < $width; $i++) {
          echo "<tr>";
          $letter = "A";
          for ($j = 0; $j < $length; $j++) {
              if ($seats[$letter.$number] === "Purchased") {
                  $class = "Purchased";
                  $tot_purchased++;
              } else if ($seats[$letter.$number] === "Reserved") {
                  if ($loggedin && $userInvolved[$letter.$number] === $_SESSION['user']) {
                      $class = "MyReservation";  
                      $tot_myReservation++;
                      $_SESSION['reserved'][$letter."-".$number] = "reserved";
                  } else {
                      $class = "Reserved";
                  }
                  $tot_reserved++;
              } else {
                  $class = "Free";
                  $tot_free++;
              }
                  
              echo "<td class='$class'>$letter$number</td>";   
              $letter++;
          }
          echo "</tr>";
          $number++;
      }    
      ?>
      </table>
      
      <div>
		<p><span id="message"><?php echo $message ?></span></p>
      </div>
      
      <?php
      if (!$loggedin) {
      ?>
      <div>
      	<p>Total seats: <?php echo $tot_seats ?> </p>
    	<p>Purchased seats: <span id="totPurchased"><?php echo $tot_purchased ?></span> </p>
    	<p>Reserved seats: <span id="totReserved"><?php echo $tot_reserved ?></span> </p>
    	<p>Free seats: <span id="totFree"><?php echo $tot_free ?></span> </p>
      </div>
      <?php 
      }
      ?>
      
    </div>

<?php
    $page = basename($_SERVER['PHP_SELF']);
    require_once 'footer.php';
?>