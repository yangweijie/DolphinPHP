<?php

namespace app\common\service\Console;

use think\facade\Cache;
use think\Service as BaseService;

/**
 * 自动注册模块命令
 */
class AutoRegModeConsoleService extends BaseService
{
    public function boot()
    {
        $commands = Cache::get('commands');
        if (!$commands) {
            $commands =   $this->getFile();
            Cache::set('commands', $commands, 86400);
        }
        $this->commands($commands);
    }

    function getFile()
    {
        $dir =    root_path() . 'app';
        $items =   scandir($dir);
        $dirs = [];
        foreach ($items as $item) {

            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR . 'command';

            if (@is_dir($path)) {

                $dirs[$item][] = $path;
            }
        }
        $commands = [];
        foreach ($dirs as $model => $dir) {

            foreach ($dir as $key => $value) {
                $Files =   scandir($value);
                foreach ($Files as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    // dump([$value . DIRECTORY_SEPARATOR . $file, basename($file, '.php')]);
                    // dump($model . ':' . strtolower(basename($file, '.php')));
                    // dump(class_exists("app\\$model\\command\\" . basename($file, '.php')), "app\\$model\\command\\" . basename($file, '.php'));
                    $commands[$model . ':' . strtolower(basename($file, '.php'))] = "app\\$model\\command\\" . basename($file, '.php');
                }
            }
        }
        return $commands;
    }
}
