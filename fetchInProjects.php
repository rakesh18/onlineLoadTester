<?php
    $projects = array_filter(glob('projects/inplace/'.$_COOKIE['userName'].'_*'), 'is_dir');
    $list = "";
    foreach ($projects as $p)
    {
        $p = str_replace('projects/inplace/'.$_COOKIE['userName'].'_', '', $p);
        $list .= '<option value = "'.$p.'">';
    }

    echo $list;
?>