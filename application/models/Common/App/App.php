<?php
/**
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2019-10-25
 * Time: 09:03
 */

namespace App;

use Nos\Comm\Redis;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ResourceNotFoundException;

class AppModel
{

    const REDIS_APP_HASH_KEY = 'app';

    /**
     * 通过appId获取appSecret
     * @param string $appId
     * @return bool|mixed|string
     * @throws ResourceNotFoundException
     * @throws CoreException
     */
    public static function get(string $appId)
    {
        $appSecret = Redis::getInstance()->hGet(self::REDIS_APP_HASH_KEY, $appId);
        if (!$appSecret) {
            throw new ResourceNotFoundException('app|app_secret_not_found');
        }
        return $appSecret;
    }

    /**
     * 设置appId与appSecret
     * @param string $appId
     * @param string $appSecret
     * @return bool
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function set(string $appId, string $appSecret)
    {
        $ret = Redis::getInstance()->hSet(self::REDIS_APP_HASH_KEY, $appId, $appSecret);
        if (!$ret) {
            throw new OperateFailedException('app|hSet_secret_failed');
        }
        return true;
    }


    /**
     * 根据业务线生成唯一uuid
     * @param string $resource
     * @return string
     */
    public static function getUuid(string $resource)
    {
        $pattern = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ1234567890';
        $string = '';
        for($i = 0; $i < 50; $i++)  {
            //生成长度50的随机数
            $string .= $pattern[mt_rand(0, 35)];
        }
        return md5(substr($string, 8, 32) . $resource);
    }

}