<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=clin_essentials',
            'username' => 'root',
            'password' => 'Zenocraft@123',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
// send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
//'useFileTransport' => false,//to send mails to real email addresses else will get stored in your mail/runtime folder
            //comment the following array to send mail using php's mail function
            /*          'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'mail.clinessentials.com',
            'username' => 'h322zkksbfpr',
            'password' => '?x&W:;E&1m5',
            'port' => '587',
            'encryption' => 'tls',
            'streamOptions' => [
            'ssl' => [
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
            ],
            ],
            ],*/
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'clinessentialsapp@gmail.com',
                'password' => 'Zenocraft@123',
                'port' => '587',
                'encryption' => 'tls',
            ],

        ],
    ],
];
