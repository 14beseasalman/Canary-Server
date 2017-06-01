<?php

include 'config.php';

// check if valid request
if (isset($_POST["action"]) && isset($_POST["id"])) {
  // get parameters
  $action = $_POST["action"];
  $id = $_POST["id"];
  
  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    http_response_code(500);
    echo "<h1>500 Internal Server Error</h1>";
    die();
  } 
  
  // proceed according to the action
  if ($action == "ON") {
    $sql = "UPDATE users SET status=1 WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
      $time = time();
      $sql = "UPDATE users SET last_time=$time WHERE id=$id";
      if ($conn->query($sql) === TRUE) {
          echo "success";
      }
    } 
  } else if ($action == "OFF") {
    $sql = "UPDATE users SET status=0 WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "success";
    }
  } else if ($action == "NEW_USER") {
    $time = time();
    $user = json_decode($_POST["user"], true);
    $refName = $user["emergencyContact"]["name"];
    $refEmail = $user["emergencyContact"]["email"];
    $uName = $user["name"];
    $sql = "INSERT INTO users (id, status, last_time, name) VALUES ($id, 0, $time, $uName)";
    if ($conn->query($sql) === TRUE) {
        $sql2 = "INSERT INTO contacts (name, email, reference) VALUES ($refName, $refEmail, $id)";
        if ($conn->query($sql2) === TRUE) {
          echo "success";
        }
    }
  } else {
    http_response_code(400);
    echo "<h1>400 HTTP Bad Request</h1>";
  }
 
  // close DB connection
  $conn->close();
} 
// throw error if invalid request
else {
  http_response_code(400);
  echo "<h1>400 HTTP Bad Request</h1>";
}
?>