<?php
    session_start();

    include('Net/SSH2.php');
    include('dbConfig.php');

    $fileType       = "";
    $userList       = $_POST['UL'];
    $portNum        = $_POST['PN'];
    $projectName    = $_COOKIE["projectName"];
    $userName       = $_COOKIE["userName"];
    $clientIp       = $_COOKIE["clientIp"];
    $clientUser     = $_COOKIE["clientUsername"];
    $clientPassword = $_COOKIE["clientPassword"];
    $location       = $_COOKIE["location"];
    $sessID         = session_id();
    $resp           = array("statusFlag" => "1", 
                            "message" => "User removed successfully.");

    $userName = $userName."_".$projectName;
    $userDir = $userName."_".$projectName."_load/";

    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error)
    {
        $resp["message"]    = "Could not conect to database";
        $resp["statusFlag"] = "0";
        $serverResp = json_encode($resp);
        echo $serverResp;
        exit(1);
    }

    $tableName = str_replace(".", "_", $clientIp);

    $sql = "update ".$tableName." set user = '', state = '' where port_number = ".$portNum.";";
    if($conn->query($sql) === TRUE)
    {}
    else
    {
        $resp["message"]    = "Failed to free the port.";
        $resp["statusFlag"] = "0";
        $serverResp = json_encode($resp);
        echo $serverResp;
        $conn->close();
        exit(1);
    }
    $conn->close();

    $userList = str_replace("br", "\n", $userList);
    $userFile = fopen("projects/".$location."/".$userDir . "reg_user.csv", "w") or die("Unable to open file!");

    /*
    if($location === "ext")
    {    
        $sshClient = new Net_SSH2($clientIp);
        if (!$sshClient->login($clientUser, $clientPassword)) 
        {
            $resp["message"]    = "Login failed";
            $resp["statusFlag"] = "0";
            $serverResp = json_encode($resp);
            echo $serverResp;
            exit(1);
        }

        $userCsvCmd = "echo '".$userList."' > /root/".$userDir."reg_user.csv";
        $shellCmdRes = $sshClient->exec($userCsvCmd);
    }
    */
    fwrite($userFile, $userList);
    fclose($userFile);

    $serverResp = json_encode($resp);
    echo $serverResp;
?>