<?php
/**
 * 统一登出
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019-11-2
 * Time: 21:39
 */

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Comm\Redis;
use Nos\Http\Response;

class V1_Unified_LogoutController extends BaseController
{

    /**
     * redis中token的key前缀
     */
    const REDIS_KEY_UNIFIED_TOKEN = 'unified_token_';

    /**
     * 登出
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'unified_token' => 'required'
        ]);
        $strToken = $aParams['unified_token'];
        Redis::getInstance()->expire(self::REDIS_KEY_UNIFIED_TOKEN . $strToken, 0);
        Response::apiSuccess();
    }
}