<?php
// Perform transaction one of the completed matches which are betted on
function bet_result($row, $db){
  $betID = $row[0];
	$user1 = $row[1];
	$winSide1 = $row[2];
	$user2 = $row[3];
	$amt = $row[4];
	$gHome = $row[5];
	$gAway = $row[6];
  $fID = $row[7];
  // Open log file for keeping track
  $myFile = fopen("log.txt", "a") or die("Can't open file");

	if(($winSide1 == 'home' && $gHome < $gAway) or ($winSide1 == 'away' && $gHome > $gAway)){
		// Transfer from user1 to user2
		try{
      fwrite($myFile, "<T start>\n");
      fwrite($myFile, "<T {$user1}.credit - '$amt'>\n");
			mysqli_query($db, "UPDATE users SET credit = credit-'$amt' WHERE username = '".$user1."';");
      fwrite($myFile, "<T {$user2}.credit + '$amt'>\n");
			mysqli_query($db, "UPDATE users SET credit = credit+'$amt' WHERE username = '".$user2."';");
			$time = time();
			mysqli_query($db, "INSERT INTO Bet_Results values('".$user1."','".$user2."','$amt', '$time', '$fID');");
			mysqli_query($db, "DELETE FROM Bets WHERE bet_id = '$betID';");
      fwrite($myFile, "<T commit>\n");
			mysqli_commit($db);
		}
		catch(Exception $e){
      fwrite($myFile, "<T error found, rolling back>\n");
			mysqli_rollback($db);
		}
	}
	if(($winSide1 == 'home' && $gHome > $gAway) or ($winSide1 == 'away' && $gHome < $gAway)){
	// Transfer from user2 to user1
		try{
      fwrite($myFile, "<T start>\n");
      fwrite($myFile, "<T {$user2}.credit - '$amt'>\n");
			mysqli_query($db, "UPDATE users SET credit = credit-'$amt' WHERE username = '".$user2."';");
      fwrite($myFile, "<T {$user1}.credit + '$amt'>\n");
			mysqli_query($db, "UPDATE users SET credit = credit+'$amt' WHERE username = '".$user1."';");
			$time = time();
			mysqli_query($db, "INSERT INTO Bet_Results values('".$user2."','".$user1."','$amt', '$time', '$fID');");
			mysqli_query($db, "DELETE FROM Bets WHERE bet_id = '$betID';");
      fwrite($myFile, "<T commit>\n");
			mysqli_commit($db);
		}
		catch(Exception $e){
      fwrite($myFile, "<T error found, rolling back>\n");
			mysqli_rollback($db);
		}
	}
  fclose($myFile);

  // Game tied, no winners, the placed bet is deleted
  mysqli_query($db, "DELETE FROM Bets WHERE bet_id = '$betID';");
  mysqli_commit($db);
}

// This function is used as delete cascade & trigger are not available in this version of SQL
function delete_user($db){
  $result = mysqli_query($db, "SELECT username FROM users WHERE credit < 5;");
  while($row = $result->fetch_row()){
    $user = $row[0];
    mysqli_query($db, "DELETE FROM users WHERE username = '".$user."';");
    mysqli_query($db, "DELETE FROM Likes_Teams WHERE user = '".$user."';");
    mysqli_query($db, "DELETE FROM Bet_Requests WHERE requester = '".$user."' OR requested = '".$user."';");
    mysqli_query($db, "DELETE FROM Bets WHERE user1 = '".$user."' OR user2 = '".$user."';");
    mysqli_query($db, "DELETE FROM Bet_Results WHERE fromUser = '".$user."' OR toUser = '".$user."';");
  }
}

?>
