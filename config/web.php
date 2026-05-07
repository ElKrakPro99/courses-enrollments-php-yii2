<?php

$config = [
    'id'         => 'course-enrollment',
    'name'       => 'Sistema de Inscripción para formación',
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
            'identityClass'   => 'app\models\UserIdentity',
            'enableAutoLogin' => false,
            'loginUrl'        => ['/'],
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

                // Admin
                'admin/login' => 'admin/login',
                'admin/logout' => 'admin/logout',
                'admin' => 'admin/dashboard',
                'admin/crear-curso' => 'admin/create-course',
                'admin/editar-curso/<id:\d+>' => 'admin/update-course',
                'admin/eliminar-curso/<id:\d+>' => 'admin/delete-course',
                'admin/metricas/<id:\d+>' => 'admin/metrics',
                'admin/eliminar-inscripcion/<id:\d+>' => 'admin/delete-enrollment',
                // Gestión de managers (solo admin)
                'admin/managers' => 'admin/managers',
                'admin/crear-manager' => 'admin/create-manager',
                'admin/editar-manager/<id:\d+>' => 'admin/update-manager',
                'admin/eliminar-manager/<id:\d+>' => 'admin/delete-manager',

                // Manager
                'manager/login' => 'manager/login',
                'manager/logout' => 'manager/logout',
                'manager' => 'manager/dashboard',
                'manager/crear-curso' => 'manager/create-course',
                'manager/editar-curso/<id:\d+>' => 'manager/update-course',
                'manager/metricas/<id:\d+>' => 'manager/metrics',
                
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