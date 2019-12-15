<?php
/**
 * 用户更新接口
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/27
 * Time: 10:22 上午
 */

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException as ParamValidateFailedExceptionAlias;
use Nos\Exception\ResourceNotFoundException;
use Nos\Http\Request;
use Nos\Http\Response;
use User\UserModel;

class V1_User_UpdateController extends BaseController
{

    /**
     * 更新用户
     * @throws CoreException
     * @throws OperateFailedException
     * @throws ParamValidateFailedExceptionAlias
     * @throws ResourceNotFoundException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'id'   => 'required|numeric',
            'data' => 'required'
        ]);
        UserModel::updateUserById($aParams['id'], $aParams['data']);
        return Response::apiSuccess();
    }
}
