<!--?
session_start();

include('Net/SSH2.php');
$nickname       = "";
$location       = "";
$network        = "";
$pcscfIp        = "";
$borderUser     = "";
$borderPassword = "";
$icscfIp        = "";
$sbcIp          = "";
$clientIp       = "";
$clientUser     = "";
$clientPassword = "";

$nickname = $_POST["nickname"];
$location = $_POST["LOC"];
$network  = $_POST["NWRK"];

if($network === "ims")
{
  $pcscfIp        = $_POST["PCSCFIP"];
  $borderUser     = $_POST["PCSCFIPU"];
  $borderPassword = $_POST["PCSCFIPP"];
  $icscfIp        = $_POST["ICSCFIP"];

  //echo $pcscfIp."<br>";
  //echo $icscfIp."<br>";
  //echo $borderUser."<br>";
  //echo $borderPassword."<br>";
}
else
{
  $sbcIp          = $_POST["SBCIP"];
  $borderUser     = $_POST["SBCIPU"];
  $borderPassword = $_POST["SBCIPP"];

  //echo $sbcIp."<br>";
  //echo $borderUser."<br>";
  //echo $borderPassword."<br>";
  $sshSBC = new Net_SSH2($sbcIp);
  if (!$sshSBC->login($borderUser, $borderPassword)) 
  {
    exit('Couldnot connect to border server.');
  }

  $procIdCmd = "ps -ef | grep cdotsi";
  $shellCmdRes = $sshSBC->exec($procIdCmd);
  //echo "Command output: ".$shellCmdRes."<br>";
  if(strpos($shellCmdRes, "cdotsi") === FALSE ||
     strpos($shellCmdRes, "locationalias") === FALSE)
  {
    exit("Border server is not running. Please cconsult the server team for the issue.");
  }
}

$userDir = $nickname."_loadtester/";

if($location === "ext")
{
  $clientIp       = $_POST["EIP"];
  $clientUser     = $_POST["EIPU"];
  $clientPassword = $_POST["EIPP"];

  $sshClient = new Net_SSH2($clientIp);
  if (!$sshClient->login($clientUser, $clientPassword)) 
  {
    exit('Could not connect to client system.');
  }

  /*
   * Removing if the directory already esists.
   */
  $rmDirCmd = "rm -rf /root/".$userDir."/";
  $shellCmdRes = $sshClient->exec($rmDirCmd);

  /*
   * Creating the directory which will store all the scripts, xml and csv files.
   */
  $createDirCmd = "mkdir /root/".$userDir."/";
  $shellCmdRes = $sshClient->exec($createDirCmd);
  $chmodUserDirCmd = "chmod -R 777 /root/".$userDir."/";
  $shellCmdRes = $sshClient->exec($chmodUserDirCmd);

  /*
   * Create the scenario file.
   */
  $createScenarioCmd = "touch /root/".$userDir."scenario.xml";
  $shellCmdRes = $sshClient->exec($createScenarioCmd);

  /*
   * Create the user file for resgister.
   */
  $createUserCmd = "touch /root/".$userDir."userForReg.csv";
  $shellCmdRes = $sshClient->exec($createUserCmd);

  /*
   * Create the user file for calls.
   */
  $createUserCmd = "touch /root/".$userDir."userForCall.csv";
  $shellCmdRes = $sshClient->exec($createUserCmd);
}
else
{
  if(!file_exists($userDir))
  {
    mkdir($userDir, 0777);
  }
}

/*
 * Setting global values in session cookies for 1 day.
 */
if(!isset($_COOKIE["nickname"]))
  setcookie("nickname", $nickname, time() + (86400 * 30), "/");
if(!isset($_COOKIE["LOC"]))
  setcookie("LOC", $location, time() + (86400 * 30), "/");
if(!isset($_COOKIE["NWRK"]))
  setcookie("NWRK", $network, time() + (86400 * 30), "/");
if(!isset($_COOKIE["PCSCFIP"]))
  setcookie("PCSCFIP", $pcscfIp, time() + (86400 * 30), "/");
if(!isset($_COOKIE["ICSCFIP"]))
  setcookie("ICSCFIP", $icscfIp, time() + (86400 * 30), "/");
if(!isset($_COOKIE["SBCIP"]))
  setcookie("SBCIP", $sbcIp, time() + (86400 * 30), "/");
if(!isset($_COOKIE["PCSCFIPU"]))
  setcookie("PCSCFIPU", $borderUser, time() + (86400 * 30), "/");
if(!isset($_COOKIE["PCSCFIPP"]))
  setcookie("PCSCFIPP", $borderUser, time() + (86400 * 30), "/");
if(!isset($_COOKIE["SBCIPU"]))
  setcookie("SBCIPU", $borderUser, time() + (86400 * 30), "/");
