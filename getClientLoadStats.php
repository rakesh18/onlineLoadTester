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
            }
            $userCsvCmd = "tail -1 /root/".$clientStats;
            $shellCmdRes = $sshClient->exec($userCsvCmd);
            $origRes = explode(";", $shellCmdRes);
        }
        else
        {
            $ocheckProc = $sshClient->exec("ps o pid= -p ".$oprocessId);
            $tcheckProc = $sshClient->exec("ps o pid= -p ".$tprocessId);
            if(strlen($coheckProc) > 1 &&
               strlen($tcheckProc) > 1)
            {
                $resp["statusFlag"] = "ON"; 
            }
            else
            {
                $resp["statusFlag"] = "OFF";  
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
        $res = explode(";", exec("tail -1 projects/inplace/".$clientStats));
    }

    $mts = explode("_", $msgTags);
    $k = 0;
    $msgTags = explode(";", $mts[0]);
    $msgTagsLen = sizeof($msgTags) - 1;
    $i = 2;
    for($j = 0; $j < $msgTagsLen; $j +=1, $k += 1)
    {
        if(is_numeric($msgTags[$j]))
        {
            $resp[$msgTags[$j]."_".$k] = $origRes[$i]."/".$origRes[$i + 3];
            $i = $i + 4;
        }
        else
        {
            $resp[$msgTags[$j]."_".$k] = $origRes[$i];
            $i = $i + 2;
        }
    }
    if(strlen($mts[1]) > 0)
    {
        $msgTags = explode(";", $mts[0]);
        $msgTagsLen = sizeof($msgTags) - 1;
        $i = 2;
        for($j = 0; $j < $msgTagsLen; $j +=1, $k += 1)
        {
            if(is_numeric($msgTags[$j]))
            {
                $resp[$msgTags[$j]."_".$k] = $origRes[$i]."/".$origRes[$i + 3];
                $i = $i + 4;
            }
            else
            {
                $resp[$msgTags[$j]."_".$k] = $origRes[$i];
                $i = $i + 2;
            }
        }
    }

    echo json_encode($resp);
?>