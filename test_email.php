<?php
require_once __DIR__ . '/db/connection.php';
require_once __DIR__ . '/includes/mailer.php';
$res = sendVerificationEmail('josephashraf2004@gmail.com', 'Joseph', '123456');
echo $res ? 'Success' : 'Failed';
?>
