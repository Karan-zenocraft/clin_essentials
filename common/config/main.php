<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=clin_essentials',
            'username' => 'root',
            'password' => 'Zenocraft@123',
            'charset' => 'utf8',
        ],
        'assetManager' => [
            'bundles' => [
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false, // do not load bootstrap assets for a specific asset bundle
                ],
            ],
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
// send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
//'useFileTransport' => false,//to send mails to real email addresses else will get stored in your mail/runtime folder
            //comment the following array to send mail using php's mail function
            'transport' => [
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
            ],
        ],
        /*     'pdf' => [
    'class' => Pdf::classname(),
    'format' => Pdf::FORMAT_A4,
    'orientation' => Pdf::ORIENT_PORTRAIT,
    'destination' => Pdf::DEST_DOWNLOAD,
    'mode' => MODE_UTF8,
    'tempPath' => Yii::$app->getAlias('@app/runtime/mpdf'),
    // refer settings section for all configuration options
    ],*/
    ],
];
