<?php
    session_start();
    include('dbConfig.php');
    include('Net/SSH2.php');

    $oprocessId        = $_POST['OPID'];
    $tprocessId        = $_POST['TPID'];
    $submenu           = $_POST['SM'];
    $msgTags           = $_POST['MT'];
    $projectName       = $_COOKIE["projectName"];
    $userName          = $_COOKIE["userName"];
    $clientIp          = $_COOKIE["clientIp"];
    $clientUsername    = $_COOKIE["clientUsername"];
    $clientPassword    = $_COOKIE["clientPassword"];
    $location          = $_COOKIE["location"];
    $network           = $_COOKIE['network'];
    $origClientStats   = "";
    $termClientStats   = "";
    $origRes           = "";
    $termRes           = "";
    $resp              = array();
    
    $path = $userName."_".$projectName."/".$network."/".$submenu."/";

    if($submenu === "reg" ||
       $submenu === "imsreg" ||
       $submenu === "ltereg")
    {
        $origClientStats = $path."orig_reg_*_counts.csv";
    }
    else
    {
        $origClientStats = $path."orig_scenario_*_counts.csv";
        $termClientStats = $path."term_scenario_*_counts.csv";
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

        if($submenu === "reg" ||
           $submenu === "imsreg" ||
           $submenu === "ltereg")
        {
            $checkProc = $sshClient->exec("ps o pid= -p ".$oprocessId);
            if(strlen($checkProc) > 1)
            {
                $resp["statusFlag"] = "ON"; 
            }
            else
            {
                $resp["statusFlag"] = "OFF";  
                $killProc = "kill -9 ".$oprocessId." screen -wipe";
                $shellCmdRes = $sshClient->exec($killProc);
            }
            $userCsvCmd = "tail -1 /root/".$origClientStats;
            $shellCmdRes = $sshClient->exec($userCsvCmd);
            $origRes = explode(";", $shellCmdRes);
        }
        else
        {
            $ocheckProc = $sshClient->exec("ps o pid= -p ".$oprocessId);
            $tcheckProc = $sshClient->exec("ps o pid= -p ".$tprocessId);
            if(strlen($ocheckProc) > 1 &&
               strlen($tcheckProc) > 1)
            {
                $resp["statusFlag"] = "ON"; 
            }
            else
            {
                $resp["statusFlag"] = "OFF";  
                sleep(2);
                $killProc = "kill -9 ".$oprocessId." ".$tprocessId." screen -wipe";
                $shellCmdRes = $sshClient->exec($killProc);
            }
            $userCsvCmd = "tail -1 /root/".$origClientStats;
            $shellCmdRes = $sshClient->exec($userCsvCmd);
            $origRes = explode(";", $shellCmdRes);
            $userCsvCmd = "tail -1 /root/".$termClientStats;
            $shellCmdRes = $sshClient->exec($userCsvCmd);
            $termRes = explode(";", $shellCmdRes);
        }
    }
    else
    {
        if($submenu === "reg" ||
           $submenu === "imsreg" ||
           $submenu === "ltereg")
        {
            $checkProc = exec("ps o pid= -p ".$oprocessId);
            if(strlen($checkProc) > 1)
            {
                $resp["statusFlag"] = "ON"; 
            }
            else
            {
                $resp["statusFlag"] = "OFF";  
                $shellCmdRes = $sshClient->exec($killProc);
            }
            $userCsvCmd = "tail -1 projects/inplace/".$origClientStats;
            $shellCmdRes = exec($userCsvCmd);
            $origRes = explode(";", $shellCmdRes);
        }
        else
        {
            $ocheckProc = exec("ps o pid= -p ".$oprocessId);
            $tcheckProc = exec("ps o pid= -p ".$tprocessId);
            if(strlen($coheckProc) > 1 &&
               strlen($tcheckProc) > 1)
            {
                $resp["statusFlag"] = "ON"; 
            }
            else
            {
                $resp["statusFlag"] = "OFF"; 
                sleep(2);
                $killProc = "kill -9 ".$oprocessId." ".$tprocessId." screen -wipe";
                $shellCmdRes = $sshClient->exec($killProc);
            }
            $userCsvCmd = "tail -1 projects/inplace/".$origClientStats;
            $shellCmdRes = exec($userCsvCmd);
            $origRes = explode(";", $shellCmdRes);
            $userCsvCmd = "tail -1 projects/inplace/".$termClientStats;
            $shellCmdRes = exec($userCsvCmd);
            $termRes = explode(";", $shellCmdRes);
        }
    }

    $mts = explode("_", $msgTags);
    $k = 0;
    $msgTags = explode(";", $mts[0]);
    $msgTagsLen = sizeof($msgTags) - 1;
    $i = 2;
    for($j = 0; $j < $msgTagsLen; $j +=1, $k += 1)
    {
        if($msgTags[$j][0] === "I")
        {
            $resp[$msgTags[$j]."_".$k] = $origRes[$i]."/".$origRes[$i + 3];
            $i = $i + 4;
        }
        else if($msgTags[$j][0] === "O")
        {
            $resp[$msgTags[$j]."_".$k] = $origRes[$i];
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
    if(strlen($mts[1]) > 1)
    {
        $msgTags = explode(";", $mts[1]);
        $msgTagsLen = sizeof($msgTags) - 1;
        $i = 2;
        for($j = 0; $j < $msgTagsLen; $j +=1, $k += 1)
        {
            if($msgTags[$j][0] === "I")
        {
            $resp[$msgTags[$j]."_".$k] = $termRes[$i]."/".$termRes[$i + 3];
            $i = $i + 4;
        }
        else if($msgTags[$j][0] === "O")
        {
            $resp[$msgTags[$j]."_".$k] = $termRes[$i];
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