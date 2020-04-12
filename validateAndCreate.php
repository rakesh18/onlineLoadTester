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
  $resp->statusFlag  = "1";
  $resp->message     = "Success";

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

  if(!isset($_COOKIE["userName"]))
    setcookie("userName", $userName, time() + (86400 * 30), "/");
  if(!isset($_COOKIE["projectName"]))
    setcookie("projectName", $projectName, time() + (86400 * 30), "/");
  if(!isset($_COOKIE["location"]))
    setcookie("location", $location, time() + (86400 * 30), "/");
  if(!isset($_COOKIE["network"]))
    setcookie("network", $network, time() + (86400 * 30), "/");
  if(!isset($_COOKIE["clientUsername"]))
    setcookie("clientUsername", $clientUsername, time() + (86400 * 30), "/");
  if(!isset($_COOKIE["clientPassword"]))
    setcookie("clientPassword", $clientPassword, time() + (86400 * 30), "/");
  if(!isset($_COOKIE["borderIp"]))
    setcookie("borderIp", $borderIp, time() + (86400 * 30), "/");
  if(!isset($_COOKIE["networkIp"]))
    setcookie("networkIp", $networkIp, time() + (86400 * 30), "/");
  if(!isset($_COOKIE["borderUsername"]))
    setcookie("borderUsername", $borderUsername, time() + (86400 * 30), "/");
  if(!isset($_COOKIE["borderPassword"]))
    setcookie("borderPassword", $borderPassword, time() + (86400 * 30), "/");

  $userDir = $uerName."_".$projectName."_load/";

  if($network === "ims")
  {
    /*
    $sshPCSCF = new Net_SSH2($borderIp);
    if (!$sshPCSCF->login($borderUsername, $borderPassword)) 
    {
      $resp->message    = "Server connect failure";
      $resp->statusFlag = "0";
      echo $resp;
      exit(1);
    }

    $procIdCmd = "ps -ef | grep cdotpsi";
    $shellCmdRes = $sshPCSCF->exec($procIdCmd);
    
    if(strpos($shellCmdRes, "cdotpsi") === FALSE ||
      strpos($shellCmdRes, "locationalias") === FALSE)
    {
      $resp->message    = "Border server not running. Please contact the server team for the issue.";
      $resp->statusFlag = "0";
      echo $resp;
      exit(1);
    }
    */
  }
  else
  {
    /*
    $sshSBC = new Net_SSH2($borderIp);
    if (!$sshSBC->login($borderUsername, $borderPassword)) 
    {
      $resp->message    = "Server connect failure";
      $resp->statusFlag = "0";
      echo $resp;
      exit(1);
    }

    $procIdCmd = "ps -ef | grep cdotsi";
    $shellCmdRes = $sshSBC->exec($procIdCmd);
    
    if(strpos($shellCmdRes, "cdotsi") === FALSE ||
      strpos($shellCmdRes, "locationalias") === FALSE)
    {
      $resp->message    = "Border server not running. Please contact the server team for the issue.";
      $resp->statusFlag = "0";
      echo $resp;
      exit(1);
    }
    */
  }
  
  if($location === "external")
  {
    /*
    $sshClient = new Net_SSH2($clientIp);
    if (!$sshClient->login($clientUsername, $clientPassword)) 
    {
      $resp->message    = "Server connect failure";
      $resp->statusFlag = "0";
      echo $resp;
      exit(1);
    }

    $createDirCmd    = "mkdir /root/".$userDir."/";
    $shellCmdRes     = $sshClient->exec($createDirCmd);
    $chmodUserDirCmd = "chmod -R 777 /root/".$userDir."/";
    $shellCmdRes     = $sshClient->exec($chmodUserDirCmd);

    $files = array_diff(scandir("defaultScenarios"), array('.', '..'));
    foreach ($files as $f)
    {
      $createScenarioCmd = "touch /root/" . $userDir . $f";
      $shellCmdRes = $sshClient->exec($createScenarioCmd);
    }
    */
  }
  else
  {
    $clientIp = $systemIp;
  }
  if(!isset($_COOKIE["clientIp"]))
    setcookie("clientIp", $clientIp, time() + (86400 * 30), "/");

  $conn = new mysqli($server, $user, $pass, $db);
  if($conn->connect_error)
  {
    $resp->message    = "Could not conect to database";
    $resp->statusFlag = "0";
    echo $resp;
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
      $resp->message    = "Table create error: ".$conn->error;
      $resp->statusFlag = "0";
      echo $resp;
      $conn->close();
      exit(1);
    }

    $sql = "";
    for($i = 6000; $i < 10000; $i++)
    {
      $sql .= "insert into " . $newTableName . "(port_number, user, state) values(" . $i . ", '', '');";
    }

    if ($conn->multi_query($sql) === FALSE)
    {
      $resp->message    = "Data insert error: ".$conn->error;
      $resp->statusFlag = "0";
      echo $resp;
      $conn->close();
      exit(1);
    }

    $conn->close();
  }
  
  if(!file_exists("projects/" . $userDir))
  {
    mkdir("projects/".$userDir, 0777);
    $files = array_diff(scandir("defaultScenarios"), array('.', '..'));
    foreach ($files as $f)
    {
      copy('defaultScenarios/'.$f, 'projects/'.$userDir.$f);
    }
  }  
  
  echo $resp;
?>