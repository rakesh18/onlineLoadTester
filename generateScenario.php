<?php
  session_start();

  include('Net/SSH2.php');

  $scenario          = $_POST['S'];
  $submenu           = $_POST['SM'];
  $endpoint          = $_POST['EP'];
  $mark              = $_POST['M'];
  $projectName       = $_COOKIE["projectName"];
  $userName          = $_COOKIE["userName"];
  $clientIp          = $_COOKIE["clientIp"];
  $clientUsername    = $_COOKIE["clientUsername"];
  $clientPassword    = $_COOKIE["clientPassword"];
  $location          = $_COOKIE["location"];
  $network           = $_COOKIE['network'];
  $resp               = array("statusFlag" => "1", 
                              "message" => "Scenario generated successfully");

  $tableName = str_replace(".", "_", $clientIp);
  $userDir   = $userName."_".$projectName."/".$network."/".$submenu."/";

  if($submenu === "reg" ||
     $submenu === "imsreg" ||
     $submenu === "ltereg")
  {
    $userFile = fopen("projects/".$location."/".$tableName."/".$userDir.$endpoint."_reg.xml", "w") or die("Unable to open file!");
    $userEndFile = "/root/".$userDir.$endpoint."_reg.xml";
  }
  else
  {
    if($mark === "R")
    {
      $userFile = fopen("projects/".$location."/".$tableName."/".$userDir.$endpoint."_reg.xml", "w") or die("Unable to open file!");
      $userEndFile = "/root/".$userDir.$endpoint."_scenario.xml";
    }
    else
    {
      $userFile = fopen("projects/".$location."/".$tableName."/".$userDir.$endpoint."_scenario.xml", "w") or die("Unable to open file!");
      $userEndFile = "/root/".$userDir.$endpoint."_scenario.xml";
    }
  }

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

    $userCsvCmd = "echo '".$scenario."' > ".$userEndFile;
    $shellCmdRes = $sshClient->exec($userCsvCmd);
  }

  fwrite($userFile, $scenario);
  fclose($userFile);

  echo json_encode($resp);
?>