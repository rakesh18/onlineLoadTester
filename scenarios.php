<?php
  session_start();
  include('dbConfig.php');
  
  $usersList = "";
  $borderIp  = "";
  $clientIp  = "";
  $msgTags   = "";
  $procId    = "";
  $startTime = "";
  $origlp    = "";
  $termlp    = "";
  $submenu   = "";

  if(isset($_COOKIE["userName"]))
    $userName = $_COOKIE['userName'];
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

  if(isset($_GET['menu']))
  {
    $menu = $_GET['menu'];
    echo '<div id = "topPane">
          </div>';
  }
  else
  {
    exit("Server error");
  }
  if(isset($_GET['submenu']))
  {
    $submenu = $_GET['submenu'];
  }
  $userDir = "projects/".$location."/".$userName."_".$projectName."_load/".$submenu."/";
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
              if($submenu === "reg")
              {
                echo '<div id = "regEndPoints" class = "endPoints">
                        <div id = "origEndUserOpt">ORIGINATING</div>
                      </div>';
              }
              else if($submenu === "basiccall")
              {
                echo '<div id = "basicCallEndPoints" class = "endPoints">
                        <div id = "origEndUserOpt">ORIGINATING</div>
                        <div id = "termEndUserOpt">TERMINATING</div>
                      </div>';
              }
              else if($submenu === "ivrscall")
              {
                echo '<div id = "ivrsCallEndPoints" class = "endPoints">
                        <div id = "origEndUserOpt">ORIGINATING</div>
                      </div>';
              }
              else if($submenu === "xfer")
              {
                echo '<div id = "xferCallEndPoints" class = "endPoints">
                        <div id = "origEndUserOpt">ORIGINATING</div>
                        <div id = "midEndUserOpt">TRANSFERER</div>
                        <div id = "termEndUserOpt">TERMINATING</div>
                      </div>';
              }
              else if($submenu === "msg")
              {
                echo '<div id = "msgEndPoints" class = "endPoints">
                        <div id = "origEndUserOpt">ORIGINATING</div>
                        <div id = "termEndUserOpt">TERMINATING</div>
                      </div>';
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
            echo '
                  <div id = "callOptions">
                    <div id = "reg">
                      REGISTRATION
                    </div>

                    <div id = "basicCall">
                      BASIC CALL
                    </div>

                    <div id = "ivrsCall">
                      IVRS CALL
                    </div>

                    <div id = "xferCall">
                      TRANSFER
                    </div>

                    <div id = "msg">
                      MESSAGE
                    </div>
                  </div>';
          }
          else if($menu === "scenarios")
          {
            echo '<div id = "scenarioOptions">';
            if($submenu === "reg")
            {
              echo '<div id = "regScenario">';
                $scenarioFile = fopen($userDir . "reg_scenario.xml", "r") or die("Unable to open file!");
                $sendTag = 0;
                $recvTag = 0;
                $startCustomField = 0;

                while(!feof($scenarioFile))
                {
                  $line = fgets($scenarioFile);
                  if(strlen($line) == 1)
                    continue;
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
                      echo '<div class = "origRegRqst">';
                      $msgTags .= "REGISTER;";
                    }
                    else if(strpos($line, "</send") !== FALSE)
                    {
                      $sendTag = 0;
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
                      
                      if(strpos($line, "403") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg403resp" value = "403 Forbidden" checked = "checked"  class = "respInput">
                              <label for = "reg403resp" class = "respLabel">403 Forbidden</label>';
                        $msgTags .= "403;";
                      }
                      else if(strpos($line, "503") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg503resp" value = "503 Service Unavailable" checked = "checked"  class = "respInput">
                              <label for = "reg503resp" class = "respLabel">503 Service Unavailable</label>';
                        $msgTags .= "503;";
                      }
                      else if(strpos($line, "500") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg500resp" value = "500 Server Internal Error" checked = "checked"  class = "respInput">
                              <label for = "reg500resp" class = "respLabel">500 Server Internal Error</label>';
                        $msgTags .= "500;";
                      }
                      else if(strpos($line, "401") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg401resp" value = "401 Unauthorized" checked = "checked" onclick = "return false;" class = "respInput">
                              <label for = "reg401resp" class = "respLabel">401 Unauthorized</label>';
                        $msgTags .= "401;";
                      }
                      else if(strpos($line, "200") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg200resp" checked = "checked" onclick = "return false;" class = "respInput">
                              <label for = "reg200resp" class = "respLabel">200 OK</label>';
                        $msgTags .= "200;";
                      }
                    }
                    else if(strpos($line, "<!--recv") !== FALSE)
                    {
                      if($recvTag == 0)
                      {
                        echo '<div class = "origRegResp">';
                        $recvTag = 1;
                      }
                      
                      if(strpos($line, "403") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg403resp" value = "403 Forbidden" class = "respInput">
                              <label for = "reg403resp" class = "respLabel">403 Forbidden</label>';
                      }
                      else if(strpos($line, "503") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg503resp" value = "503 Service Unavailable" class = "respInput">
                              <label for = "reg503resp" class = "respLabel">503 Service Unavailable</label>';
                      }
                      else if(strpos($line, "500") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg500resp" value = "500 Server Internal Error" class = "respInput">
                              <label for = "reg500resp" class = "respLabel">500 Server Internal Error</label>';
                      }
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
                  else if(substr($line, 0, 1) === ']' || 
                          substr($line, 0, 1) === '\n' || 
                          substr($line, 0, 1) === '\r' || 
                          substr($line, 0, 1) === ' ')
                    continue;
                  else
                  {
                    $line = str_replace("<", "&lt;", $line);
                    $line = str_replace(">", "&gt;", $line);
                  }
                  if($sendTag == 1)
                  {
                    if(strpos($line, "Contact") !== FALSE)
                    {
                      echo 'Contact: &lt;sip:[field0]@[field2]:[field3]&gt;;expires=<input type = "number" value = "864000" class = "regExpires"><br>';
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
                echo '<input type = "button" id = "scenarioGen" value = "GENERATE">';
              echo '</div>';
            }
            else if($submenu === "basiccall")
            {

            }
            else if($submenu === "ivrscall")
            {
              
            }
            else if($submenu === "xfer")
            {
              
            }
            else if($submenu === "msg")
            {
              
            }
            echo '</div>';
          }
          else if($menu === "users")
          {
            if($submenu === "reg")
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
                    <input type = "password" class = "pass" value = "">';
                  echo '</div>';
              echo '</div>';
              echo '<br>
              <div id = "addUserMsg">
                  <input type = "checkbox" id = "userRange" disabled = "disabled">
                  <label for = "userRange">Check this to add range of users</label>
              </div>
              <div id = "userCtrl">
                  <i id = "addUser" class="fa fa-plus fa-1x" aria-hidden="true" style = "cursor: pointer;">&nbsp;Add User</i>
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
                      $userFile = fopen($userDir . "reg_user.csv", "r") or die("Unable to open file!");
                      $i = 0;
                      $usersList = "";
                      while(!feof($userFile))
                      {
                        $line = fgets($userFile);
                        if(strlen($line) < 10)
                          continue;

                        if(strncmp($line, "SEQUENTIAL", 10) == 0)
                        {
                          $usersList = "SEQUENTIALbr";
                          continue;
                        }

                        $cols = explode(";", $line);
                        echo '<tr class = "warning" id = "users_row_' . $i . '" ondblclick = "removeUserRow(this)">';
                        foreach ($cols as $col)
                        {
                          echo '<td>' . $col . '</td>';
                          $usersList .= $col . ";";
                          if(((int)$col) >= 50000 && ((int)$col) < 60000)
                          {
                            $origlp = $col;
                          }
                        }
                        $usersList = substr_replace($usersList, "br", -2);
                        echo '</tr>';
                        $i += 1;
                      }
                      fclose($userFile);
                echo '</tbody>
                    </table>';
              echo '</div>';
            }
            else if($submenu === "basiccall")
            {

            }
            else if($submenu === "ivrscall")
            {
              
            }
            else if($submenu === "xfer")
            {
              
            }
            else if($submenu === "msg")
            {
              
            }
          }
          else if($menu === "run")
          {
            if($submenu === "reg")
            {
              echo "<br><h2>&nbsp;&nbsp;Registration</h2><br>";
              $userFile = $userDir."/reg_user.csv";
              $totalLines = intval(exec("wc -l ".$userFile));
              if($totalLines < 3)
              {
                echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;You have not provided any user yet.</p>";
                exit(1);
              }

              echo '<div id = "msg_tags" style = "height: 20%; margin-left: 20px; "></div><br><br>';


            }
            else if($submenu === "basiccall")
            {
              echo "<br><h2>&nbsp;&nbsp;Basic Call</h2><br>";
            }
            elseif($submenu === "ivrscall")
            {
              echo "<br><h2>&nbsp;&nbsp;IVRS Call</h2><br>";
            }
            else if($submenu === "xfer")
            {
              echo "<br><h2>&nbsp;&nbsp;Transfer</h2><br>";
            }
            else if($submenu === "msg")
            {
              echo "<br><h2>&nbsp;&nbsp;Message</h2><br>";
            }

            echo '<div>';
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

                $conn = new mysqli($server, $user, $pass, $db);
                if($conn->connect_error)
                {
                    echo "Could not connect to database";
                    exit(1);
                }
                $tableName = str_replace(".", "_", $clientIp);
                $uname = $userName."_".$projectName."_".$submenu."_".$tableName;
                $sql   = "select proc_id,start_time from load_status where user = '".$uname."' and status = 'running';";
                $result = $conn->query($sql);

                if($result->num_rows > 0) 
                {
                  while($row = $result->fetch_assoc()) 
                  {
                    $procId = $row['proc_id'];
                    $startTime = $row['start_time'];
                  }
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
                $conn->close();
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
      var submenu = '<?php if(isset($_GET['submenu'])) echo $submenu; ?>';
      var menu = '<?php echo $menu; ?>';
      var userList = "";
      var msgTags = "";
      var statsUpdateTimer = "";
      var sbcsigStatsTimer = "";
      var ngcpeStatsTimer = "";
      var fsStatsTimer = "";
      var msStatsTimer = "";
      var pid = '<?php echo $procId; ?>';
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

      $(document).ready(function(){
        if(submenu.localeCompare("reg") == 0)
        {
          $('#regEndPoints').show("slow");
        }
        else if(submenu.localeCompare("basiccall") == 0)
        {
          $('#basicCallEndPoints').show("slow");
        }
        else if(submenu.localeCompare("ivrscall") == 0)
        {
          $('#ivrsCallEndPoints').show("slow");
        }
        else if(submenu.localeCompare("xfer") == 0)
        {
          $('#xferCallEndPoints').show("slow");
        }
        else if(submenu.localeCompare("msg") == 0)
        {
          $('#msgEndPoints').show("slow");
        }

        if(menu.localeCompare("users") == 0)
        {
          userList = '<?php echo $usersList; ?>';
          //userList = userList.replace(/br/g, "\n");
          console.log("CSV: " + userList);
        }
        else if(menu.localeCompare("scenarios") == 0)
        {
          msgTags = '<?php echo $msgTags; ?>';
          localStorage.setItem("msgTags", msgTags);
        }
        else if(menu.localeCompare("run") == 0)
        {
          msgTags = localStorage.getItem("msgTags");
          if(msgTags.length > 0)
          {
            var msg_tags = msgTags.split(";");
            var maxlen = 10;
            for(var i = 0;i < msg_tags.length-1;i++)
            {
              var len = msg_tags[i].length;
              if(len >= maxlen)
              {
                maxlen = len;
              }
            }
            var width = (maxlen * 20);
            var row = "";
            row += '<table class="table">';
            row += '<thead>';
            row += '<tr>';
            for(var i = 0;i < msg_tags.length-1;i++)
            {
              var len = msg_tags[i].length;
              if(isNaN(parseInt(msg_tags[i])))
              {
                row += '<th id = "msgTagsHead" style = "width: ' + width +'px;"><center><div id = "msgTags' + msg_tags[i] + '_' + i + '" class = "msgTagsNameValueRqst">' + msg_tags[i] + '<br>0</div></center></th>';
              }
              else
              {
                row += '<th id = "msgTagsHead" style = "width: ' + width +'px;"><center><div id = "msgTags' + msg_tags[i] + '_' + i + '" class = "msgTagsNameValueResp">' + msg_tags[i] + '<br>0/0</div></center></th>';
              }
            }
            row += '</tr></thead></table><br>';
            $('#msg_tags').append(row);
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
                { x: 0, y: 0.0 }
                ]
              },
              {
                type: "line",
                axisYIndex: 1,
                dataPoints: [
                { x: 0, y: 0.0 }
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
                { x: 0, y: 0.0 }
                ]
              },
              {
                type: "line",
                axisYIndex: 1,
                dataPoints: [
                { x: 0, y: 0.0 }
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
                { x: 0, y: 0.0 }
                ]
              },
              {
                type: "line",
                axisYIndex: 1,
                dataPoints: [
                { x: 0, y: 0.0 }
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
                { x: 0, y: 0.0 }
                ]
              },
              {
                type: "line",
                axisYIndex: 1,
                dataPoints: [
                { x: 0, y: 0.0 }
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

      window.onbeforeunload = function(event)
      {
        if($('#runLoad').val().localeCompare("STOP") == 0)
        {
          var r = confirm("Load running. Do you want to quit?");
          return r;
        }
      };

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
                    sbcsig_x += 3;
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
                    ngcpe_x += 3;
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
                    fs_x += 3;
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
                    ms_x += 3;
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
          obj.append("<div class = 'newRqstHdr' ondblclick='removeNewRqstHdr(this)'>" + fld + ":" + val + "</div>");
      });

      function removeNewRqstHdr(obj)
      {
        obj.remove();
      }

      $('#optionMenu').click(function() {
        if(menu.localeCompare("run") == 0 &&
           $('#runLoad').val().localeCompare("STOP") == 0)
        {
          alert("Load is running. Your not allowed to leave.");
        }
        else
        {
          window.location.href = "scenarios.php?menu=cases";
        }
      });
      $('#scenarioMenu').click(function() {
        if(menu.localeCompare("run") == 0 &&
           $('#runLoad').val().localeCompare("STOP") == 0)
        {
          alert("Load is running. Your not allowed to leave.");
        }
        else
        {
          if(submenu.localeCompare("") != 0)
          {
            if(menu.localeCompare("scenarios"))
            {
              window.location.href = "scenarios.php?menu=scenarios&submenu=" + submenu;
            }
          }
          else
          {
            alert("Select a test case first.");
          }
        }
      });
      $('#userMenu').click(function() {
        if(menu.localeCompare("run") == 0 &&
           $('#runLoad').val().localeCompare("STOP") == 0)
        {
          alert("Load is running. Your not allowed to leave.");
        }
        else
        {
          if(submenu.localeCompare("") != 0)
          {
            if(menu.localeCompare("users"))
            {
              window.location.href = "scenarios.php?menu=users&submenu=" + submenu;
            }
          }
          else
          {
            alert("Select a test case first.");
          }
        }
      });
      $('#runMenu').click(function() {
        if(menu.localeCompare("run"))
        {
          if(submenu.localeCompare("") != 0)
          {
            window.location.href = "scenarios.php?menu=run&submenu=" + submenu;
          }
          else
          {
            alert("Select a test case first.");
          }
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
                                      <input type = "password" class = "pass" value = "">\
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

      function removeUserRow(obj)
      {
        var r = confirm("Do you want to proceed?");
        if (r == false) 
        {
          return false;
        }
        var text = obj.innerHTML.replace(/<td>/g,"").replace(/<\/td>/g,";").replace(/\n;/g,"br");
        var port_number = obj.firstChild.nextSibling.nextSibling.nextSibling.innerText;
        console.log(port_number);
        console.log(text);
        userList = userList.replace(text, "");
        console.log(userList);

        if(submenu.localeCompare("reg") == 0)
        {
          if(userList.length > 12)
          {
            $.ajax({
                url: 'generateRegUserCsv.php',
                type: 'POST',
                data: {
                    UL: userList,
                    PR: "1"
                },
                success: function(result, status){
                  console.log(result);
                  var resp = JSON.parse(result);
                  if(resp.statusFlag.localeCompare("0") == 0)
                  {
                    alert(resp.message);
                  }
                  if(origlp.length == 0)
                    origlp = resp.statusFlag;
                  location.reload();
                },
                error: function(status, error) {
                    alert(status+","+error);
                }
            });
          }
          else
          {
            $.ajax({
                url: 'removeRegUserFromCsv.php',
                type: 'POST',
                data: {
                    UL: userList,
                    PN: port_number
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
                    alert(status+","+error);
                }
            });
          }
        }
          
      }

      $('input[id=generateCsv]').click(function() {
          var r = confirm("Do you want to proceed?");
          if (r == false) 
          {
            return false;
          }
          if(submenu.localeCompare("reg") == 0)
          {
            var usersCnt = $("input[class*='uname']").length;
            var U, P, U1, U2;
            var userRange = false;
            var lip = '<?php echo $clientIp; ?>';
            var lp = "";
            var server = '<?php echo $borderIp; ?>';
            var tempUsersList = "";
            var portReq = 1;

            if(origlp.length == 0)
              lp = "pr";
            else
              lp = origlp;

            if(document.getElementById("userRange") != null)
              userRange = document.getElementById("userRange").checked;

            $('#userDataBody').empty();

            if(userRange == true)
            {
              U1 = document.getElementsByClassName("uname")[0].value;
              U2 = document.getElementsByClassName("uname")[1].value;
              P = document.getElementsByClassName("pass")[0].value;

              if(U1.length == 0 ||
                U2.length == 0 ||
                P.length == 0)
              {
                alert("Fields cannot be empty");
                return false;
              }
              for(var i = U1, j = 0; i < U2;i++,j++)
              {
                tempUsersList += i+';[authentication username='+i+' password='+P+'];'+lip+';'+lp+';'+server+"br";
              }
              tempUsersList += i+';[authentication username='+i+' password='+P+'];'+lip+';'+lp+';'+server+"br";
            }
            else
            {
              for(var i = 0;i < (usersCnt - 1);i++)
              {
                U = document.getElementsByClassName("uname")[i].value;
                P = document.getElementsByClassName("pass")[i].value;
                if(U.length == 0 ||
                  P.length == 0)
                {
                  alert("Fields cannot be empty");
                  return false;
                }
                tempUsersList += U+';[authentication username='+U+' password='+P+'];'+lip+';'+lp+';'+server+"br";
              }
              U = document.getElementsByClassName("uname")[i].value;
              P = document.getElementsByClassName("pass")[i].value;
              if(U.length == 0 ||
                P.length == 0)
              {
                alert("Fields cannot be empty");
                return false;
              }
              tempUsersList += U+';[authentication username='+U+' password='+P+'];'+lip+';'+lp+';'+server+"br";
            }

            userList += tempUsersList;
            console.log(userList);
            
            $.ajax({
                url: 'generateRegUserCsv.php',
                type: 'POST',
                data: {
                    UL: userList,
                    PR: portReq
                },
                success: function(result, status){
                  console.log(result);
                  var resp = JSON.parse(result);
                  if(resp.statusFlag.localeCompare("0") == 0)
                  {
                    alert(resp.message);
                  }
                  if(origlp.length == 0)
                    origlp = resp.statusFlag;
                  location.reload();
                },
                error: function(status, error) {
                    alert(status+","+error);
                }
            });
          }

          return false;
      });

      $('#reg').click(function(){
          window.location.href = "scenarios.php?menu=scenarios&submenu=reg";
      });
      $('#basicCall').click(function(){
        window.location.href = "scenarios.php?menu=scenarios&submenu=basiccall";
      });
      $('#ivrsCall').click(function(){
        window.location.href = "scenarios.php?menu=scenarios&submenu=ivrscall";
      });
      $('#xferCall').click(function(){
        window.location.href = "scenarios.php?menu=scenarios&submenu=xfer";
      });
      $('#msg').click(function(){
        window.location.href = "scenarios.php?menu=scenarios&submenu=msg";
      });

      $('#scenarioGen').click(function(){
        var r = confirm("Do you want to proceed?");
        if (r == true) 
        {
          if(submenu.localeCompare("reg") == 0)
          {
            genRegScenario();
          }
          else if(submenu.localeCompare("basiccall") == 0)
          {
            genBasicCallScenario();
          }
          else if(submenu.localeCompare("ivrscall") == 0)
          {
            genIvrsCallScenario();
          }
          else if(submenu.localeCompare("xfer") == 0)
          {
            genXferScenario();
          }
          else if(submenu.localeCompare("msg") == 0)
          {
            genMsgScenario();
          } 
        }
      });

      function genRegScenario()
      {
        var headLine = "<\?xml version = '1.0' encoding = 'ISO-8859-1' ?>\n" +
                       "<!DOCTYPE scenario SYSTEM 'sipp.dtd'>\n";
      
        var scenarioName = "<scenario name = 'Register Load Test'>\n";
        var scenarioNameEnd = "</scenario>\n";

        var sndTagStart = "<send>\n";
        var sndTagEnd = "</send>\n";
        var rcvTagStart = "<recv>\n";
        var rcvTagEnd = "</recv>\n";

        var dataTagStart = "<![CDATA[\n\n";
        var dataTagEnd = "\n]]>\n";

        var label = 0;
        var resp = 1;

        var scenario = "";

        scenario = headLine +
                   scenarioName;

        msgTags = "";

        $('#regScenario').children().each(function () {
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
            msgTags += "REGISTER;"; 
          }
          else if(this.className.localeCompare("origRegResp") == 0)
          {
            if(this.firstChild.checked == true &&
               this.firstChild.id.localeCompare("reg403resp") == 0 &&
               resp == 1)
            {
              scenario += "<recv response = \"403\" optional = \"true\" next = \"1\">\n</recv>\n\n";
              label = 1;
              msgTags += "403;";
            }
            else if(this.firstChild.checked == false &&
                    this.firstChild.id.localeCompare("reg403resp") == 0 &&
                    resp == 1)
            {
              scenario += "<!--recv response = \"403\" optional = \"true\" next = \"1\">\n</recv-->\n\n";
            }

            if(this.firstChild.checked == true &&
              this.firstChild.id.localeCompare("reg503resp") == 0 &&
              resp == 1)
            {
              scenario += "<recv response = \"503\" optional = \"true\" next = \"1\">\n</recv>\n\n";
              label = 1;
              msgTags += "503;";
            }
            else if(this.firstChild.checked == false &&
              this.firstChild.id.localeCompare("reg503resp") == 0 &&
              resp == 1)
            {
              scenario += "<!--recv response = \"503\" optional = \"true\" next = \"1\">\n</recv-->\n\n";
            }

            if(this.firstChild.checked == true &&
              this.firstChild.id.localeCompare("reg500resp") == 0 &&
              resp == 1)
            {
              scenario += "<recv response = \"500\" optional = \"true\" next = \"1\">\n</recv>\n\n";
              label = 1;
              msgTags += "500;";
            }
            else if(this.firstChild.checked == false &&
              this.firstChild.id.localeCompare("reg500resp") == 0 &&
              resp == 1)
            {
              scenario += "<!--recv response = \"500\" optional = \"true\" next = \"1\">\n</recv-->\n\n";
            }

            if(this.firstChild.checked == true &&
                    this.firstChild.id.localeCompare("reg401resp") == 0 &&
                    resp == 1)
            {
              scenario += "<recv response = \"401\" auth = \"true\">\n</recv>\n\n";
              resp = 2;
              msgTags += "401;";
            }
            else if(this.firstChild.checked == true &&
                    this.firstChild.id.localeCompare("reg200resp") == 0 &&
                    resp == 2)
            {
              scenario += "<recv response = \"200\" crlf = \"true\">\n</recv>\n\n";
              resp = 1;
              if(label > 0)
              {
                scenario += "<label id='" + label + "'/>\n";
              }
              msgTags += "200;";
            }
          }
        });
        scenario += scenarioNameEnd;
        console.log(scenario);
        
        $.ajax({
          url: 'generateRegScenario.php',
          type: 'POST',
          data: {
              S: scenario
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
              alert(status+","+error);
          }
        });
      }

      function updateClientLoadStats(){
          $.ajax({
              type: "POST",
              url: "getClientLoadStats.php",
              data: {
                PID: pid,
                SM: submenu,
                MT: msgTags
              },
              success:function(data)
              {
                  console.log(data);
                  if(data[0] == '<')
                  {
                    console.log("Connection issue");
                    statsUpdateTimer = setTimeout(function(){
                                          updateClientLoadStats();
                                      }, 2000);
                    return false;
                  }

                  var resp = JSON.parse(data);
                  console.log(resp);
                  if(resp.statusFlag != null &&
                     resp.statusFlag.localeCompare("0") == 0)
                  {
                    alert(resp.message);
                    clearTimeout(statsUpdateTimer);
                  }
                  else
                  {
                    var runFlag = 1;
                    for(keys in resp)
                    {
                      if(keys.localeCompare("statusFlag") == 0 &&
                         resp[keys].localeCompare("OFF") == 0)
                      {
                        runFlag = 0;
                      }
                      else if(keys.localeCompare("statusFlag"))
                      {
                        document.getElementById("msgTags" + keys).innerHTML = keys.substr(0, keys.indexOf('_')) + "<br>" + resp[keys];
                      }
                    }
                    if(runFlag == 1)
                    {
                      var curTime = new Date();
                      var totTime = curTime - startTime;
                      document.getElementById("totTime").innerHTML = "Total-Time: " + Math.floor((totTime / 1000)) + " sec";
                      statsUpdateTimer = setTimeout(function(){
                                          updateClientLoadStats();
                                      }, 2000);
                    }
                    else
                    {
                      clearTimeout(statsUpdateTimer);
                      stopLoad();
                    }
                  }
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
                    else
                    {
                      document.getElementById("msgTags" + keys).innerHTML = keys.substr(0, keys.indexOf('_')) + "<br>" + resp[keys];
                    }
                  }
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
                  updateClientLoadStats();
                }
              }
          });
      }

      function stopLoad()
      {
        var r = confirm("Are you sure?");
        if (r == false) 
        {
          updateClientLoadStats();
          return false;
        }
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
              clearTimeout(statsUpdateTimer);
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
          $('#totTime').hide();
          startLoad();
        }
        else
        {
          clearTimeout(statsUpdateTimer);
          stopLoad();
        }
      });

    </script>
  </body>
</html>