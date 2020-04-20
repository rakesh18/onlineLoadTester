<?php
  session_start();

  include('globals.php');
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
  $sshClient         = "";
  $makeDir           = 0;
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

  if($location === "inplace")
    $clientIp = $systemIp;
  
  $userDir = $userName."_".$projectName;

  if($location === "external")
  {
    $try = 2;
    $sshClient = new Net_SSH2($clientIp);
    while($try > 0 &&
          !($res = $sshClient->login($clientUsername, $clientPassword)))
    {
      $try = $try - 1;
    }
    if (!$res) 
    {
      $resp["message"]    = "Client is not in network.\nTry again later.";
      $resp["statusFlag"] = "0";
      $serverResp = json_encode($resp);
      echo $serverResp;
      exit(1);
    }

    $chckDir = "ls -R /root/".$userDir."/ | wc -l | tail -1 | awk '{print $1}'";
    $res = $sshClient->exec($chckDir);
    if($res < 120)
    {
      $makeDir = 1;
    }
  }

  if(file_exists("projects/".$location."/".$userDir) && 
     $location === "inplace")
  {
    $resp["message"] = "User with this project already exist.\nDo you wish to use this or create a new one?";
    $resp["statusFlag"] = "2";
    $serverResp = json_encode($resp);
    echo $serverResp;
    exit(1);
  }
  else if(file_exists("projects/".$location."/".$userDir) &&
          $location === "external" &&
          $makeDir == 0)
  {
    $resp["message"] = "User with this project already exist.\nDo you wish to use this or create a new one?";
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

  if($location === "external" &&
     $makeDir == 1)
  {
    $shellCmdRes = $sshClient->exec($createScenarioCmd);
    $subnetwork = array_diff(scandir("defaultScenarios"), array('.', '..'));
    foreach ($subnetwork as $sn)
    {
      if(is_dir("defaultScenarios/".$sn) === TRUE)
      {
        $subtestcases = array_diff(scandir("defaultScenarios/".$sn), array('.', '..'));
        foreach ($subtestcases as $stc)
        {
          if(is_dir("defaultScenarios/".$sn."/".$stc) === TRUE)
          {
            $dirName      = "/root/".$userDir."/".$sn."/".$stc;
            $createDirCmd = "mkdir -p ".$dirName;
            $shellCmdRes  = $sshClient->exec($createDirCmd);
            $files        = array_diff(scandir("defaultScenarios/".$sn."/".$stc), array('.', '..'));
            foreach ($files as $f)
            {
              $fileContent       = file_get_contents("defaultScenarios/".$sn."/".$stc."/".$f);
              $createScenarioCmd = "echo '".$fileContent."' > ".$dirName."/".$f;
              $shellCmdRes       = $sshClient->exec($createScenarioCmd);
              copy('defaultScenarios/'.$sn."/".$stc."/".$f, 
                   'projects/'.$location."/".$userDir."/".$sn."/".$stc."/".$f);
            }
          }
        }
      }
    }
    $chmodCmd    = "chmod -R 777 /root/".$userDir;
    $shellCmdRes = $sshClient->exec($chmodCmd);
  }
  else
  {
    mkdir("projects/".$location."/".$userDir, 0777);
    $subnetwork = array_diff(scandir("defaultScenarios"), array('.', '..'));
    foreach ($subnetwork as $sn)
    {
      if(is_dir("defaultScenarios/".$sn) === TRUE)
      {
        mkdir("projects/".$location."/".$userDir."/".$sn, 0777);
        $subtestcases = array_diff(scandir("defaultScenarios/".$sn), array('.', '..'));
        foreach ($subtestcases as $stc)
        {
          if(is_dir("defaultScenarios/".$sn."/".$stc) === TRUE)
          {
            mkdir("projects/".$location."/".$userDir."/".$sn."/".$stc, 0777);
            $files = array_diff(scandir("defaultScenarios/".$sn."/".$stc), array('.', '..'));
            foreach ($files as $f)
            {
              copy('defaultScenarios/'.$sn."/".$stc."/".$f, 
                  'projects/'.$location."/".$userDir."/".$sn."/".$stc."/".$f);
            }
          }
        }
      }
    }
    exec("chmod -R 777 "."projects/".$location."/".$userDir);
  }
  
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
  setcookie("clientIp", $clientIp, time() + (86400 * 30), "/");

  echo json_encode($resp);
?>