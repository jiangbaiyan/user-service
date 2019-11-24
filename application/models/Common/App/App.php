<?php
/**
 * App模型
 * Created by PhpStorm.
 * User: baiyan
 * Date: 2019-10-25
 * Time: 09:03
 */

namespace Common\App;

use Nos\Comm\Redis;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ResourceNotFoundException;

class AppModel
{

    const REDIS_APP_HASH_KEY = 'app';

    /**
     * 通过appId获取appSecret
     * @param string $strAppId
     * @return bool|mixed|string
     * @throws CoreException
     * @throws ResourceNotFoundException
     */
    public static function getAppSecretByAppId(string $strAppId)
    {
        $strAppSecret = Redis::getInstance()->hGet(self::REDIS_APP_HASH_KEY, $strAppId);
        if (!$strAppSecret) {
            throw new ResourceNotFoundException('app|app_secret_not_found');
        }
        return $strAppSecret;
    }

    /**
     * 设置appId与appSecret
     * @param string $strAppId
     * @param string $strAppSecret
     * @return bool
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function set(string $strAppId, string $strAppSecret)
    {
        $ret = Redis::getInstance()->hSet(self::REDIS_APP_HASH_KEY, $strAppId, $strAppSecret);
        if (!$ret) {
            throw new OperateFailedException('app|hSet_secret_failed');
        }
        return true;
    }

}
