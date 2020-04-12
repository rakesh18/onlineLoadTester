<?php
  session_start();

  include('Net/SSH2.php');

  $scenario       = $_POST['S'];
  $projname       = $_COOKIE["projctName"];
  $uname          = $_COOKIE["userName"];
  $clientIp       = $_COOKIE["EIP"];
  $clientUser     = $_COOKIE["EIPU"];
  $clientPassword = $_COOKIE["EIPP"];
  $location       = $_COOKIE["LOC"];

  $userDir = $uname."_".$projname."_load/";

  $userFile = fopen("projects/".$userDir . "reg_scenario.xml", "w") or die("Unable to open file!");

  //if($location === "ext")
  {    
    /*
    $sshClient = new Net_SSH2($clientIp);
    if (!$sshClient->login($clientUser, $clientPassword)) 
    {
      echo "Login Failed";
      exit('Login Failed');
    }

    $userCsvCmd = "echo '".$userList."' > /root/".$userDir."userForReg.csv";
    $shellCmdRes = $sshClient->exec($userCsvCmd);
    */
    fwrite($userFile, $scenario);
    fclose($userFile);
    
    echo "Scenario created successfully";
  }
  /*
  else
  {
    fwrite($userFile, $scenario);
    fclose($userFile);
    
    echo "CSV created successfully";
  }
  */
?>