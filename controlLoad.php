<?php
    session_start();
    include('dbConfig.php');
    include('Net/SSH2.php');

    $parameter      = $_POST['PAR'];
    $submenu        = $_POST["SM"];
    $projectName    = $_COOKIE["projectName"];
    $userName       = $_COOKIE["userName"];
    $clientIp       = $_COOKIE["clientIp"];
    $clientUsername = $_COOKIE["clientUsername"];
    $clientPassword = $_COOKIE["clientPassword"];
    $resp           = array();

    $ssh = new Net_SSH2($clientIp);
    if (!$ssh->login($clientUsername, $clientPassword)) 
    {
        $resp["message"]    = "Login Failed to Client";
        $resp["statusFlag"] = "0";
        $serverResp = json_encode($resp);
        echo $serverResp;
        exit(1);
    }

    $screenName = $userName."_".$projectName."_".$submenu."_orig";
    $runCmd = 'screen -S '.$screenName.' -p 0 -X stuff "'.$parameter.'^M"';
    $out = $ssh->exec($runCmd);

    $resp["message"]    = $parameter;
    $resp["statusFlag"] = "1";

    echo json_encode($resp);
?>