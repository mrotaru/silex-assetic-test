<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new SilexAssetic\AsseticServiceProvider());

$app['assetic.path_to_web']     = __DIR__;
$app['assetic.path_to_cache']   = __DIR__ . '/../cache/assetic' ;
$app['assetic.options']         = array(
                                        'debug' => true,
                                    );
// Setup the filter manager
$app['assetic.filter_manager'] = $app->share(
    $app->extend('assetic.filter_manager', function($fm, $app) {
        $fm->set('scssphp', new Assetic\Filter\ScssphpFilter());
        $fm->set('jsmin',   new Assetic\Filter\JSMinPlusFilter());
        return $fm;
    })
);

// Setup the asset manager
$app['assetic.asset_manager'] = $app->share(
    $app->extend('assetic.asset_manager', function($am, $app) {

        // SCSS assets
        $am->set('styles', new Assetic\Asset\AssetCache(
            new Assetic\Asset\GlobAsset(
                __DIR__ . '/../vendor/jlong/sass-twitter-bootstrap/lib/bootstrap.scss',
                array($app['assetic.filter_manager']->get('scssphp'))
            ),
            new Assetic\Cache\FilesystemCache( $app['assetic.path_to_cache'] )
        ));
        $am->get('styles')->setTargetPath('css/styles.css');

        // JavaScript assets
        $am->set('javascripts', new Assetic\Asset\AssetCache(
            new Assetic\Asset\GlobAsset(
                __DIR__ . '/../vendor/jlong/sass-twitter-bootstrap/js/*.js',
                array( $app['assetic.filter_manager']->get('jsmin') )
                ),
                new Assetic\Cache\FilesystemCache( $app['assetic.path_to_cache'] )
            )
        );
        $am->get('javascripts')->setTargetPath('js/javascripts.js');

        return $am;
    })
);

$app->get('/hello', function () use ($app) {
    $html = '<html><head>'
        . '<link rel="stylesheet" href="css/styles.css">'
        . '<script src="js/javascripts.js"></script>'
        . '</head><body>'
        . '<p>Silex Assetic test</p>'
        . '</body></html>';

  return $html;
});

$app->run();
