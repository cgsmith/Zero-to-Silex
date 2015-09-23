<?php
use Silex\Provider;

require __DIR__ . '/vendor/autoload.php';


$app = new Silex\Application();
$app['debug'] = true;


// Below is required for user related stuff - they are used elsewhere as well
$simpleUserProvider = new SimpleUser\UserServiceProvider();
$app->register(new Provider\DoctrineServiceProvider());
$app->register(new Provider\SecurityServiceProvider());
$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\RememberMeServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\UrlGeneratorServiceProvider());
$app->register(new Provider\SwiftmailerServiceProvider());
$app->register(new Provider\TwigServiceProvider());
$app->register($simpleUserProvider);

// Pimple dumper for auto hinting in the IDE
$app->register(new Sorien\Provider\PimpleDumpProvider());

// Configure twig to look for layouts
$app['twig.path'] = [__DIR__.'/src/views'];

// Database options needed for security provider
$app['db.options'] = array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'zero2silex',
    'user' => 'root',
    'password' => '',
);


// Mount SimpleUser routes.
$app->mount('/user', $simpleUserProvider);

// Firewall configurations
$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/user/login$',
    ),
    'index' => array(
        'pattern' => '^/$',
    ),
    'register' => array(
        'pattern' => '^/user/register$',
    ),
    'forgot-password' => array(
        'pattern' => '^/user/forgot-password$',
    ),
    'secured_area' => array(
        'pattern' => '^.*$',
        'anonymous' => false,
        'remember_me' => array(),
        'form' => array(
            'login_path' => '/user/login',
            'check_path' => '/user/login_check',
        ),
        'logout' => array(
            'logout_path' => '/user/logout',
        ),
        'users' => $app->share(function ($app) {
            return $app['user.manager'];
        }),
    ),
);

// Tell the user provider to override the layout.twig
$app['user.options'] = [
    'templates' => [
        'layout' => 'layout.twig',
    ]
];
$app['twig.templates'] = ['layout'=>'views/layout.twig'];

$app->get('/', 'app\controller\index::indexAction');

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello ' . $app->escape($name);
});

$app->run();
