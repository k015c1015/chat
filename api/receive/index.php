<?php

  require_once("../common/index.php");
  
  $requestMethod = "GET";
  
  // main
  if ($_SERVER["REQUEST_METHOD"] == $requestMethod) {
    if (!checkUser($userID = htmlspecialchars($_GET["userid"]))) {
      exit("Error: This user is not registered.");
    }
    $tag = strtolower(preg_replace("/\s/", "", htmlspecialchars($_GET["tag"])));
    $startID = intval(htmlspecialchars($_GET["startid"]));
    $type = htmlspecialchars($_GET["type"]);
    $json = getFreshJSON($tag, $startID);
    echoTable($json, $type);
  }
?>
