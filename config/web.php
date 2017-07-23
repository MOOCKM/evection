<?php
 $params = require(__DIR__ . '/params.php');

$config = [
'modules' => [
        'api' => [
            'class' => 'app\modules\api\api',
        ],
    ],
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'urlManager' => [  
                'class' => 'yii\web\UrlManager',  
                'enablePrettyUrl' => true,  
                'enableStrictParsing' =>false,  
                'showScriptName' => false,  
                'rules' => [  
                    '' => 'site/index',
                    ['class' => 'yii\rest\UrlRule', 
                    'controller' =>['api/user', 'api/evection','api/traffic','api/department','api/job','api/retrial','api/approver','api/role','api/department'],  
                      'pluralize' => false,  
                    ],
                ],                    
            ],  
            'request' => [  
                'class' => '\yii\web\Request',  
                'enableCookieValidation' => false,  
                'parsers' => [  
                    'application/json' => 'yii\web\JsonParser',  
                ],  
                'cookieValidationKey' => 'md5',  
            ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
             'enableSession'=>false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
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
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (!YII_ENV_DEV) {
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
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
