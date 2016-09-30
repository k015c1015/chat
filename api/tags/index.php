<?php
   
  require_once("../common/index.php");
  
  $requestMethod = "GET";
  
  // main
  if ($_SERVER["REQUEST_METHOD"] == $requestMethod) {
    $tagsFilePath = "../files/tags.json";
    if (!checkUser($userID = htmlspecialchars($_GET["userid"]))) {
      exit("Error: This user is not registered.");
    }
    $type = htmlspecialchars($_GET["type"]);
    $json = file_get_contents($tagsFilePath);
    echoTable($json, $type);
  }
  
?>