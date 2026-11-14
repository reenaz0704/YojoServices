<?php
// Copy to config/mail.local.php on the server and fill in your SMTP mailbox details.
return [
    'enabled'    => true,
    'host'       => 'smtp.your-host.com',
    'port'       => 465,            // 465 for ssl, 587 for tls
    'encryption' => 'ssl',          // ssl | tls | none
    'username'   => 'info@yojosolutions.com',
    'password'   => 'mailbox-password',
    'from_email' => 'info@yojosolutions.com',
    'from_name'  => 'Yojo Solutions',
    'notify_to'  => 'info@yojosolutions.com',
];
