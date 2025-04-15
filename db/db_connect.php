<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "nate";
// added in the 3309 port number siince the xammp port number i sent is 3309 only include here no where else
$conn = new mysqli($host,$user,$password,$dbname);
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}else{
    echo  "Connection successful";
}
?>