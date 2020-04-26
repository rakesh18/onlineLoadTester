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
    $oscreenName    = $userName."_".$projectName."_".$submenu."_orig";
    $tscreenName    = $userName."_".$projectName."_".$submenu."_term";
    $uname          = $userName."_".$projectName."_".$submenu."_".$tableName;
    $path           = $userName."_".$projectName."/".$network."/".$submenu."/";
    $resp           = array("message" => "Load Started",
                            "statusFlag" => "1");
    $oprocId = "";
    $tprocId = "";
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
    }

    //Remove previous logs
    if($location === "external")
    {
        $runCmd = "rm -f /root/".$path."orig_*_*.csv";
        $shellCmdRes = $sshClient->exec($runCmd);
        $runCmd = "rm -f /root/".$path."term_*_*.csv";
        $shellCmdRes = $sshClient->exec($runCmd);
    }
    else
    {
        $runCmd = "rm -f projects/inplace/".$path."orig_*_*.csv";
        $shellCmdRes = exec($runCmd);
        $runCmd = "rm -f projects/inplace/".$path."term_*_*.csv";
        $shellCmdRes = exec($runCmd);
    }

    if($location === "external")
    {
        $checkProc = "";
        //Get orig & term local ports respectively
        $oport = explode(";", file_get_contents("projects/external/".$tableName."/".$path."orig_user.csv"))[3];
        $tport = explode(";", file_get_contents("projects/external/".$tableName."/".$path."term_user.csv"))[3];
        
        //Register Orig Client
        $runCmd = "/root/".$path."./sipp ".$borderIp." -sf /root/".$path."orig_reg.xml -inf /root/".$path."orig_user.csv -i ".$clientIp." -p ".$oport." -m 1 -nd -trace_error_codes -fd 1";
        $screenRunCmd = "screen -d -m -S ".$oscreenName." ".$runCmd;
        $shellCmdRes = $sshClient->exec($screenRunCmd);
        $procIdCmd = "pgrep -P `ps -ef | grep ".$oscreenName." | awk '{print $2}' | head -1`";
        $oprocId = $sshClient->exec($procIdCmd);
        if(strlen($oprocId) > 0)
        {
            $oprocId = str_replace("\n","",$oprocId);
            $oprocId = str_replace("\r","",$oprocId);
            $checkProc = $sshClient->exec("ps o pid= -p ".$oprocId);
            while(strlen($checkProc) > 1)
            {
                $checkProc = $sshClient->exec("ps o pid= -p ".$oprocId);
            }
        }
        $userCsvCmd = "tail -2 /root/".$path."orig_reg_*_error_codes.csv | head -1 | cut -d';' -f3";
        $stats = $sshClient->exec($userCsvCmd);
        if(strlen($stats) > 1)
        {
            $resp['message']    = "Originating user registration failure \n with error code: ".str_replace(",","",$stats);
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
        //Register Term Client
        $runCmd = "/root/".$path."./sipp ".$borderIp." -sf /root/".$path."term_reg.xml -inf /root/".$path."term_user.csv -i ".$clientIp." -p ".$tport." -m 1 -nd -trace_error_codes -fd 1";
        $screenRunCmd = "screen -d -m -S ".$tscreenName." ".$runCmd;
        $shellCmdRes = $sshClient->exec($screenRunCmd);
        $procIdCmd = "pgrep -P `ps -ef | grep ".$tscreenName." | awk '{print $2}' | head -1`";
        $tprocId = $sshClient->exec($procIdCmd);
        if(strlen($tprocId) > 0)
        {
            $tprocId = str_replace("\n","",$tprocId);
            $tprocId = str_replace("\r","",$tprocId);
            $checkProc = $sshClient->exec("ps o pid= -p ".$tprocId);
            while(strlen($checkProc) > 1)
            {
                $checkProc = $sshClient->exec("ps o pid= -p ".$tprocId);
            }
        }
        $userCsvCmd = "tail -2 /root/".$path."term_reg_*_error_codes.csv | head -1 | cut -d';' -f3";
        $stats = $sshClient->exec($userCsvCmd);
        if(strlen($stats) > 1)
        {
            $resp['message']    = "Terminating user registration failure \n with error code: ".str_replace(",","",$stats);
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }

        //Start Term Client
        $runCmd = "/root/".$path."./sipp ".$borderIp." -sf /root/".$path."term_scenario.xml -inf /root/".$path."term_user.csv -i ".$clientIp." -p ".$tport." -nd -watchdog_interval 86400000 -trace_counts -trace_error_codes -fd 1";
        $screenRunCmd = "screen -d -m -S ".$tscreenName." ".$runCmd;
        $shellCmdRes = $sshClient->exec($screenRunCmd);
        $procIdCmd = "pgrep -P `ps -ef | grep ".$tscreenName." | awk '{print $2}' | head -1`";
        $tprocId = $sshClient->exec($procIdCmd);
        if(strlen($tprocId) < 1)
        {
            $resp['message']    = "Terminating client failed to start.";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
        $tprocId = str_replace("\n","",$tprocId);
        $tprocId = str_replace("\r","",$tprocId);
        $ocheckProc = $sshClient->exec("ps o pid= -p ".$tprocId);
        if(strlen($ocheckProc) < 1)
        {
            $resp['message']    = "Terminating client failed to start.";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
        //Start Orig Client
        if($loadLimit > 0)
        {
            $runCmd = "/root/".$path."./sipp ".$borderIp." -sf /root/".$path."orig_scenario.xml -inf /root/".$path."orig_user.csv -i ".$clientIp." -p ".$oport." -r ".$loadRate." -m ".$loadLimit." -nd -watchdog_interval 86400000 -trace_stat -trace_counts -trace_error_codes -fd 1";
        }
        else
        {
            $runCmd = "/root/".$path."./sipp ".$borderIp." -sf /root/".$path."orig_scenario.xml -inf /root/".$path."orig_user.csv -i ".$clientIp." -p ".$oport." -r ".$loadRate." -nd -watchdog_interval 86400000 -trace_stat -trace_counts -trace_error_codes -fd 1";
        }
        $screenRunCmd = "screen -d -m -S ".$oscreenName." ".$runCmd;
        $shellCmdRes = $sshClient->exec($screenRunCmd);
        $procIdCmd = "pgrep -P `ps -ef | grep ".$oscreenName." | awk '{print $2}' | head -1`";
        $oprocId = $sshClient->exec($procIdCmd);
        if(strlen($oprocId) < 1)
        {
            $resp['message']    = "Originating client failed to start.";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
        $oprocId = str_replace("\n","",$oprocId);
        $oprocId = str_replace("\r","",$oprocId);
        $tcheckProc = $sshClient->exec("ps o pid= -p ".$oprocId);
        if(strlen($tcheckProc) > 1 &&
           strlen($ocheckProc) > 1)
        {
            $runFlag = 1;
            $resp["oprocId"] = $oprocId;
            $resp["tprocId"] = $tprocId;
        }
    }
    else
    {
        //Get orig & term local ports respectively
        $oport = explode(";", file_get_contents("projects/inplace/".$path."orig_user.csv"))[3];
        $tport = explode(";", file_get_contents("projects/inplace/".$path."term_user.csv"))[3];
        
        //Register Orig Client
        $runCmd = "projects/inplace/".$path."./sipp ".$borderIp." -sf /root/".$path."orig_reg.xml -inf /root/".$path."orig_user.csv -i ".$clientIp." -p ".$oport." -m 1 -nd -trace_error_codes -fd 1";
        $screenRunCmd = "screen -d -m -S ".$oscreenName." ".$runCmd;
        $shellCmdRes = exec($screenRunCmd);
        $procIdCmd = "pgrep -P `ps -ef | grep ".$oscreenName." | awk '{print $2}' | head -1`";
        $oprocId = exec($procIdCmd);
        if(strlen($oprocId) > 0)
        {
            $oprocId = str_replace("\n","",$oprocId);
            $oprocId = str_replace("\r","",$oprocId);
            $checkProc = $sshClient->exec("ps o pid= -p ".$oprocId);
            while(strlen($checkProc) > 1)
            {
                $checkProc = $sshClient->exec("ps o pid= -p ".$oprocId);
            }
        }
        $userCsvCmd = "tail -2 projects/inplace/".$path."orig_reg_*_error_codes.csv | head -1 | cut -d';' -f3";
        $stats = exec($userCsvCmd);
        if(strlen($stats) > 1)
        {
            $resp['message']    = "Originating user registration failure \n with error code: ".str_replace(",","",$stats);
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
        //Register Term Client
        $runCmd = "projects/inplace/".$path."./sipp ".$borderIp." -sf /root/".$path."term_reg.xml -inf /root/".$path."term_user.csv -i ".$clientIp." -p ".$tport." -m 1 -nd -trace_error_codes -fd 1";
        $screenRunCmd = "screen -d -m -S ".$tscreenName." ".$runCmd;
        $shellCmdRes = exec($screenRunCmd);
        $procIdCmd = "pgrep -P `ps -ef | grep ".$tscreenName." | awk '{print $2}' | head -1`";
        $tprocId = exec($procIdCmd);
        if(strlen($tprocId) > 0)
        {
            $tprocId = str_replace("\n","",$tprocId);
            $tprocId = str_replace("\r","",$tprocId);
            $checkProc = $sshClient->exec("ps o pid= -p ".$tprocId);
            while(strlen($checkProc) > 1)
            {
                $checkProc = $sshClient->exec("ps o pid= -p ".$tprocId);
            }
        }
        $userCsvCmd = "tail -2 projects/inplace/".$path."term_reg_*_error_codes.csv | head -1 | cut -d';' -f3";
        $stats = exec($userCsvCmd);
        if(strlen($stats) > 1)
        {
            $resp['message']    = "Terminating user registration failure \n with error code: ".str_replace(",","",$stats);
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
        //Start Term Client
        $runCmd = "projects/inplace/".$path."./sipp ".$borderIp." -sf /root/".$path."term_scenario.xml -inf /root/".$path."term_user.csv -i ".$clientIp." -p ".$tport." -nd -watchdog_interval 86400000 -trace_counts -trace_error_codes -fd 1";
        $screenRunCmd = "screen -d -m -S ".$tscreenName." ".$runCmd;
        $shellCmdRes = exec($screenRunCmd);
        $procIdCmd = "pgrep -P `ps -ef | grep ".$tscreenName." | awk '{print $2}' | head -1`";
        $tprocId = exec($procIdCmd);
        if(strlen($tprocId) < 1)
        {
            $resp['message']    = "Terminating client failed to start.";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
        $tprocId = str_replace("\n","",$tprocId);
        $tprocId = str_replace("\r","",$tprocId);
        $ocheckProc = exec("ps o pid= -p ".$tprocId);
        if(strlen($ocheckProc) < 1)
        {
            $resp['message']    = "Terminating client failed to start.";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
        //Start Orig Client
        if($loadLimit > 0)
        {
            $runCmd = "projects/inplace/".$path."./sipp ".$borderIp." -sf /root/".$path."orig_scenario.xml -inf /root/".$path."orig_user.csv -i ".$clientIp." -p ".$oport." -r ".$loadRate." -m ".$loadLimit." -nd -watchdog_interval 86400000 -trace_stat -trace_counts -trace_error_codes -fd 1";
        }
        else
        {
            $runCmd = "projects/inplace/".$path."./sipp ".$borderIp." -sf /root/".$path."orig_scenario.xml -inf /root/".$path."orig_user.csv -i ".$clientIp." -p ".$oport." -r ".$loadRate." -nd -watchdog_interval 86400000 -trace_stat -trace_counts -trace_error_codes -fd 1";
        }
        $screenRunCmd = "screen -d -m -S ".$oscreenName." ".$runCmd;
        $shellCmdRes = exec($screenRunCmd);
        $procIdCmd = "pgrep -P `ps -ef | grep ".$oscreenName." | awk '{print $2}' | head -1`";
        $oprocId = exec($procIdCmd);
        if(strlen($oprocId) < 1)
        {
            $resp['message']    = "Originating client failed to start.";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }
        $oprocId = str_replace("\n","",$oprocId);
        $oprocId = str_replace("\r","",$oprocId);
        $tcheckProc = exec("ps o pid= -p ".$oprocId);
        if(strlen($tcheckProc) > 1 &&
           strlen($ocheckProc) > 1)
        {
            $runFlag = 1;
            $resp["oprocId"] = $oprocId;
            $resp["tprocId"] = $tprocId;
        }
    }

    $mts = explode("_", $msgTags);
    if($runFlag == 1)
    {
        $conn = new mysqli($server, $user, $pass, $db);
        if($conn->connect_error)
        {
            $resp["message"]    = "Could not conect to database";
            $resp["statusFlag"] = "0";
            if($location === "external")
            {
                $killProc = "kill -9 ".$oprocId." ".$tprocId." screen -wipe";
                $shellCmdRes = $sshClient->exec($killProc);
            }
            else
            {
                $killProc = "kill -9 ".$oprocId." ".$tprocId." screen -wipe";
                $shellCmdRes = exec($killProc);
            }
            echo json_encode($resp);
            exit(1);
        }

        $sql = "insert into load_status(user, status, start_time, orig_proc_id, term_proc_id, orig_msg_tags, term_msg_tags) values('".$uname."', 'running', '".$startTime."', '".$oprocId."', '".$tprocId."', '".$mts[0]."', '".$mts[1]."');";

        if ($conn->query($sql) === FALSE)
        {
            $resp["message"]    = "Data insert error: ".$conn->error;
            $resp["statusFlag"] = "0";
            if($location === "external")
            {
                $killProc = "kill -9 ".$oprocId." ".$tprocId." screen -wipe";
                $shellCmdRes = $sshClient->exec($killProc);
            }
            else
            {
                $killProc = "kill -9 ".$oprocId." ".$tprocId." screen -wipe";
                $shellCmdRes = exec($killProc);
            }
            echo json_encode($resp);
            $conn->close();
            exit(1);
        }
        $conn->close();
    }
    else
    {
        $resp["statusFlag"] = "2";
        $ostats = "";
        $tstats = "";
        if($location === "external")
        {
            $killProc = "kill -9 ".$oprocId." ".$tprocId." screen -wipe";
            $shellCmdRes = $sshClient->exec($killProc);
            $statCmd = "tail -1 /root/".$path."orig_scenario_*_.csv";
            $stats = $sshClient->exec($statCmd);
            $stats = explode(";", $stats);
            $resp["totCalls"] = $stats[12];
            $resp["sucCalls"] = $stats[15];
            $resp["fldCalls"] = $stats[17];

            $userCsvCmd = "tail -1 /root/".$path."orig_scenario_*_counts.csv";
            $ostats = $sshClient->exec($userCsvCmd);
            $ostats = explode(";", $ostats);
            $userCsvCmd = "tail -1 /root/".$path."term_scenario_*_counts.csv";
            $tstats = $sshClient->exec($userCsvCmd);
            $tstats = explode(";", $tstats);
        }
        else
        {
            $killProc = "kill -9 ".$oprocId." ".$tprocId." screen -wipe";
            $shellCmdRes = exec($killProc);
            $statCmd = "tail -1 projects/inplace/".$path."orig_scenario_*_.csv";
            $stats = exec($statCmd);
            $stats = explode(";", $stats);
            $resp["totCalls"] = $stats[12];
            $resp["sucCalls"] = $stats[15];
            $resp["fldCalls"] = $stats[17];

            $userCsvCmd = "tail -1 projects/inplace/".$path."orig_scenario_*_counts.csv";
            $ostats = exec($userCsvCmd);
            $ostats = explode(";", $ostats);
            $userCsvCmd = "tail -1 projects/inplace/".$path."term_scenario_*_counts.csv";
            $tstats = exec($userCsvCmd);
            $tstats = explode(";", $tstats);
        }
        $k = 0;
        $msgTags = explode(";", $mts[0]);
        $msgTagsLen = sizeof($msgTags) - 1;
        $i = 2;
        for($j = 0; $j < $msgTagsLen; $j +=1, $k += 1)
        {
            if($msgTags[$j][0] === "I")
            {
                $resp[$msgTags[$j]."_".$k] = $ostats[$i]."/".$ostats[$i + 3];
                $i = $i + 4;
            }
            else if($msgTags[$j][0] === "O")
            {
                $resp[$msgTags[$j]."_".$k] = $ostats[$i];
                $i = $i + 2;
            }
            else if($msgTags[$j][0] === "N")
            {
                $i = $i + 2;
            }
            else if($msgTags[$j][0] === "P")
            {
                $i = $i + 2;
                $k -= 1;
            }
        }
        $msgTags = explode(";", $mts[0]);
        $msgTagsLen = sizeof($msgTags) - 1;
        $i = 2;
        for($j = 0; $j < $msgTagsLen; $j +=1, $k += 1)
        {
            if($msgTags[$j][0] === "I")
            {
                $resp[$msgTags[$j]."_".$k] = $tstats[$i]."/".$tstats[$i + 3];
                $i = $i + 4;
            }
            else if($msgTags[$j][0] === "O")
            {
                $resp[$msgTags[$j]."_".$k] = $tstats[$i];
                $i = $i + 2;
            }
            else if($msgTags[$j][0] === "N")
            {
                $i = $i + 2;
            }
            else if($msgTags[$j][0] === "P")
            {
                $i = $i + 2;
                $k -= 1;
            }
        }
    }

    echo json_encode($resp);
?>