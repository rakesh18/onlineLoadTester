var scenarioSeleted = "";

$('.addUserHeaderToMsg').click(function(){
    var obj = $(this).prev();
    var val = obj.val();
    obj = obj.prev().prev();
    var fld = obj.val();
    obj = $(this).parent().prev().prev();
    obj.append(fld + ":" + val + "<br>");
});

$(document).ready(function(){
    $('#callOptions').show();
    $('#scenarioOptions').hide();
    $('.endPoints').hide();
    $('#users').hide();
    $('runOptions').hide();
});

$('#userMenu').click(function() {
    $('.menus').removeClass("active");
    $("#userMenu").addClass("menus active");
    $('#callOptions').hide();
    $('#scenarioOptions').hide();
    $('.endPoints').hide("slow");
    $('runOptions').hide();
    if(scenarioSeleted.localeCompare("") == 0)
    {
        $("#errUsers").show();
    }
    else
    {
        $("#errUsers").hide();
        $('#users').show();
    }
});
$('#optionMenu').click(function() {
    window.location.href = "contact.php?menu=cases";
});
$('#scenarioMenu').click(function() {
    $('.menus').removeClass("active");
    $("#scenarioMenu").addClass("menus active");
    $('#users').hide();
    $('#callOptions').hide();
    $('runOptions').hide();
    $('#scenarioOptions').show();
    if(scenarioSeleted.localeCompare("R") == 0)
    {
        $('#regEndPoints').show("slow");
    }
    else if(scenarioSeleted.localeCompare("B") == 0)
    {
        $('#basicCallEndPoints').show("slow");
    }
    else if(scenarioSeleted.localeCompare("I") == 0)
    {
        $('#ivrsCallEndPoints').show("slow");
    }
    else if(scenarioSeleted.localeCompare("T") == 0)
    {
        $('#xferCallEndPoints').show("slow");
    }
    else if(scenarioSeleted.localeCompare("M") == 0)
    {
        $('#msgEndPoints').show("slow");
    }
    else if(scenarioSeleted.localeCompare("") == 0)
    {
        $("#errUsers").show();
    }
});
$('#runMenu').click(function() {
    $('.menus').removeClass("active");
    $("#runMenu").addClass("menus active");
    $('#users').hide();
    $('#callOptions').hide();
    $('#scenarioOptions').hide();
    $('.endPoints').hide("slow");
    $('runOptions').show();
});

$('i[id=addUser]').click(function () {

    var usersCnt = $("div[class*='users']").length;
    $("#userDetails").append('<div class = "users" id = "users_' + usersCnt + '">\
                                <br>\
                                <i id = "userDel" onclick = "removeUserAdded(this)" style = "cursor:pointer;" class="fa fa-times" aria-hidden="true"></i><br>\
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

    if(usersCnt > 2)
    {
        $("#addUserMsg").hide();
    }
});

function removeUserAdded(obj)
{
    var id = obj.parentNode.id;
    obj.parentNode.remove();
    document.getElementById(id).remove();
    usersCnt = $("div[class*='users']").length;
    if(usersCnt == 2)
    {
        $("#addUserMsg").show("slow");
    }
}

$('input[id=generateCsv]').click(function() {
    
    var usersCnt = $("input[class*='uname']").length;
    var users = "";
    var pass  = "";
    var U, P;

    $('#userDataBody').empty();

    for(i = 0;i < (usersCnt - 1);i++)
    {
        U = document.getElementsByClassName("uname")[i].value;
        P = document.getElementsByClassName("pass")[i].value;
        users +=  U + ";";
        pass  +=  P + ";";
        if(i % 2 == 0)
        {
            $('#userDataBody').append('<tr class = "warning" id = "users_' + i + '">\
                                        <td>' + U + '</td>\
                                        <td></td>\
                                        <td>192.168.137.43</td>\
                                        <td>5082</td>\
                                        <td>192.168.137.42</td>\
                                        <td></td>\
                                    </tr>');
        }
        else
        {
            $('#userDataBody').append('<tr class = "info" id = "users_' + i + '">\
                                        <td>' + U + '</td>\
                                        <td></td>\
                                        <td>192.168.137.43</td>\
                                        <td>5082</td>\
                                        <td>192.168.137.42</td>\
                                        <td></td>\
                                    </tr>');
        }
    }
    U = document.getElementsByClassName("uname")[i].value;
    P = document.getElementsByClassName("pass")[i].value;
    users +=  U;
    pass  +=  P;
    if(i % 2 == 0)
    {
        $('#userDataBody').append('<tr class = "warning" id = "users_' + i + '">\
                                    <td>' + U + '</td>\
                                    <td></td>\
                                    <td>192.168.137.43</td>\
                                    <td>5082</td>\
                                    <td>192.168.137.42</td>\
                                    <td></td>\
                                </tr>');
    }
    else
    {
        $('#userDataBody').append('<tr class = "info" id = "users_' + i + '">\
                                    <td>' + U + '</td>\
                                    <td></td>\
                                    <td>192.168.137.43</td>\
                                    <td>5082</td>\
                                    <td>192.168.137.42</td>\
                                    <td></td>\
                                </tr>');
    }

    /*
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
    */

    return false;
});

$('#reg').click(function(){
    scenarioSeleted = "R";
    $('#scenarioMenu').click();
    $('#regEndPoints').show("slow");
});
$('#basicCall').click(function(){
    scenarioSeleted = "B";
    $('#scenarioMenu').click();
    $('#basicCallEndPoints').show("slow");
});
$('#ivrsCall').click(function(){
    scenarioSeleted = "I";
    $('#scenarioMenu').click();
    $('#ivrsCallEndPoints').show("slow");
});
$('#xferCall').click(function(){
    scenarioSeleted = "T";
    $('#scenarioMenu').click();
    $('#xferCallEndPoints').show("slow");
});
$('#msg').click(function(){
    scenarioSeleted = "M";
    $('#scenarioMenu').click();
    $('#msgEndPoints').show("slow");
});
