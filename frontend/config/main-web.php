<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'FOOHRwE8WKbbRmq-cZMHC1mr8Mgow4WO',
        ],
	'mailer' => [
		'class' => 'yii\swiftmailer\Mailer',
		'useFileTransport' => false,
		'transport' => [
			'class' => 'Swift_SmtpTransport',
                	'host' => 'smtp.yandex.ru',
	                'username' => 'webmaster@dcook.site',
        	        'password' => 'Vthkby2010',
                	'port' => 465,
	        	'encryption' => 'ssl',
		],
        ],
        'cache' => [
		'class' => 'yii\caching\MemCache',
	//	'useMemcached' => true
        ]
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;

