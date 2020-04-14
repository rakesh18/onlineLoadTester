<?php
$line = "[test]world";
if(preg_match_all("/\[\w+.+\]/", $line, $matches))
{
    $text = $matches[0][0];
}
else
{
    $text = "";
}
if(isset($_COOKIE["hello"]))
{
    echo $_COOKIE["hello"]."<br>";
}
setcookie("hello", "hello", time() + (86400 * 30), "/");

echo substr($text, 1, -1);

echo (int)"[autgh]";
?>