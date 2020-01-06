<?php

function p($obj, $f = 1)
{
    print "<pre>";
    print_r($obj);
    print "</pre>";
    if ($f == 1)
        die;
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/aliases.php');
require(__DIR__ . '/../../common/config/params.php');


//require(__DIR__ . '/../../common/twiliophp/Services/Twilio.php');

$config = yii\helpers\ArrayHelper::merge(
                require(__DIR__ . '/../../common/config/main.php'),
                // require(__DIR__ . '/../../common/config/main-local.php'),
                require(__DIR__ . '/../config/main.php')
                // require(__DIR__ . '/../config/main-local.php')
);
require(__DIR__ . '/../../common/config/aliases.php');

$application = new yii\web\Application($config);
$application->run();
