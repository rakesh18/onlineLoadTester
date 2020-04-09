<?php
    session_start();

    include('Net/SSH2.php');

    $users = $_POST['U'];
    $pass  = $_POST['P'];
    $nickname = $_COOKIE["nickname"];
    $clientIp = $_COOKIE["EIP"];
    $clientUser = $_COOKIE["EIPU"];
    $clientPassword = $_COOKIE["EIPP"];
    $location = $_COOKIE["LOC"];
    $sbcIp = $_COOKIE["SBCIP"];

    $userDir = $nickname."_loadtester/";

    $userList = explode(";", $users);
    $passList = explode(";", $pass);

    if($location === "ext")
    {    
      $sshClient = new Net_SSH2($clientIp);
      if (!$sshClient->login($clientUser, $clientPassword)) 
      {
        echo "Login Failed";
        exit('Login Failed');
      }
    
      /*
       * Removing if the directory already esists.
       */
      $dataStart = "SEQUENTIAL";
      $userCsvCmd = "echo '".$dataStart."' > /root/".$userDir."userForReg.csv";
      $shellCmdRes = $sshClient->exec($userCsvCmd);

      $len = sizeof($userList);
      for($i = 0;$i < $len;$i += 2)
      {
        $j = $i;
        $k = $i + 1;
        $data = "".$userList[$j].";[authentication username=".$userList[$j]." password=".$passList[$j]."];".$clientIp.";5080;".$sbcIp.";".$userList[$k];
        $userCsvCmd = "echo '".$data."' >> /root/".$userDir."userForReg.csv";
        $shellCmdRes = $sshClient->exec($userCsvCmd);
        $data = "".$userList[$k].";[authentication username=".$userList[$k]." password=".$passList[$k]."];".$clientIp.";5080;".$sbcIp.";".$userList[$j];
        $userCsvCmd = "echo '".$data."' >> /root/".$userDir."userForReg.csv";
        $shellCmdRes = $sshClient->exec($userCsvCmd);
      }
      
      echo "CSV created successfully";
    }
?>