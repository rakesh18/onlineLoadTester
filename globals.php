<?php
    $systemIp  = "192.168.105.101";
    $responses = array("100" => "Trying",
                       "180" => "Ringing",
                       "183" => "Session Progress",
                       "200" => "OK",
                       "202" => "Accepted",
                       "400" => "Bad Reuest",
                       "401" => "Unauthorized",
                       "403" => "Forbidden",
                       "405" => "Method Not Allowd",
                       "406" => "Not Acceptable",
                       "408" => "Request Timeout",
                       "423" => "Interval Too Brief",
                       "481" => "No Call/Transaction",
                       "487" => "Request Terminated",
                       "500" => "Server Internal Error",
                       "502" => "Bad Gateway",
                       "503" => "Service Unavailable",
                       "604" => "Does Not Exist Anywhere",
                       "ACK" => "",
                       "BYE" => "",
                       "UPDATE" => "",
                       "INVITE" => "");

    $submenus = array("reg" => "REGISTRATION", 
                      "msg" => "MESSAGE", 
                      "call" => "CALL", 
                      "samvadcall" => "SAMVAD CALL", 
                      "ims2imscall" => "IMS-to-IMS CALL", 
                      "ims2ltecall" => "IMS-to-LTE CALL", 
                      "lte2imscall" => "LTE-to-IMS CALL", 
                      "lte2ltecall" => "LTE-to-LTE CALL", 
                      "ims2imsmsg" => "IMS-to-IMS MESSAGE", 
                      "ims2ltemsg" => "IMS-to-LTE MESSAGE", 
                      "lte2imsmsg" => "LTE-to-IMS MESSAGE", 
                      "lte2ltemsg" => "LTE-to-LTE MESSAGE", 
                      "imsreg" => "IMS REGISTRATION", 
                      "ltereg" => "LTE REGISTRATION");

    $terminals = array("originating", "terminating");

    $eps = array("orig" => "term",
                 "term" => "orig");
?>