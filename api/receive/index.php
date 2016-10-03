<?php

  require_once("../common/index.php");
  
  $requestMethod = "GET";
  $PARAMS = $_GET;
  
  // main
  if ($_SERVER["REQUEST_METHOD"] == $requestMethod) {
    if (!checkUser($userID = htmlspecialchars($PARAMS["userid"]))) {
      exit("Error: This user is not registered.");
    }
    $tag = strtolower(preg_replace("/\s/", "", htmlspecialchars($PARAMS["tag"])));
    $startID = intval(htmlspecialchars($PARAMS["startid"]));
    $type = htmlspecialchars($PARAMS["type"]);
    $json = getFreshJSON($tag, $startID);
    echoTable($json, $type);
  }
?>
