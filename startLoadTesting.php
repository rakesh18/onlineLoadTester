<?php
    session_start();

    include('dbConfig.php');
    include('Net/SSH2.php');

    $projectName = $_COOKIE["projectName"];
    $userName = $_COOKIE["userName"];
    $location = $_COOKIE["location"];
    $network        = $_COOKIE["network"];
    $clientIp = $_COOKIE["clientIp"];
    $clientUsername = $_COOKIE["clientUsername"];
    $clientPassword = $_COOKIE["clientPassword"];
    $borderIp       = $_COOKIE["borderIp"];
    $borderUsername       = $_COOKIE["borderUsername"];
    $borderPassword       = $_COOKIE["borderPassword"];
    $submenu = $_POST["SM"];
    $tableName = str_replace(".", "_", $clientIp);
    $uname = $userName."_".$projectName."_".$submenu."_".$tableName;
    $path = $userName."_".$projectName."_load/".$submenu."/";
    $resp = array("message" => "Load Started",
                  "statusFlag" => "1");
    $procId = "";
    $runFlag = 0;

    if($network === "ims")
    {
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
    }
    else
    {
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
    }
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

        $runCmd = "rm -f /root/".$path."/reg_scenario_*.csv";
        $shellCmdRes = $sshClient->exec($runCmd);

        $port = explode(";", file_get_contents("projects/external/".$path."/reg_user.csv"))[3];
        $runCmd = "/root/".$path."/./sipp ".$borderIp." -sf /root/".$path."/reg_scenario.xml -inf /root/".$path."/reg_user.csv -i ".$clientIp." -p ".$port." -r 5 -m 1 -trace_stat -trace_counts -fd 5 -bg";
        //echo $runCmd;exit(1);
        $shellCmdRes = $sshClient->exec($runCmd);
        //echo $shellCmdRes;exit(1);
        if(preg_match_all("/\[\w+.+\]/", $shellCmdRes, $matches))
        {
            $procId = substr($matches[0][0], 1, -1);
        }
        //echo $procId;exit(1);
        $chckProc = "ps -ef | grep ".$procId;
        $shellCmdRes = $sshClient->exec($chckProc);
        //echo $shellCmdRes;exit(1);
        if(substr_count($shellCmdRes, $procId) > 2)
        {
            $runFlag = 1;
            $resp["procId"] = $procId;
        }
    }
    else
    {
        $runCmd = "";
        $shellCmdRes = exec($runCmd);
        
        if(preg_match_all("/\[\w+.+\]/", $procId, $matches))
        {
            $procId = substr($matches[0][0], 1, -1);
        }
        $chckProc = "ps -ef | grep ".$procId;
        $shellCmdRes = exec($chckProc);
        if(substr_count($shellCmdRes, $procId) > 2)
        {
            $runFlag = 1; 
        }
    }
    if($runFlag == 1)
    {
        $conn = new mysqli($server, $user, $pass, $db);
        if($conn->connect_error)
        {
            $resp["message"]    = "Could not conect to database";
            $resp["statusFlag"] = "0";
            $killProc = "kill -9 ".$procId;
            $shellCmdRes = $sshClient->exec($killProc);
            echo $resp;
            exit(1);
        }

        $sql = "insert into load_status(user, status, proc_id) values('".$uname."', 'running', '".$procId."');";

        if ($conn->query($sql) === FALSE)
        {
            $resp["message"]    = "Data insert error: ".$conn->error;
            $resp["statusFlag"] = "0";
            $killProc = "kill -9 ".$procId;
            $shellCmdRes = $sshClient->exec($killProc);
            echo $resp;
            $conn->close();
            exit(1);
        }
        $conn->close();
    }
    else
    {
        $resp["statusFlag"] = "2";
        if($location === "external")
        {
            $userCsvCmd = "tail -1 /root/".$path."/reg_scenario_*_counts.csv";
            $shellCmdRes = $sshClient->exec($userCsvCmd);
            $res = explode(";", $shellCmdRes);
        }
        else
        {
            $res = explode(";", exec("tail -1 projects/inplace//".$path."/reg_scenario_*_counts.csv"));
        }
        $msgTags = explode(";", $msgTags);
        $msgTagsLen = sizeof($msgTags) - 1;
        $i = 2;
        for($j = 0; $j < $msgTagsLen; $j +=1)
        {
            $resp[$msgTags[$j]."_".$j] = $res[$i]."/".$res[$i + 3];
            if(is_numeric($msgTags[$j]))
            {
                $i = $i + 4;
            }
            else
            {
                $i = $i + 2;
            }
        }
    }

    $finalResp = json_encode($resp);
    echo $finalResp;
?>