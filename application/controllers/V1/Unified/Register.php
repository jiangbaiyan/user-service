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

class V1_Unified_RegisterController extends BaseController
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
            'password' => 'required',
            'callback_url' => 'required'
        ]);
        $strEmail    = $aParams['email'];
        $strPassword = $aParams['password'];
        $strAppId    = $aParams['appId'];
        $strName     = $aParams['name'] ?? '';
        // 查询该appId是否已经在资源节点中注册
        $aResource = ResourceModel::getResourceByFullKey($aParams['appId']);
        if (empty($aResource['total'])) {
            throw new OperateFailedException("register|{$strAppId}_was_not_registered_in_resource");
        }
        $aResource   = $aResource['data'][0];
        $nResourceId = $aResource['id'];
        // 组装插入数据
        $aInsert = [
            'email'       => $strEmail,
            'password'    => $strPassword,
            'name'        => $strName,
            'resource_id' => $nResourceId,
            'is_activate' => UserModel::NOT_ACTIVATE
        ];
        Db::beginTransaction();
        // 入库
        $nUserId = UserModel::create($aInsert);
        $strJwtKey = Config::get('application.ini')['jwt_key'];
        // 根据用户数据获取加密token
        try {
            $aSeed = [
                'id' => $nUserId,
                'time' => time()
            ];
            $strToken = JWT::encode($aSeed, $strJwtKey);
        } catch (\Exception $e) {
            throw new OperateFailedException('register|unified_token_encode_failed|key:' . $strJwtKey . '|payload:' . json_encode($aSeed) . '|internal_error:' . $e->getMessage());
        }
        // token一个月过期
        $bool = Redis::getInstance()->set(self::REDIS_KEY_UNIFIED_TOKEN . $strToken, $nUserId, 2592000);
        if (!$bool) {
            throw new OperateFailedException('register|redis_set_token_failed');
        }
        // 获取回调配置
        $aHttpConfig = Config::get('http.ini');
        $strHost     = $aHttpConfig['host'];
        $strParams   = base64_encode($nUserId . '_' . $aParams['callback_url']);
        $strActivateCallback = $strHost . '/v1/unified/callback?data=' . $strParams;
        $strContent  = "请点击该链接激活您的账号：\n" . $strActivateCallback;
        // 发邮件
        Email::send($strEmail, '请激活您的用户账号', $strContent);
        Db::commit();
        // 返回token
        Response::apiSuccess(['unified_token' => $strToken]);
    }
}
