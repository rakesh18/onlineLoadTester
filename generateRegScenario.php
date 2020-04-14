<?php
  session_start();

  include('Net/SSH2.php');

  $scenario          = $_POST['S'];
  $projectName       = $_COOKIE["projectName"];
  $userName          = $_COOKIE["userName"];
  $clientIp          = $_COOKIE["clientIp"];
  $clientUsername    = $_COOKIE["clientUsername"];
  $clientPassword    = $_COOKIE["clientPassword"];
  $location          = $_COOKIE["location"];
  $resp               = array("statusFlag" => "1", 
                              "message" => "Scenario generated successfully");

  $userDir = $userName."_".$projectName."_load/reg/";

  $userFile = fopen("projects/".$location."/".$userDir . "reg_scenario.xml", "w") or die("Unable to open file!");

  if($location === "external")
  {    
    $sshClient = new Net_SSH2($clientIp);
    if (!$sshClient->login($clientUsername, $clientPassword)) 
    {
      $resp["message"]    = "Login Failed to Client";
      $resp["statusFlag"] = "0";
      $serverResp = json_encode($resp);
      echo $serverResp;
      exit(1);
    }

    $userCsvCmd = "echo '".$scenario."' > /root/".$userDir."reg_scenario.xml";
    $shellCmdRes = $sshClient->exec($userCsvCmd);
  }

  fwrite($userFile, $scenario);
  fclose($userFile);

  $serverResp = json_encode($resp);
  echo $serverResp;
?>