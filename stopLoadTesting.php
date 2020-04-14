<?php
    session_start();

    include('dbConfig.php');
    include('Net/SSH2.php');

    $projectName = $_COOKIE["projectName"];
    $userName = $_COOKIE["userName"];
    $clientIp = $_COOKIE["clientIp"];
    $clientUsername = $_COOKIE["clientUsername"];
    $clientPassword = $_COOKIE["clientPassword"];
    $network = $_COOKIE["network"];
    $location = $_COOKIE["location"];
    $procId = $_POST['PID'];
    $tableName = str_replace(".", "_", $clientIp);
    $submenu = $_POST["SM"];
    $uname = $userName."_".$projectName."_".$submenu."_".$tableName;
    $path = $userName."_".$projectName."_load/".$submenu."/";
    $resp = array("message" => "Load Stopped",
                  "statusFlag" => "1");

    if($location === "external")
    {
        $sshClient = new Net_SSH2($clientIp);
        if (!$sshClient->login($clientUsername, $clientPassword)) 
        {
            $resp->message    = "Server connect failure";
            $resp->statusFlag = "0";
            echo $resp;
            exit(1);
        }

        $stopCmd = "kill -9 ".$procId;
        $shellCmdRes = $sshClient->exec($stopCmd);
    }
    else
    {
        $stopCmd = "kill -9 ".$procId;
        $shellCmdRes = exec($stopCmd);
    }
    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error)
    {
        $resp["message"]    = "Could not conect to database";
        $resp["statusFlag"] = "0";
        echo $resp;
        exit(1);
    }

    $sql = "delete from load_status where user = '".$uname."';";

    if ($conn->multi_query($sql) === FALSE)
    {
      $resp["message"]    = "Data insert error: ".$conn->error;
      $resp["statusFlag"] = "0";
      echo $resp;
      $conn->close();
      exit(1);
    }
    $conn->close();

    $finalResp = json_encode($resp);
    echo $finalResp;
?>