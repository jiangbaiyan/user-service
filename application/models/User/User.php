<?php
/**
 * 用户模型
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/27
 * Time: 10:07 上午
 */

namespace User;

use CommonModel;
use Nos\Comm\Redis;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ResourceNotFoundException;
use Nos\Exception\UnauthorizedException;
use Resource\ResourceModel;

class UserModel extends CommonModel
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

    /**
     * 更新用户
     * @param int $nId
     * @param $aData
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     * @throws ResourceNotFoundException
     */
    public static function updateUserById(int $nId, $aData)
    {
        $aUpdate = [];
        if (isset($aData['name'])) {
            $aUpdate['name'] = $aData['name'];
        }
        if (isset($aData['email'])) {
            $aUser = self::select(['id'], [
                ['email', '=', $aData['email']],
                ['id', '!=', $nId]
            ]);
            if ($aUser['total']) {
                throw new OperateFailedException("user_model|email:{$aData['email']}_has_been_registered");
            }
            $aUpdate['email'] = $aData['email'];
        }
        if (isset($aData['is_activate'])) {
            $aUpdate['is_activate'] = $aData['is_activate'];
        }
        if (isset($aData['resource_id'])) {
            $aResource = ResourceModel::getById((int)$aData['resource_id'])['data'];
            if (!$aResource) {
                throw new ResourceNotFoundException("user_model|resource_id:{$aData['resource_id']}_not_found");
            }
        }
        return self::updateById($nId, $aData);
    }

}
