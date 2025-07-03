<?php

namespace app\common\logic;

use app\admin\model\Download as ModelDownload;
use app\common\tools\ExcelTools;
use think\facade\Cache;
use think\facade\Db;

/**
 * 下载处理
 */
class Download
{
    /**
     * 导出数据为excel
     */
    static public function DbToExcelToFile($data)
    {
        if ($data) {
            $title = $data['title'] . '-' . date('YmdHi');
            $sql = deSql($data['sql']);
            $sql  = self::removeLimitFromSql($sql);

            $redata =	Db::query($sql);

            $excelConfig = [
                'file_name' => $title,
                'title' => $title,
            ];
            if (isset($data['method'])) {
                list($class_name, $method_name) = 	explode('::', $data['method']);
                if (method_exists($class_name, $method_name)) {
                    $excelConfig['titles'] = $data['method']();
                }
            }

            $ExcelTools =   ExcelTools::outputProjectExcel($redata, $excelConfig);

            $base_name =  'download' . DIRECTORY_SEPARATOR . $title;
            $file_name = root_path()  . 'public' . DIRECTORY_SEPARATOR .   $base_name;
            $download_url = $data['domain'] . DIRECTORY_SEPARATOR .  $base_name;
            $file_name = ExcelTools::saveExcel($ExcelTools, $file_name);

            if ($file_name) {
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);

                $url = $download_url . '.' . $extension;

                $res =	ModelDownload::where(['id' => $data['id'], 'module' => $data['module'], 'title' =>  $data['title']])->update(['url' => $url, 'status' => 1]);
                if ($res) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
    }
    /**
     * 删除sql语句的limit部分
     */
    private	static function removeLimitFromSql($sql)
    {
        // 匹配LIMIT部分的正则表达式
        $pattern = '/\s+LIMIT\s+\d+(?:\s*,\s*\d+)?\s*$/i';

        // 删除LIMIT部分
        $sql = preg_replace($pattern, '', $sql);

        return $sql;
    }


    /**
     * 删除过期文件
     */
    public static function deleteOutTimeFile()
    {
        $downloads = ModelDownload::where('')->select();
        foreach ($downloads as $download) {
            //检查文件最后跟新时间与当前时间相差1天则删除该文件

            if (time() - strtotime($download['update_time']) > 86400) {

                //替换URL中的域名
                $file = str_replace(config('app.app_host'), '', $download['url']);
                $file_path = root_path() . 'public' . DIRECTORY_SEPARATOR . $file;

                //判断文件是否存在，存在则删除
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
                //删除数据库记录
                ModelDownload::where(['id' => $download['id']])->delete();
            }
        }
    }
}