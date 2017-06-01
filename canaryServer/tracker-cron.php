<?php

include 'config.php';

// setup DB connection
$conn = new mysqli($servername, $username, $password, $dbname);

// process tracking jobs
$sql = "SELECT * FROM users WHERE status = 1 ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $lastTime = (int)$row["last_time"];
      $uID = $row["id"];
      $uName = $row["name"];
      $currentTime = time();
      // email emergency contact if a user hasn't responded to the tracking pings
      if (($currentTime - $lastTime) / 60 > 10) {
          $sql2 = "SELECT * FROM contacts WHERE reference = $uID";
          $result2 = $conn->query($sql2);
          if ($result2->num_rows > 0) {
            while($row2 = $result2->fetch_assoc()) {
              $contactName = $row2["name"];
              $contactEmail = $row2["email"];
              $msg = "Dear $contactName, \n\n It seems $uName might be in an emergency. Please check up on him / her.";
              $msg = wordwrap($msg,70);
              //$res = mail($contactEmail,"Canary Tracker",$msg);
            }
          }
      }
  }
}
$conn->close();

?>