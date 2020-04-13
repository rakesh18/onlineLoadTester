<?php
    session_start();
    include('dbConfig.php');
    include('Net/SSH2.php');

    $scenario          = $_POST['S'];
    $projectName       = $_COOKIE["projectName"];
    $userName          = $_COOKIE["userName"];
    $clientIp          = $_COOKIE["clientIp"];
    $clientUsername    = $_COOKIE["clientUsername"];
    $clientPassword    = $_COOKIE["clientPassword"];
    $location          = $_COOKIE["location"];
    $resp               = array("statusFlag" => "1", 
                                "message" => "Scenario generated successfully");

    /*
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

        $userCsvCmd = "echo '".$scenario."' > /root/".$userDir."reg_scenario.xml";
        $shellCmdRes = $sshClient->exec($userCsvCmd);
    }
    */
?>