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
    <?php
      if(isset($_COOKIE['userName']) && strlen($_COOKIE['userName']) > 0)
      {
        echo '
        <style>
          div#clientDetails {
            display: none;
            margin: 0px 0px 0px 0px;
          }
          div#networkIp {
            display: none;
          }
        </style>';
      }
    ?>
  </head>

  <body>
    <br>
    <div class = "container">
    <?php
    if(isset($_COOKIE['userName']) && strlen($_COOKIE['userName']) > 0)
    {
      echo '
      <form class = "well form-horizontal" action = "" id = "startForm" method = "POST" onsubmit = "enterRoomForm()" enctype = "multipart/form-datam">
        <fieldset>
          <legend><center><h2><b>Welcome to Load Tester</b></h2></center></legend><br>

          <div class = "form-group">
            <label class = "col-md-4 control-label">User Name</label>  
            <div class = "col-md-4 inputGroupContainer">
            <div class = "input-group">
            <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
            <input  name = "userName" placeholder = "User Name" class = "form-control" type = "text" value = "'.$_COOKIE['userName'].'" readonly = "true">
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
                  <input name = "clientIp" placeholder = "Client IP" class = "form-control" type = "text" list = "clients">
                  <datalist id = "clients">
                  </datalist>
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
            <label class = "col-md-4 control-label" >Project Name</label> 
              <div class = "col-md-4 inputGroupContainer">
              <div class = "input-group">
                <span class = "input-group-addon"><i class = "glyphicon glyphicon-file"></i></span>
                <input name = "projectName" placeholder = "Project Name" class = "form-control" type = "text" list="projects">
                <datalist id = "projects">';
          $projs = array_filter(glob('projects/inplace/'.$_COOKIE['userName'].'_*'), 'is_dir');
          foreach ($projs as $p)
          {
            echo '<option value = "'.str_replace('projects/inplace/'.$_COOKIE['userName'].'_', '', $p).'">';
          }
          echo '</datalist>
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
              <center><button type="submit" class="btn btn-warning">SUBMIT <span class="glyphicon glyphicon-send"></span></button></center>
            </div>
          </div>

          <label class="col-md-4 control-label"></label>
          <div class="col-md-4"><br>
            <center><input id = "signOut" type = "button" value = "LogOut" style = "border-radius: 5px;background-color: #eb9316;"></center>
          </div>
        </fieldset>
      </form>';
    }
    else
    {
      echo '
      <form class = "well form-horizontal" action = "" id = "signInSignUpForm" method = "POST" onsubmit = "userAuthForm()" enctype = "multipart/form-datam">
        <fieldset>
          <legend><center><h2><b>Welcome to Load Tester</b></h2></center></legend><br>
          <div class = "form-group">
            <label class = "col-md-4 control-label" >Username</label> 
            <div class = "col-md-4 inputGroupContainer">
              <div class = "input-group">
                <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
                <input name = "username" placeholder = "Username" class = "form-control" type = "text">
              </div>
            </div>
          </div>

          <div class = "form-group">
            <label class = "col-md-4 control-label" >Password</label> 
            <div class = "col-md-4 inputGroupContainer">
              <div class = "input-group">
                <span class = "input-group-addon"><i class = "glyphicon glyphicon-user"></i></span>
                <input name = "password" placeholder = "Password" class = "form-control" type = "password">
              </div>
            </div>
          </div>
          <input id = "mode" name = "mode" type = "text" style = "display: none">

          <div class="form-group">
            <label class = "col-md-4 control-label"></label>
            <div class="col-md-4"><br>
              <center><button id = "L" type="submit" class="btn btn-warning" onclick = "setMode(this)">Login<span class="glyphicon glyphicon-send"></span></button></center>
            </div>
          </div>
          <div class="form-group">
            <label class = "col-md-4 control-label"></label>
            <div class="col-md-4"><br>
              <center><button id = "R" type="submit" class="btn btn-warning" onclick = "setMode(this)">Register<span class="glyphicon glyphicon-send"></span></button></center>
            </div>
          </div>
        </fieldset>
      </form>';
    }
    ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
    <script src="https://static.codepen.io/assets/common/stopExecutionOnTimeout-157cd5b220a5c80d4ff8e0e70ac069bffd87a61252088146915e8726e5d9f147.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.4.5/js/bootstrapvalidator.min.js'></script>
    <script type = "text/javascript">
      /* If visited the page by back button */
      if(performance.navigation.type == 2)
      {
        location.reload();
      }
      $(document).ready(function() {
        <?php
        if(isset($_COOKIE['userName']) && strlen($_COOKIE['userName']) > 0)
        {
          echo "
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
                    max: 10,
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
                    max: 15,
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
        $('select[name=\"location\"]').change(function(){
          if($(this).val() == 'external'){
            $.ajax({
              url: 'fetchClients.php',
              type: 'POST',
              success: function(result, status, xhr){
                console.log(result);
                $('#projects').empty();
                $('#clients').append(result);
                $('#clientDetails').show('slow');
              },
              error: function(status) {
                  alert(status);
                  location.reload();
              }
            });
          }
          else if($(this).val() == 'inplace'){
            $.ajax({
              url: 'fetchInProjects.php',
              type: 'POST',
              success: function(result, status, xhr){
                console.log(result);
                $('#projects').empty();
                $('#projects').append(result);
                $('#clientDetails').hide('slow');
              },
              error: function(result, status) {
                  alert(status);
                  location.reload();
              }
            });
          }
        });
        $('input[name=\"clientIp\"]').bind('input', function(){
          var client = $('input[name=\"clientIp\"]').val();
          $.ajax({
            url: 'fetchOutProjects.php',
            type: 'POST',
            data: {
              C: client
            },
            success: function(result, status, xhr){
              console.log(result);
              $('#projects').empty();
              $('#projects').append(result);
            },
            error: function(result, status) {
                alert(status);
                location.reload();
            }
          });
        });
        $('select[name=\"network\"]').change(function(){
          if($(this).val() == 'ims'){
            $('#networkIp').show('slow');
          }
          else if($(this).val() == 'max'){
            $('#networkIp').hide('slow');
          }
        });

        function enterRoomForm()
        {
          if($('form').data('submitted') === true)
          {
            //alert('form alreday submitted');
          }
          else
          {
            var form_data = new FormData(document.getElementById('startForm'));
            var params = {};
            //alert('Sending form details...');
            $('form').data('submitted', true);
            $.ajax({
              url: 'validateAndCreate.php',
              type: 'POST',
              data: form_data,
              processData: false,
              contentType: false,
              success: function(result, status, xhr){
                console.log(result);
                if(result[0] == '<')
                {
                  alert('Client communication error.\\nTry again later.');
                  console.log(result);
                  location.reload();
                }
                var resp = JSON.parse(result);
                if(resp.statusFlag.localeCompare('0') == 0)
                {
                  alert(resp.message);
                  location.reload();
                }
                else if(resp.statusFlag.localeCompare('1') == 0)
                {
                  alert(resp.message);
                  const roomForm = document.createElement('form');
                  params['menu'] = 'cases';
                  roomForm.method = 'POST';
                  roomForm.action = 'scenarios.php';
                  for (const key in params)
                  {
                    if(params.hasOwnProperty(key))
                    {
                      const hiddenField = document.createElement('input');
                      hiddenField.type = 'hidden';
                      hiddenField.name = key;
                      hiddenField.value = params[key];
                      roomForm.appendChild(hiddenField);
                    }
                  }
                  document.body.appendChild(roomForm);
                  roomForm.submit();
                }
                else if(resp.statusFlag.localeCompare('2') == 0)
                {
                  const roomForm = document.createElement('form');
                  params['menu'] = 'cases';
                  roomForm.method = 'POST';
                  roomForm.action = 'scenarios.php';
                  for (const key in params)
                  {
                    if(params.hasOwnProperty(key))
                    {
                      const hiddenField = document.createElement('input');
                      hiddenField.type = 'hidden';
                      hiddenField.name = key;
                      hiddenField.value = params[key];
                      roomForm.appendChild(hiddenField);
                    }
                  }
                  document.body.appendChild(roomForm);
                  roomForm.submit();
                }
              },
              error: function(status) {
                  alert(status);
                  location.reload();
              }
            });
          }
          return false;
        }
        $('#signOut').click(function(){
          var r = confirm('Are you sure want to log out?');
          if(r == false)
            return false;

          $.ajax({
            url: 'userAuthentication.php',
            type: 'POST',
            data: {
              mode: 'logout'
            },
            success: function(result, status, xhr){
              console.log(result);
              if(result[0] == '<')
              {
                alert('Server Error.');
              }
              location.reload();
            },
            error: function(status) {
                alert(status);
                location.reload();
            }
          });
        });";
      }
      else
      {
      echo "
      $('#signInSignUpForm').bootstrapValidator({
          feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            username: {
              validators: {
                stringLength: {
                  min: 5,
                  max: 10,
                },
                notEmpty: {
                  message: 'Please enter user name'
                }
              }
            },
            password: {
              validators: {
                stringLength: {
                  min: 5,
                  max: 10,
                },
                notEmpty: {
                  message: 'Please enter client password'
                }
              }
            },
          }
        })
      });
      function setMode(obj)
      {
        document.getElementById('mode').value = obj.id;
        return true;
      }
      function userAuthForm()
      {
        if($('form').data('submitted') === true)
        {
          //alert('form alreday submitted');
        }
        else
        {
          var form_data = new FormData(document.getElementById('signInSignUpForm'));
          //alert('Sending form details...');
          $('form').data('submitted', true);
          
          $.ajax({
            url: 'userAuthentication.php',
            type: 'POST',
            data: form_data,
            processData: false,
            contentType: false,
            success: function(result, status, xhr){
              console.log(result);
              if(result[0] == '<')
              {
                alert('Server Error.'+result);
                location.reload();
              }
              var resp = JSON.parse(result);
              if(resp.statusFlag.localeCompare('2') == 0)
              {
                window.location.href = 'index.php';
                window.location.replace('index.php');
              }
              else
              {
                alert(resp.message);
                location.reload();
              }
            },
            error: function(status) {
                alert(status);
                location.reload();
            }
          });
        }
        return false;
      }";
      }
      ?>
    </script>
  </body>
</html>