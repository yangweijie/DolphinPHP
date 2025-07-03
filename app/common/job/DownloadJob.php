<?php

namespace app\common\job;

use app\admin\model\Download;
use app\common\logic\Download as LogicDownload;
use app\common\tools\ExcelTools;
use think\facade\Cache;
use think\facade\Db;
use think\Log;
use think\queue\Job;

/**
 * 消费者类
 * 用于处理 dismiss_job_queue 队列中的任务
 * 用于牌局解散
 */
class DownloadJob
{
    /**
     * fire是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param array|mixed $data 下载数据
     */
    public function fire(Job $job, $data)
    {
        //执行业务处理
        if ($this->doJob($data)) {
            $job->delete(); //任务执行成功后删除
            // Log::log("dismiss job has been down and deleted");
        } else {
            //检查任务重试次数
            if ($job->attempts() > 3) {
                // Log::log("dismiss job has been retried more that 3 times");
                $job->delete();
            }
        }
    }
    /**
     * 根据消息中的数据进行实际的业务处理
     */
    private function doJob($data)
    {
        // return	Cache::set('download', $data);

        return	LogicDownload::DbToExcelToFile($data);
    }
}
