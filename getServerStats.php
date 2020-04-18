<?php
    session_start();
    include('dbConfig.php');
    include('Net/SSH2.php');

    $module         = $_POST['MODULE'];
    if(isset($_POST["IP"]))
        $c5Ip = $_POST["IP"];
    if(isset($_POST["U"]))
        $c5User = $_POST["U"];
    if(isset($_POST["P"]))
        $c5Pass = $_POST["P"];
    $borderIp          = $_COOKIE["borderIp"];
    $borderUsername    = $_COOKIE["borderUsername"];
    $borderPassword    = $_COOKIE["borderPassword"];
    $resp              = array();

    if($module === "cdotsi")
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
        if(strlen($memUsage) < 1 || strlen($cpuUsage) < 1)
        {
            $resp["statusFlag"] = "0";
            $resp["message"] = "Server stopped or not working";
        }
        else
        {
            $resp["mem"] = $memUsage;
            $resp["cpu"] = $cpuUsage;
        }
    }
    else if(($module === "call_agent") ||
            ($module === "fserver") ||
            ($module === "mserver"))
    {
        $ssh = new Net_SSH2($c5Ip);
        if (!$ssh->login($c5User, $c5Pass)) 
        {
            $resp["message"]    = "Login Failed to C5".$c5Ip.$c5User.$c5Pass;
            $resp["statusFlag"] = "0";
            $serverResp = json_encode($resp);
            echo $serverResp;
            exit(1);
        }

        //mem ussage of cdotsi with pid
        $runCmd = "top -b -n 1 -p `ps o pid= -C ".$module."` | tail -2 | head -1 | awk '{print $10}'";
        $memUsage = $ssh->exec($runCmd);
        //cpu ussage of cdotsi with pid
        $runCmd = "top -b -n 1 -p `ps o pid= -C ".$module."` | tail -2 | head -1 | awk '{print $9}'";
        $cpuUsage = $ssh->exec($runCmd);
        if(strlen($memUsage) < 1 || strlen($cpuUsage) < 1)
        {
            $resp["statusFlag"] = "0";
            $resp["message"] = "Server stopped or not working";
        }
        else
        {
            $resp["mem"] = $memUsage;
            $resp["cpu"] = $cpuUsage;
        }
    }

    echo json_encode($resp);
?>