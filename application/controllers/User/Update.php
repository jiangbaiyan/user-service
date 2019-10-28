<?php

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Http\Response;
use User\UserModel;

/**
 * 用户更新接口
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/27
 * Time: 10:22 上午
 */

class User_UpdateController extends BaseController
{

    /**
     * 更新用户
     * @throws CoreException
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'data' => 'required',
            'query' => 'required|array'
        ]);
        UserModel::updateUser($aParams['query'], $aParams['data']);
        Response::apiSuccess('更新成功');
    }
}