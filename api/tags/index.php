<?php
   
  require_once("../common/index.php");
  
  $requestMethod = "GET";
  $PARAMS = $_GET;
  
  // main
  if ($_SERVER["REQUEST_METHOD"] == $requestMethod) {
    $tagsFilePath = "../files/tags.json";
    if (!checkUser($userID = htmlspecialchars($PARAMS["userid"]))) {
      exit("Error: This user is not registered.");
    }
    $type = htmlspecialchars($PARAMS["type"]);
    $json = file_get_contents($tagsFilePath);
    echoTable($json, $type);
  }
  
?>