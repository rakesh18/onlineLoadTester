<?php
    $systemIp  = "192.168.105.102";
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

    $submenus = array("reg", "msg", "call", "samvadcall", "ims2imscall", "ims2ltecall", "lte2imscall", "lte2ltecall", "ims2imsmsg", "ims2ltemsg", "lte2imsmsg", "lte2ltemsg", "imsreg", "ltereg");

    $terminals = array("originating", "terminating");

    $eps = array("orig" => "term",
                 "term" => "orig");
?>