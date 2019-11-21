<?php
/**
 * 用户模型
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/27
 * Time: 10:07 上午
 */

namespace User;

use Model;
use Nos\Comm\Redis;
use Nos\Exception\CoreException;
use Nos\Exception\UnauthorizedException;

class UserModel extends Model
{

    public static $table = 'user';

    const NOT_ACTIVATE = 0; // 未激活
    const ACTIVATE     = 1; // 已激活

    /**
     * redis中token的key前缀
     */
    const REDIS_KEY_UNIFIED_TOKEN = 'unified_token_';


    /**
     * 通过邮箱获取用户
     * @param string $strEmail
     * @param array $aField
     * @param int $page
     * @param int $length
     * @return array
     * @throws CoreException
     */
    public static function getUserByEmail(string $strEmail, array $aField = ['*'], int $page = 0, int $length = 0)
    {
        return self::getListCommon([
            ['email', '=', $strEmail]
        ], $aField, $page, $length);
    }


    /**
     * 根据统一token获取用户信息
     * @param string $strToken
     * @param array $aField
     * @return array
     * @throws CoreException
     * @throws UnauthorizedException
     */
    public static function getUserByUnifiedToken(string $strToken, array $aField = ['*'])
    {
        $nUserId = Redis::getInstance()->get(self::REDIS_KEY_UNIFIED_TOKEN . $strToken);
        if (empty($nUserId)) {
            throw new UnauthorizedException("user_model|token:{$strToken}_invalid");
        }
        return self::getById($nUserId, $aField);
    }

}
