<?php
  session_start();

  include('Net/SSH2.php');
  include('dbConfig.php');

  $fileType       = "";
  $origUsersList  = $_POST['OUL'];
  $termUsersList  = $_POST['TUL'];
  $portReq        = (int)$_POST['PR'];
  $submenu        = $_POST['SM'];
  $projectName    = $_COOKIE["projectName"];
  $userName       = $_COOKIE["userName"];
  $clientIp       = $_COOKIE["clientIp"];
  $clientUsername = $_COOKIE["clientUsername"];
  $clientPassword = $_COOKIE["clientPassword"];
  $location       = $_COOKIE["location"];
  $network        = $_COOKIE["network"];
  $sessID         = session_id();
  $resp           = array("statusFlag" => "1", 
                          "message" => "CSV file generated successfully");
  $port           = "";

  $tableName = str_replace(".", "_", $clientIp);
  $userName  = $userName."_".$projectName;
  $userDir   = $userName."/".$network."/".$submenu."/";

  if(strlen($origUsersList) > 0)
  {
    if(strpos($origUsersList, "pr") !== FALSE)
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

      $sql = "update ".$tableName." set user = '".$userName."_".$network."_".$submenu."_orig', state = '".$sessID."' where user = '' limit 1;";
      if($conn->query($sql) === TRUE)
      {
        $sql = "select port_number from ".$tableName." where user = '".$userName."_".$network."_".$submenu."_orig' and state = '".$sessID."' limit 1;";
        $result = $conn->query($sql);
        if($result->num_rows > 0) 
        {
          while($row = $result->fetch_assoc())
          {
            $port = $row['port_number'];
            $origUsersList = str_replace("pr", "".$port, $origUsersList);
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
    $origUsersList = str_replace("br", "\n", $origUsersList);
    $filename = "projects/".$location."/".$tableName."/".$userDir."orig_user.csv";
    $extFilename = "/root/".$userDir."orig_user.csv";
    $userFile = fopen($filename, "w") or die("Unable to open file!");

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

      $userCsvCmd = "echo '".$origUsersList."' > ".$extFilename;
      $shellCmdRes = $sshClient->exec($userCsvCmd);
    }

    fwrite($userFile, $origUsersList);
    fclose($userFile);

    $resp["oport"] = $port;
  }

  if(strlen($termUsersList) > 0)
  {
    if(strpos($termUsersList, "pr") !== FALSE)
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

      $sql = "update ".$tableName." set user = '".$userName."_".$network."_".$submenu."_term', state = '".$sessID."' where user = '' limit 1;";
      if($conn->query($sql) === TRUE)
      {
        $sql = "select port_number from ".$tableName." where user = '".$userName."_".$network."_".$submenu."_term' and state = '".$sessID."' limit 1;";
        $result = $conn->query($sql);
        if($result->num_rows > 0) 
        {
          while($row = $result->fetch_assoc())
          {
            $port = $row['port_number'];
            $termUsersList = str_replace("pr", "".$port, $termUsersList);
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
    $termUsersList = str_replace("br", "\n", $termUsersList);
    $filename = "projects/".$location."/".$tableName."/".$userDir."term_user.csv";
    $extFilename = "/root/".$userDir."term_user.csv";
    $userFile = fopen($filename, "w") or die("Unable to open file!");

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

      $userCsvCmd = "echo '".$termUsersList."' > ".$extFilename;
      $shellCmdRes = $sshClient->exec($userCsvCmd);
    }

    fwrite($userFile, $termUsersList);
    fclose($userFile);

    $resp["tport"] = $port;
  }

  echo json_encode($resp);
?>