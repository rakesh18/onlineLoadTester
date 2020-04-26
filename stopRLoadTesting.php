<?php
    session_start();

    include('dbConfig.php');
    include('Net/SSH2.php');

    $procId         = $_POST['PID'];
    $submenu        = $_POST["SM"];
    $msgTags        = $_POST['MT'];
    $projectName    = $_COOKIE["projectName"];
    $userName       = $_COOKIE["userName"];
    $clientIp       = $_COOKIE["clientIp"];
    $clientUsername = $_COOKIE["clientUsername"];
    $clientPassword = $_COOKIE["clientPassword"];
    $network        = $_COOKIE["network"];
    $location       = $_COOKIE["location"];
    $tableName      = str_replace(".", "_", $clientIp);
    $uname          = $userName."_".$projectName."_".$submenu."_".$tableName;
    $path           = $userName."_".$projectName."/".$network."/".$submenu."/";
    $resp           = array("message" => "Load Stopped",
                            "statusFlag" => "1");
    
    if($location === "external")
    {
        $sshClient = new Net_SSH2($clientIp);
        $try = 2;
        while(($try > 0) && 
              ($res = $sshClient->login($clientUsername, $clientPassword)) === FALSE)
        {
            $try -= 1;
        }
        if (!$res) 
        {
            $resp['message']    = "Client connect failure";
            $resp['statusFlag'] = "0";
            echo json_encode($resp);
            exit(1);
        }

        $stopCmd = "kill -9 ".$procId." screen -wipe";
        $shellCmdRes = $sshClient->exec($stopCmd);

        $statCmd = "tail -1 /root/".$path."orig_reg_*_.csv";
        $stats = $sshClient->exec($statCmd);
        $stats = explode(";", $stats);
        $resp["totCalls"] = $stats[12];
        $resp["sucCalls"] = $stats[15];
        $resp["fldCalls"] = $stats[17];

        $statCountCmd = "tail -1 /root/".$path."orig_reg_*_counts.csv";
        $stats = $sshClient->exec($statCountCmd);
        $stats = explode(";", $stats);
        $msgTags = explode(";", $msgTags);
        $msgTagsLen = sizeof($msgTags) - 1;
        $i = 2;
        for($j = 0; $j < $msgTagsLen; $j +=1)
        {
            if($msgTags[$j][0] === "I")
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
    else
    {
        $stopCmd = "kill -9 ".$procId." screen -wipe";
        $shellCmdRes = exec($stopCmd);

        $statCmd = "tail -1 projects/inplace/".$path."orig_reg_*_.csv";
        $stats = exec($statCmd);
        $stats = explode(";", $stats);
        $resp["totCalls"] = $stats[12];
        $resp["sucCalls"] = $stats[15];
        $resp["fldCalls"] = $stats[17];

        $statCountCmd = "tail -1 projects/inplace/".$path."orig_reg_*_counts.csv";
        $stats = exec($statCountCmd);
        $stats = explode(";", $stats);
        $msgTags = explode(";", $msgTags);
        $msgTagsLen = sizeof($msgTags) - 1;
        $i = 2;
        for($j = 0; $j < $msgTagsLen; $j +=1)
        {
            if($msgTags[$j][0] === "I")
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

    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error)
    {
        $resp["message"]    = "Could not conect to database";
        $resp["statusFlag"] = "0";
        echo json_encode($resp);
        exit(1);
    }

    $sql = "delete from load_status where user = '".$uname."';";

    if ($conn->multi_query($sql) === FALSE)
    {
        $resp["message"]    = "Data delete error: ".$conn->error;
        $resp["statusFlag"] = "0";
        echo json_encode($resp);
        $conn->close();
        exit(1);
    }
    $conn->close();

    $finalResp = json_encode($resp);
    echo $finalResp;
?>