<?php
    session_start();

    include('Net/SSH2.php');
    include('dbConfig.php');

    $fileType       = "";
    $userList       = $_POST['UL'];
    $portNum        = $_POST['PN'];
    $submenu        = $_POST['SM'];
    $ep             = $_POST['EP'];
    $projectName    = $_COOKIE["projectName"];
    $userName       = $_COOKIE["userName"];
    $clientIp       = $_COOKIE["clientIp"];
    $clientUser     = $_COOKIE["clientUsername"];
    $clientPassword = $_COOKIE["clientPassword"];
    $location       = $_COOKIE["location"];
    $network        = $_COOKIE["network"];
    $sessID         = session_id();
    $resp           = array("statusFlag" => "1", 
                            "message" => "User removed successfully.");

    $tableName = str_replace(".", "_", $clientIp);
    $userName  = $userName."_".$projectName;
    $userDir   = $userName."/".$network."/".$submenu."/";

    $ports = explode("br", $portNum);
    $eps = explode(";", $ep);
    $i = 0;
    foreach ($ports as $p)
    {
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

        $sql = "update ".$tableName." set user = '', state = '' where port_number = ".$p.";";
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
        if($eps[$i] === "O")
        {
            $filename = "projects/".$location."/".$tableName."/".$userDir."orig_user.csv";
            $extFilename = "/root/".$userDir."orig_user.csv";
        }
        else
        {
            $filename = "projects/".$location."/".$tableName."/".$userDir ."term_user.csv";
            $extFilename = "/root/".$userDir."term_user.csv";
        }
        $userFile = fopen($filename, "w") or die("Unable to open file!");

        if($location === "ext")
        {    
            $sshClient = new Net_SSH2($clientIp);
            if (!$sshClient->login($clientUsername, $clientPassword)) 
            {
                $resp["message"]    = "Login failed";
                $resp["statusFlag"] = "0";
                $serverResp = json_encode($resp);
                echo $serverResp;
                exit(1);
            }

            $userCsvCmd = "echo '".$userList."' > ".$extFilename;
            $shellCmdRes = $sshClient->exec($userCsvCmd);
        }

        fwrite($userFile, $userList);
        fclose($userFile);
        $i = $i + 1;
    }

    echo json_encode($resp);
?>