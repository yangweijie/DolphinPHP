<?php

use Webman\App;
use function OpenTelemetry\Instrumentation\hook;

if(extension_loaded('opentelemetry')){
    hook(App::class,
        'guessControllerAction',
        pre: static function ($demo, array $params, string $class, string $function, ?string $filename, ?int $lineno) {
//            var_dump($params);
//            var_dump(2222);
            return $params;
        },
        post: static function (mixed $demo, array $params, $returnValue, ?Throwable $exception) :mixed {
//            var_dump('post:');
//            var_dump('params');
//            var_dump($params);
//            var_dump($returnValue);
            list($pathExplode, $action, $suffix, $classPrefix) = $params;
            if(empty($pathExplode)){
                $pathExplode = [
                    'index','index'
                ];
            }
            $app = strtolower($pathExplode[0]);
            $default_controller_layer = \Webman\Config::get('module.default_controller_layer', []);
            if(!in_array($app, $default_controller_layer)){
                if(!$returnValue){
                    if(count($pathExplode) == 4){
                        if(strtolower($pathExplode[1]) == 'admin'){
                            $controller_layer = 'admin';
                        }else{
                            $controller_layer = 'home';
                        }
                        $returnValue = [
                            'plugin'=>'',
                            'controller'=>"app\\{$app}\\{$controller_layer}\\".ucfirst($pathExplode[2]),
                            'action'=>$action,
                        ];
                        $returnValue['app'] = '';
                    }
                }
            }else{
                if($returnValue){
                    $returnValue['app'] = $app;
                }else{
                    $controller_layer = 'controller';
                    $returnValue = [
                        'app'=>$app,
                        'plugin'=>'',
                        'controller'=>"app\\{$app}\\{$controller_layer}\\".ucfirst($pathExplode[1]),
                        'action'=>$action,
                    ];
                }
            }
//            var_dump('after_post：');
//            var_dump($returnValue);
            return $returnValue;
        }
    );
}


