<?php

  require_once("../common/index.php");
  
  $requestMethod = "POST";
  
  // main
  if ($_SERVER["REQUEST_METHOD"] == $requestMethod) {
    $messageMaxLength = 256;
    $ngRegexp = "/(エッチ|陰毛|いんもう|まんこ|膣|ま○こ|ま〇こ|まんk|マソコ|オメコ|ヴァギナ|バギナ|ワギナ|クリトリス|ちんこ|ちんk|ちんちん|チンポ|ペニス|penis|きんたま|金玉|肉棒|勃起|おっき|ボッキ|精子|射精|ザーメン|●～|○～|〇～|セックス|SEX|S○X|S〇X|体位|淫乱|アナル|anus|おっぱい|oppai|おっぱお|巨乳|きょぬ|爆乳|超乳|きょにゅう|きょにゅー|貧乳|ひんぬ|無乳|微乳|つるぺた|ちっぱい|ペチャパイ|ひんにゅう|ひんにゅー|谷間|たにま|何カップ|なにカップ|手ブラ|てブラ|パンツ|パンティ|パンt|ノーパン|乳首|ちくび|自慰|オナニ|オナ二|オナヌ|マスターベーション|マスタベーション|しこって|しこしこ|シコシコ|脱げ|ぬげ|脱いで|ぬいで|脱ごう|ぬごう|喘いで|喘げ|喘ぐ|あえいで|クンニ|フェラ|まんぐり|パイズリ|風俗|ふうぞく|ふーぞく|ソープ|デリヘル|ヘルス|姦|包茎|ほうけい|童貞|どうてい|どうてー|どーてー|どーてい|性器|処女|やりまん|乱交|バイブ|ローター|パイパン|中出し|中田氏|スカトロ|糞|クソ|うんこ|うんち|パコパコ|ホモ|homo|ぱいぱい|ノーブラ|手コキ|手マン|潮吹|きもい|きしょい|きめえ|きめぇ|変態|馬鹿|阿呆|ばーか|baka|fuck|f*ck|ファック|不細工|醜男|醜女|ぶさいく|ブス|かす|カス|気違い|気狂い|キチガイ|マジキチ|馬路基地|基地外|ブタ|くたばれ|潰せ|bitch|ビッチ|死す|死な|死ぬ|しぬ|死ね|しね|氏ね|shine|死の|死ん|ﾀﾋ|タヒ|殺さ|殺し|殺す|ころす|殺せ|ころせ|殺そ|乞食|ばばあ|ばばぁ|BBA|くず|クズ|屑|大麻|麻薬|レイプ|犯し)/";
    
    // param
    if (!checkUser($userID = htmlspecialchars($_POST["userid"]))) {
      exit("Error: This user is not registered.");
    }
    $tag = strtolower(preg_replace("/\s/", "", htmlspecialchars($_POST["tag"])));
    $user = htmlspecialchars($_POST["userid"]);
    $startID = intval(htmlspecialchars($_POST["startid"]));
    $type = htmlspecialchars($_POST["type"]);
    $message = trim(preg_replace("/\s+/", " ", htmlspecialchars($_POST["message"])));
    $trimedMessage = preg_replace("/\s/", "", $message);
    $name = getJapaneseName($user);
    
    // error
    if (!checkBAN($name)) {
      exit("Error: BAN");
    }
    if ($trimedMessage == "") {
      exit("Error: This message is empty.");
    }
    preg_match("/:/", $trimedMessage, $matches);
    if (
      (count($matches)) &&
      (explode(":", $message)[1] == "")
    ) {
      exit("Error: This message is empty.");
    }
    if (strlen($message) >= $messageMaxLength) {
      exit("Error: This message is long. (within ".$messageMaxLength." characters)");
    }
    preg_match($ngRegexp, $trimedMessage, $matches);
    if (count($matches) > 0) {
      exit("Error: This message contains some NG words.");
    }
    $retFlag = false;
    $arr = json_decode(getFreshJSON($tag, 0));
    foreach ($arr as $lines) {
      if (($lines[2] == $user)&&($lines[4] == $message)) {
        $retFlag = true;
      }
    }
    if ($retFlag) {
      exit("Error: This message that has already been posted.");
    }
    if (isEmoji($message)) {
      $message = "emoji:".$message;
    }
    
    // regist and echo
    $command = explode(":", $message)[0];
    addComment("*all", $user, $name, $message);
    if (substr($tag, 0, 1) != "*") {
      addComment($tag, $user, $name, $message);
      $json = getFreshJSON($tag, $startID);
      echoTable($json, $type);
    }
  }
  
  // regist
  function addComment($tag, $user, $name, $message) {
    $logFilePath = "../files/log/".sha1($tag).".json";
    $tagsFilePath = "../files/tags.json";
    $json = array();
    $newestId = -1;
    $newestDate = date("Y-m-d H:i:s");
    $defaultMaxCount = 100;
    if (is_file($logFilePath)) {
      $lines = json_decode(file_get_contents($logFilePath));
      $maxCount = (1 + count($lines) < $defaultMaxCount) ? 1 + count($lines) : $defaultMaxCount;
      for ($i = 0; $i < $maxCount - 1; $i++) {
        $line = $lines[$i];
        array_push($json, $line);
        if ($i == 0) {
          $newestId = $line[0];
          $newestDate = $line[1];
        }
      }
    }
    array_unshift($json, array(++$newestId, date("Y-m-d H:i:s"), $user, $name, $message));
    file_put_contents($logFilePath, json_encode($json));
    
    if ($tag != "*all") {
      $json = array();
      $defaultMaxCount = 100;
      if (is_file($tagsFilePath)) {
        $lines = json_decode(file_get_contents($tagsFilePath));
        $maxCount = (1 + count($lines) < $defaultMaxCount) ? 1 + count($lines) : $defaultMaxCount;
        for ($i = 0; $i < $maxCount - 1; $i++) {
          $line = $lines[$i];
          array_push($json, $line);
        }
      }
      array_unshift($json, $tag);
      $json = array_unique($json);
      $arr = array();
      foreach ($json as $value) {
        array_push($arr, $value);
      }
      file_put_contents($tagsFilePath, json_encode($arr));
    }
  }

?>
