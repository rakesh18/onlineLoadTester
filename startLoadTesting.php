<?php
    session_start();

    include('dbConfig.php');
    include('Net/SSH2.php');

    $projectName = $_COOKIE["projectName"];
    $userName = $_COOKIE["userName"];
    $network        = $_COOKIE["network"];
    $clientIp = $_COOKIE["clientIp"];
    $borderIp       = $_COOKIE["borderIp"];
    $borderUsername       = $_COOKIE["borderUsername"];
    $borderPassword       = $_COOKIE["borderPassword"];
    $submenu = $_POST["SM"];
    $tableName = str_replace(".", "_", $clientIp);
    $uname = $userName."_".$projectName."_".$submenu."_".$tableName;
    $resp = array("message" => "Load Started",
                  "statusFlag" => "1");

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
    //Go to the client side to run and then when its done successfully update the table
    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error)
    {
        $resp["message"]    = "Could not conect to database";
        $resp["statusFlag"] = "0";
        echo $resp;
        exit(1);
    }

    $sql = "insert into load_status(user, status) values('".$uname."', 'running');";

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