if(!isset($_COOKIE["SBCIPP"]))
  setcookie("SBCIPP", $borderPassword, time() + (86400 * 30), "/");
if(!isset($_COOKIE["EIP"]))
  setcookie("EIP", $clientIp, time() + (86400 * 30), "/");
if(!isset($_COOKIE["EIPU"]))
  setcookie("EIPU", $clientUser, time() + (86400 * 30), "/");
if(!isset($_COOKIE["EIPP"]))
  setcookie("EIPP", $clientPassword, time() + (86400 * 30), "/");

/*
  list($user, $pid, $shellCmdRes) = preg_split('/\s+/', $shellCmdRes);
  echo "PID: ".$pid."<br>";

  if(strlen($pid) > 0)
  {
    $topCmd = "top -n 1 -p ".$pid." | grep 'cdotsi'";
    echo $topCmd."<br>";
    $shellCmdRes = $ssh->exec($topCmd);
    echo "Command output: ".$shellCmdRes."<br>";
  }
  else
  {
    echo "Process not running.<br>";
  }

  $sippCmd = "/root/INVITE_LOAD_TEST/inv_s/sipp 192.168.137.42 -sf /root/INVITE_LOAD_TEST/inv_s/uas_register.xml -inf /root/INVITE_LOAD_TEST/inv_s/uas_user.csv -i 192.168.137.43 -p 5080 -m 1 -trace_counts -bg";
  $shellCmdRes = $ssh->exec($sippCmd);
  echo $shellCmdRes;
*/
?-->
<!DOCTYPE html>
<html>
<head>
  <title>Load Tester</title>
  <h1> Basic Call Testing </h1>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js">
  </script>
  <script type = "text/javascript" src = "JS/jquery.min.js">
  </script>
  <style>
    p.msgs 
    {
      font-size: 20px;
      border: 2px solid #00000063;
      background-color: #9e9e9e47;
      padding: 10px 5px 10px 10px;
    }

    div#origInvResp 
    {
      font-size: 20px;
    }
  </style>
</head>

