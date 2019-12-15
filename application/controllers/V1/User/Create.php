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
            'email'       => 'required|email',
            'password'    => 'required',
            'resource_id' => 'required|numeric',
            'is_active'   => 'numeric'
        ]);
        $aInsert = [
            'email'       => $aParams['email'],
            'password'    => $aParams['password'],
            'resource_id' => $aParams['resource_id'],
            'is_active'   => $aParams['is_active'] ?? 0
        ];
        UserModel::create($aInsert);
        return Response::apiSuccess();
    }
}
