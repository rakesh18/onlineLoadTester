<?php
    $clients = array_diff(scandir("projects/external"), array('.', '..', '.DS_Store'));
    $list = "";
    foreach ($clients as $c)
    {
        $c = str_replace("_", ".", $c);
        $list .= '<option value = "'.$c.'">';
    }

    echo $list;
?>