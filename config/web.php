<?php

$config = [
    'id'         => 'course-enrollment',
    'name'       => 'Sistema de Inscripción de Cursos',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'aliases'    => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'TU_CLAVE_SECRETA_AQUI_CAMBIALA_POR_FAVOR',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass'   => 'app\models\AdminTab',
            'enableAutoLogin' => false,
            'loginUrl'        => ['admin/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class'            => \yii\symfonymailer\Mailer::class,
            'viewPath'         => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require __DIR__ . '/db.php',
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules' => [
                // Rutas públicas
                'cursos'                          => 'public-user/available-courses',
                'curso/<id:\d+>'                  => 'public-user/course-data',
                'inscripcion/<course_id:\d+>'      => 'public-user/enroll',
                
                // Rutas administrativas
                'admin'                           => 'admin/dashboard',
                'admin/login'                     => 'admin/login',
                'admin/logout'                    => 'admin/logout',
                'admin/crear-curso'               => 'admin/create-course',
                'admin/editar-curso/<id:\d+>'     => 'admin/update-course',
                'admin/eliminar-curso/<id:\d+>'   => 'admin/delete-course',
                'admin/metricas/<id:\d+>'         => 'admin/metrics',
                'admin/eliminar-inscripcion/<id:\d+>' => 'admin/delete-enrollment',
                
                // Ruta principal
                ''                                => 'public-user/available-courses',
            ],
        ],
    ],
    'params' => require __DIR__ . '/params.php',
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'      => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;