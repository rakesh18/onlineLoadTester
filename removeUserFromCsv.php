<?php
    session_start();

    include('Net/SSH2.php');
    include('dbConfig.php');

    $fileType = "";
    $userList = $_POST['UL'];
    $portNum = $_POST['PN'];
    $projname = $_COOKIE["projctName"];
    $uname = $_COOKIE["userName"];
    $clientIp = $_COOKIE["EIP"];
    $clientUser = $_COOKIE["EIPU"];
    $clientPassword = $_COOKIE["EIPP"];
    $location = $_COOKIE["LOC"];
    $sessID = session_id();

    $userDir = $uname."_".$projname."_load/";
    $uname = $uname."_".$projname;

    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error)
    {
        die("Connection failed to establish.");
    }

    $tableName = str_replace(".", "_", $clientIp);

    $sql = "update ".$tableName." set user = '', state = '' where port_number = ".$portNum.";";
    if($conn->query($sql) === TRUE)
    {}
    else
    {
        echo 'DB ERROR';
    }
    $conn->close();

    $userList = str_replace("br", "\n", $userList);
    $userFile = fopen("projects/".$userDir . "reg_user.csv", "w") or die("Unable to open file!");

    //if($location === "ext")
    {    
        /*
        $sshClient = new Net_SSH2($clientIp);
        if (!$sshClient->login($clientUser, $clientPassword)) 
        {
        echo "Login Failed";
        exit('Login Failed');
        }

        $userCsvCmd = "echo '".$userList."' > /root/".$userDir."userForReg.csv";
        $shellCmdRes = $sshClient->exec($userCsvCmd);
        */
        fwrite($userFile, $userList);
        fclose($userFile);
        
        echo "User removed successfully";
    }
    /*
    else
    {
        fwrite($userFile, $userList);
        fclose($userFile);
        
        echo "CSV created successfully";
    }
    */
?>