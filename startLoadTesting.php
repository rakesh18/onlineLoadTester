<?php
    session_start();

    include('dbConfig.php');
    include('Net/SSH2.php');

    $submenu        = $_POST["SM"];
    $loadRate       = $_POST["LR"];
    $loadLimit      = $_POST["LL"];
    $startTime      = $_POST["ST"];
    $msgTags        = $_POST['MT'];
    $projectName    = $_COOKIE["projectName"];
    $userName       = $_COOKIE["userName"];
    $location       = $_COOKIE["location"];
    $network        = $_COOKIE["network"];
    $clientIp       = $_COOKIE["clientIp"];
    $clientUsername = $_COOKIE["clientUsername"];
    $clientPassword = $_COOKIE["clientPassword"];
    $borderIp       = $_COOKIE["borderIp"];
    $borderUsername = $_COOKIE["borderUsername"];
    $borderPassword = $_COOKIE["borderPassword"];
    $tableName      = str_replace(".", "_", $clientIp);
    $screenName     = $userName."_".$projectName."_".$submenu;
    $uname          = $userName."_".$projectName."_".$submenu."_".$tableName;
    $path           = $userName."_".$projectName."_load/".$submenu."/";
    $resp           = array("message" => "Load Started",
                            "statusFlag" => "1");
    $procId  = "";
    $runFlag = 0;

    if($network === "ims")
    {
        $sshPCSCF = new Net_SSH2($borderIp);
        if (!$sshPCSCF->login($borderUsername, $borderPassword)) 
        {
            $resp['message']    = "Server connect failure";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }

        $checkProc = $sshPCSCF->exec("ps o pid= -C cdotpsi");
        
        if(strlen($checkProc) < 2)
        {
            $resp['message']    = "Border server not running. Please contact the server team for the issue.";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
    }
    else
    {
        $sshSBC = new Net_SSH2($borderIp);
        if (!$sshSBC->login($borderUsername, $borderPassword)) 
        {
            $resp['message']    = "Server connect failure";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }

        $checkProc = $sshSBC->exec("ps o pid= -C cdotsi");
        
        if(strlen($checkProc) < 2)
        {
            $resp['message']    = "Border server not running. Please contact the server team for the issue.";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
    }
    if($location === "external")
    {
        $sshClient = new Net_SSH2($clientIp);
        if (!$sshClient->login($clientUsername, $clientPassword)) 
        {
            $resp['message']    = "Client connect failure";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }

        $runCmd = "rm -f /root/".$path.$submenu."_scenario_*.csv";
        $shellCmdRes = $sshClient->exec($runCmd);

        $port = explode(";", file_get_contents("projects/external/".$path.$submenu."_user.csv"))[3];
        if($loadLimit > 0)
        {
            $runCmd = "/root/".$path."./sipp ".$borderIp." -sf /root/".$path.$submenu."_scenario.xml -inf /root/".$path.$submenu."_user.csv -i ".$clientIp." -p ".$port." -r ".$loadRate." -m ".$loadLimit." -nd -watchdog_interval 86400000 -trace_stat -trace_counts -trace_error_codes -fd 1";
        }
        else
        {
            $runCmd = "/root/".$path."./sipp ".$borderIp." -sf /root/".$path.$submenu."_scenario.xml -inf /root/".$path.$submenu."_user.csv -i ".$clientIp." -p ".$port." -r ".$loadRate." -nd -watchdog_interval 86400000 -trace_stat -trace_counts -trace_error_codes -fd 1";
        }
        $screenRunCmd = "screen -d -m -S ".$screenName." ".$runCmd;
        $shellCmdRes = $sshClient->exec($screenRunCmd);
        $procIdCmd = "pgrep -P `ps -ef | grep ".$screenName." | awk '{print $2}' | head -1`";
        $procId = $sshClient->exec($procIdCmd);
        $procId = str_replace("\n","",$procId);
        $procId = str_replace("\r","",$procId);

        /*
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
        */
        $checkProc = $sshClient->exec("ps o pid= -p ".$procId);
        if(strlen($checkProc) > 1)
        {
            $runFlag = 1;
            $resp["procId"] = $procId;
        }
    }
    else
    {
        
    }
    if($runFlag == 1)
    {
        $conn = new mysqli($server, $user, $pass, $db);
        if($conn->connect_error)
        {
            $resp["message"]    = "Could not conect to database";
            $resp["statusFlag"] = "0";
            $killProc = "kill -9 ".$procId." screen -wipe";
            $shellCmdRes = $sshClient->exec($killProc);
            echo json_encode($resp);
            exit(1);
        }

        $sql = "insert into load_status(user, status, proc_id, start_time, msg_tags) values('".$uname."', 'running', '".$procId."', '".$startTime."', '".$msgTags."');";

        if ($conn->query($sql) === FALSE)
        {
            $resp["message"]    = "Data insert error: ".$conn->error;
            $resp["statusFlag"] = "0";
            $killProc = "kill -9 ".$procId." screen -wipe";
            $shellCmdRes = $sshClient->exec($killProc);
            echo json_encode($resp);
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
            $statCmd = "tail -1 /root/".$path.$submenu."_scenario_*_.csv";
            $stats = $sshClient->exec($statCmd);
            $stats = explode(";", $stats);
            $resp["totCalls"] = $stats[12];
            $resp["sucCalls"] = $stats[15];
            $resp["fldCalls"] = $stats[17];

            $userCsvCmd = "tail -1 /root/".$path.$submenu."_scenario_*_counts.csv";
            $stats = $sshClient->exec($userCsvCmd);
            $stats = explode(";", $stats);
        }
        else
        {
            $stats = explode(";", exec("tail -1 projects/inplace/".$path."/reg_scenario_*_counts.csv"));
        }
        $msgTags = explode(";", $msgTags);
        $msgTagsLen = sizeof($msgTags) - 1;
        $i = 2;
        for($j = 0; $j < $msgTagsLen; $j +=1)
        {
            if(is_numeric($msgTags[$j]))
            {
                $resp[$msgTags[$j]."_".$j] = $stats[$i]."/".$stats[$i + 3];
                $i = $i + 4;
            }
            else
            {
                $resp[$msgTags[$j]."_".$j] = $stats[$i];
                $i = $i + 2;
            }
        }
    }

    echo json_encode($resp);
?>