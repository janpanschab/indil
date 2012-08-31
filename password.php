<?php

$password = 'hrachoslav';
$pass = sha1($password . str_repeat('hjs324nk2n', 10));

echo $pass;