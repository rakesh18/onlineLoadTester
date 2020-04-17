<?php
    session_start();
    include('dbConfig.php');
    include('Net/SSH2.php');

    $module         = $_POST['MODULE'];
    if(isset($_POST["IP"]))
        $ip = $_POST["IP"];
    if(isset($_POST["U"]))
        $user = $_POST["P"];
    if(isset($_POST["U"]))
        $pass = $_POST["P"];
    $borderIp          = $_COOKIE["borderIp"];
    $borderUsername    = $_COOKIE["borderUsername"];
    $borderPassword    = $_COOKIE["borderPassword"];
    $resp              = array();

    if($module === "sbcsig")
    {
        $ssh = new Net_SSH2($borderIp);
        if (!$ssh->login($borderUsername, $borderPassword)) 
        {
            $resp["message"]    = "Login Failed to Border";
            $resp["statusFlag"] = "0";
            $serverResp = json_encode($resp);
            echo $serverResp;
            exit(1);
        }

        //mem ussage of cdotsi with pid
        $runCmd = "top -b -n 1 -p `ps o pid= -C cdotsi` | tail -2 | head -1 | awk '{print $10}'";
        $memUsage = $ssh->exec($runCmd);
        //cpu ussage of cdotsi with pid
        $runCmd = "top -b -n 1 -p `ps o pid= -C cdotsi` | tail -2 | head -1 | awk '{print $9}'";
        $cpuUsage = $ssh->exec($runCmd);
        $resp["mem"] = $memUsage;
        $resp["cpu"] = $cpuUsage;
    }

    echo json_encode($resp);
?>