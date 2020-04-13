<?php
    session_start();

    include('dbConfig.php');
    include('Net/SSH2.php');

    $projectName = $_COOKIE["projectName"];
    $userName = $_COOKIE["userName"];
    $clientIp = $_COOKIE["clientIp"];
    $tableName = str_replace(".", "_", $clientIp);
    $submenu = $_POST["SM"];
    $uname = $userName."_".$projectName."_".$submenu."_".$tableName;
    $resp = array("message" => "Load Stopped",
                  "statusFlag" => "1");

    //Go to the client side to stop and then when its done successfully update the table
    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error)
    {
        $resp["message"]    = "Could not conect to database";
        $resp["statusFlag"] = "0";
        echo $resp;
        exit(1);
    }

    $sql = "delete from load_status where user = '".$uname."';";

    if ($conn->multi_query($sql) === FALSE)
    {
      $resp["message"]    = "Data insert error: ".$conn->error;
      $resp["statusFlag"] = "0";
      echo $resp;
      $conn->close();
      exit(1);
    }
    $conn->close();

    $finalResp = json_encode($resp);
    echo $finalResp;
?>