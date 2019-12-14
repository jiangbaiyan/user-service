<?php
/**
 * 用户创建接口
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/27
 * Time: 10:20 上午
 */

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Http\Response;
use User\UserModel;

class V1_User_CreateController extends BaseController
{

    /**
     * 用户创建接口
     * @return string
     * @throws CoreException
     * @throws ParamValidateFailedException
     * @throws OperateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'data' => 'required|array'
        ]);
        $aUser = UserModel::getUserByEmail($aParams['data']['email']);
        if ($aUser['total']) {
            throw new OperateFailedException("register|email:{$aParams['data']['email']}_has_been_registered");
        }
        UserModel::create($aParams['data']);
        return Response::apiSuccess();
    }
}
