<?php

$db = require __DIR__ . '/db.php';
$modules = require __DIR__ . '/modules.php';
$urlManager = require(__DIR__ . '/url-manager.php');
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@template' => '@app/templates',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '1mZiE2HyTMr3u2R68-3X0U8jTA0nO6iy',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'session' => [
            'class' => 'yii\mongodb\Session',
            // 'sessionCollection' => 'session'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'authManager' => [
            'class' => 'yii\mongodb\rbac\MongoDbManager',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => '',
                'password' => '',
                'port' => '587',
                'encryption' => 'tls',
            ],
            'useFileTransport' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\mongodb\log\MongoDbTarget',
                    'levels' => ['error', 'warning'],
                    // 'logCollection' => 'log'
                ],
            ],
        ],
        'db' => $db,
        'mongodb' => $db,
        'urlManager' => $urlManager,
    ],
    'modules' => $modules,
    'params' => $params,
];

// configuration adjustments for vendor package `mdmsoft/yii2-admin`
if (ENABLE_MDMSOFT_ADMIN) {
    // $config['bootstrap'][] = 'admin';
    // $config['aliases']['@mdm/admin'] = '@vendor/mdmsoft/yii2-admin';
    $config['modules']['admin'] = [
        'class' => 'mdm\admin\Module',
        'layout' => 'top-menu',
    ];
    $config['as access'] = [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'site/*',
            'admin/*',
            // 'some-controller/some-action',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ],
    ];
}

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'model' => [
                'class' => 'yii\mongodb\gii\model\Generator',
                'templates' => [
                    'default' => '@template/gii/model/default',
                ],
            ],
        ]
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    if (ENABLE_MDMSOFT_ADMIN) {
        $config['as access']['allowActions'][] = 'gii/*';
    }
}

return $config;
