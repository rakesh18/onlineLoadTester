<?php
  session_start();

  include('dbConfig.php');
  include('Net/SSH2.php');

  /*
   * Variables
   */
  $sessId            = session_id();
  $userName          = "";
  $projectName       = "";
  $location          = "";
  $network           = "";
  $borderIp          = "";
  $borderUser        = "";
  $borderPassword    = "";
  $networkIp         = "";
  $clientIp          = "";
  $clientUsername    = "";
  $clientPassword    = "";
  $resp              = array("statusFlag" => "1", 
                         "message" => "Success");

  $userName       = $_POST["userName"];
  $projectName    = $_POST["projectName"];
  $location       = $_POST["location"];
  $network        = $_POST["network"];
  $clientIp       = $_POST["clientIp"];
  $clientUsername = $_POST["clientUsername"];
  $clientPassword = $_POST["clientPassword"];
  $borderIp       = $_POST["borderIp"];
  $networkIp      = $_POST["networkIp"];
  $borderUsername = $_POST["borderUsername"];
  $borderPassword = $_POST["borderPassword"];

  setcookie("userName", $userName, time() + (86400 * 30), "/");
  setcookie("projectName", $projectName, time() + (86400 * 30), "/");
  setcookie("location", $location, time() + (86400 * 30), "/");
  setcookie("network", $network, time() + (86400 * 30), "/");
  setcookie("clientUsername", $clientUsername, time() + (86400 * 30), "/");
  setcookie("clientPassword", $clientPassword, time() + (86400 * 30), "/");
  setcookie("borderIp", $borderIp, time() + (86400 * 30), "/");
  setcookie("networkIp", $networkIp, time() + (86400 * 30), "/");
  setcookie("borderUsername", $borderUsername, time() + (86400 * 30), "/");
  setcookie("borderPassword", $borderPassword, time() + (86400 * 30), "/");
  if($location === "inplace")
    $clientIp = $systemIp;
  setcookie("clientIp", $clientIp, time() + (86400 * 30), "/");
  
  $userDir = $userName."_".$projectName."_load/";

  if(file_exists("projects/".$location."/" . $userDir))
  {
    $resp["message"] = "Project for this user already exist.\nDo you wish to use this or create your own?";
    $resp["statusFlag"] = "2";
    $serverResp = json_encode($resp);
    echo $serverResp;
    exit(1);
  }
  
  $conn = new mysqli($server, $user, $pass, $db);
  if($conn->connect_error)
  {
    $resp["message"]    = "Could not conect to database";
    $resp["statusFlag"] = "0";
    $serverResp = json_encode($resp);
    echo $serverResp;
    exit(1);
  }

  $sql = "show tables;";
  $result = $conn->query($sql);
  $flag = 0;
  $newTableName = str_replace(".", "_", $clientIp);

  if($result->num_rows > 0) 
  {
    while($row = $result->fetch_assoc()) 
    {
      if($row["Tables_in_loadtester"] === $newTableName)
      {
        $flag = 1;
        break;
      }
    }
  }

  if($flag == 0)
  {
    $sql = "create table " . $newTableName . "(port_number int primary key, user varchar(50), state varchar(100));"; 
    if($conn->query($sql) === FALSE)
    {
      $resp["message"]    = "Table create error: ".$conn->error;
      $resp["statusFlag"] = "0";
      $serverResp = json_encode($resp);
      echo $serverResp;
      $conn->close();
      exit(1);
    }

    $sql = "";
    for($i = 50000; $i < 60000; $i++)
    {
      $sql .= "insert into " . $newTableName . "(port_number, user, state) values(" . $i . ", '', '');";
    }

    if ($conn->multi_query($sql) === FALSE)
    {
      $resp["message"]    = "Data insert error: ".$conn->error;
      $resp["statusFlag"] = "0";
      $serverResp = json_encode($resp);
      echo $serverResp;
      $conn->close();
      exit(1);
    }

    $conn->close();
  }

  if($location === "external")
  {
    $sshClient = new Net_SSH2($clientIp);
    if (!$sshClient->login($clientUsername, $clientPassword)) 
    {
      $resp["message"]    = "Client connect failure";
      $resp["statusFlag"] = "0";
      $serverResp = json_encode($resp);
      echo $serverResp;
      exit(1);
    }

    $createDirCmd    = "mkdir /root/".$userDir."/";
    $shellCmdRes     = $sshClient->exec($createDirCmd);
    $chmodUserDirCmd = "chmod -R 777 /root/".$userDir."/";
    $shellCmdRes     = $sshClient->exec($chmodUserDirCmd);

    $files = array_diff(scandir("defaultScenarios"), array('.', '..'));
    foreach ($files as $f)
    {
      if(is_dir("defaultScenarios/".$f) === TRUE)
      {
        $createDirCmd    = "mkdir /root/".$userDir."/".$f;
        $shellCmdRes     = $sshClient->exec($createDirCmd);
        $chmodUserDirCmd = "chmod -R 777 /root/".$userDir."/".$f;
        $shellCmdRes     = $sshClient->exec($chmodUserDirCmd);
        $files2 = array_diff(scandir("defaultScenarios/".$f), array('.', '..'));
        foreach ($files2 as $f2)
        {
          $fileContent = file_get_contents('defaultScenarios/'.$f.'/'.$f2);
          $createScenarioCmd = "echo '".$fileContent."' > /root/".$userDir."/".$f."/".$f2;
          $shellCmdRes = $sshClient->exec($createScenarioCmd);
        }
      }
    }
  }
  else
  {
    $clientIp = $systemIp;
  }

  mkdir("projects/".$location."/".$userDir, 0777);
  $files = array_diff(scandir("defaultScenarios"), array('.', '..'));
  foreach ($files as $f)
  {
    if(is_dir("defaultScenarios/".$f) === TRUE)
    {
      mkdir("projects/".$location."/".$userDir."/".$f, 0777);
      $files2 = array_diff(scandir("defaultScenarios/".$f), array('.', '..'));
      foreach ($files2 as $f2)
      {
        copy('defaultScenarios/'.$f."/".$f2, 'projects/'.$location."/".$userDir."/".$f."/".$f2);
      }
    }
  }
  
  $serverResp = json_encode($resp);
  echo $serverResp;
?>