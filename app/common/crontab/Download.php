<?php

namespace app\common\crontab;

use app\admin\model\Download as ModelDownload;

/**
 * 删除过期下载文件
 */
class Download
{
    static private $number = 0;
    /**
     * Cron6位：* * * * * *	秒、分、时、天、月、周
     */
    const CRONTAB = '*/1 * * * * *';
    static	function handle()
    {


    }
}
