<?php
/**
 * 统一注册
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/28
 * Time: 10:20 上午
 */

use Firebase\JWT\JWT;
use Nos\Comm\Config;
use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Resource\ResourceModel;
use User\UserModel;
use Nos\Comm\Redis;
use Nos\Http\Response;

class Unified_RegisterController extends BaseController
{

    /**
     * redis中token的key前缀
     */
    const REDIS_KEY_JWT_TOKEN = 'jwt_token_';

    /**
     * 统一注册
     * @throws CoreException
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        // 验证外层
        Validator::make($aParams = Request::all(), [
            'data' => 'required'
        ]);
        // 验证内层
        Validator::make($aData = $aParams['data'], [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $strEmail    = $aData['email'];
        $strPassword = $aData['password'];
        $strAppId    = $aParams['appId'];
        // 防止重复注册
        $aUser = UserModel::getUserByEmail($strEmail);
        if ($aUser) {
            throw new OperateFailedException("register|email:{$aData['email']}_has_been_registered");
        }
        // 查询该appId是否已经在资源节点中注册
        $aResource = ResourceModel::getResourceByName($aParams['appId']);
        if (empty($aResource)) {
            throw new OperateFailedException("register|{$aParams['appId']}_was_not_registered_in_resource");
        }
        // 组装插入数据
        $aInsert = [
            'email' => $strEmail,
            'password' => md5($strAppId . $strPassword),
            'resource_id' => $aResource['id']
        ];
        // 入库
        if (!UserModel::createUser($aInsert)) {
            throw new OperateFailedException('register|created_user_failed|data:' . json_encode($aData));
        }
        // 获取刚入库的用户
        $aUser = UserModel::getUserByEmail($strEmail);
        $strJwtKey = Config::get('application.ini')['jwt_key'];
        // 根据用户数据获取加密token
        try {
            $strToken = JWT::encode($aUser, $strJwtKey);
        } catch (\Exception $e) {
            throw new OperateFailedException('register|jwt_token_encode_failed|key:' . $strJwtKey . '|payload:' . json_encode($aUser) . '|internal_error:' . $e->getMessage());
        }
        // token一个月过期
        $bool = Redis::getInstance()->set(self::REDIS_KEY_JWT_TOKEN . $strToken, $aUser['id'], 2592000);
        if (!$bool) {
            throw new OperateFailedException('register|redis_set_token_failed');
        }
        // 返回用户数据+token
        return Response::apiSuccess([
            'user'          => $aData,
            'unified_token' => $strToken
        ]);
    }
}