<body>
  <!-- User Details -->
  <p>Enter User Details:</p>
  <div id = 'userDetails'>
    <div class = 'users'>
      <p>Username : <input type = 'number'   class = "unames" placeholder = 'Enter user name here'></p>
      <p>Password : <input type = 'password' class = "pass" placeholder = 'Enter password here'></p>
    </div>
    <div class = 'users' id = 'users'>
      <p>Username : <input type = 'number'   class = "unames" placeholder = 'Enter user name here'></p>
      <p>Password : <input type = 'password' class = "pass" placeholder = 'Enter password here'></p>
    </div>
  </div>
  <input type = 'button' id = 'addUser' value = 'Add User'>
  <div id = 'addUserMsg'>
    <p>* You can also provide range of users or add individually</p>
    <input type = 'checkbox' id = 'uerRange'>
    <label for = 'userRange'>Check this to add range of users</label>
  </div>
  <br><input type = "button" id = "generateCsv" value = "Generate">

  <!-- Scenarios -->
  <p><b>Originating Scenario</b></p>
  <div id = "origScenario">
    <p id = "origInvMsg" class = "msgs">
    INVITE sip:[field5]@[field4] SIP/2.0<br>
    Via: SIP/2.0/UDP [field2]:[field3];branch=[branch]<br>
    From: [field0] &lt;sip:[field0]@[field4]&gt;;tag=[call_number]<br>
    To: &lt;sip:[field5]@[field4]&gt;<br>
    Call-ID: [call_id]<br>
    CSeq: 1 INVITE<br>
    Contact: sip:[field0]@[field2]:[field3]<br>
    Max-Forwards: 70<br>
    Subject: Performance Test<br>
    Content-Type: application/sdp<br>
    Content-Length: [len]<br>
    </p>
    <label>Add your header if any</label><br>
    <input type = "text" id = "userHeaderFieldInv" placeholder = "User header filed name">
    :
    <input type = "text" id = "userHeaderValueInv" placeholder = "User header value">
    <input type = "button" id = "addUserHeader2InvMsg" value = "ADD">
    <p id = "origInvMsgSDP" class = "msgs">
    v=0<br>
    o=sipp 53655765 1 IN IP4 [field2]<br>
    s=-<br>
    c=IN IP[media_ip_type] [media_ip]<br>
    t=0 0<br>
    m=audio [media_port] RTP/AVP 8 101<br>
    a=rtpmap:8 PCMA/8000<br>
    a=rtpmap:101 telephone-event/8000<br>
    a=fmtp:101 0-15<br>
    a=ptime:20<br>
    </p>

    <div id = "origInvResp">
      <input type = 'checkbox' id = '100resp'>
      <label for = '100resp'>Response 100</label><br>

      <input type = 'checkbox' id = '180resp'>
      <label for = '180resp'>Response 180</label><br>

      <input type = 'checkbox' id = '183resp'>
      <label for = '183resp'>Response 183</label><br>

      <input type = 'checkbox' id = '400resp'>
      <label for = '400resp'>Response 400</label><br>

      <input type = 'checkbox' id = '403resp'>
      <label for = '403resp'>Response 403</label><br>

      <input type = 'checkbox' id = '408resp'>
      <label for = '408resp'>Response 408</label><br>

      <input type = 'checkbox' id = '481resp'>
      <label for = '481resp'>Response 481</label><br>

      <input type = 'checkbox' id = '487resp'>
      <label for = '487resp'>Response 487</label><br>

      <input type = 'checkbox' id = '500resp'>
      <label for = '500resp'>Response 500</label><br>

      <input type = 'checkbox' id = '503resp'>
      <label for = '503resp'>Response 503</label><br>

      <input type = 'checkbox' id = '200resp' checked = "checked" onclick = "return false;">
      <label for = '200resp'>Response 200</label>
    </div>

    <p id = "origAckMsg" class = "msgs">
    ACK sip:[field5]@[field4] SIP/2.0<br>
    Via: SIP/2.0/UDP [field2]:[field3];branch=[branch]<br>
    From: [field0] &lt;sip:[field0]@[field4]&gt;;tag=[call_number]<br>
    To: &lt;sip:[field5]@[field4]&gt;[peer_tag_param]<br>
    Call-ID: [call_id]<br>
    CSeq: 1 ACK<br>
    Contact: sip:[field0]@[field2]:[field3]<br>
    Max-Forwards: 70<br>
    Subject: Performance Test<br>
    Content-Length: 0<br>
    </p>
    <label>Add your header if any</label><br>
    <input type = "text" id = "userHeaderFieldAck" placeholder = "User header filed name">
    :
    <input type = "text" id = "userHeaderValueAck" placeholder = "User header value">
    <input type = "button" id = "addUserHeader2AckMsg" value = "ADD"><br>

    <input type = 'checkbox' id = 'rtpSession'>
    <label for = 'rtpSession'>Check to allow RTP session</label><br>

    <p id = "origByeMsg" class = "msgs">
    BYE sip:[field5]@[field4] SIP/2.0<br>
    Via: SIP/2.0/UDP [field2]:[field3];branch=[branch]<br>
    From: [field0] &lt;sip:[field0]@[field4]&gt;;tag=[call_number]<br>
    To: &lt;sip:[field5]@[field4]&gt;[peer_tag_param]<br>
    Call-ID: [call_id]<br>
    CSeq: 2 BYE<br>
    Contact: sip:[field0]@[field2]:[field3]<br>
    Max-Forwards: 70<br>
    Subject: Performance Test<br>
    Content-Length: 0<br>
    </p>
    <label>Add your header if any</label><br>
    <input type = "text" id = "userHeaderFieldBye" placeholder = "User header filed name">
    :
    <input type = "text" id = "userHeaderValueBye" placeholder = "User header value">
    <input type = "button" id = "addUserHeader2ByeMsg" value = "ADD"><br>

    <input type = 'checkbox' id = 'bye200resp' checked = "checked" onclick = "return false;">
    <label for = 'bye200resp'>BYE</label>


  </div>
  <p>Terminating Scenario</p>
  <input type = 'button' id = 'submitScenario' value = 'Submit'>

  <script type = "text/javascript">
    $('input[id=addUser]').click(function () {
      
      usersCnt = $("div[class*='users']").length;
      $("#users").append("<div class = 'users' id = 'users'>\
                            <p class = 'userDel' onclick = 'removeUserAdd(this)' style = 'cursor:pointer;'>R</p>\
                            <p>Username : <input type = 'number'   ng-model = 'username' placeholder = 'Enter user name here'></p>\
                            <p>Password : <input type = 'password' ng-model = 'password' placeholder = 'Enter password here'></p>\
                          </div>");
                          
      usersCnt += 1;

      if(usersCnt > 2)
      {
        $("#addUserMsg").hide();
      }
    });

    function removeUserAdd(obj)
    {
      obj.parentNode.remove();
      usersCnt = $("div[class*='users']").length;
      if(usersCnt == 2)
      {
        $("#addUserMsg").show("slow");
      }
    }

    $('input[id=generateCsv]').click(function() {
      usersCnt = $("input[class*='unames']").length;
      users = "";
      pass  = "";
      for(i = 0;i < (usersCnt - 1);i++)
      {
        users += document.getElementsByClassName("unames")[i].value + ";";
        pass  += document.getElementsByClassName("pass")[i].value + ";";
      }
      users += document.getElementsByClassName("unames")[i].value;
      pass  += document.getElementsByClassName("pass")[i].value;
      alert(users+","+pass);

      $.ajax({
          url: 'generateFiles.php',
          type: 'POST',
          dataType: 'JSON',
          data: {
            U: users,
            P: pass
          },
          success: function(result, status){
              alert(result+","+status);
          },
          error: function(status, error) {
            alert(status+","+error);
          }
      });

      return false;
    });

    $('input[id=addUserHeader2InvMsg]').click(function() {
      field = $('#userHeaderFieldInv').val();
      value = $('#userHeaderValueInv').val();
      console.log(field+","+value);
      $('#origInvMsg').append(field + ": " + value + "<br>");
    });

    $('input[id=addUserHeader2AckMsg]').click(function() {
      field = $('#userHeaderFieldAck').val();
      value = $('#userHeaderValueAck').val();
      console.log(field+","+value);
      $('#origAckMsg').append(field + ": " + value + "<br>");
    });

    $('input[id=addUserHeader2ByeMsg]').click(function() {
      field = $('#userHeaderFieldBye').val();
      value = $('#userHeaderValueBye').val();
      console.log(field+","+value);
      $('#origByeMsg').append(field + ": " + value + "<br>");
    });

    $('input[id=submitScenario]').click(function () {
      headLine = "<\?xml version = '1.0' encoding = 'ISO-8859-1' ?>\n" +
                 "<!DOCTYPE scenario SYSTEM 'sipp.dtd'>\n";
      
      scenarioName = "<scenario name = 'Basic Sipstone UAC'>\n";
      scenarioNameEnd = "</scenario>\n";

      sndTagStart = "<send>\n";
      sndTagEnd = "</send>\n";
      rcvTagStart = "<recv>\n";
      rcvTagEnd = "</recv>\n";

      dataTagStart = "<![CDATA[\n";
      dataTagEnd = "\n]]>\n";

      invMsg = $('#origInvMsg').text();
      invSdp = $('#origInvMsgSDP').text();
      invResp = "\n";
      if(document.getElementById('100resp').checked == true)
      {
        invResp += "<recv response = '100' optional = 'true'></recv>\n\n";
      }
      if(document.getElementById('180resp').checked == true)
      {
        invResp += "<recv response = '180' optional = 'true'></recv>\n\n";
      }
      if(document.getElementById('183resp').checked == true)
      {
        invResp += "<recv response = '183' optional = 'true'></recv>\n\n";
      }
      if(document.getElementById('400resp').checked == true)
      {
        invResp += "<recv response = '400' optional = 'true' next = '1'></recv>\n\n";
      }
      if(document.getElementById('403resp').checked == true)
      {
        invResp += "<recv response = '403' optional = 'true' next = '1'></recv>\n\n";
      }
      if(document.getElementById('408resp').checked == true)
      {
        invResp += "<recv response = '408' optional = 'true' next = '1'></recv>\n\n";
      }
      if(document.getElementById('481resp').checked == true)
      {
        invResp += "<recv response = '481' optional = 'true' next = '1'></recv>\n\n";
      }
      if(document.getElementById('487resp').checked == true)
      {
        invResp += "<recv response = '487' optional = 'true' next = '1'></recv>\n\n";
      }
      if(document.getElementById('500resp').checked == true)
      {
        invResp += "<recv response = '500' optional = 'true' next = '1'></recv>\n\n";
      }
      if(document.getElementById('503resp').checked == true)
      {
        invResp += "<recv response = '503' optional = 'true' next = '1'></recv>\n\n";
      }

      invResp += "<recv response = '200' rtd = 'true'></recv>\n\n";

      ackMsg = $('#origAckMsg').text();

      rtpMsg = "\n";
      if(document.getElementById("rtpSession").checked == true)
      {
        rtpMsg += "<nop>\n<action>\n<exec rtp_stream = 'test_5sec.wav' />\n</action>\n</nop>\n\n";
      }

      callDuration = "<pause milliseconds = '55000'/>\n\n"

      byeMsg = $('#origByeMsg').text();
      byeResp = "\n<recv response = '200' crlf = 'true'>\n</recv>\n\n"

      origScenario = headLine +
                     scenarioName +
                     sndTagStart +
                     dataTagStart +
                     invMsg +
                     invSdp +
                     dataTagEnd +
                     sndTagEnd +
                     invResp +
                     sndTagStart +
                     dataTagStart +
                     ackMsg +
                     dataTagEnd +
                     sndTagEnd +
                     rtpMsg +
                     callDuration +
                     sndTagStart +
                     dataTagStart +
                     byeMsg +
                     dataTagEnd +
                     sndTagEnd +
                     byeResp +
                     scenarioNameEnd;
      console.log(origScenario);

    });
  </script>
</body>
</html>