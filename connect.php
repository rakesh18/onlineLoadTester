<?php
  session_start();

  if(isset($_COOKIE["userName"]))
  {
    $userName = $_COOKIE['userName'];
  }
  if(isset($_COOKIE["projctName"]))
  {
    $projctName = $_COOKIE['projctName'];
  }
  $userDir = "projects/" . $userName."_".$projctName . "_load/";

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

  $usersList = "";
  $borderip = "";
  $clientip = "";

  if(isset($_COOKIE['BORDERIP']))
  {
    $borderip = $_COOKIE['BORDERIP'];
  }
  if(isset($_COOKIE['EIP']))
  {
    $clientip = $_COOKIE['EIP'];
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Load Tester</title>
    <link rel = "stylesheet" href = "https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href = "https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel = "stylesheet">
    <link href = "CSS/style.css" type = "text/css" rel = "stylesheet">
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
                      }
                      else if(strpos($line, "503") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg503resp" value = "503 Service Unavailable" checked = "checked"  class = "respInput">
                              <label for = "reg503resp" class = "respLabel">503 Service Unavailable</label>';
                      }
                      else if(strpos($line, "401") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg401resp" value = "401 Unauthorized" checked = "checked" onclick = "return false;" class = "respInput">
                              <label for = "reg401resp" class = "respLabel">401 Unauthorized</label>';
                      }
                      else if(strpos($line, "200") !== FALSE)
                      {
                        echo '<input type = "checkbox" id = "reg200resp" checked = "checked" onclick = "return false;" class = "respInput">
                              <label for = "reg200resp" class = "respLabel">200 OK</label>';
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
                    }
                    if(strpos($line, "</recv") !== FALSE)
                    {
                      if($recvTag == 1)
                      {
                        echo '</div>';
                        $recvTag = 0;
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
                    else
                    {
                      echo $line."<br>";
                    }
                  }
                }
                fclose($scenarioFile);
                echo '<input type = "button" id = "scenarioGen" value = "GENERATE">';
              echo '</div>';
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
                  <input type = "checkbox" id = "userRange">
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
          }
          ?>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
    <script>
      var scenarioSeleted = "";
      var submenu = '<?php if(isset($_GET['submenu'])) echo $submenu; ?>';
      var menu = '<?php echo $menu; ?>';
      var userList = "";

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
          obj.append("<div class = 'newRqstHdr' ondblclick='removeNewRqstHdr(this)'>" + fld + ":" + val + "</div>");
      });

      function removeNewRqstHdr(obj)
      {
        obj.remove();
      }

      $('#optionMenu').click(function() {
        window.location.href = "connect.php?menu=cases";
      });
      $('#scenarioMenu').click(function() {
        if(submenu.localeCompare("") != 0)
        {
          if(menu.localeCompare("scenarios") == 0)
          {
            location.reload();
          }
          else
          {
            window.location.href = "connect.php?menu=scenarios&submenu=" + submenu;
          }
        }
        else
        {
          alert("Select a test case first.");
        }
      });
      $('#userMenu').click(function() {
        if(submenu.localeCompare("") != 0)
        {
          if(menu.localeCompare("users") == 0)
          {
            location.reload();
          }
          else
          {
            window.location.href = "connect.php?menu=users&submenu=" + submenu;
          }
        }
        else
        {
          alert("Select a test case first.");
        }
      });
      $('#runMenu').click(function() {
        if(submenu.localeCompare("") != 0)
        {
          if(menu.localeCompare("run") == 0)
          {
            location.reload();
          }
          else
          {
            window.location.href = "connect.php?menu=run&submenu=" + submenu;
          }
        }
        else
        {
          alert("Select a test case first.");
        }
      });

      $('i[id=addUser]').click(function () {

          var usersCnt = $("div[class*='users']").length;
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

          if(usersCnt = 2)
          {
            $("#addUserMsg").show("slow");
          }
          else
          {
            $("#addUserMsg").hide();
            document.getElementById("userRange").checked = false;
          }
      });

      function removeUserAdded(obj)
      {
          var id = obj.parentNode.id;
          obj.parentNode.remove();
          usersCnt = $("div[class*='users']").length;
          if(usersCnt == 2)
          {
              $("#addUserMsg").show("slow");
          }
          else
          {
            $("#addUserMsg").hide();
            document.getElementById("userRange").checked = false;
          }
      }

      function removeUserRow(obj)
      {
        var text = obj.innerHTML.replace(/<td>/g,"").replace(/<\/td>/g,";").replace(/\n;/g,"br");
        var port_number = obj.firstChild.nextSibling.nextSibling.nextSibling.innerText;
        console.log(port_number);
        console.log(text);
        userList = userList.replace(text, "");
        console.log(userList);

        $.ajax({
              url: 'removeUserFromCsv.php',
              type: 'POST',
              data: {
                  UL: userList,
                  PN: port_number
              },
              success: function(result, status){
                  alert(result+","+status);
                  location.reload();
              },
              error: function(status, error) {
                  alert(status+","+error);
              }
          });
          
      }

      $('input[id=generateCsv]').click(function() {
          
          var usersCnt = $("input[class*='uname']").length;
          var U, P, U1, U2;
          var userRange = false;
          var lip = '<?php echo $clientip; ?>';
          var lp = 'pr';
          var server = '<?php echo $borderip; ?>';
          var tempUsersList = "";
          var portReq = 1;

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
          alert(userList);

          $.ajax({
              url: 'generateUserCsv.php',
              type: 'POST',
              data: {
                  UL: userList,
                  PR: portReq
              },
              success: function(result, status){
                  alert(result+","+status);
                  location.reload();
              },
              error: function(status, error) {
                  alert(status+","+error);
              }
          });

          return false;
      });

      $('#reg').click(function(){
          window.location.href = "connect.php?menu=scenarios&submenu=reg";
      });
      $('#basicCall').click(function(){
        window.location.href = "connect.php?menu=scenarios&submenu=basiccall";
      });
      $('#ivrsCall').click(function(){
        window.location.href = "connect.php?menu=scenarios&submenu=ivrscall";
      });
      $('#xferCall').click(function(){
        window.location.href = "connect.php?menu=scenarios&submenu=xfer";
      });
      $('#msg').click(function(){
        window.location.href = "connect.php?menu=scenarios&submenu=msg";
      });

      $('#scenarioGen').click(function(){
        if(submenu.localeCompare("reg") == 0)
        {
          genRegScenario();
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
          }
          else if(this.className.localeCompare("origRegResp") == 0)
          {
            if(this.firstChild.checked == true &&
               this.firstChild.id.localeCompare("reg403resp") == 0 &&
               resp == 1)
            {
              scenario += "<recv response = '403' optional = 'true' next = '1'>\n</recv>\n\n";
              label = 1;
            }
            else if(this.firstChild.checked == false &&
                    this.firstChild.id.localeCompare("reg403resp") == 0 &&
                    resp == 1)
            {
              scenario += "<!--recv response = '403' optional = 'true' next = '1'>\n</recv-->\n\n";
            }

            if(this.firstChild.checked == true &&
              this.firstChild.id.localeCompare("reg503resp") == 0 &&
              resp == 1)
            {
              scenario += "<recv response = '503' optional = 'true' next = '1'>\n</recv>\n\n";
              label = 1;
            }
            else if(this.firstChild.checked == false &&
              this.firstChild.id.localeCompare("reg503resp") == 0 &&
              resp == 1)
            {
              scenario += "<!--recv response = '503' optional = 'true' next = '1'>\n</recv-->\n\n";
            }

            if(this.firstChild.checked == true &&
                    this.firstChild.id.localeCompare("reg401resp") == 0 &&
                    resp == 1)
            {
              scenario += "<recv response = '401' auth = 'true'>\n</recv>\n\n";
              resp = 2;
            }
            else if(this.firstChild.checked == true &&
                    this.firstChild.id.localeCompare("reg200resp") == 0 &&
                    resp == 2)
            {
              scenario += "<recv response = '200' crlf = 'true'>\n</recv>\n\n";
              resp = 1;
              if(label > 0)
              {
                scenario += "<label id='" + label + "'/>\n";
              }
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
              S: scenario
          },
          success: function(result, status){
              alert(result+","+status);
              location.reload();
          },
          error: function(status, error) {
              alert(status+","+error);
          }
        });*/
      }

    </script>
  </body>
</html>