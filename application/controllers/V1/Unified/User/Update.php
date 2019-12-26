<?php
/**
 * 统一修改用户信息
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019-11-3
 * Time: 10:16
 */

use Nos\Comm\Redis;
use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Exception\UnauthorizedException;
use Nos\Http\Response;
use User\UserModel;

class V1_Unified_User_UpdateController extends BaseController
{

    /**
     * redis中token的key前缀
     */
    const REDIS_KEY_UNIFIED_TOKEN = 'unified_token_';

    /**
     * 允许修改的字段
     */
    const ALLOWED_UPDATED_FIELD = [
        'password',
        'name'
    ];


    /**
     * 更新用户信息
     * @throws OperateFailedException
     * @throws UnauthorizedException
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'unified_token' => 'required',
            'data' => 'required'
        ]);
        $strToken = $aParams['unified_token'];
        $nUserId = Redis::getInstance()->get(self::REDIS_KEY_UNIFIED_TOKEN . $strToken);
        if (empty($nUserId)) {
            throw new OperateFailedException("unified_update_user|token_invalid");
        }
        foreach ($aParams['data'] as $strField => &$strValue) {
            // 允许修改密码和昵称
            if (!in_array($strField, self::ALLOWED_UPDATED_FIELD)) {
                throw new OperateFailedException("字段{$strField}不允许修改");
            }
            // 修改密码后要特殊处理
            if ($strField == 'password') {
                $strValue = md5($aParams['appId'] . $strValue);
            }
        }
        UserModel::updateById($nUserId, $aParams['data']);
        return Response::apiSuccess();
    }
}
