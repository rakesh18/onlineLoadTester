<?php
  session_start();

  include('Net/SSH2.php');
  include('dbConfig.php');

  $fileType       = "";
  $userList       = $_POST['UL'];
  $portReq        = $_POST['PR'];
  $projectName    = $_COOKIE["projectName"];
  $userName       = $_COOKIE["userName"];
  $clientIp       = $_COOKIE["clientIp"];
  $clientUsername = $_COOKIE["clientUsername"];
  $clientPassword = $_COOKIE["clientPassword"];
  $location       = $_COOKIE["location"];
  $sessID         = session_id();
  $resp           = array("statusFlag" => "1", 
                          "message" => "CSV file generated successfully");

  $userName = $userName."_".$projectName;
  $userDir  = $userName."_".$projectName."_load/";

  if(strpos($userList, "pr") !== FALSE)
  {
    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error)
    {
      $resp["message"]    = "Could not conect to database";
      $resp["statusFlag"] = "0";
      $serverResp = json_encode($resp);
      echo $serverResp;
      exit(1);
    }

    $tableName = str_replace(".", "_", $clientIp);

    $sql = "update ".$tableName." set user = '".$userName."', state = '".$sessID."' where user = '' limit ".$portReq.";";
    if($conn->query($sql) === TRUE)
    {
      $sql = "select port_number from ".$tableName." where user = '".$userName."' and state = '".$sessID."' limit ".$portReq.";";
      $result = $conn->query($sql);
      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc())
        {
          $port = $row['port_number'];
          $userList = str_replace("pr", "".$port, $userList);
        }
      }
      else
      {
        $resp["message"]    = "No free ports left";
        $resp["statusFlag"] = "0";
        $serverResp = json_encode($resp);
        echo $serverResp;
        $conn->close();
        exit(1);
      }
    }
    else
    {
      $resp["message"]    = "No free ports left";
      $resp["statusFlag"] = "0";
      $serverResp = json_encode($resp);
      echo $serverResp;
      $conn->close();
      exit(1);
    }
    $conn->close();
  }

  $userList = str_replace("br", "\n", $userList);
  $userFile = fopen("projects/".$location."/".$userDir . "reg_user.csv", "w") or die("Unable to open file!");

  /*
  if($location === "external")
  {    
    $sshClient = new Net_SSH2($clientIp);
    if (!$sshClient->login($clientUsername, $clientPassword)) 
    {
      $resp["message"]    = "Login failed to client";
      $resp["statusFlag"] = "0";
      $serverResp = json_encode($resp);
      echo $serverResp;
      exit(1);
    }

    $userCsvCmd = "echo '".$userList."' > /root/".$userDir."reg_user.csv";
    $shellCmdRes = $sshClient->exec($userCsvCmd);
    
  }
  */
  fwrite($userFile, $userList);
  fclose($userFile);

  $serverResp = json_encode($resp);
  echo $serverResp;
?>