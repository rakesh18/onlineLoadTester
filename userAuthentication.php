<?php
    include('dbConfig.php');
    if(isset($_POST['username']))
        $userName = $_POST['username'];
    if(isset($_POST['password']))
        $password = $_POST['password'];
    if(isset($_POST['mode']))
        $mode     = $_POST['mode'];
    
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