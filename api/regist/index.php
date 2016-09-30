<?php
  
  require_once("../common/index.php");
  
  $requestMethod = "GET";
  
  if ($_SERVER["REQUEST_METHOD"] == $requestMethod) {
    $usersFilePath = "../files/users.json";
    $userID = mt_rand(0, 99999999);
    if (is_file($usersFilePath)) {
      $json = json_decode(file_get_contents($usersFilePath));
      do {
        $userID = mt_rand(0, 99999999);
      } while (in_array($userID, $json));
    } else {
      $json = array(12345678);
    }
    array_unshift($json, $userID);
    file_put_contents($usersFilePath, json_encode($json));
    echo($userID);
  }
  
?>