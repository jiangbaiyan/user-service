<?php
/**
 * 统一根据token查询用户
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019-11-3
 * Time: 10:10
 */

use Nos\Comm\Redis;
use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Exception\UnauthorizedException;
use Nos\Http\Response;
use User\UserModel;

class Unified_User_QueryController extends BaseController
{

    /**
     * 根据token获取用户信息
     * @throws UnauthorizedException
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'unified_token' => 'required'
        ]);
        $strToken = $aParams['unified_token'];
        $aUser = UserModel::getUserByUnifiedToken($strToken, [
            'id', 'email', 'is_activate', 'name', 'created_at', 'updated_at', 'is_delete'
        ]);
        Response::apiSuccess($aUser);
    }
}
