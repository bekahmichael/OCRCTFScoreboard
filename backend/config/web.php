<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 's1Cj_dAB1LOzfhF4Dp1tDnTCQYRs7f2f',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'application/crypt' => 'app\modules\site\web\CryptParser',
            ],
        ],
        'response' => [
            'format' => 'json',
            'formatters' => [
                'crypt' => [
                    'class' => 'app\modules\site\web\CryptResponseFormatter',
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\UserIdentity',
            'enableSession' => false,
            // 'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            // 'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],

        'db' => require(__DIR__ . '/db.php'),

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

        'formatter' => [
            'dateFormat' => 'php:m/d/Y',
            'timeFormat' => 'php:H:i',
       ],
    ],

    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'general' => [
            'class' => 'app\modules\general\Module',
        ],
        'member' => [
            'class' => 'app\modules\member\Module',
        ],
        'scoreboard' => [
            'class' => 'app\modules\scoreboard\Module',
        ],
        'quiz' => [
            'class' => 'app\modules\quiz\Module',
        ],
    ],

    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
