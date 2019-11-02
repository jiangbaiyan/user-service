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
use Nos\Comm\Db;
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
    const REDIS_KEY_UNIFIED_TOKEN = 'unified_token_';

    /**
     * 统一注册
     * @throws CoreException
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $strEmail    = $aParams['email'];
        $strPassword = $aParams['password'];
        $strAppId    = $aParams['appId'];
        // 防止重复注册
        $aUser = UserModel::getUserByEmail($strEmail);
        if ($aUser) {
            throw new OperateFailedException("register|email:{$strEmail}_has_been_registered");
        }
        // 查询该appId是否已经在资源节点中注册
        $aResource = ResourceModel::getResourceByFullKey($aParams['appId']);
        if (empty($aResource)) {
            throw new OperateFailedException("register|{$strAppId}_was_not_registered_in_resource");
        }
        // 组装插入数据
        $aInsert = [
            'email' => $strEmail,
            'password' => md5($strAppId . $strPassword),
            'resource_id' => $aResource['id'],
            'is_activate' => UserModel::NOT_ACTIVATE
        ];
        Db::beginTransaction();
        // 入库
        if (!UserModel::createUser($aInsert)) {
            throw new OperateFailedException('register|created_user_failed|data:' . json_encode($aInsert));
        }
        // 获取刚入库的用户
        $aUser = UserModel::getUserByEmail($strEmail);
        $strJwtKey = Config::get('application.ini')['jwt_key'];
        // 根据用户数据获取加密token
        try {
            $aSeed = [
                'id' => $aUser['id'],
                'time' => time()
            ];
            $strToken = JWT::encode($aSeed, $strJwtKey);
        } catch (\Exception $e) {
            throw new OperateFailedException('register|unified_token_encode_failed|key:' . $strJwtKey . '|payload:' . json_encode($aUser) . '|internal_error:' . $e->getMessage());
        }
        // token一个月过期
        $bool = Redis::getInstance()->set(self::REDIS_KEY_UNIFIED_TOKEN . $strToken, $aUser['id'], 2592000);
        if (!$bool) {
            throw new OperateFailedException('register|redis_set_token_failed');
        }
        // 发邮件
        $strActivateCallback = 'http://152.136.125.67:9600/unified/callback?id=' . $aUser['id'];
        $strContent = "请点击该链接激活您的账号：\n" . $strActivateCallback;
        Email::send($strEmail, '请激活您的用户账号', $strContent);
        Db::commit();
        // 返回token
        Response::apiSuccess([
            'unified_token' => $strToken
        ]);
    }
}