<?php
    session_start();
    include('dbConfig.php');
    include('Net/SSH2.php');

    $processId         = $_POST['PID'];
    $submenu           = $_POST['SM'];
    $msgTags           = $_POST['MT'];
    $projectName       = $_COOKIE["projectName"];
    $userName          = $_COOKIE["userName"];
    $clientIp          = $_COOKIE["clientIp"];
    $clientUsername    = $_COOKIE["clientUsername"];
    $clientPassword    = $_COOKIE["clientPassword"];
    $location          = $_COOKIE["location"];
    $resp              = array();
    
    $path = $userName."_".$projectName."_load/".$submenu."/";

    if($submenu === "reg")
    {
        $clientStats       = $path.$submenu."_scenario_*_counts.csv";
        
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

            $checkProc = $sshClient->exec("ps o pid= -p ".$processId);
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
            $res = explode(";", $shellCmdRes);
        }
        else
        {
            $res = explode(";", exec("tail -1 projects/inplace/".$clientStats));
        }
        
        $msgTags = explode(";", $msgTags);
        $msgTagsLen = sizeof($msgTags) - 1;
        $i = 2;
        for($j = 0; $j < $msgTagsLen; $j +=1)
        {
            if(is_numeric($msgTags[$j]))
            {
                $resp[$msgTags[$j]."_".$j] = $res[$i]."/".$res[$i + 3];
                $i = $i + 4;
            }
            else
            {
                $resp[$msgTags[$j]."_".$j] = $res[$i];
                $i = $i + 2;
            }
        }
    }

    echo json_encode($resp);
?>