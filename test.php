<?php
include 'Net/SSH2.php';
include 'globals.php';
/*
$ssh = new Net_SSH2('192.168.137.43');
if (!$ssh->login('root', 'hakunamatata')) {
    exit('Login Failed');
}

//to run and get the pid of sipp
$runCmd = "/root/rocky_invite_load/reg/./sipp 192.168.137.42 -sf /root/rocky_invite_load/reg/reg_scenario.xml -inf /root/rocky_invite_load/reg/reg_user.csv -i 192.168.137.43 -p 50000 -r 5 -trace_stat -trace_counts -fd 10";
$screenRunCmd = "screen -d -m -S rocky_invite_reg ".$runCmd;
$output = $ssh->exec($screenRunCmd);

$procIdCmd = "pgrep -P `ps -ef | grep rocky_invite_reg | awk '{print $2}' | head -1`";
$procId = $ssh->exec($procIdCmd);
echo "PID: ".$procId."<br>";

//to check whether sipp started sucessfully or not
$procId = $ssh->exec("ps o pid= -p ".$procId);
echo "Running... ".$procId;
*/

/*
//to increase load by 1
$runCmd = 'screen -S rocky_invite_reg -p 0 -X stuff "+^M"';
$output = $ssh->exec($runCmd);
echo $output;
//to stop sipp
$runCmd = 'kill -9 '.procId." & screen -wipe";
$output = $ssh->exec($runCmd);
echo $output;
*/

/*
//mem ussage of cdotsi with pid
$runCmd = "top -b -n 1 -p `ps o pid= -C cdotsi` | tail -2 | head -1 | awk '{print $10}'";
$output = $ssh->exec($runCmd);
echo $output;
//cpu ussage of cdotsi with pid
$runCmd = "top -b -n 1 -p `ps o pid= -C cdotsi` | tail -2 | head -1 | awk '{print $9}'";
$output = $ssh->exec($runCmd);
echo $output;
*/

echo exec("ls -R projects/external/rocky_invites | wc -l | tail -1 | awk '{print $1}'");
?>