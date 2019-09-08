<?php
session_start();
require_once 'functions.php';

if (isset($_SESSION['time'])) {
    $res = array();
    
    $diff = time() - $_SESSION['time'];
    if ($diff > $max_inactivity) {
        $res['new_status'] = "Error";
        $res['message'] = "Session expired! Please log in...";
        echo json_encode($res);
    } else if (isset($_REQUEST['seat_letter']) && isset($_REQUEST['seat_number']) && isset($_REQUEST['old_status'])) {
        $connection = connectMysql();
        $seat_letter = sanitizeString($_REQUEST['seat_letter'], $connection);
        $seat_number = sanitizeString($_REQUEST['seat_number'], $connection);
        $old_status  = sanitizeString($_REQUEST['old_status'], $connection);
        
        if (!ctype_upper($seat_letter) || !is_numeric($seat_number)) {
            $res['new_status'] = "Error";
            $res['message'] = "Error: seat id " . $seat_letter.$seat_number . " is invalid.";
            die(json_encode($res));
        }
        
        $regexSeatStatus = "/^[a-zA-Z]+$/"; // word with capital letter
        if (!preg_match($regexSeatStatus, $old_status)) {
            $res['new_status'] = "Error";
            $res['message'] = "Error: status " . $old_status . " is invalid.";
            die(json_encode($res));
        }
        
        try {
            $connection->autocommit(false);
            
            $result = queryMysqlWithException("SELECT Status, UserInvolved 
                                                FROM reservations 
                                                WHERE Row_letter='$seat_letter' AND Column_number='$seat_number' FOR UPDATE", $connection);
            $row = $result->fetch_assoc();
            $status = $row["Status"];
            $userInvolved = $row["UserInvolved"];
            $user = $_SESSION['user'];
            
            // set explicitly the current status
            if (!$status)
                $status = "Free";
            else if ($status === "Reserved" && $user === $userInvolved)
                $status = "MyReservation";
            
            // generate new status and perform operation based on old status
            if ($status === "Purchased") {             
                $new_status = "Purchased";
                $message = "Unable to reserve the seat, already booked.";
            } else if ($status === "Free") {
                // user trying to remove his old reservation
                if ($old_status === "MyReservation") {
                    $new_status = "Free";
                    $message = "Seat reservation cancelled correctly.";
                    unset($_SESSION['reserved'][$seat_letter."-".$seat_number]);
                } else { // user trying to buy a free seat
                    queryMysqlWithException("INSERT INTO reservations (Row_letter, Column_number, Status, UserInvolved)
                                                VALUES ('$seat_letter', '$seat_number', 'Reserved', '$user')", $connection);
                     
                    $new_status = "MyReservation";
                    $message = "Seat reserved successfully.";
                    $_SESSION['reserved'][$seat_letter."-".$seat_number] = "reserved";
                }
            } else if ($status === "Reserved") {
                // override reservation of other user with my reservation
                if ($old_status === "Free" || $old_status === "Reserved") {
                    queryMysqlWithException("UPDATE reservations
                                                SET UserInvolved = '$user'
                                                WHERE Row_letter='$seat_letter' AND Column_number='$seat_number'", $connection);
                    
                    $new_status = "MyReservation";
                    $message = "Seat reserved successfully.";
                    $_SESSION['reserved'][$seat_letter."-".$seat_number] = "reserved";
                } else if ($old_status === "MyReservation") { // try to remove reservation but someone has already overwritten it
                    $new_status = "Reserved";
                    $message = "No need to cancel the reservation: someone has already overwritten it.";
                    unset($_SESSION['reserved'][$seat_letter."-".$seat_number]);
                }
            } else if ($status === "MyReservation") {
                queryMysqlWithException("DELETE FROM reservations
                                            WHERE Row_letter='$seat_letter' AND Column_number='$seat_number'", $connection);
               
                $new_status = "Free";
                $message = "Seat reservation cancelled correctly.";
                unset($_SESSION['reserved'][$seat_letter."-".$seat_number]);
            }
            
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            $new_status = "Error";
            $message = $e->getMessage();
        }
        $connection->close();
        
        $res['new_status'] = $new_status;
        $res['message'] = $message;
        echo json_encode($res);
    }
}
?>