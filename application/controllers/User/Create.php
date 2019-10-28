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

class User_CreateController extends BaseController
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
        UserModel::createUser($aParams['data']);
        return Response::apiSuccess('创建成功');
    }
}
