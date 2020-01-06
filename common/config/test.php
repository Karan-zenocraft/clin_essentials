<?php
return [
    'id' => 'app-common-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'user' => [
            'class' => 'yii\web\Users',
            'identityClass' => 'common\models\Users',
        ],
    ],
];
