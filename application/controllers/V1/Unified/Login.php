<?php
/**
 * 统一登录
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/28
 * Time: 10:20 上午
 */

use Firebase\JWT\JWT;
use Nos\Comm\Config;
use Nos\Comm\Redis;
use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Exception\UnauthorizedException;
use Nos\Http\Request;
use Nos\Http\Response;
use User\UserModel;


class V1_Unified_LoginController extends BaseController
{

    /**
     * redis中token的key前缀
     */
    const REDIS_KEY_UNIFIED_TOKEN = 'unified_token_';


    /**
     * 统一登录
     * @return string
     * @throws CoreException
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws UnauthorizedException
     */
    public function indexAction()
    {
        $aParams = Request::all();
        // 若请求中有token，检验token并返回用户数据
        if (!empty($aParams['unified_token'])) {
            $strToken = $aParams['unified_token'];
            $aUser = UserModel::getUserByUnifiedToken($strToken);
            if (empty($aUser['total'])) {
                throw new OperateFailedException("login|user_not_exist");
            }
            $aUser = $aUser['data'][0];
            // 如果未激活，告诉客户端，需要做对应跳转
            if ($aUser['is_activate'] == UserModel::NOT_ACTIVATE) {
                return Response::apiSuccess([
                    'is_activate'   => UserModel::NOT_ACTIVATE,
                    'unified_token' => '',
                ]);
            } else {
                return Response::apiSuccess([
                    'is_activate'   => UserModel::ACTIVATE,
                    'unified_token' => $strToken,
                ]);
            }
        } else { // 没有token，需重新登录
            Validator::make($aParams = Request::all(), [
                'email' => 'email|required',
                'password' => 'required'
            ]);
            $strEmail = $aParams['email'];
            $aUser = UserModel::getUserByEmail($strEmail);
            if (empty($aUser['total'])) {
                throw new OperateFailedException("login|user:{$strEmail}_not_registered");
            }
            $aUser = $aUser['data'][0];
            // 如果未激活，不返回token
            if ($aUser['is_activate'] == UserModel::NOT_ACTIVATE) {
                return Response::apiSuccess([
                    'is_activate'   => UserModel::NOT_ACTIVATE,
                    'unified_token' => '',
                ]);
            }
            // 取出jwt_key
            $strJwtKey = Config::get('application.ini')['jwt_key'];
            // 取出数据库中的密码
            $strBackPassword  = $aUser['password'];
            // 将前端传过来的密码进行同样的加密运算
            $strFrontPassword = md5($strJwtKey . $aParams['password']);
            // 判断二者是否相等
            if ($strFrontPassword != $strBackPassword) {
                throw new UnauthorizedException("login|wrong_password");
            }
            // 根据用户数据获取加密token
            try {
                $aSeed = [
                    'id' => $aUser['id'],
                    'time' => time()
                ];
                $strToken = JWT::encode($aSeed, $strJwtKey);
            } catch (\Exception $e) {
                throw new OperateFailedException('login|unified_token_encode_failed|key:' . $strJwtKey . '|payload:' . json_encode($aUser) . '|internal_error:' . $e->getMessage());
            }
            // token一个月过期
            $bool = Redis::getInstance()->set(self::REDIS_KEY_UNIFIED_TOKEN. $strToken, $aUser['id'], 2592000);
            if (!$bool) {
                throw new OperateFailedException('login|redis_set_token_failed');
            }
            return Response::apiSuccess([
                'is_activate'   => UserModel::ACTIVATE,
                'unified_token' => $strToken
            ]);
        }
    }
}
