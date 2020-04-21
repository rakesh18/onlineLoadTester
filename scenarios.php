<?php
  session_start();

  include('globals.php');
  include('dbConfig.php');
  
  $origUsersList   = "";
  $termUsersList = "";
  $borderIp    = "";
  $clientIp    = "";
  $origMsgTags = "";
  $termMsgTags = "";
  $origProcId  = "";
  $termProcId  = "";
  $startTime   = "";
  $origlp      = "";
  $termlp      = "";
  $submenu     = "";
  $loadRunning = 0;
  $endpoints   = "";

  if(isset($_COOKIE["userName"]))
  {
    $userName = $_COOKIE['userName'];
    if(strlen($userName) < 2)
    {
      echo "<h1>Access Denied</h1>";
      exit(1);
    }
  }
  else
  {
    echo "<h1>Access Denied</h1>";
    exit(1);
  }
  if(isset($_COOKIE["projectName"]))
    $projectName = $_COOKIE['projectName'];
  if(isset($_COOKIE["clientIp"]))
    $clientIp = $_COOKIE['clientIp'];
  if(isset($_COOKIE["location"]))
    $location = $_COOKIE['location'];
  if(isset($_COOKIE["network"]))
    $network = $_COOKIE['network'];
  if(isset($_COOKIE['borderIp']))
    $borderIp = $_COOKIE['borderIp'];

  $userDir = "projects/".$location."/".$userName."_".$projectName."/".$network."/";

  if(isset($_GET['menu']))
  {
    $menu = $_GET['menu'];
    if($menu === "cases" ||
       $menu === "scenarios" ||
       $menu === "users" ||
       $menu === "run")
    {
      echo '<div id = "topPane">
              <h4 style = "float: right;margin-right: 10px;margin-top: 10px;">Welcome '.$userName.'</h4>
            </div>';
    }
    else
    {
      echo "<h1>Oops!!! Your not allowed to enter here</h1>";
      exit(1);
    }
  }
  else
  {
    echo "<h1>Oops!!! Your not allowed to enter here</h1>";
    exit(1);
  }
  if(isset($_GET['submenu']))
  {
    $submenu = $_GET['submenu'];
    if($menu === "cases")
    {
      echo "<h1>Oops!!! Your not allowed to enter here</h1>";
      exit(1);
    }
    if(array_key_exists($submenu, $submenus) === FALSE)
    {
      echo "<h1>Ooops!!! Your not allowed to enter here</h1>";
      exit(1);
    }
    $userDir .= $submenu."/";
  }
  else if($menu === "scenarios" ||
          $menu === "users" ||
          $menu === "run")
  {
    echo "<h1>Access Denied</h1>";
    exit(1);
  }
  if(isset($_GET['endpoints']))
  {
    $endpoints = $_GET['endpoints'];
    if(in_array($endpoints, $terminals) === FALSE ||
       $submenu !== "scenarios")
    {
      echo "<h1>Oops!!! Your not allowed to enter here</h1>";
      exit(1);
    }
  }
  else if($menu === "scenarios")
  {
    echo "<h1>Oops!!! Your not allowed to enter here</h1>";
    exit(1);
  }

  if($menu === "scenarios" ||
     $menu === "users")
  {
    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error)
    {
        echo "Could not connect to database";
        exit(1);
    }
    $tableName = str_replace(".", "_", $clientIp);
    $uname = $userName."_".$projectName."_".$network."_".$submenu."_".$tableName;
    $sql   = "select orig_proc_id from load_status where user = '".$uname."' and status = 'running';";
    $result = $conn->query($sql);

    if($result->num_rows > 0) 
    {
      while($row = $result->fetch_assoc()) 
      {
        $origProcId = $row['orig_proc_id'];
      }
    }
    $conn->close();
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Load Tester</title>
    <link rel = "stylesheet" href = "https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href = "https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel = "stylesheet">
    <link href = "https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel = "stylesheet">
    <link href = "CSS/style.css" type = "text/css" rel = "stylesheet">
    <style>
      div#runParameters {
        border: 2px solid black;
        border-radius: 10px;
        margin-left: 40px;
        padding: 10px 10px 10px 10px;
        width: 300px;
      }
      div#finalResult {
        padding: 0px 10px 10px 10px;
        width: 290px;
        display: block;
      }
      input#load_rate {
        padding: 5px 5px 5px 5px;
        font-size: 20px;
        border-radius: 5px;
        width: 100px;
      }
      input#load_limit {
        padding: 5px 5px 5px 5px;
        font-size: 20px;
        border-radius: 5px;
        width: 100px;
      }
      input#runLoad {
        border-radius: 6px;
      }
      input#pauseLoad {
        border-radius: 6px;
      }
      div#serverStats {
        margin-left: 40px;
        width: 95%;
        margin-bottom: 10px;
      }
    </style>
  </head>

  <body>
    <div id = "bottomPane">
      <div id  = "bottomLeftPane">
        <div id = "menuPane">
          <?php
            if($menu === "cases")
            {
              echo '<div id = "optionMenu" class = "menus active">
                      TEST CASES
                    </div>';
            }
            else
            {
              echo '<div id = "optionMenu">
                      TEST CASES
                    </div>';
            }
            if($menu === "scenarios")
            {
              echo '<div id = "scenarioMenu" class = "menus active">
                      SCENARIOS
                    </div>';
              if(($submenu === "reg") ||
                 ($submenu === "ltereg") ||
                 ($submenu === "imsreg"))
              {
                echo '<div id = "regEndPoints" class = "endPoints">
                        <div id = "origEndUserOpt" class = "endPointsName">ORIGINATING</div>
                      </div>';
              }
              else if(($submenu === "call") ||
                      ($submenu === "msg") ||
                      ($submenu === "ims2imscall") ||
                      ($submenu === "ims2ltecall") ||
                      ($submenu === "lte2imscall") ||
                      ($submenu === "lte2ltecall") ||
                      ($submenu === "ims2imsmsg") ||
                      ($submenu === "ims2ltemsg") ||
                      ($submenu === "lte2imsmsg") ||
                      ($submenu === "lte2ltemsg"))
              {
                echo '<div id = "basicCallEndPoints" class = "endPoints">';
                if($endpoints === "originating")
                {
                  echo '<div id = "origEndUserOpt" class = "endPointsName" style = "border-radius: 5px;background-color: rgb(131, 131, 8);">ORIGINATING</div>
                        <div id = "termEndUserOpt" class = "endPointsName">TERMINATING</div>';
                }
                else
                {
                  echo '<div id = "origEndUserOpt" class = "endPointsName">ORIGINATING</div>
                  <div id = "termEndUserOpt" class = "endPointsName" style = "background-color: rgb(131, 131, 8);">TERMINATING</div>';
                }
                echo '</div>';
              }
            }
            else
            {
              echo '<div id = "scenarioMenu">
                      SCENARIOS
                    </div>';
            }
            if($menu === "users")
            {
              echo '<div id = "userMenu" class = "menus active">
                      USERS
                    </div>';
            }
            else
            {
              echo '<div id = "userMenu">
                      USERS
                    </div>';
            }
            if($menu === "run")
            {
              echo '<div id = "runMenu" class = "menus active">
                      RUN
                    </div>';
            }
            else
            {
              echo '<div id = "runMenu">
                      RUN
                    </div>';
            }
          ?>          
        </div>
      </div>
      <div id = "bottomRightPane">
        <?php
          if($menu === "cases")
          {
            if($network === "ims")
            {
              echo '
                <div id = "callOptions">
                  <div class = "testCaseName" id = "one" value = "imsreg>IMS BAISC REGISTRATION</div>
                  <div class = "testCaseName" id = "two" value = "ltereg">LTE BASIC REGISTRATION</div>
                  <div class = "testCaseName" id = "one" value = "ims2imscall">IMS-to-IMS BASIC CALL</div>
                  <div class = "testCaseName" id = "two" value = "ims2ltecall">IMS-to-LTE BASIC CALL</div>
                  <div class = "testCaseName" id = "one" value = "lte2imscall">LTE-to-IMS BASIC CALL</div>
                  <div class = "testCaseName" id = "two" value = "lte2ltecall">LTE-to-LTE BASIC CALL</div>
                  <div class = "testCaseName" id = "one" value = "ims2imsmsg">IMS-to-IMS BASIC MESSAGE</div>
                  <div class = "testCaseName" id = "two" value = "ims2ltemsg">IMS-to-LTE BASIC MESSAGE</div>
                  <div class = "testCaseName" id = "one" value = "lte2imsmsg">LTE-to-IMS BASIC MESSAGE</div>
                  <div class = "testCaseName" id = "two" value = "lte2ltemsg">LTE-to-LTE BASIC MESSAGE</div>
                </div>';
            }
            else
            {
              echo '
                <div id = "callOptions">
                  <div class = "testCaseName" id = "one" value = "reg">BAISC REGISTRATION</div>
                  <div class = "testCaseName" id = "two" value = "call">BASIC CALL</div>
                  <div class = "testCaseName" id = "one" value = "msg">BASIC TEXT MESSAGE</div>
                  <div class = "testCaseName" id = "one" value = "samvadcall">SAMVAD CALL</div>
                </div>';
            }
            
          }
          else if($menu === "scenarios")
          {
            echo '<div id = "scenarioOptions">';
            if(($submenu === "reg")    ||
               ($submenu === "ltereg") ||
               ($submenu === "imsreg"))
            {
              $ep = substr($endpoints, 0, 4);
              echo '<div id = "regScenario">';
                $scenarioFile = fopen($userDir.$ep."_reg.xml", "r") or die("Unable to open file!");
                $sendTag = 0;
                $recvTag = 0;
                $startCustomField = 0;
                $labelIdActivated = 0;
                $req = 0;

                while(!feof($scenarioFile))
                {
                  $line = fgets($scenarioFile);
                  if(strlen($line) == 1 ||
                     substr($line, 0, 1) === ']' || 
                     substr($line, 0, 1) === '\n' || 
                     substr($line, 0, 1) === '\r' || 
                     substr($line, 0, 1) === ' ')
                  {
                    continue;
                  }
                  if(substr($line, 0, 1) === "<")
                  {
                    if(strpos($line, "<send") !== FALSE)
                    {
                      $sendTag = 1;
                      if($recvTag == 1)
                      {
                        echo '</div>';
                        $recvTag = 0;
                      }
                      if($labelIdActivated == 1)
                      {
                        $labelIdActivated = 0;
                        echo '<label style = "margin-left: 10px;"><b>Message Sent For Unexpected Responses&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down fa-1x"></i></b></label><br>';
                      }
                      echo '<div class = "origRegRqst">';
                      $origMsgTags .= "OREGISTER;";
                    }
                    else if(strpos($line, "</send") !== FALSE)
                    {
                      $sendTag = 0;
                      $req = $req + 1;
                      echo '</div>';
                      echo '<p>* [field#] will be read from CSV file</p>';
                      echo '<div id = "newHeaderAdd">
                              <label>New Header</label><br>
                              <input type = "text" class = "userHeaderField" value = "" placeholder = "Field Name">
                              &nbsp;&nbsp;&nbsp;&nbsp;<b>:</b>&nbsp;&nbsp;&nbsp;&nbsp;
                              <input type = "text" class = "userHeaderValue" value = "" placeholder = "Field Value">
                              &nbsp;&nbsp;&nbsp;&nbsp;
                              <input type = "button" class = "addUserHeaderToMsg" value = "ADD">
                            </div>';
                    }
                    else if(strpos($line, "<recv") !== FALSE)
                    {
                      if($recvTag == 0)
                      {
                        echo '<div class = "origRegResp">';
                        $recvTag = 1;
                      }

                      $respCode = str_replace("\"", "", explode(" ", str_replace(">", " ", explode("=", $line)[1]))[0]);

                      if(strpos($line, "optional") === FALSE)
                      {
                        echo '<input type = "checkbox" id = "'.$req.$respCode.'mandatory" value = "'.$respCode.'" checked = "checked" onclick = "return false;" class = "respInput">
                              <label for = "'.$req.$respCode.'mandatory" class = "respLabel">'.$respCode.' '.$responses[$respCode].'</label>';
                        $origMsgTags .= "I".$respCode.";";
                      }
                      else
                      {
                        echo '<input type = "checkbox" id = "'.$req.$respCode.'optional" value = "'.$respCode.'" checked = "checked"  class = "respInput">
                              <label for = "'.$req.$respCode.'optional" class = "respLabel">'.$respCode.' '.$responses[$respCode].'</label>';
                        $origMsgTags .= "I".$respCode.";";
                      }
                    }
                    else if(strpos($line, "<!--recv") !== FALSE)
                    {
                      if($recvTag == 0)
                      {
                        echo '<div class = "origRegResp">';
                        $recvTag = 1;
                      }

                      $respCode = str_replace("\"", "", explode(" ", str_replace(">", " ", explode("=", $line)[1]))[0]);

                      echo '<input type = "checkbox" id = "'.$req.$respCode.'optional" value = "'.$respCode.'" class = "respInput">
                            <label for = "'.$req.$respCode.'optional" class = "respLabel">'.$respCode.' '.$responses[$respCode].'</label>';
                    }
                    else if(strpos($line, "<label") !== FALSE)
                    {
                      $labelIdActivated = 1;
                    }
                    if(strpos($line, "</recv") !== FALSE)
                    {
                      if($recvTag == 1)
                      {
                        echo '</div>';
                        $recvTag = 0;
                        $startCustomField = 0;
                      }
                    }
                    continue;
                  }
                  else
                  {
                    $line = str_replace("<", "&lt;", $line);
                    $line = str_replace(">", "&gt;", $line);
                  }
                  if($sendTag == 1)
                  {
                    if(strpos($line, "Contact") !== FALSE)
                    {
                      echo 'Contact: &lt;sip:[field0]@[field2]:[field3]&gt;;expires=<input type = "number" value = "864000" class = "regExpires" min = "300"><br>';
                    }
                    else if(strpos($line, "User-Agent") !== FALSE)
                    {
                      echo $line."<br>";
                      $startCustomField = 1;
                    }
                    else
                    {
                      if($startCustomField == 1)
                      {
                        echo "<div class = 'newRqstHdr' ondblclick='removeNewRqstHdr(this)'>".$line."</div>";
                      }
                      else
                      {
                        echo $line."<br>";
                      }
                    }
                  }
                }
                fclose($scenarioFile);
                setcookie($userName.$projectName.$network.$submenu,
                          $origMsgTags,
                          time() + (86400 * 30), "/");
                echo '<input type = "button" class = "scenarioGen" value = "GENERATE">';
              echo '</div>';
            }
            else if(($submenu === "call")        ||
                    ($submenu === "msg")         ||
                    ($submenu === "samvadcall")  ||
                    ($submenu === "ims2imscall") ||
                    ($submenu === "ims2ltecall") ||
                    ($submenu === "lte2imscall") ||
                    ($submenu === "lte2ltecall") ||
                    ($submenu === "ims2imsmsg")  ||
                    ($submenu === "ims2ltemsg")  ||
                    ($submenu === "lte2imsmsg")  ||
                    ($submenu === "lte2ltemsg"))
            {
              $ep = substr($endpoints, 0, 4);
              echo '<div class = "testCaseName" id = "one" value = "callmsgreg">REGISTRATION</div>';
              echo '<div id = "regScenario" style = "display: block;">';
                $scenarioFile = fopen($userDir.$ep."_reg.xml", "r") or die("Unable to open file!");
                $sendTag = 0;
                $recvTag = 0;
                $startCustomField = 0;
                $labelIdActivated = 0;
                $req = 0;

                while(!feof($scenarioFile))
                {
                  $line = fgets($scenarioFile);
                  if(strlen($line) == 1 ||
                     substr($line, 0, 1) === ']' || 
                     substr($line, 0, 1) === '\n' || 
                     substr($line, 0, 1) === '\r' || 
                     substr($line, 0, 1) === ' ')
                  {
                    continue;
                  }
                  if(substr($line, 0, 1) === "<")
                  {
                    if(strpos($line, "<send") !== FALSE)
                    {
                      $sendTag = 1;
                      if($recvTag == 1)
                      {
                        echo '</div>';
                        $recvTag = 0;
                      }
                      if($labelIdActivated == 1)
                      {
                        $labelIdActivated = 0;
                        echo '<label style = "margin-left: 10px;"><b>Message Sent For Unexpected Responses&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down fa-1x"></i></b></label><br>';
                      }
                      echo '<div class = "origRegRqst">';
                    }
                    else if(strpos($line, "</send") !== FALSE)
                    {
                      $sendTag = 0;
                      $req = $req + 1;
                      echo '</div>';
                      echo '<p>* [field#] will be read from CSV file</p>';
                      echo '<div id = "newHeaderAdd">
                              <label>New Header</label><br>
                              <input type = "text" class = "userHeaderField" value = "" placeholder = "Field Name">
                              &nbsp;&nbsp;&nbsp;&nbsp;<b>:</b>&nbsp;&nbsp;&nbsp;&nbsp;
                              <input type = "text" class = "userHeaderValue" value = "" placeholder = "Field Value">
                              &nbsp;&nbsp;&nbsp;&nbsp;
                              <input type = "button" class = "addUserHeaderToMsg" value = "ADD">
                            </div>';
                    }
                    else if(strpos($line, "<recv") !== FALSE)
                    {
                      if($recvTag == 0)
                      {
                        echo '<div class = "origRegResp">';
                        $recvTag = 1;
                      }

                      $respCode = str_replace("\"", "", explode(" ", str_replace(">", " ", explode("=", $line)[1]))[0]);

                      if(strpos($line, "optional") === FALSE)
                      {
                        echo '<input type = "checkbox" id = "'.$req.$respCode.'mandatory" value = "'.$respCode.'" checked = "checked" onclick = "return false;" class = "respInput">
                              <label for = "'.$req.$respCode.'mandatory" class = "respLabel">'.$respCode.' '.$responses[$respCode].'</label>';
                      }
                      else
                      {
                        echo '<input type = "checkbox" id = "'.$req.$respCode.'optional" value = "'.$respCode.'" class = "respInput">
                              <label for = "'.$req.$respCode.'optional" class = "respLabel">'.$respCode.' '.$responses[$respCode].'</label>';
                      }
                    }
                    else if(strpos($line, "<label") !== FALSE)
                    {
                      $labelIdActivated = 1;
                    }
                    if(strpos($line, "</recv") !== FALSE)
                    {
                      if($recvTag == 1)
                      {
                        echo '</div>';
                        $recvTag = 0;
                        $startCustomField = 0;
                      }
                    }
                    continue;
                  }
                  else
                  {
                    $line = str_replace("<", "&lt;", $line);
                    $line = str_replace(">", "&gt;", $line);
                  }
                  if($sendTag == 1)
                  {
                    if(strpos($line, "Contact") !== FALSE)
                    {
                      echo 'Contact: &lt;sip:[field0]@[field2]:[field3]&gt;;expires=<input type = "number" value = "864000" class = "regExpires" min = "300"><br>';
                    }
                    else if(strpos($line, "User-Agent") !== FALSE)
                    {
                      echo $line."<br>";
                      $startCustomField = 1;
                    }
                    else
                    {
                      if($startCustomField == 1)
                      {
                        echo "<div class = 'newRqstHdr' ondblclick='removeNewRqstHdr(this)'>".$line."</div>";
                      }
                      else
                      {
                        echo $line."<br>";
                      }
                    }
                  }
                }
                fclose($scenarioFile);
                echo '<input type = "button" class = "scenarioGen" value = "GENERATE"><br><br>';
              echo '</div>';
              echo '<div class = "testCaseName" id = "two" value = "callmsg">CALL / MESSAGE</div>';
              echo '<div id = "callmsgScenario" style = "display: block;">';
                $scenarioFile = fopen($userDir.$ep."_scenario.xml", "r") or die("Unable to open file!");
                $sendTag = 0;
                $recvTag = 0;
                $startCustomField = 0;
                $labelIdActivated = 0;
                $req = 0;
                $msgTags = "";

                while(!feof($scenarioFile))
                {
                  $line = fgets($scenarioFile);
                  if(strlen($line) == 1 ||
                     substr($line, 0, 1) === ']' || 
                     substr($line, 0, 1) === '\n' || 
                     substr($line, 0, 1) === '\r' || 
                     substr($line, 0, 1) === ' ')
                  {
                    continue;
                  }
                  if(substr($line, 0, 1) === "<")
                  {
                    if(strpos($line, "<send") !== FALSE)
                    {
                      $sendTag = 1;
                      if($recvTag == 1)
                      {
                        echo '</div>';
                        $recvTag = 0;
                      }
                      if($labelIdActivated == 1)
                      {
                        $labelIdActivated = 0;
                        echo '<label id = "optionalRqst" style = "margin-left: 10px;"><b>Message Sent For Unexpected Responses&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down fa-1x"></i></b></label><br>';
                      }
                      echo '<div class = "origRegRqst">';
                      $msgTags .= "OREQ;";
                    }
                    else if(strpos($line, "</send") !== FALSE)
                    {
                      $sendTag = 0;
                      $startCustomField = 0;
                      $req = $req + 1;
                      echo '</div>';
                      echo '<p>* [field#] will be read from CSV file</p>';
                      echo '<div id = "newHeaderAdd">
                              <label>New Header</label><br>
                              <input type = "text" class = "userHeaderField" value = "" placeholder = "Field Name">
                              &nbsp;&nbsp;&nbsp;&nbsp;<b>:</b>&nbsp;&nbsp;&nbsp;&nbsp;
                              <input type = "text" class = "userHeaderValue" value = "" placeholder = "Field Value">
                              &nbsp;&nbsp;&nbsp;&nbsp;
                              <input type = "button" class = "addUserHeaderToMsg" value = "ADD">
                            </div>';
                    }
                    else if(strpos($line, "<recv") !== FALSE)
                    {
                      if($recvTag == 0)
                      {
                        echo '<div class = "origRegResp">';
                        $recvTag = 1;
                      }
                      $respCode = str_replace("\"", "", explode(" ", str_replace(">", " ", explode("=", $line)[1]))[0]);

                      if(strpos($line, "optional") === FALSE)
                      {
                        echo '<input type = "checkbox" id = "'.$req.$respCode.'mandatory" value = "'.$respCode.'" checked = "checked" onclick = "return false;" class = "respInput">
                              <label for = "'.$req.$respCode.'mandatory" class = "respLabel">'.$respCode.' '.$responses[$respCode].'</label>';
                        $msgTags .= "I".$respCode.";";
                      }
                      else
                      {
                        echo '<input type = "checkbox" id = "'.$req.$respCode.'optional" value = "'.$respCode.'" checked = "checked"  class = "respInput">
                              <label for = "'.$req.$respCode.'optional" class = "respLabel">'.$respCode.' '.$responses[$respCode].'</label>';
                        $msgTags .= "I".$respCode.";";
                      }
                    }
                    else if(strpos($line, "<!--recv") !== FALSE)
                    {
                      if($recvTag == 0)
                      {
                        echo '<div class = "origRegResp">';
                        $recvTag = 1;
                      }

                      $respCode = str_replace("\"", "", explode(" ", str_replace(">", " ", explode("=", $line)[1]))[0]);

                      echo '<input type = "checkbox" id = "'.$req.$respCode.'optional" value = "'.$respCode.'" class = "respInput">
                            <label for = "'.$req.$respCode.'optional" class = "respLabel">'.$respCode.' '.$responses[$respCode].'</label>';
                    }
                    else if(strpos($line, "<label") !== FALSE)
                    {
                      $labelIdActivated = 1;
                    }
                    else if(strpos($line, "<nop") !== FALSE)
                    {
                      echo '<input type = "checkbox" id = "regRTPresp" value = "Enable RTP Flow" checked = "checked" class = "respInput">
                            <label for = "regRTPresp" class = "respLabel">Enable RTP Flow</label>&nbsp;&nbsp;';
                    }
                    else if(strpos($line, "<!--nop") !== FALSE)
                    {
                      echo '<input type = "checkbox" id = "regRTPresp" value = "Enable RTP Flow" class = "respInput">
                            <label for = "regRTPresp" class = "respLabel">Enable RTP Flow</label>&nbsp;&nbsp;';
                    }
                    else if(strpos($line, "<pause") !== FALSE)
                    {
                      echo 'Call Duration(in ms):&nbsp;&nbsp;<input id = "callDur" type = "number" value = "60000" min = "5000" style = "border-radius: 5px;padding-left: 5px;"><br>';
                    }
                    if(strpos($line, "</recv") !== FALSE)
                    {
                      if($recvTag == 1)
                      {
                        echo '</div>';
                        $recvTag = 0;
                        $startCustomField = 0;
                      }
                    }
                    continue;
                  }
                  else
                  {
                    $line = str_replace("<", "&lt;", $line);
                    $line = str_replace(">", "&gt;", $line);
                  }
                  if($sendTag == 1)
                  {
                    if(strpos($line, "Contact") !== FALSE)
                    {
                      echo 'Contact: &lt;sip:[field0]@[field2]:[field3]&gt;;expires=<input type = "number" value = "864000" class = "regExpires" min = "300"><br>';
                    }
                    else if(strpos($line, "User-Agent") !== FALSE)
                    {
                      echo $line."<br>";
                      $startCustomField = 1;
                    }
                    else
                    {
                      if(strpos($line, "v=") !== FALSE)
                      {
                        $startCustomField = 0;
                        echo '</div>';
                        echo '<div class = "origRegSdp">';
                        echo $line."<br>";
                      }
                      else if($startCustomField == 1)
                      {
                        echo "<div class = 'newRqstHdr' ondblclick='removeNewRqstHdr(this)'>".$line."</div>";
                      }
                      else
                      {
                        $startCustomField = 0;
                        if(strpos($msgTags, "REQ") !== FALSE)
                        {
                          $reqName = explode(" ", $line);
                          if(strpos($reqName[0], "SIP") !== FALSE)
                          {
                            $msgTags = str_replace("REQ", $reqName[1], $msgTags);
                          }
                          else
                          {
                            $msgTags = str_replace("REQ", $reqName[0], $msgTags);
                          }
                        }
                        echo $line."<br>";
                      }
                    }
                  }
                }
                fclose($scenarioFile);
                $scenarioFile = fopen($userDir.$eps[$ep]."_scenario.xml", "r") or die("Unable to open file!");
                $msgTags2 = "";

                while(!feof($scenarioFile))
                {
                  $line = fgets($scenarioFile);
                  if(strlen($line) == 1 ||
                     substr($line, 0, 1) === ']' || 
                     substr($line, 0, 1) === '\n' || 
                     substr($line, 0, 1) === '\r' || 
                     substr($line, 0, 1) === ' ')
                  {
                    continue;
                  }
                  if(substr($line, 0, 1) === "<")
                  {
                    if(strpos($line, "<send") !== FALSE)
                    {
                      $msgTags2 .= "OREQ;";
                    }
                    else if(strpos($line, "<recv") !== FALSE)
                    {
                      $respCode = str_replace("\"", "", explode(" ", str_replace(">", " ", explode("=", $line)[1]))[0]);
                      $msgTags2 .= "I".$respCode.";";
                    }
                    continue;
                  }
                  if(strpos($msgTags2, "REQ") !== FALSE)
                  {
                    $reqName = explode(" ", $line);
                    if(strpos($reqName[0], "SIP") !== FALSE)
                    {
                      $msgTags2 = str_replace("REQ", $reqName[1], $msgTags2);
                    }
                    else
                    {
                      $msgTags2 = str_replace("REQ", $reqName[0], $msgTags2);
                    }
                  }
                }
                fclose($scenarioFile);
                if($ep === "orig")
                {
                  $origMsgTags = $msgTags;
                  $termMsgTags = $msgTags2;
                }
                else
                {
                  $origMsgTags = $msgTags2;
                  $termMsgTags = $msgTags;
                }
                setcookie($userName.$projectName.$network.$submenu."orig",
                          $origMsgTags,
                          time() + (86400 * 30), "/");
                setcookie($userName.$projectName.$network.$submenu."term",
                          $termMsgTags,
                          time() + (86400 * 30), "/");
                echo '<input type = "button" class = "scenarioGen" value = "GENERATE">';
              echo '</div>';
            }
            echo '</div>';
          }
          else if($menu === "users")
          {
            if($submenu === "reg" ||
               $submenu === "imsreg" ||
               $submenu === "ltereg")
            {
              echo '<div class = "container" id = "users">';
                echo '<br>';
                echo '<h2>Users</h2>';
                echo '<div id = "userDetails">';
                  echo '<div class = "users" id = "users_0">';
                    echo '<label id = "userLabel"> Username </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label id = "passLabel"> Password </label>
                    <br>
                    <input type = "number" class = "uname" value = "">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type = "number" class = "pass" value = "">';
                  echo '</div>';
              echo '</div>';
              echo '
              <div id = "addUserMsg">
                  <input type = "checkbox" id = "userRange" disabled = "disabled">
                  <label for = "userRange">Selected for adding range of users</label>
              </div>
              <div id = "userCtrl">
                  <br><i id = "addUser" class="fa fa-plus fa-1x" aria-hidden="true" style = "cursor: pointer;">&nbsp;Add User</i>
                  <br><br><input type = "button" id = "generateCsv" value = "Generate">
              </div>
              <br>';
              echo '<table class="table">
                      <thead>
                        <tr>
                            <th>UserName</th>
                            <th>AuthHeader</th>
                            <th>LocalIP</th>
                            <th>LocalPort</th>
                            <th>Server</th>
                        </tr>
                      </thead>
                      <tbody id = "userDataBody">';
                      $userFile = fopen($userDir."orig_user.csv", "r") or die("Unable to open file!");
                      $i = 0;
                      $origUsersList = "";
                      while(!feof($userFile))
                      {
                        $line = fgets($userFile);
                        if(strlen($line) < 10)
                          continue;

                        if(strncmp($line, "SEQUENTIAL", 10) == 0)
                        {
                          $origUsersList = "SEQUENTIALbr";
                          continue;
                        }

                        $cols = explode(";", $line);
                        echo '<tr class = "warning" id = "users_row_' . $i . '" ondblclick = "removeUserRow(this)">';
                        foreach ($cols as $col)
                        {
                          echo '<td>' . $col . '</td>';
                          $origUsersList .= $col . ";";
                          if(((int)$col) >= 50000 && ((int)$col) < 60000)
                          {
                            $origlp = $col;
                          }
                        }
                        $origUsersList = substr_replace($origUsersList, "br", -2);
                        echo '</tr>';
                        $i += 1;
                      }
                      fclose($userFile);
                echo '</tbody>
                    </table>';
              echo '</div>';
            }
            else if(($submenu === "call")        ||
                    ($submenu === "msg")         ||
                    ($submenu === "samvadcall")  ||
                    ($submenu === "ims2imscall") ||
                    ($submenu === "ims2ltecall") ||
                    ($submenu === "lte2imscall") ||
                    ($submenu === "lte2ltecall") ||
                    ($submenu === "ims2imsmsg")  ||
                    ($submenu === "ims2ltemsg")  ||
                    ($submenu === "lte2imsmsg")  ||
                    ($submenu === "lte2ltemsg"))
            {
              echo '<div class = "container" id = "users">';
                echo '<br>';
                echo '<h2>Users</h2><br>';
                echo '<div id = "userDetails">';
                  echo '<h4>Originating</h4>';
                  echo '<div class = "users" id = "users_0">';
                    echo '<label id = "userLabel"> Username </label>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <label id = "passLabel"> Password </label>
                          <br>
                          <input type = "number" class = "uname" value = "">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <input type = "number" class = "pass" value = "">';
                  echo '</div>';
                  echo '<br><br>';
                  echo '<h4>Terminating</h4>';
                  echo '<div class = "users" id = "users_1">';
                    echo '<label id = "userLabel"> Username </label>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <label id = "passLabel"> Password </label>
                          <br>
                          <input type = "number" class = "uname" value = "">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <input type = "number" class = "pass" value = ""><br>
                          <input type = "number" class = "cname" value = ""  placeholder = "Called Number" style = "margin-top: 5px;"><br>
                          <input type = "checkbox" id = "calledNumberSelect" style = "margin-top: 5px;">
                          <label for = "calledNumberSelect" style = "margin-top: 5px;">Check if called number is same as username</label>';
                  echo '</div>';
                echo '</div>';
                echo '<br>';
                echo '
                <div id = "userCtrl">
                    <input type = "button" id = "generateCsv" value = "Generate" style = "margin-top: -20px;">
                </div>
                <br>';
                echo '<table class="table">
                        <thead>
                          <tr>
                              <th>EndPoint</th>
                              <th>UserName</th>
                              <th>AuthHeader</th>
                              <th>LocalIP</th>
                              <th>LocalPort</th>
                              <th>Server</th>
                              <th>OtherEndUser</th>
                          </tr>
                        </thead>
                        <tbody id = "userDataBody">';
                        $userFile = fopen($userDir."orig_user.csv", "r") or die("Unable to open file!");
                        $i = 0;
                        $origUsersList = "";
                        while(!feof($userFile))
                        {
                          $line = fgets($userFile);
                          if(strlen($line) < 10)
                            continue;

                          if(strncmp($line, "SEQUENTIAL", 10) == 0)
                          {
                            $origUsersList = "SEQUENTIALbr";
                            continue;
                          }

                          $cols = explode(";", $line);
                          echo '<tr class = "warning" id = "users_row_' . $i . '" ondblclick = "removeUserRow(this)">';
                          echo '<td>ORIG</td>';
                          foreach ($cols as $col)
                          {
                            echo '<td>' . $col . '</td>';
                            $origUsersList .= $col . ";";
                            if(((int)$col) >= 50000 && ((int)$col) < 60000)
                            {
                              $origlp = $col;
                            }
                          }
                          $origUsersList = substr_replace($origUsersList, "br", -2);
                          echo '</tr>';
                          $i += 1;
                        }
                        fclose($userFile);
                        $userFile = fopen($userDir."term_user.csv", "r") or die("Unable to open file!");
                        $termUsersList = "";
                        while(!feof($userFile))
                        {
                          $line = fgets($userFile);
                          if(strlen($line) < 10)
                            continue;

                          if(strncmp($line, "SEQUENTIAL", 10) == 0)
                          {
                            $termUsersList = "SEQUENTIALbr";
                            continue;
                          }

                          $cols = explode(";", $line);
                          echo '<tr class = "warning" id = "users_row_' . $i . '" ondblclick = "removeUserRow(this)">';
                          echo '<td>TERM</td>';
                          foreach ($cols as $col)
                          {
                            echo '<td>' . $col . '</td>';
                            $termUsersList .= $col . ";";
                            if(((int)$col) >= 50000 && ((int)$col) < 60000)
                            {
                              $termlp = $col;
                            }
                          }
                          $termUsersList = substr_replace($termUsersList, "br", -2);
                          echo '</tr>';
                          $i += 1;
                        }
                        fclose($userFile);
                  echo '</tbody>
                      </table>';
              echo '</div>';
            }
            if(strlen($origUsersList) > 12 ||
               strlen($termUsersList) > 12)
            {
              echo '<input type = "button" id = "removeAllUserRows" value = "Remove All" style = "border-radius: 5px;margin-left: 55px;">';
            }
          }
          else if($menu === "run")
          {
            if($submenu === "reg" ||
               $submenu === "imsreg" ||
               $submenu === "ltereg")
            {
              $userFile = $userDir."/orig_user.csv";
              $totalLines = intval(exec("wc -l ".$userFile));
              if($totalLines < 3)
              {
                echo "<h1>&nbsp;&nbsp;&nbsp;&nbsp;You have not provided any user yet.</h1>";
                exit(1);
              }
            }
            else
            {
              $userFile = $userDir."/orig_user.csv";
              $totalLines = intval(exec("wc -l ".$userFile));
              if($totalLines < 3)
              {
                echo "<h1>&nbsp;&nbsp;&nbsp;&nbsp;You have not provided any user yet.</h1>";
                exit(1);
              }
              $userFile = $userDir."/term_user.csv";
              $totalLines = intval(exec("wc -l ".$userFile));
              if($totalLines < 3)
              {
                echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;You have not provided any user yet.</p>";
                exit(1);
              }
            }

            echo '<br><h2>&nbsp;&nbsp;'.$submenus[$submenu].'</h2><br>';
            echo '<div id = "msg_tags" style = "margin-left: 20px; position: relative;">';
            $conn = new mysqli($server, $user, $pass, $db);
            if($conn->connect_error)
            {
                echo "Could not connect to database";
                exit(1);
            }
            $tableName = str_replace(".", "_", $clientIp);
            $uname = $userName."_".$projectName."_".$network."_".$submenu."_".$tableName;
            $sql   = "select start_time, orig_proc_id, term_proc_id, orig_msg_tags, term_msg_tags from load_status where user = '".$uname."' and status = 'running';";
            $result = $conn->query($sql);

            if($result->num_rows > 0) 
            {
              while($row = $result->fetch_assoc()) 
              {
                $startTime   = $row['start_time'];
                $origProcId  = $row['orig_proc_id'];
                $termProcId  = $row['term_proc_id'];
                $origMsgTags = $row['orig_msg_tags'];
                $termMsgTags = $row['term_msg_tags'];
              }
            }
            $conn->close();
            if($submenu === "reg" ||
               $submenu === "imsreg" ||
               $submenu === "ltereg")
            {
              if($origProcId === "")
              {
                if(isset($_COOKIE[$userName.$projectName.$network.$submenu."orig"]))
                {
                  $origMsgTags = $_COOKIE[$userName.$projectName.$network.$submenu."orig"];
                }
                else
                {
                  echo '<script>
                          window.location.href = "index.php";
                          window.location.replace("index.php");
                        </script>';
                }
              }
              $mts = explode(";", $origMsgTags);
              $maxLen = 10;
              foreach ($mts as $m)
              {
                $len = strlen($m);
                if($len >= $maxLen)
                  $maxLen = $len;
              }

              $width = 20 * $maxLen;
              $i = 0;
              echo '<h4>Originating</h4>';
              echo '<table class = "table">';
                echo '<thead>';
                  echo '<tr>';
                    foreach($mts as $m)
                    {
                      if(strlen($m) < 2)
                        continue;

                      if((int)($m) == 0)
                      {
                        echo '<th id = "msgTagsHead" style = "width: '.$width.'px;"><center><div id = "msgTags'.$m.'_'.$i.'" class = "msgTagsNameValueRqst">'.$m.'<br>0</div></center></th>';
                      }
                      else
                      {
                        echo '<th id = "msgTagsHead" style = "width: '.$width.'px;"><center><div id = "msgTags'.$m.'_'.$i.'" class = "msgTagsNameValueResp">'.$m.'<br>0/0</div></center></th>';
                      }
                      $i = $i + 1;
                    }
                  echo '</tr>';
                echo '</thead>';
              echo '</table>';
            }
            else
            {
              if($origProcId === "")
              {
                if(isset($_COOKIE[$userName.$projectName.$network.$submenu."orig"]))
                {
                  $origMsgTags = $_COOKIE[$userName.$projectName.$network.$submenu."orig"];
                }
                else
                {
                  echo '<script>
                          window.location.href = "index.php";
                          window.location.replace("index.php");
                        </script>';
                }
                if(isset($_COOKIE[$userName.$projectName.$network.$submenu."term"]))
                {
                  $termMsgTags = $_COOKIE[$userName.$projectName.$network.$submenu."orig"];
                }
                else
                {
                  echo '<script>
                          window.location.href = "index.php";
                          window.location.replace("index.php");
                        </script>';
                }
              }
              $mts = explode(";", $origMsgTags);
              $maxLen = 10;
              foreach ($mts as $m)
              {
                $len = strlen($m);
                if($len >= $maxLen)
                  $maxLen = $len;
              }

              $width = 20 * $maxLen;
              $i = 0;
              echo '<h4>Originating</h4>';
              echo '<table class="table">';
                echo '<thead>';
                  echo '<tr>';
                    foreach($mts as $m)
                    {
                      if(strlen($m) < 2)
                        continue;

                      if((int)($m) == 0)
                      {
                        echo '<th id = "msgTagsHead" style = "width: '.$width.'px;"><center><div id = "msgTags'.$m.'_'.$i.'" class = "msgTagsNameValueRqst">'.$m.'<br>0</div></center></th>';
                      }
                      else
                      {
                        echo '<th id = "msgTagsHead" style = "width: '.$width.'px;"><center><div id = "msgTags'.$m.'_'.$i.'" class = "msgTagsNameValueResp">'.$m.'<br>0/0</div></center></th>';
                      }
                      $i = $i + 1;
                    }
                  echo '</tr>';
                echo '</thead>';
              echo '</table><br>';
              $mts = explode(";", $termMsgTags);
              $maxLen = 10;
              foreach ($mts as $m)
              {
                $len = strlen($m);
                if($len >= $maxLen)
                  $maxLen = $len;
              }

              $width = 20 * $maxLen;
              echo '<h4>Terminating</h4>';
              echo '<table class="table">';
                echo '<thead>';
                  echo '<tr>';
                    foreach($mts as $m)
                    {
                      if(strlen($m) < 2)
                        continue;

                      if((int)($m) == 0)
                      {
                        echo '<th id = "msgTagsHead" style = "width: '.$width.'px;"><center><div id = "msgTags'.$m.'_'.$i.'" class = "msgTagsNameValueRqst">'.$m.'<br>0</div></center></th>';
                      }
                      else
                      {
                        echo '<th id = "msgTagsHead" style = "width: '.$width.'px;"><center><div id = "msgTags'.$m.'_'.$i.'" class = "msgTagsNameValueResp">'.$m.'<br>0/0</div></center></th>';
                      }
                      $i = $i + 1;
                    }
                  echo '</tr>';
                echo '</thead>';
              echo '</table>';
            }
            echo '</div><br><br>';

            echo '<div style = "position: relative;">';
              echo '<table class = "table" style = "margin-top: -30px;"><thead><tr>';
              echo '<th style = "width: 290px;font-family: Font Awesome\ 5 Free;">';
              echo '<div id = "runParameters">';
                echo '<div id = "loadRateParameter">';
                  echo '<label style = "font-size: 20px;"><b>Enter rate&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</b></label><input type = "number" value = "1" id = "load_rate">';
                echo '</div>';
                echo '<br>';
                echo '<div id = "loadLimitParameter">';
                  echo '<label style = "font-size: 20px;"><b>Enter run limit &nbsp;&nbsp:&nbsp&nbsp;</b></label><input type = "number" value = "0" id = "load_limit"><br>';
                  echo '<label> * 0 for no max limit</label>';
                echo '</div>';
                echo '<br>';

                if($origProcId !== "")
                {
                  echo '<input type = "button" value = "STOP" id = "runLoad">&nbsp;&nbsp;';
                  echo '<input type = "button" value = "PAUSE" id = "pauseLoad" style = "display: none;" onclick = "controlLoad(\'P\')">';
                  echo '<br><br><label id = "totTime" style = "display: none;">Total-Time: 0</label>';
                }
                else
                {
                  echo '<input type = "button" value = "RUN" id = "runLoad">&nbsp;&nbsp;';
                  echo '<input type = "button" value = "PAUSE" id = "pauseLoad" style = "display: none;" onclick = "controlLoad(\'P\')">';
                  echo '<br><br><label id = "totTime" style = "display: none;">Total-Time: 0</label>';
                }
                
                echo '<div id = "controlParameters" style = "position: relative;margin-top: 20px;display: none;">';
                  echo '<i class="fa fa-arrow-circle-o-up fa-2x" aria-hidden="true" style = "margin-right: 20px;margin-left: 5px; cursor: pointer;" onclick = "controlLoad(\'+\')"></i>Increase by 1<br>';
                  echo '<i class="fa fa-arrow-circle-o-down fa-2x" aria-hidden="true" style = "margin-right: 20px;margin-top: 20px;margin-left: 5px; cursor: pointer;" onclick = "controlLoad(\'-\')"></i>Decrease by 1<br>';
                  echo '<i class="fa fa-arrow-circle-up fa-2x" aria-hidden="true" style = "margin-right: 20px;margin-top: 20px;margin-left: 5px; cursor: pointer;" onclick = "controlLoad(\'*\')"></i>Increase by 10<br>';
                  echo '<i class="fa fa-arrow-circle-down fa-2x" aria-hidden="true" style = "margin-right: 20px;margin-top: 20px;margin-left: 5px; cursor: pointer;" onclick = "controlLoad(\'\/\')"></i>Decrease by 10';
                echo '</div>';
              echo '</div>';
              echo '</th>';
              echo '<th>';
              echo '<div id = "finalResult" style = "display: none;">';
                echo '<table><tbody>';
                echo '<tr style = "background-color: rgb(227, 238, 227);">';
                echo '<th>';
                  echo '<div id = "totCalls" style = "padding-top: 5px;padding-bottom: 5px;font-family: Font Awesome\ 5 Free;background-color: #9292ff;border-radius: 50px;font-size: 20px;width: 180px;border: 2px solid blue;">
                          <center>Calls Created<br>0</center>';
                  echo '</div>';
                echo '</th>';
                echo '<th>';
                  echo '<div id = "sucCalls" style = "padding-top: 5px;padding-bottom: 5px;font-family: Font Awesome\ 5 Free;background-color: #90d490;border-radius: 50px;font-size: 20px;width: 180px;border: 2px solid green;">
                          <center>Successfull Calls<br>0</center>';
                  echo '</div>';
                echo '</th>';
                echo '<th>';
                  echo '<div id = "fldCalls" style = "padding-top: 5px;padding-bottom: 5px;font-family: Font Awesome\ 5 Free;background-color: #e8a4a4;border-radius: 50px;font-size: 20px;width: 180px;border: 2px solid red;">
                          <center>Failed Calls<br>0</center>';
                  echo '</div>';
                echo '</th>';
                echo '</tr>';
                echo '<tr style = "background-color: rgb(227, 238, 227);">';
                echo '<th>';
                echo '</th>';
                echo '<th>';
                  echo '<div id = "sucRate" style = "padding-top: 5px;padding-bottom: 5px;font-family: Font Awesome\ 5 Free;background-color: #90d490;border-radius: 50px;font-size: 20px;width: 180px;border: 2px solid green;">
                          <center>Success Rate<br>0%</center>';
                  echo '</div>';
                echo '</th>';
                echo '<th>';
                echo '</th>';
                echo '</tr>';
                echo '</tbody></table>';
              echo '</div>';
              echo '</th>';
              echo '</tr></tbody></table>';
            echo '</div>';

            echo '<div id = "serverStats" style = "display: none;">';
              echo '<label style="font-size: 25px;"><b>Server Statistics</b></label><br>';
              if($network === "ims")
              {

              }
              else
              {
                echo '<div id = "sbcsig">
                        <label><b>SBC_SIG</b></label><br>
                        <input type = "button" class = "showServerStats" value = "START" style = "border-radius: 10px;">
                        <div id = "sbcSigUsage" style="height:300px;width:100%;border:2px solid black;border-radius: 10px;margin-top: 10px;"></div>
                      </div>';
                echo '<br><br>';
                echo '<div id = "ngcpe">
                        <label><b>NGCPE</b></label><br>
                        <input type = "text" id = "c5Ip" value = "" placeholder = "Enter C5 IP" style = "border-radius: 5px;">&nbsp;&nbsp;
                        <input type = "text" id = "c5Username" value = "" placeholder = "Enter C5 Username" style = "border-radius: 5px;">&nbsp;&nbsp;
                        <input type = "password" id = "c5Password" value = "" placeholder = "Enter C5 Password" style = "border-radius: 5px;"><br>
                        <input type = "button" class = "showServerStats" value = "START" style = "border-radius: 10px;margin-top: 5px;"><br>
                        <div id = "ngcpeUsage" style="height:300px;width:100%;border:2px solid black;border-radius: 10px;margin-top: 10px;"></div><br>
                      </div>';
                echo '<div id = "fs">
                        <label><b>FS</b></label><br>
                        <input type = "button" class = "showServerStats" value = "START" style = "border-radius: 10px;"><br>
                        <div id = "fsUsage" style="height:300px;width:100%;border:2px solid black;border-radius: 10px;margin-top: 10px;"></div><br>
                      </div>';
                echo '<div id = "ms">
                        <label><b>MS</b></label><br>
                        <input type = "button" class = "showServerStats" value = "START" style = "border-radius: 10px;"><br>
                        <div id = "msUsage" style="height:300px;width:100%;border:2px solid black;border-radius: 10px;margin-top: 10px;"></div>
                      </div>';
              }
            echo '</div>';
          }
          ?>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
    <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script>
      var scenarioSeleted = "";
      var submenu = '<?php echo $submenu; ?>';
      var endpoints = '<?php echo $endpoints; ?>';
      var menu = '<?php echo $menu; ?>';
      var origUsersList = '<?php echo $origUsersList; ?>', 
          termUsersList = '<?php echo $termUsersList; ?>';
      var origMsgTags = '<?php echo $origMsgTags; ?>';
      var termMsgTags = '<?php echo $termMsgTags; ?>';
      var origStatsUpdateTimer = "",
          termStatsUpdateTimer = "";
      var sbcsigStatsTimer = "";
      var ngcpeStatsTimer = "";
      var fsStatsTimer = "";
      var msStatsTimer = "";
      var origProcId = '<?php echo $origProcId; ?>';
      var termProcId = '<?php echo $termProcId; ?>';
      var startTime = new Date('<?php echo $startTime; ?>');
      var origlp = "<?php echo $origlp; ?>";
      var termlp = "<?php echo $termlp; ?>";
      var chartsbcsig, chartngcpe, chartfs, chartms;
      var sbcsig_x = 0;
      var ngcpe_x = 0;
      var fs_x = 0;
      var ms_x = 0;
      var c5Ip, c5Username, c5Password;
      var network = '<?php echo $network; ?>';
      var stopTotTimer = 0;
      var reloadRun = 0;

      // Document START
      $(document).ready(function(){
        if(menu.localeCompare("scenarios") == 0)
        {
          if((submenu.localeCompare("reg") == 0) ||
           (submenu.localeCompare("ltereg") == 0) ||
           (submenu.localeCompare("imsreg") == 0))
          {
            $('#regEndPoints').show("slow");
          }
          else if((submenu.localeCompare("call") == 0) ||
                  (submenu.localeCompare("msg") == 0) ||
                  (submenu.localeCompare("ims2imscall") == 0) ||
                  (submenu.localeCompare("ims2ltecall") == 0) ||
                  (submenu.localeCompare("lte2imscall") == 0) ||
                  (submenu.localeCompare("lte2ltecall") == 0) ||
                  (submenu.localeCompare("ims2imsmag") == 0) ||
                  (submenu.localeCompare("ims2ltemsg") == 0) ||
                  (submenu.localeCompare("lte2imsmsg") == 0) ||
                  (submenu.localeCompare("lte2ltemsg") == 0))
          {
            $('#basicCallEndPoints').show("slow");
          }
          origMsgTags = '<?php echo $origMsgTags; ?>';
          localStorage.setItem("origMsgTags", origMsgTags);
          termMsgTags = '<?php echo $termMsgTags; ?>';
          localStorage.setItem("termMsgTags", termMsgTags);
          console.log(origMsgTags+"\n"+termMsgTags);
        }
        else if(menu.localeCompare("users") == 0)
        {
          console.log(origUsersList+"\n"+termUsersList);
        }
        else if(menu.localeCompare("run") == 0)
        {
          if(network.localeCompare("ims") == 0)
          {

          }
          else
          {
            chartsbcsig = new CanvasJS.Chart("sbcSigUsage",
            {
              title:{
                text: "SBC-SIG MEMORY & CPU USSAGE"
              },
              axisX:{
                title: "Time(sec)",
              },
              axisY: [
              {
                title: "MEM Usage",
                lineColor: "#369EAD",
              },
              {
                title: "CPU Usage",
                lineColor: "#C24642",
              }
              ],
              data: [
              {
                type: "line",
                axisYIndex: 0,
                dataPoints: [
                ]
              },
              {
                type: "line",
                axisYIndex: 1,
                dataPoints: [
                ]
              }
              ],
            });
            chartngcpe = new CanvasJS.Chart("ngcpeUsage",
            {
              title:{
                text: "NGCPE MEMORY & CPU USSAGE"
              },
              axisX:{
                title: "Time(sec)",
              },
              axisY: [
              {
                title: "MEM Usage",
                lineColor: "#369EAD",
              },
              {
                title: "CPU Usage",
                lineColor: "#C24642",
              }
              ],
              data: [
              {
                type: "line",
                axisYIndex: 0,
                dataPoints: [
                ]
              },
              {
                type: "line",
                axisYIndex: 1,
                dataPoints: [
                ]
              }
              ],
            });
            chartfs = new CanvasJS.Chart("fsUsage",
            {
              title:{
                text: "FS MEMORY & CPU USSAGE"
              },
              axisX:{
                title: "Time(sec)",
              },
              axisY: [
              {
                title: "MEM Usage",
                lineColor: "#369EAD",
              },
              {
                title: "CPU Usage",
                lineColor: "#C24642",
              }
              ],
              data: [
              {
                type: "line",
                axisYIndex: 0,
                dataPoints: [
                ]
              },
              {
                type: "line",
                axisYIndex: 1,
                dataPoints: [
                ]
              }
              ],
            });
            chartms = new CanvasJS.Chart("msUsage",
            {
              title:{
                text: "MS MEMORY & CPU USSAGE"
              },
              axisX:{
                title: "Time(sec)",
              },
              axisY: [
              {
                title: "MEM Usage",
                lineColor: "#369EAD",
              },
              {
                title: "CPU Usage",
                lineColor: "#C24642",
              }
              ],
              data: [
              {
                type: "line",
                axisYIndex: 0,
                dataPoints: [
                ]
              },
              {
                type: "line",
                axisYIndex: 1,
                dataPoints: [
                ]
              }
              ],
            });
          }
          if($('#runLoad').val().localeCompare("STOP") == 0)
          {
            $('#load_rate').attr('readonly', true);
            $('#load_limit').attr('readonly', true);
            $('#pauseLoad').show();
            $('#totTime').show();
            $('#controlParameters').show("show");
            $('#finalResult').hide();
            $('#serverStats').show();
            renderCharts();
            localStorage.setItem("runStatus", "running");
            updateClientLoadStats();                  
          }
        }
      });

      /***********************************************************/
      // Test Cases Select
      /***********************************************************/
      $('#optionMenu').click(function() {
        if(menu.localeCompare("run") == 0 &&
           $('#runLoad').val().localeCompare("STOP") == 0)
        {
          var r = confirm("Load is running.\nChanges won't be submited until you stop load.\nWan't to continue?");
          if(r == true)
          {
            window.location.href = "scenarios.php?menu=cases";
          }
        }
        else if(menu.localeCompare("cases"))
        {
          window.location.href = "scenarios.php?menu=cases";
        }
      });

      /***********************************************************/
      // Scenario
      /***********************************************************/
      $('#scenarioMenu').click(function() {
        if(menu.localeCompare("run") == 0 &&
           $('#runLoad').val().localeCompare("STOP") == 0)
        {
          var r = confirm("Load is running.\nChanges won't be submited until you stop load.\nWan't to continue?");
          if(r == true)
          {
            window.location.href = "scenarios.php?menu=scenarios&submenu="+submenu+"&endpoints="+endpoints;
          }
        }
        else
        {
          if(submenu.localeCompare("") != 0)
          {
            if(menu.localeCompare("scenarios"))
            {
              window.location.href = "scenarios.php?menu=scenarios&submenu="+submenu+"&endpoints="+endpoints;
            }
          }
          else
          {
            alert("Select a test case first.");
          }
        }
      });
      $('.testCaseName').click(function(){
        if($(this).attr('value').localeCompare("callmsgreg") == 0)
        {
          $('#regScenario').toggle("slow");
        }
        else if($(this).attr('value').localeCompare("callmsg") == 0)
        {
          $('#callmsgScenario').toggle("slow");
        }
        else
        {
          window.location.href = "scenarios.php?menu=scenarios&submenu="+$(this).attr('value')+"&endpoints=originating";
        }
      });
      $('.endPointsName').click(function(){
        var name = $(this).text().toLowerCase();
        if(endpoints.localeCompare(name) != 0)
        {
          window.location.href = "scenarios.php?menu=scenarios&submenu="+submenu+"&endpoints="+name;
        }
      });
      $('.addUserHeaderToMsg').click(function(){
          var obj = $(this).prev();
          var val = obj.val();
          obj.val('');
          obj = obj.prev().prev();
          var fld = obj.val();
          obj.val('');
          if(val.length == 0 ||
             fld.length == 0)
          {
            alert("Fields cannot be empty.");
            return false;
          }
          obj = $(this).parent().prev().prev();
          if(obj.text().indexOf("v=") >= 0)
            obj = obj.prev();
          obj.append("<div class = 'newRqstHdr' ondblclick='removeNewRqstHdr(this)'>" + fld + ":" + val + "</div>");
      });
      function removeNewRqstHdr(obj)
      {
        obj.remove();
      }
      $('.scenarioGen').click(function(){
        var r;
        if(origProcId.localeCompare(""))
        {
          r = confirm("Load running. Changes will effect after you restart load.\nContinue?");
          if(r == false)
          {
            return false;
          }
        }
        else
        {
          r = confirm("Do you want to proceed?");
          if (r == false) 
          {
            return false;
          }
        }
        if((submenu.localeCompare("reg") == 0) ||
           (submenu.localeCompare("ltereg") == 0) ||
           (submenu.localeCompare("imsreg") == 0))
        {
          genRegScenario("R");
        }
        else if((submenu.localeCompare("call") == 0) ||
                (submenu.localeCompare("msg") == 0) ||
                (submenu.localeCompare("ims2imscall") == 0) ||
                (submenu.localeCompare("ims2ltecall") == 0) ||
                (submenu.localeCompare("lte2imscall") == 0) ||
                (submenu.localeCompare("lte2ltecall") == 0) ||
                (submenu.localeCompare("ims2imsmag") == 0) ||
                (submenu.localeCompare("ims2ltemsg") == 0) ||
                (submenu.localeCompare("lte2imsmsg") == 0) ||
                (submenu.localeCompare("lte2ltemsg") == 0))
        {
          if($(this).parent().attr('id').localeCompare("regScenario") == 0)
          {
            genRegScenario("R");
          }
          else
          {
            genCallMsgScenario("C");
          }
        }
      });
      function genRegScenario(mark)
      {
        var headLine = "<\?xml version = '1.0' encoding = 'ISO-8859-1' ?>\n" +
                       "<!DOCTYPE scenario SYSTEM 'sipp.dtd'>\n";
      
        var scenarioName = "<scenario name = 'Register Load Test'>\n\n";
        var scenarioNameEnd = "</scenario>\n";

        var sndTagStart = "<send>\n";
        var sndTagEnd = "</send>\n\n";

        var dataTagStart = "<![CDATA[\n";
        var dataTagEnd = "]]>\n";

        var label = 0;
        var resp = 1;

        var scenario = "";

        var ep = endpoints.substring(0,4);

        scenario = headLine +
                   scenarioName;

        $('#regScenario').children().each(function () {
          var respCode;
          if(this.className.localeCompare("origRegRqst") == 0)
          {
            var rqst = $(this).text();
            var expireVal = $(this).find('.regExpires').val();
            rqst = rqst.replace("expires=", "expires=" + expireVal + "\n");
            scenario += sndTagStart +
                        dataTagStart +
                        rqst +
                        dataTagEnd +
                        sndTagEnd;
          }
          else if(this.className.localeCompare("origRegResp") == 0)
          {
            if(this.firstChild.checked == true)
            {
              respCode = this.firstChild.value;
              if(this.firstChild.id.indexOf("optional") >= 0)
              {
                scenario += "<recv response=\""+respCode+"\" optional=\"true\" next=\"1\"></recv>\n\n";
                label = 1;
              }
              else
              {
                scenario += "<recv response=\""+respCode+"\"></recv>\n\n";
              }
              if(respCode.localeCompare("200") == 0 && label > 0)
              {
                scenario += "<label id='" + label + "'/>\n";
                label = 0;
              }
            }
            else if(this.firstChild.checked == false)
            {
              respCode = this.firstChild.value;
              scenario += "<!--recv response=\""+respCode+"\" optional=\"true\" next=\"1\"></recv-->\n\n";
            }
          }
          else if(this.id.localeCompare("optionalRqst") == 0)
          {
            if(label > 0)
            {
              scenario += "<label id='" + label + "'/>\n";
              label = 0;
            }
          }
        });
        scenario += scenarioNameEnd;
        console.log(scenario);
        /*
        $.ajax({
          url: 'generateRegScenario.php',
          type: 'POST',
          data: {
              S: scenario,
              SM: submenu,
              EP: ep,
              M: mark
          },
          success: function(result, status){
              var resp = JSON.parse(result);
              if(resp.statusFlag.localeCompare("0") == 0)
              {
                alert(resp.message);
              }
              location.reload();
          },
          error: function(status, error) {
              alert(status);
          }
        });*/
      }
      function genCallMsgScenario(mark)
      {
        var headLine = "<\?xml version = '1.0' encoding = 'ISO-8859-1' ?>\n" +
                       "<!DOCTYPE scenario SYSTEM 'sipp.dtd'>\n";
      
        var scenarioName = "<scenario name = 'Register Load Test'>\n\n";
        var scenarioNameEnd = "</scenario>\n";

        var sndTagStart = "<send>\n";
        var sndTagEnd = "</send>\n\n";

        var dataTagStart = "<![CDATA[\n";
        var dataTagEnd = "]]>\n";

        var label = 0;
        var resp = 1;

        var scenario = "";
        var ep = endpoints.substring(0,4);

        var respCode;
        var reqstEndPending = 0;

        scenario = headLine +
                   scenarioName;

        $('#callmsgScenario').children().each(function () {
          if(this.className.localeCompare("origRegRqst") == 0)
          {
            var rqst = $(this).text();
            var expireVal = $(this).find('.regExpires').val();
            rqst = rqst.replace("expires=", "expires=" + expireVal + "\n");
            scenario += sndTagStart +
                        dataTagStart +
                        rqst;
            reqstEndPending = 1;
          }
          else if(this.className.localeCompare("origRegSdp") == 0)
          {
            var rqst = $(this).text();
            var expireVal = $(this).find('.regExpires').val();
            rqst = rqst.replace("expires=", "expires=" + expireVal + "\n");
            scenario += "\n" +
                        rqst +
                        dataTagEnd +
                        sndTagEnd;
            reqstEndPending = 0; 
          }
          else if(this.className.localeCompare("origRegResp") == 0)
          {
            if(this.firstChild.checked == true)
            {
              respCode = this.firstChild.value;
              if(this.firstChild.id.indexOf("optional") >= 0)
              {
                scenario += "<recv response=\""+respCode+"\" optional=\"true\" next=\"1\"></recv>\n\n";
                label = 1;
              }
              else
              {
                scenario += "<recv response=\""+respCode+"\"></recv>\n\n";
              }
            }
            else if(this.firstChild.checked == false)
            {
              respCode = this.firstChild.value;
              scenario += "<!--recv response=\""+respCode+"\" optional=\"true\" next=\"1\"></recv-->\n\n";
            }
          }
          else if(this.id.localeCompare("newHeaderAdd") == 0)
          {
            if(reqstEndPending == 1)
            {
              scenario += dataTagEnd +
                          sndTagEnd;
              reqstEndPending = 0;
            }
          }
          else if(this.id.localeCompare("optionalRqst") == 0)
          {
            if(label > 0)
            {
              scenario += "<label id='" + label + "'/>\n";
              label = 0;
            }
          }
          else if(this.id.localeCompare("regRTPresp") == 0)
          {
            scenario += '<nop><action><exec rtp_stream="test_5sec.wav" /></action></nop>\n';
          }
          else if(this.id.localeCompare("callDur") == 0)
          {
            scenario += '<pause milliseconds="'+this.value+'"/>\n\n';
          }
        });
        scenario += scenarioNameEnd;
        console.log(scenario);
        /*
        $.ajax({
          url: 'generateRegScenario.php',
          type: 'POST',
          data: {
              S: scenario,
              SM: submenu,
              EP: ep,
              M: mark
          },
          success: function(result, status){
              var resp = JSON.parse(result);
              if(resp.statusFlag.localeCompare("0") == 0)
              {
                alert(resp.message);
              }
              location.reload();
          },
          error: function(status, error) {
              alert(status);
          }
        });*/
      }

      /***********************************************************/
      // User CSVs
      /***********************************************************/
      $('#userMenu').click(function() {
        if(menu.localeCompare("run") == 0 &&
           $('#runLoad').val().localeCompare("STOP") == 0)
        {
          var r = confirm("Load is running.\nChanges won't be submited until you stop load.\nWan't to continue?");
          if(r == true)
          {
            window.location.href = "scenarios.php?menu=users&submenu"+submenu;
          }
        }
        else
        {
          if(submenu.localeCompare("") != 0)
          {
            if(menu.localeCompare("users"))
            {
              window.location.href = "scenarios.php?menu=users&submenu="+submenu;
            }
          }
          else
          {
            alert("Select a test case first.");
          }
        }
      });
      $('#calledNumberSelect').click(function(){
        if(this.checked == true)
        {
          $('.cname').attr('value', document.getElementsByClassName("uname")[1].value);
        }
        else
        {
          $('.cname').attr('value', "");
        }
      }); 
      $('i[id=addUser]').click(function () {
          var usersCnt = $("div[class*='users']").length;
          if(usersCnt == 2)
          {
            return false;
          }
          $("#userDetails").append('<div class = "users" id = "users_' + usersCnt + '">\
                                      <br>\
                                      <i onclick = "removeUserAdded(this)" style = "cursor:pointer;" class="fa fa-times" aria-hidden="true"></i><br>\
                                      <label id = "userLabel"> Username </label>\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      <label id = "passLabel"> Password </label>\
                                      <br>\
                                      <input type = "number" class = "uname" value = "">\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                      <input type = "number" class = "pass" value = "">\
                                      </div>');
                      
          usersCnt += 1;

          if(usersCnt == 2)
          {
            $("#addUserMsg").show("slow");
            document.getElementById("userRange").checked = true;
            document.getElementById("addUser").style.opacity = "0.1";
          }
      });
      function removeUserAdded(obj)
      {
          var id = obj.parentNode.id;
          obj.parentNode.remove();
          usersCnt = $("div[class*='users']").length;
          $("#addUserMsg").hide();
          document.getElementById("userRange").checked = false;
          document.getElementById("addUser").style.opacity = "1";
      }
      $('input[id=generateCsv]').click(function() {
        var r;
        var U, P, U1, U2, P1, P2, CN;
        var userRange = false;
        var lip = '<?php echo $clientIp; ?>';
        var olp = "", tlp = "";
        var server = '<?php echo $borderIp; ?>';
        var tempoUsersList = "SEQUENTIALbr", temptUsersList = "SEQUENTIALbr";
        var portReq = 1;
        var k = 0;

        if(origProcId.localeCompare(""))
        {
          r = confirm("Load running. Changes will effect after you restart load.\nContinue?");
          if(r == false)
          {
            return false;
          }
        }
        else
        {
          r = confirm("Do you want to proceed?");
          if (r == false) 
          {
            return false;
          }
        }
        {
          if(origlp.length == 0)
            olp = "pr";
          else
            olp = origlp;
          if(termlp.length == 0)
            tlp = "pr";
          else
            tlp = termlp;

          if(document.getElementById("userRange") != null)
            userRange = document.getElementById("userRange").checked;

          if(userRange == true)
          {
            tempoUsersList = origUsersList;
            U1 = document.getElementsByClassName("uname")[0].value;
            U2 = document.getElementsByClassName("uname")[1].value;
            P1 = document.getElementsByClassName("pass")[0].value;
            P2 = document.getElementsByClassName("pass")[1].value;

            if(U1.length == 0 ||
               U2.length == 0 ||
               P1.length == 0 ||
               P2.length == 0)
            {
              alert("Fields cannot be empty");
              return false;
            }
            if(P1 != P2)
              k = 1;
            else
              k = 0;
            for(var i = U1, P = P1; i <= U2;i++,P = P + k)
            {
              if(origUsersList.indexOf(i) >= 0)
              {
                continue;
              }
              tempoUsersList += i+';[authentication username='+i+' password='+P+'];'+lip+';'+olp+';'+server+"br";
            }
            portReq = 1;
          }
          else if(submenu.localeCompare("reg") == 0 ||
                  submenu.localeCompare("imsreg") == 0 ||
                  submenu.localeCompare("ltereg") == 0)
          {
            tempoUsersList = origUsersList;
            U = document.getElementsByClassName("uname")[0].value;
            P = document.getElementsByClassName("pass")[0].value;
            if(U.length == 0 ||
              P.length == 0)
            {
              alert("Fields cannot be empty");
              return false;
            }
            else if(origUsersList.indexOf(U) >= 0)
            {
              alert("User alreday present.");
              return false;
            }
            tempoUsersList += U+';[authentication username='+U+' password='+P+'];'+lip+';'+olp+';'+server+"br";
            portReq = 1;
          }
          else
          {
            if(origUsersList.length > 12 &&
               termUsersList.length > 12)
            {
              alert("Single user for each side allowed.");
              return false;
            }

            U1 = document.getElementsByClassName("uname")[0].value;
            P1 = document.getElementsByClassName("pass")[0].value;
            if((U1.length == 0 ||
                P1.length == 0) &&
               origUsersList.length == 12)
            {
              alert("Orig side fields cannot be empty");
              return false;
            }

            U2 = document.getElementsByClassName("uname")[1].value;
            P2 = document.getElementsByClassName("pass")[1].value;
            CN = document.getElementsByClassName("cname")[0].value;
            if((U2.length == 0 ||
                P2.length == 0 ||
                CN.length == 0) &&
               termUsersList.length == 12)
            {
              alert("Term side fields cannot be empty");
              return false;
            }
            
            if(origUsersList.length > 12)
            {
              if(U1.length != 0 ||
                 P1.length != 0)
              {
                alert("Single user for ORIG side allowed.");
                return false;
              }
              tempoUsersList = origUsersList;
            }
            else if(U1.localeCompare(U2) == 0 ||
                    termUsersList.indexOf(U1) >= 0)
            {
              alert("Orig and Term users cannot be same");
              return false;
            }
            else if(CN.length == 0)
            {
              alert("Provide the Called number");
              return false;
            }
            else
            {
              tempoUsersList += U1+';[authentication username='+U1+' password='+P1+'];'+lip+';'+olp+';'+server+";"+CN+"br";
            }

            if(termUsersList.length > 12)
            {
              if(U2.length != 0 ||
                 P2.length != 0)
              {
                alert("Single user for TERM side allowed.");
                return false;
              }
              temptUsersList = termUsersList;
            }
            else if(U2.localeCompare(U1) == 0 ||
                    origUsersList.split(U2).length >= 3)
            {
              alert("Term and Orig users cannot be same");
              return false;
            }
            else
            {
              temptUsersList += U2+';[authentication username='+U2+' password='+P2+'];'+lip+';'+tlp+';'+server+"br";
              if(origUsersList.length > 12)
              {
                var temp = origUsersList.substring(0, origUsersList.lastIndexOf(";"));
                tempoUsersList = temp+";"+CN+"br";
              }
            }
            portReq = 2;
          }

          console.log(tempoUsersList+"\n"+temptUsersList);
        
          $.ajax({
              url: 'generateRegUserCsv.php',
              type: 'POST',
              data: {
                  OUL: tempoUsersList,
                  TUL: temptUsersList,
                  PR: portReq,
                  SM: submenu
              },
              success: function(result, status){
                console.log(result);
                var resp = JSON.parse(result);
                if(resp.statusFlag.localeCompare("0") == 0)
                {
                  alert(resp.message);
                }
                else
                {
                  if(resp.oport != null && origlp.length == 0)
                    origlp = resp.oport;
                  if(resp.tport != null && termlp.length == 0)
                    termlp = resp.tport;
                  location.reload();
                }
              },
              error: function(status, error) {
                  alert(status);
              }
          });
        }

        return false;
      });
      function removeUserRow(obj)
      {
        var r = confirm("Do you want to proceed?");
        if (r == false) 
        {
          return false;
        }
        var text, port_number;
        var tempoUsersList = "", temptUsersList = "", ep = "";

        if(submenu.localeCompare("reg") == 0 ||
           submenu.localeCompare("imsreg") == 0 ||
           submenu.localeCompare("ltereg") == 0)
        {
          text = obj.innerHTML.replace(/<td>/g,"").replace(/<\/td>/g,";").replace(/\n;/g,"br");
          port_number = obj.firstChild.nextSibling.nextSibling.nextSibling.innerText;
          console.log(port_number);
          console.log(text);
          tempoUsersList = origUsersList.replace(text, "");
          console.log(tempoUsersList);
          if(tempoUsersList.length > 12)
          {
            $.ajax({
                url: 'generateRegUserCsv.php',
                type: 'POST',
                data: {
                    OUL: tempoUsersList,
                    TUL: temptUsersList,
                    PR: "1",
                    SM: submenu
                },
                success: function(result, status){
                  console.log(result);
                  var resp = JSON.parse(result);
                  if(resp.statusFlag.localeCompare("0") == 0)
                  {
                    alert(resp.message);
                  }
                  else
                  {
                    if(resp.oport != null &&
                       origlp.length == 0)
                    {
                      origlp = resp.oport;
                    }
                    location.reload();
                  }
                },
                error: function(status, error) {
                    alert(status);
                }
            });
          }
          else
          {
            $.ajax({
                url: 'removeRegUserFromCsv.php',
                type: 'POST',
                data: {
                    UL: tempoUsersList,
                    PN: port_number,
                    SM: submenu,
                    EP: "O"
                },
                success: function(result, status){
                    var resp = JSON.parse(result);
                    if(resp.statusFlag.localeCompare("0") == 0)
                    {
                      alert(resp.message);
                    }
                    else
                    {
                      location.reload();
                    }
                },
                error: function(status, error) {
                    alert(status);
                }
            });
          }
        }
        else
        {
          text = obj.innerHTML.replace(/<td>/g,"").replace(/<\/td>/g,";").replace(/\n;/g,"br");
          port_number = obj.firstChild.nextSibling.nextSibling.nextSibling.nextSibling.innerText;
          console.log(port_number);
          console.log(text);
          if(text.indexOf("ORIG") >= 0)
          {
            text = text.replace(/ORIG;/g,"");
            tempoUsersList = origUsersList.replace(text, "");
            console.log(tempoUsersList);
            ep = "O";
          }
          else if(text.indexOf("ORIG") >= 0)
          {
            text = text.replace(/TERM;/g,"");
            tempoUsersList = termUsersList.replace(text, "");
            console.log(tempoUsersList);
            ep = "T";
          }
          $.ajax({
            url: 'removeRegUserFromCsv.php',
            type: 'POST',
            data: {
                UL: tempoUsersList,
                PN: port_number,
                SM: submenu,
                EP: ep
            },
            success: function(result, status){
                var resp = JSON.parse(result);
                if(resp.statusFlag.localeCompare("0") == 0)
                {
                  alert(resp.message);
                }
                else
                {
                  location.reload();
                }
            },
            error: function(status, error) {
                alert(status);
            }
          });
        } 
      }
      $('#removeAllUserRows').click(function(){
        var r = confirm("Do you want to proceed?");
        if (r == false) 
        {
          return false;
        }
        var obj, port_number, tempoUsersList;
        if(submenu.localeCompare("reg") == 0 ||
           submenu.localeCompare("imsreg") == 0 ||
           submenu.localeCompare("ltereg") == 0)
        {
          obj = document.getElementById("users_row_0");
          port_number = obj.firstChild.nextSibling.nextSibling.nextSibling.innerText;
          console.log(port_number);
          tempoUsersList = "SEQUENTIALbr";
          $.ajax({
            url: 'removeRegUserFromCsv.php',
            type: 'POST',
            data: {
                UL: tempoUsersList,
                PN: port_number,
                SM: submenu,
                EP: "O"
            },
            success: function(result, status){
                var resp = JSON.parse(result);
                if(resp.statusFlag.localeCompare("0") == 0)
                {
                  alert(resp.message);
                  return false;
                }
                else
                {
                  location.reload();
                }
            },
            error: function(status, error) {
                alert(status);
                return false;
            }
          });
        } 
        else
        {
          obj = document.getElementById("users_row_0");
          if(obj != null)
            port_number = obj.firstChild.nextSibling.nextSibling.nextSibling.nextSibling.innerText;
          console.log(port_number);
          tempoUsersList = "SEQUENTIALbr";
          obj = document.getElementById("users_row_1");
          if(obj != null)
            port_number += "br"+obj.firstChild.nextSibling.nextSibling.nextSibling.nextSibling.innerText;
          console.log(port_number);
          $.ajax({
            url: 'removeRegUserFromCsv.php',
            type: 'POST',
            data: {
                UL: tempoUsersList,
                PN: port_number,
                SM: submenu,
                EP: "O;T"
            },
            success: function(result, status){
              console.log(result);
                var resp = JSON.parse(result);
                if(resp.statusFlag.localeCompare("0") == 0)
                {
                  alert(resp.message);
                  return false;
                }
                else
                {
                  location.reload();
                }
            },
            error: function(status, error) {
                alert(status);
                return false;
            }
          });
        }
      });

      /***********************************************************/
      // Run options
      /***********************************************************/
      $('#runMenu').click(function() {
        if(menu.localeCompare("run"))
        {
          if(submenu.localeCompare("") != 0)
          {
            window.location.href = "scenarios.php?menu=run&submenu="+submenu;
          }
          else
          {
            alert("Select a test case first.");
          }
        }
      });

      function renderCharts()
      {
        if(network.localeCompare("ims") == 0)
        {

        }
        else
        {
          chartsbcsig.render();
          chartngcpe.render();
          chartfs.render();
          chartms.render();
        }
      }

      function controlLoad(val)
      {
        var lr = parseInt($('#load_rate').val());
        if(val.localeCompare('-') == 0)
        {
          if((lr - 1) < 1)
          {
            alert("Cannot decrease below this");
            return false;
          }
        }
        else if(val.localeCompare('/') == 0)
        {
          if((lr - 10) < 1)
          {
            alert("Cannot decrease below this");
            return false;
          }
        }
        $.ajax({
          type: "POST",
          url: "controlLoad.php",
          data: {
            PAR: val,
            SM: submenu
          },
          success:function(data)
          {
            console.log(data);
            if(data[0] == '<')
            {
              console.log("Connection issue");
              return false;
            }

            var resp = JSON.parse(data);
            console.log(resp);
            if(resp.statusFlag != null &&
                resp.statusFlag.localeCompare("0") == 0)
            {
              alert(resp.message);
            }
            else
            {
              if(resp.message.localeCompare("p") == 0)
              {
                if($('#pauseLoad').val().localeCompare("pause") == 0)
                {
                  $('#pauseLoad').attr('value', 'resume');
                  stopTotTimer = 1;
                }
                else if($('#pauseLoad').val().localeCompare("resume") == 0)
                {
                  $('#pauseLoad').attr('value', 'pause');
                  stopTotTimer = 0;
                }
              }
              else if(resp.message.localeCompare("+") == 0)
              {
                var num = parseInt($('#load_rate').val());
                num += 1;
                $('#load_rate').attr('value', num);
              }
              else if(resp.message.localeCompare("-") == 0)
              {
                var num = parseInt($('#load_rate').val());
                num -= 1;
                $('#load_rate').attr('value', num);
              }
              else if(resp.message.localeCompare("*") == 0)
              {
                var num = parseInt($('#load_rate').val());
                num += 10;
                $('#load_rate').attr('value', num);
              }
              else if(resp.message.localeCompare("/") == 0)
              {
                var num = parseInt($('#load_rate').val());
                num -= 10;
                $('#load_rate').attr('value', num);
              }
            }
          }
        });
        return false;
      }

      $('.showServerStats').click(function(){
        c5Ip = $('#c5Ip').val();
        c5Username = $('#c5Username').val();
        c5Password = $('#c5Password').val();

        if($(this).parent().attr('id').localeCompare("sbcsig") == 0)
        {
          if($(this).attr('value').localeCompare("START") == 0)
          {
            sbcsigStatsShow(this);
            $(this).attr('value', 'STOP');
          }
          else
          {
            clearTimeout(sbcsigStatsTimer);
            $(this).attr('value', 'START');
          }       
        }
        else if($(this).parent().attr('id').localeCompare("ngcpe") == 0)
        {
          if(c5Ip.localeCompare("") == 0 &&
             c5Username.localeCompare("") == 0 && 
             c5Password.localeCompare("") == 0)
          {
            alert("Pleasse provide C5 credentials");
            return false;
          }
          if($(this).attr('value').localeCompare("START") == 0)
          {
            ngcpeStatsShow(this);
            $(this).attr('value', 'STOP');
          }
          else
          {
            clearTimeout(sbcsigStatsTimer);
            $(this).attr('value', 'START');
          }       
        }
        else if($(this).parent().attr('id').localeCompare("fs") == 0)
        {
          if(c5Ip.localeCompare("") == 0 &&
             c5Username.localeCompare("") == 0 && 
             c5Password.localeCompare("") == 0)
          {
            alert("Pleasse provide C5 credentials");
            return false;
          }
          if($(this).attr('value').localeCompare("START") == 0)
          {
            fsStatsShow(this);
            $(this).attr('value', 'STOP');
          }
          else
          {
            clearTimeout(sbcsigStatsTimer);
            $(this).attr('value', 'START');
          }       
        }
        else if($(this).parent().attr('id').localeCompare("ms") == 0)
        {
          if(c5Ip.localeCompare("") == 0 &&
             c5Username.localeCompare("") == 0 && 
             c5Password.localeCompare("") == 0)
          {
            alert("Pleasse provide C5 credentials");
            return false;
          }
          if($(this).attr('value').localeCompare("START") == 0)
          {
            msStatsShow(this);
            $(this).attr('value', 'STOP');
          }
          else
          {
            clearTimeout(sbcsigStatsTimer);
            $(this).attr('value', 'START');
          }       
        }
      });

      function sbcsigStatsShow(obj)
      {
          $.ajax({
              type: "POST",
              url: "getServerStats.php",
              data: {
                MODULE: "cdotsi"
              },
              success:function(data)
              {
                  console.log(data);
                  if(data[0] == '<')
                  {
                    console.log("Connection issue");
                    sbcsigStatsTimer = setTimeout(function(){
                                          sbcsigStatsShow();
                                      }, 5000);
                    return false;
                  }

                  var resp = JSON.parse(data);
                  console.log(resp);
                  if(resp.statusFlag != null &&
                     resp.statusFlag.localeCompare("0") == 0)
                  {
                    alert(resp.message);
                    clearTimeout(sbcsigStatsTimer);
                    obj.value = "START";
                  }
                  else
                  {
                    var mem = parseFloat(resp.mem);
                    var cpu = parseFloat(resp.cpu);
                    chartsbcsig.data[0].dataPoints.push({x: sbcsig_x, y: mem});
                    chartsbcsig.data[1].dataPoints.push({x: sbcsig_x, y: cpu});
                    chartsbcsig.render();
                    sbcsig_x += 5;
                    sbcsigStatsTimer = setTimeout(function(){
                                          sbcsigStatsShow();
                                    }, 5000);
                    document.getElementById("c5Ip").disabled = true;
                    document.getElementById("c5Username").disabled = true;
                    document.getElementById("c5Password").disabled = true;
                  }
              }
          });
      }
      function ngcpeStatsShow(obj)
      {
          $.ajax({
              type: "POST",
              url: "getServerStats.php",
              data: {
                MODULE: "call_agent",
                IP: c5Ip,
                U: c5Username,
                P: c5Password
              },
              success:function(data)
              {
                  console.log(data);
                  if(data[0] == '<')
                  {
                    console.log("Connection issue");
                    ngcpeStatsTimer = setTimeout(function(){
                                          ngcpeStatsShow();
                                      }, 5000);
                    return false;
                  }

                  var resp = JSON.parse(data);
                  console.log(resp);
                  if(resp.statusFlag != null &&
                     resp.statusFlag.localeCompare("0") == 0)
                  {
                    alert(resp.message);
                    clearTimeout(ngcpeStatsTimer);
                    obj.value = "START";
                  }
                  else
                  {
                    var mem = parseFloat(resp.mem);
                    var cpu = parseFloat(resp.cpu);
                    chartngcpe.data[0].dataPoints.push({x: ngcpe_x, y: mem});
                    chartngcpe.data[1].dataPoints.push({x: ngcpe_x, y: cpu});
                    chartngcpe.render();
                    ngcpe_x += 5;
                    ngcpeStatsTimer = setTimeout(function(){
                                          ngcpeStatsShow();
                                    }, 5000);
                    $('#c5Ip').attr('readonly', true);
                    $('#c5Username').attr('readonly', true);
                    $('#c5Password').attr('readonly', true);
                  }
              }
          });
      }
      function fsStatsShow(obj)
      {
          $.ajax({
              type: "POST",
              url: "getServerStats.php",
              data: {
                MODULE: "fserver",
                IP: c5Ip,
                U: c5Username,
                P: c5Password
              },
              success:function(data)
              {
                  console.log(data);
                  if(data[0] == '<')
                  {
                    console.log("Connection issue");
                    fsStatsTimer = setTimeout(function(){
                                          fsStatsShow();
                                      }, 5000);
                    return false;
                  }

                  var resp = JSON.parse(data);
                  console.log(resp);
                  if(resp.statusFlag != null &&
                     resp.statusFlag.localeCompare("0") == 0)
                  {
                    alert(resp.message);
                    clearTimeout(fsStatsTimer);
                    obj.value = "START";
                  }
                  else
                  {
                    var mem = parseFloat(resp.mem);
                    var cpu = parseFloat(resp.cpu);
                    chartfs.data[0].dataPoints.push({x: fs_x, y: mem});
                    chartfs.data[1].dataPoints.push({x: fs_x, y: cpu});
                    chartfs.render();
                    fs_x += 5;
                    fsStatsTimer = setTimeout(function(){
                                          fsStatsShow();
                                    }, 5000);
                    $('#c5Ip').attr('readonly', true);
                    $('#c5Username').attr('readonly', true);
                    $('#c5Password').attr('readonly', true);
                  }
              }
          });
      }
      function msStatsShow(obj)
      {
          $.ajax({
              type: "POST",
              url: "getServerStats.php",
              data: {
                MODULE: "mserver",
                IP: c5Ip,
                U: c5Username,
                P: c5Password
              },
              success:function(data)
              {
                  console.log(data);
                  if(data[0] == '<')
                  {
                    console.log("Connection issue");
                    msStatsTimer = setTimeout(function(){
                                          msStatsShow();
                                      }, 5000);
                    return false;
                  }

                  var resp = JSON.parse(data);
                  console.log(resp);
                  if(resp.statusFlag != null &&
                     resp.statusFlag.localeCompare("0") == 0)
                  {
                    alert(resp.message);
                    clearTimeout(msStatsTimer);
                    obj.value = "START";
                  }
                  else
                  {
                    var mem = parseFloat(resp.mem);
                    var cpu = parseFloat(resp.cpu);
                    chartms.data[0].dataPoints.push({x: ms_x, y: mem});
                    chartms.data[1].dataPoints.push({x: ms_x, y: cpu});
                    chartms.render();
                    ms_x += 5;
                    msStatsTimer = setTimeout(function(){
                                          msStatsShow();
                                    }, 5000);
                    $('#c5Ip').attr('readonly', true);
                    $('#c5Username').attr('readonly', true);
                    $('#c5Password').attr('readonly', true);
                  }
              }
          });
      }

      function updateClientLoadStats(){
          $.ajax({
              type: "POST",
              url: "getClientLoadStats.php",
              data: {
                OPID: origProcId,
                TPID: termProcId,
                SM: submenu,
                MT: origMsgTags+"_"+termMsgTags
              },
              success:function(data)
              {
                  console.log(data);
                  if(data[0] == '<')
                  {
                    console.log("Connection issue");
                    origStatsUpdateTimer = setTimeout(function(){
                                          updateClientLoadStats();
                                      }, 5000);
                    return false;
                  }

                  var resp = JSON.parse(data);
                  console.log(resp);
                  if(resp.statusFlag != null &&
                     resp.statusFlag.localeCompare("0") == 0)
                  {
                    alert(resp.message);
                    clearTimeout(origStatsUpdateTimer);
                  }
                  else
                  {
                    var runFlag = 1;
                    for(keys in resp)
                    {
                      if((keys.localeCompare("statusFlag") == 0 &&
                         resp[keys].localeCompare("OFF") == 0) ||
                         (keys.localeCompare("message") == 0))
                      {
                        runFlag = 0;
                        break;
                      }
                      else if(document.getElementById("msgTags" + keys) != null)
                      {
                        document.getElementById("msgTags" + keys).innerHTML = keys.substr(0, keys.indexOf('_')) + "<br>" + resp[keys];
                      }
                    }
                    if(runFlag == 1)
                    {
                      if(stopTotTime == 0)
                      {
                        var curTime = new Date();
                        var totTime = curTime - startTime;
                        document.getElementById("totTime").innerHTML = "Total-Time: " + Math.floor((totTime / 1000)) + " sec";
                      }
                      origStatsUpdateTimer = setTimeout(function(){
                                          updateClientLoadStats();
                                      }, 5000);
                    }
                    else
                    {
                      clearTimeout(origStatsUpdateTimer);
                      stopLoad();
                    }
                  }
              },
              error:function(data)
              {
                alert(data);
              }
          });
      }

      function startLoad()
      {
        var loadRate = document.getElementById("load_rate").value;
        var loadLimit = document.getElementById("load_limit").value;
        startTime = new Date();
          $.ajax({
              type: "POST",
              url: "startLoadTesting.php",
              data: {
                SM: submenu,
                LR: loadRate,
                LL: loadLimit,
                ST: startTime,
                MT: msgTags
              },
              success:function(data)
              {
                console.log(data);
                //return false;
                if(data[0] == '<')
                {
                  alert("Connection issue with the client");
                  return false;
                }
                var resp = JSON.parse(data);
                if(resp.statusFlag.localeCompare("0") == 0)
                {
                  alert(resp.message);
                }
                else if(resp.statusFlag.localeCompare("2") == 0)
                {
                  for(keys in resp)
                  {
                    if(keys.localeCompare("statusFlag") == 0 ||
                       keys.localeCompare("message") == 0)
                    {
                      continue;
                    }
                    else if(keys.localeCompare("totCalls") == 0)
                    {
                      document.getElementById("totCalls").innerHTML = "<center>Calls Created<br>"+resp[keys]+"</center>";
                      totalCalls = parseInt(resp[keys]);
                    }
                    else if(keys.localeCompare("sucCalls") == 0)
                    {
                      document.getElementById("sucCalls").innerHTML = "<center>Calls Created<br>"+resp[keys]+"</center>";
                      successCalls = parseInt(resp[keys]);
                    }
                    else if(keys.localeCompare("fldCalls") == 0)
                    {
                      document.getElementById("fldCalls").innerHTML = "<center>Calls Created<br>"+resp[keys]+"</center>";
                    }
                    else if(document.getElementById("msgTags" + keys) != null)
                    {
                      document.getElementById("msgTags" + keys).innerHTML = keys.substr(0, keys.indexOf('_')) + "<br>" + resp[keys];
                    }
                  }
                  var curTime = new Date();
                  var totTime = curTime - startTime;
                  document.getElementById("totTime").innerHTML = "Total-Time: " + Math.floor((totTime / 1000)) + " sec";
                }
                else
                {
                  document.getElementById("runLoad").blur();
                  $('#runLoad').attr('value', 'STOP');
                  localStorage.setItem("runStatus", "running");
                  pid = resp.procId;
                  $('#load_rate').attr('readonly', false);
                  $('#load_limit').attr('readonly', false);
                  $('#controlParameters').show("show");
                  $('#serverStats').show();
                  renderCharts();
                  $('#pauseLoad').show();
                  $('#totTime').show();
                  $('#finalResult').hide();
                  origStatsUpdateTimer = setTimeout(function(){
                                          updateClientLoadStats();
                                      }, 5000);
                }
              },
              error:function(data)
              {
                alert(data);
              }
          });
      }

      function stopLoad()
      {
        $.ajax({
            type: "POST",
            url: "stopLoadTesting.php",
            data: {
              SM: submenu,
              PID: pid,
              MT: msgTags
            },
            success:function(data)
            {
              if(data[0] == '<')
              {
                console.log(data);
                alert("Connection issue with the client");
                return false;
              }
              var resp = JSON.parse(data);
              if(resp.statusFlag != null &&
                 resp.statusFlag.localeCompare("0") == 0)
              {
                alert(resp.message);
                return false;
              }
              var totalCalls = 0, successCalls = 0, rate = 0.0;
              for(keys in resp)
              {
                if(keys.localeCompare("totCalls") == 0)
                {
                  document.getElementById("totCalls").innerHTML = "<center>Calls Created<br>"+resp[keys]+"</center>";
                  totalCalls = parseInt(resp[keys]);
                }
                else if(keys.localeCompare("sucCalls") == 0)
                {
                  document.getElementById("sucCalls").innerHTML = "<center>Calls Created<br>"+resp[keys]+"</center>";
                  successCalls = parseInt(resp[keys]);
                }
                else if(keys.localeCompare("fldCalls") == 0)
                {
                  document.getElementById("fldCalls").innerHTML = "<center>Calls Created<br>"+resp[keys]+"</center>";
                }
                else if(document.getElementById("msgTags" + keys) != null)
                {
                  document.getElementById("msgTags" + keys).innerHTML = keys.substr(0, keys.indexOf('_')) + "<br>" + resp[keys];
                }
              }
              rate = (successCalls / totalCalls) * 100;
              document.getElementById("sucRate").innerHTML = "<center>Success Rate<br>"+rate+"%</center>";
              var curTime = new Date();
              var totTime = curTime - startTime;
              document.getElementById("totTime").innerHTML = "Total-Time: " + Math.floor((totTime / 1000)) + " sec";          
              clearTimeout(origStatsUpdateTimer);
              document.getElementById("runLoad").blur();
              $('#runLoad').attr('value', 'RUN');
              localStorage.setItem("runStatus", "stopped");
              $('#controlParameters').hide("show");
              $('#load_rate').attr('readonly', false);
              $('#load_limit').attr('readonly', false);
              $('#pauseLoad').hide();
              if(network.localeCompare("ims") == 0)
              {

              }
              else
              {
                $('.showServerStats').attr('value', 'START');
                clearTimeout(sbcsigStatsTimer);
                clearTimeout(ngcpeStatsTimer);
                clearTimeout(fsStatsTimer);
                clearTimeout(msStatsTimer);
                $('#c5Ip').attr('readonly', false);
                $('#c5Username').attr('readonly', false);
                $('#c5Password').attr('readonly', false);
              }
              $('#serverStats').hide();
              $('#finalResult').show('slow');
              reloadRun = 1;
            },
            error:function(data)
            {
              alert(data);
            }
        });
      }

      $('#runLoad').click(function(){
        if($(this).val().localeCompare("RUN") == 0)
        {
          if(reloadRun == 1)
          {
            alert("Reload the page to effect any changes if done to the scenarios or CSVs.");
          }
          else
          {
            $('#totTime').hide();
            startLoad();
          }
        }
        else
        {
          var r = confirm("Are you sure want to quit load?");
          if (r == false) 
          {
            return false;
          }
          clearTimeout(origStatsUpdateTimer);
          stopLoad();
        }
      });

      window.onbeforeunload = function(event)
      {
        if($('#runLoad').val().localeCompare("STOP") == 0)
        {
          var r = confirm("Load running. Do you want to quit?");
          return r;
        }
      };

    </script>
  </body>
</html>