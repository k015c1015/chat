<?php

  date_default_timezone_set('Japan');
  logAccess($_SERVER);
  initFiles();
  
  // create api/files
  function initFiles() {
    if (!file_exists("../files")) {
      mkdir("../files");
    }
    if (!file_exists("../files/log")) {
      mkdir("../files/log");
    }
  }
  
  // chack BAN users
  function checkBAN($user) {
    $banFilePath = "../files/ban.txt";
    $array = explode("<>", trim(file_get_contents($banFilePath)));
    foreach ($array as $value) {
      if ($user == $value) {
        return false;
      }
    }
    return true;
  }
  
  // check Emoji
  function isEmoji($text) {
    return (
      (ord($text) >= 55000)&&
      (ord($text) < 56000)
    );
  }
  
  //
  function checkUser($userID) {
    $usersFilePath = "../files/users.json";
    if (is_file($usersFilePath)) {
      $json = json_decode(file_get_contents($usersFilePath));
      $result = in_array($userID, $json);
    } else {
      $result = false;
    }
    return $result;
  }
  
  //
  function logAccess($serverInfo) {
    $filePath = "../files/access-log.txt";
    $serverInfo = "[".date("Y-m-d H:i:s")."] ".$serverInfo["PHP_SELF"].": ".$serverInfo["REMOTE_ADDR"].", ".$serverInfo["HTTP_USER_AGENT"];
    $defaultMaxCount = 100;
    $str = $serverInfo."\n".file_get_contents($filePath);
    $str = implode("\n", array_slice(explode("\n", $str), 0, $defaultMaxCount));
    file_put_contents($filePath, $str);
  }
  
  //
  function getFreshJSON($tag, $startID) {
    $filePath = "../files/log/".sha1($tag).".json";
    $json = array();
    if (is_file($filePath)) {
      $lines = json_decode(file_get_contents($filePath));
      for ($i = 0; $i < count($lines); $i++) {
        if ($lines[$i][0] >= $startID) {
          array_push($json, $lines[$i]);
        }
      }
      return json_encode($json);
    } else {
      return false;
    }
  }
  
  // core of echoXXXTable
  function echoTable($json, $type) {
    $type = strtolower($type);
    if ($json) {
      switch ($type) {
        case "xml": echoXmlTable($json); break;
        case "html": echoHtmlTable($json); break;
        case "json":
        default: echoJsonTable($json); break;
      }
    } else {
      echo("Error: Not found");
    }
  }
  
  // json
  function echoJsonTable($json) {
    header("Content-type: application/json; charset=utf-8");
    echo($json);
  }
  
  // html
  function echoHtmlTable($json) {
    header("Content-type: text/html; charset=utf-8");
	  $arr = json_decode($json);
	  echo("<table>");
	  foreach ($arr as $line) {
		  echo("<tr>");
		  foreach ($line as $item) {
			  echo("<td>".$item."</td>");
		  }
		  echo("</tr>");
	  }
	  echo("</table>");
  }
  
  // xml
  function echoXmlTable($json) {
    header("Content-type: application/xml; charset=utf-8");
	  $arr = json_decode($json);
    $nameTemps = ["id", "date", "user", "name", "message"];
    $xmlstr = "<?xml version=\"1.0\" ?><root></root>";
    $xml = new SimpleXMLElement($xmlstr);
    foreach ($arr as $line){
      $xmlitem = $xml->addChild("item");
      foreach($line as $key => $value){
        $xmlitem->addChild($nameTemps[$key], $value);
      }
    }
    print $xml->asXML();
  }

?>
