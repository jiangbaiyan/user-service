<?php
/**
 * 用户模型
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/27
 * Time: 10:07 上午
 */

namespace User;

use Nos\Base\BaseModel;
use Nos\Comm\Redis;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\UnauthorizedException;

class UserModel extends BaseModel
{

    public static $table = 'user';

    const NOT_ACTIVATE = 0; // 未激活
    const ACTIVATE     = 1; // 已激活

    /**
     * redis中token的key前缀
     */
    const REDIS_KEY_UNIFIED_TOKEN = 'unified_token_';


    /**
     * 查询用户列表
     * @param array $aQuery
     * @param array $aField
     * @param int $page
     * @param int $length
     * @return array
     * @throws CoreException
     */
    public static function getUser(array $aQuery = [], array $aField = ['*'], int $page = 0, int $length = 0)
    {
        return self::select($aField, $aQuery, [
            'page' => $page,
            'length' => $length
        ]);
    }

    /**
     * 通过用户id获取用户
     * @param int $nId
     * @param array $aField
     * @param int $page
     * @param int $length
     * @return array
     * @throws CoreException
     */
    public static function getUserById(int $nId, array $aField = ['*'], int $page = 0, int $length = 0)
    {
        return self::getUser([
            ['id', '=', $nId]
        ], $aField);
    }

    /**
     * 通过邮箱获取用户
     * @param string $strEmail
     * @return array
     * @throws CoreException
     */
    public static function getUserByEmail(string $strEmail)
    {
        return self::getUser([
            ['email', '=', $strEmail]
        ]);
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
        $aUser = self::getUserById($nUserId, $aField);
        return $aUser;
    }

    /**
     * 创建用户
     * $aData示例：
     * [
     *     'name' => 'grape',
     *     'age'  => 15
     * ]
     * @param array $aData
     * @return mixed
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function createUser(array $aData)
    {
        $nId = self::insert($aData);
        if (!$nId) {
            throw new OperateFailedException('user_model|create_batch_failed|data:' . json_encode($aData));
        }
        return $nId;
    }

    /**
     * 删除用户
     * @param int $nId 删除的用户id
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function deleteUserById(int $nId)
    {
        $nRows = self::delete([
            ['id', '=', $nId]
        ]);
        if (!$nRows) {
            throw new OperateFailedException('user_model|delete_failed|id:' . json_encode($nId));
        }
        return $nRows;
    }


    /**
     * 更新用户
     * @param int $nId 要更新的记录id
     * @param array $aData 更新的数据
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function updateUserById(int $nId, array $aData)
    {
        $nRows = self::update($aData, [
            ['id', '=', $nId]
        ]);
        if (!$nRows) {
            throw new OperateFailedException('user_model|update_failed|params:' . json_encode($aData) . '|id:' . $nId);
        }
        return $nRows;
    }
}