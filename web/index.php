<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new SilexAssetic\AsseticServiceProvider());

$app['assetic.path_to_web'] = __DIR__;
$app['assetic.options'] = array(
    'debug' => true,
);
$app['assetic.filter_manager'] = $app->share(
    $app->extend('assetic.filter_manager', function($fm, $app) {
        $fm->set('scssphp', new Assetic\Filter\ScssphpFilter());
        return $fm;
    })
);
$app['assetic.asset_manager'] = $app->share(
    $app->extend('assetic.asset_manager', function($am, $app) {
        $am->set('styles', new Assetic\Asset\AssetCache(
            new Assetic\Asset\GlobAsset(
                __DIR__ . '/../vendor/jlong/sass-twitter-bootstrap/lib/bootstrap.scss',
                array($app['assetic.filter_manager']->get('scssphp'))
            ),
            new Assetic\Cache\FilesystemCache(__DIR__ . '/cache/assetic')
        ));
        $am->get('styles')->setTargetPath('css/styles.css');

        return $am;
    })
);

$app->get('/hello/{name}', function ($name) use ($app) {
  return 'Hello '.$app->escape($name);
});

$app->run();
