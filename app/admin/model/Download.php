<?php

namespace app\admin\model;

use think\Model;

/**
 * 下载模型
 * @package app\admin\model
 */
class Download extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'admin_download';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

}