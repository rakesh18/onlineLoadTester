<?php
    $client = $_POST['C'];
    $client = str_replace(".", "_", $client);
    $projects = array_filter(glob('projects/external/'.$client.'/'.$_COOKIE['userName'].'_*'), 'is_dir');
    $list = "";
    foreach ($projects as $p)
    {
        $p = str_replace('projects/external/'.$client.'/'.$_COOKIE['userName'].'_', '', $p);
        $list .= '<option value = "'.$p.'">';
    }

    echo $list;
?>