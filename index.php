<?php
  session_start();
?>

<html>
  <head>
    <title>Load Tester</title>
    <h1>Welcom to Load Tester</h1>
    <script type = "text/javascript" src = "JS/jquery.min.js"></script>
  </head>

  <body>
    <form action="connect.php" method="POST">
      <p>Enter a nickname<p>
      <input type = "text" id = "nickname" name = "nickname" value = "" placeholder = "Nick Name"><br><br>
      <p>Choose where you want to run the load</p>
      <input type="radio" id="inplace" name="LOC" value="in" checked = "checked">
      <label for="inplace">Inplace</label><br><br>
      <input type="radio" id="external" name="LOC" value="ext">
      <label for="external">External</label><br><br>
      <div id = "extDiv" style = "display:none;">
        <input type="text" id="extIp" name="EIP" value="" placeholder="Enter the External IP"><br><br>
        <input type="text" id="extIpU" name="EIPU" value="" placeholder="Enter the External IP User"><br><br>
        <input type="password" id="extIpP" name="EIPP" value="" placeholder="Enter the External IP Password">
      </div>
      <p>Choose your network</p>
      <input type="radio" id="ims" name="NWRK" value="ims">
      <label for="ims">IMS</label><br><br>
      <div id = "pcscfDiv" style = "display: none">
        <input type="text" id="pcscfIp" name = "PCSCFIP" value="" placeholder="Enter the IP of PCSCF"><br><br>
        <input type="text" id="pcscfIpU" name = "PCSCFIPU" value="" placeholder="Enter the Border IP User"><br><br>
        <input type="password" id="pcscfIpP" name = "PCSCFIPP" value="" placeholder="Enter the Border IP Password"><br><br>
        <input type="text" id="icscfIp" name = "ICSCFIP" value="" placeholder="Enter the IP of ICSCF"><br><br>
      </div>
      <input type="radio" id="maxng" name="NWRK" value="maxng">
      <label for="maxng">MAX-NG/SAMWAD</label><br><br>
      <div id = "sbcDiv" style = "display:none">
        <input type="text" id="sbcIp" name = "SBCIP" value="" placeholder="Enter the IP of SBC"><br><br>
        <input type="text" id="sbcIpU" name = "SBCIPU" value="" placeholder="Enter the Border IP User"><br><br>
        <input type="password" id="sbcIpP" name = "SBCIPP" value="" placeholder="Enter the Border IP Password"><br><br>
      </div>
      <input type="submit" id="ok" value="GO">
    </form>
    <script type = "text/javascript">
      $('input[name=LOC]').click(function () {
	if (this.id == "external") 
	{
          $("#extDiv").show('slow');
        } 
	else 
	{
          $("#extDiv").hide('slow');
        }
      });
      $('input[name=NWRK]').click(function () {
	if (this.id == "ims") 
	{
	  $("#sbcDiv").hide();
          $("#pcscfDiv").show('slow');
        } 
	else 
	{
	  $("#pcscfDiv").hide();
          $("#sbcDiv").show('slow');
        }
      });
    </script>
  </body>
</html>