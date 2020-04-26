<?php
    session_start();

    include('dbConfig.php');
    include('Net/SSH2.php');

    if(isset($_POST['username']))
        $userName = $_POST['username'];
    if(isset($_POST['password']))
        $password = $_POST['password'];
    if(isset($_POST['mode']))
        $mode     = $_POST['mode'];
    if(isset($_COOKIE['clientIp']))
        $clientIp = $_COOKIE['clientIp'];
    if(isset($_COOKIE['clientUsername']))
        $clientUsername = $_COOKIE['clientUsername'];
    if(isset($_COOKIE['clientPassword']))
        $clientPassword = $_COOKIE['clientPassword'];
    if(isset($_COOKIE['projectName']))
        $projectName = $_COOKIE['projectName'];
    if(isset($_COOKIE['location']))
        $location = $_COOKIE['location'];
    
    $resp     = array("statusFlag" => "1",
                      "message" => "User created successfully.\nYou can sign in now.");

    if($mode === "logout")
    {
        $time = time();
        setcookie("userName",       "", $time - 1, "/");
        setcookie("projectName",    "", $time - 1, "/");
        setcookie("location",       "", $time - 1, "/");
        setcookie("network",        "", $time - 1, "/");
        setcookie("clientUsername", "", $time - 1 , "/");
        setcookie("clientPassword", "", $time - 1, "/");
        setcookie("borderIp",       "", $time - 1, "/");
        setcookie("networkIp",      "", $time - 1, "/");
        setcookie("borderUsername", "", $time - 1, "/");
        setcookie("borderPassword", "", $time - 1, "/");
        setcookie("clientIp",       "", $time - 1, "/");
        exit(1);
    }
    else if($mode === "projdel")
    {
        $tableName = str_replace(".", "_", $clientIp);
        $conn = new mysqli($server, $user, $pass, $db);
        if($conn->connect_error)
        {
            $resp["message"]    = "Could not conect to database";
            $resp["statusFlag"] = "0";
            $serverResp = json_encode($resp);
            echo $serverResp;
            exit(1);
        }
        if($location === "external")
        {
            $sshclient = new Net_SSH2($clientIp);
            $try = 2;
            while($try > 0 &&
                  !($res = $sshclient->login($clientUsername, $clientPassword)))
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
            $try = 2;
            do
            {
                $res = $sshclient->exec("rm -rf /root/".$userName."_".$projectName);
                $chckDir = "ls -Rp /root/".$userName."_".$projectName."/ | grep -v / | wc -l | tail -1 | awk '{print $1}'";
                $res = $sshClient->exec($chckDir);
                $try -= 1;
            }while($try > 0 && $res >= 72);
            if($res >= 72)
            {
                $resp['message']    = "Project cannot be removed.";
                $resp['statusFlag'] = "0";
                echo json_encode($resp);
                exit(1);
            }
            $try = 2;
            do
            {
                $res = exec("rm -rf projects/external/".$tableName."/".$userName."_".$projectName);
                $try -= 1;
            }while($try > 0 && file_exists("projects/external/".$tableName."/".$userName."_".$projectName));
            if(file_exists("projects/external/".$tableName."/".$userName."_".$projectName))
            {
                $resp['message']    = "Project cannot be removed.";
                $resp['statusFlag'] = "0";
                echo json_encode($resp);
                exit(1);
            }

            $sql = "update ".$tableName." set user = '', state = '' where user like '".$userName."_".$projectName."%';";
            $result = $conn->query($sql);
            if($result === FALSE)
            {
                $resp["message"]    = "Server Error";
                $resp["statusFlag"] = "0";
                $serverResp = json_encode($resp);
                echo $serverResp;
                exit(1);
            }
        }
        else
        {
            $try = 2;
            do
            {
                $res = exec("rm -rf projects/inplace/".$userName."_".$projectName);
                $try -= 1;
            }while($try > 0 && file_exists("projects/inplace/".$userName."_".$projectName));
            if(file_exists("projects/inplace/".$userName."_".$projectName))
            {
                $resp['message']    = "Project cannot be removed.";
                $resp['statusFlag'] = "0";
                echo json_encode($resp);
                exit(1);
            }

            $sql = "update ".$tableName." set user = '', state = '' where user like ".$userName."_".$projectName."%;";
            $result = $conn->query($sql);
            if($result === FALSE)
            {
                $resp["message"]    = "Server Error";
                $resp["statusFlag"] = "0";
                $serverResp = json_encode($resp);
                echo $serverResp;
                exit(1);
            }
        }
        $conn->close();
        $time = time();
        setcookie("userName",       "", $time - 1, "/");
        setcookie("projectName",    "", $time - 1, "/");
        setcookie("location",       "", $time - 1, "/");
        setcookie("network",        "", $time - 1, "/");
        setcookie("clientUsername", "", $time - 1 , "/");
        setcookie("clientPassword", "", $time - 1, "/");
        setcookie("borderIp",       "", $time - 1, "/");
        setcookie("networkIp",      "", $time - 1, "/");
        setcookie("borderUsername", "", $time - 1, "/");
        setcookie("borderPassword", "", $time - 1, "/");
        setcookie("clientIp",       "", $time - 1, "/");
        $resp["message"] = "Project removed";
        echo json_encode($resp);
        exit(1);
    }
    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error)
    {
        $resp["message"]    = "Could not conect to database";
        $resp["statusFlag"] = "0";
        $serverResp = json_encode($resp);
        echo $serverResp;
        exit(1);
    }

    $sql = "select * from users where username = '".$userName."' and password = md5('".$password."');";
    $result = $conn->query($sql);
    if($result === FALSE)
    {
        $resp["message"]    = "Server Error";
        $resp["statusFlag"] = "0";
        $serverResp = json_encode($resp);
        echo $serverResp;
        exit(1);
    }

    if($mode === "R")
    {
        if($result->num_rows > 0)
        {
            $resp["message"]    = "User already registered with us.";
            $resp["statusFlag"] = "0";
        }
        else
        {
            $sql = "insert into users values('".$userName."', md5('".$password."'));";
            $result = $conn->query($sql);
            if($result === "FALSE")
            {
                $resp["message"]    = "Server Error";
                $resp["statusFlag"] = "0";
            }
        }
    }
    else
    {
        if($result->num_rows > 0) 
        {
            $resp["message"]    = "User found.";
            $resp["statusFlag"] = "2";
            setcookie("userName", $userName, time() + (86400 * 30), "/");
        }
        else
        {
            $resp["message"]    = "User doesnot exists.";
            $resp["statusFlag"] = "0";
        }
    }

    $conn->close();

    echo json_encode($resp);

?>