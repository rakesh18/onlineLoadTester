<?php
  session_start();
?>

<html>
  <head>
    <title>Load Tester</title>
    <meta charset = "UTF-8">
    <link rel = "apple-touch-icon" type = "image/png" href = "https://static.codepen.io/assets/favicon/apple-touch-icon-5ae1a0698dcc2402e9712f7d01ed509a57814f994c660df9f7a952f3060705ee.png" />
    <meta name = "apple-mobile-web-app-title" content = "CodePen">
    <link rel = 'stylesheet' href = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
    <link rel = 'stylesheet' href = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css'>
    <link rel = 'stylesheet' href = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.0/css/bootstrapValidator.min.css'>
    <style>
      div#clientDetails {
        display: none;
      }
      div#networkIp {
        display: none;
      }
    </style>
  </head>

  <body>
    <br>
    <div class = "container">
      <form class = "well form-horizontal" action = "#" id = "startForm" onsubmit = "return submitForm();" method = "POST" enctype = "multipart/form-datam">
        <fieldset>
          <legend><center><h2><b>Welcome to Load Tester</b></h2></center></legend><br>

          <div class = "form-group">
            <label class = "col-md-4 control-label">User Name</label>  
            <div class = "col-md-4 inputGroupContainer">
            <div class = "input-group">
            <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
            <input  name = "userName" placeholder = "User Name" class = "form-control" type = "text">
              </div>
            </div>
          </div>

          <div class = "form-group">
            <label class = "col-md-4 control-label" >Project Name</label> 
              <div class = "col-md-4 inputGroupContainer">
              <div class = "input-group">
            <span class = "input-group-addon"><i class = "glyphicon glyphicon-file"></i></span>
            <input name = "projectName" placeholder = "Project Name" class = "form-control" type = "text">
              </div>
            </div>
          </div>

          <div class = "form-group"> 
            <label class = "col-md-4 control-label">Client Location</label>
            <div class = "col-md-4 selectContainer">
              <div class = "input-group">
                <span class = "input-group-addon"><i class = "glyphicon glyphicon-list"></i></span>
                <select name = "location" class = "form-control selectpicker">
                  <option value = "inplace">Inplace</option>
                  <option value = "external">External</option>
                </select>
              </div>
            </div>
          </div>

          <div class = "form-group" id = "clientDetails">
            <div class = "form-group">
              <label class = "col-md-4 control-label" >Client IP</label> 
                <div class = "col-md-4 inputGroupContainer">
                <div class = "input-group">
              <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
              <input name = "clientIp" placeholder = "Client IP" class = "form-control" type = "text">
                </div>
              </div>
            </div>

            <div class = "form-group">
              <label class = "col-md-4 control-label" >Client Username</label> 
                <div class = "col-md-4 inputGroupContainer">
                <div class = "input-group">
              <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
              <input name = "clientUsername" placeholder = "Client Username" class = "form-control" type = "text">
                </div>
              </div>
            </div>

            <div class = "form-group">
              <label class = "col-md-4 control-label" >Client Password</label> 
                <div class = "col-md-4 inputGroupContainer">
                <div class = "input-group">
              <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
              <input name = "clientPassword" placeholder = "Client Password" class = "form-control" type = "password">
                </div>
              </div>
            </div>
          </div>

          <div class = "form-group"> 
            <label class = "col-md-4 control-label">Network</label>
            <div class = "col-md-4 selectContainer">
              <div class = "input-group">
                <span class = "input-group-addon"><i class = "glyphicon glyphicon-list"></i></span>
                <select name = "network" class = "form-control selectpicker">
                  <option value = "max">MAX-NG/SAMVAD</option>
                  <option value = "ims">IMS</option>
                </select>
              </div>
            </div>
          </div>

          <div class = "form-group">
            <label class = "col-md-4 control-label" >Border IP</label> 
              <div class = "col-md-4 inputGroupContainer">
              <div class = "input-group">
            <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
            <input name = "borderIp" placeholder = "IP of SBC/PCSCF" class = "form-control" type = "text">
              </div>
            </div>
          </div>

          <div class = "form-group" id = "networkIp">
            <label class = "col-md-4 control-label" >Network IP</label> 
              <div class = "col-md-4 inputGroupContainer">
              <div class = "input-group">
            <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
            <input name = "networkIp" placeholder = "IP of ICSCF" class = "form-control" type = "text">
              </div>
            </div>
          </div>

          <div class = "form-group">
            <label class = "col-md-4 control-label" >Border Username</label> 
              <div class = "col-md-4 inputGroupContainer">
              <div class = "input-group">
            <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
            <input name = "borderUsername" placeholder = "Border Username" class = "form-control" type = "text">
              </div>
            </div>
          </div>

          <div class = "form-group">
            <label class = "col-md-4 control-label" >Border Password</label> 
              <div class = "col-md-4 inputGroupContainer">
              <div class = "input-group">
            <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
            <input name = "borderPassword" placeholder = "Border Password" class = "form-control" type = "password">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-4"><br>
              <center><button type="submit" class="btn btn-warning" >SUBMIT <span class="glyphicon glyphicon-send"></span></button></center>
            </div>
          </div>

        </fieldset>
      </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
    <script src="https://static.codepen.io/assets/common/stopExecutionOnTimeout-157cd5b220a5c80d4ff8e0e70ac069bffd87a61252088146915e8726e5d9f147.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.4.5/js/bootstrapvalidator.min.js'></script>
    <script type = "text/javascript">
      $(document).ready(function() {
        $('#startForm').bootstrapValidator({
          feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            userName: {
              validators: {
                stringLength: {
                  min: 2,
                },
                notEmpty: {
                  message: 'Please enter user name'
                }
              }
            },
            projectName: {
              validators: {
                stringLength: {
                  min: 2,
                },
                notEmpty: {
                  message: 'Please enter project name'
                }
              }
            },
            location: {
              validators: {
                notEmpty: {
                  message: 'Please select one location'
                }
              }
            },
            clientIp: {
              validators: {
                notEmpty: {
                  message: 'Please enter client ip'
                }
              }
            },
            clientUsername: {
              validators: {
                notEmpty: {
                  message: 'Please enter client user name'
                }
              }
            },
            clientPassword: {
              validators: {
                notEmpty: {
                  message: 'Please enter client password'
                }
              }
            },
            network: {
              validators: {
                notEmpty: {
                  message: 'Please select one network'
                }
              }
            },
            borderIp: {
              validators: {
                notEmpty: {
                  message: 'Please enter border ip'
                }
              }
            },
            networkIp: {
              validators: {
                notEmpty: {
                  message: 'Please enter network ip'
                }
              }
            },
            borderUsername: {
              validators: {
                notEmpty: {
                  message: 'Please enter border user name'
                }
              }
            },
            borderPassword: {
              validators: {
                notEmpty: {
                  message: 'Please enter border password'
                }
              }
            },
          }
        })
      });

      $('select[name="location"]').change(function(){
        if($(this).val() == "external"){
          $('#clientDetails').show("slow");
        }
        else if($(this).val() == "inplace"){
          $('#clientDetails').hide("slow");
        }
      });
      $('select[name="network"]').change(function(){
        if($(this).val() == "ims"){
          $('#networkIp').show("slow");
        }
        else if($(this).val() == "max"){
          $('#networkIp').hide("slow");
        }
      });

      function submitForm()
      {
        if($('form').data('submitted') === true)
        {
          //alert("form alreday submitted");
        }
        else
        {
          var form_data = new FormData(document.getElementById("startForm"));
          //alert("Sending form details...");
          $('form').data('submitted', true);
          $.ajax({
            url: "validateAndCreate.php",
            type: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            success: function(result, status, xhr){
              console.log(result + " , " + status);
              var resp = JSON.parse(result);
              console.log(resp);
              console.log(resp.statusFlag);
              if(resp.statusFlag.localeCompare("1") == 0)
              {
                window.location.href = "scenarios.php?menu=cases";
                window.location.replace("scenarios.php?menu=cases");
              }
              else
              {
                var r = confirm(resp.message);
                if (r == true) 
                {
                  window.location.href = "scenarios.php?menu=cases";
                  window.location.replace("scenarios.php?menu=cases");
                }
              }
            },
            error: function(status, error) {
                alert(status + " , " + error);
            }
          });
        }
        return false;
      }
    </script>
  </body>
</html>