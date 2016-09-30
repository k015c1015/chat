<?php

  date_default_timezone_set('Japan');
  logAccess($_SERVER);
  initFiles();
  
  //
  function initFiles() {
    if (!file_exists("../files")) {
      mkdir("../files");
    }
    if (!file_exists("../files/log")) {
      mkdir("../files/log");
    }
  }
  
  //
  function checkBAN($name) {
    $banFilePath = "../files/ban.txt";
    $array = explode("<>", trim(file_get_contents($banFilePath)));
    foreach ($array as $key => $value) {
      if ($name == $value) {
        return false;
      }
    }
    return true;
  }
  
  //
  function getJapaneseName($userID) {
    $kanjis = array(
      "川", "島", "田", "原", "小", "杉", "大", "中", "西", "東",
      "木", "山", "本", "村", "林", "松", "上", "下", "南", "北"
    );
    $k1 = (intval(substr($userID, 0, 1)) + intval(substr($userID, 1, 1)) + intval(substr($userID, 2, 1)) + intval(substr($userID, 3, 1))) % 20;
    $k2 = (intval(substr($userID, 4, 1)) + intval(substr($userID, 5, 1)) + intval(substr($userID, 6, 1)) + intval(substr($userID, 7, 1))) % 20;
    $name = $kanjis[$k1].$kanjis[$k2];
    return $name;
  }
  
  //
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
    $accessLogFilePath = "../files/access-log.txt";
    $serverInfo = "[".date("Y-m-d H:i:s")."] ".$serverInfo["PHP_SELF"].": ".$serverInfo["REMOTE_ADDR"].", ".$serverInfo["HTTP_USER_AGENT"];
    $str = file_get_contents($accessLogFilePath)."\n".$serverInfo;
    file_put_contents($accessLogFilePath, $str);
  }
  
  //
  function getFreshJSON($tag, $startID) {
    $filePath = "../files/log/".sha1($tag).".json";
    $json = array();
    if (is_file($filePath)) {
      $lines = json_decode(file_get_contents($filePath));
      for ($i = count($lines) - 1; $i >= 0; $i--) {
        $line = $lines[$i];
        $id = $line[0];
        if ($id >= $startID) {
          array_unshift($json, $line);
          $startID = $id;
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
