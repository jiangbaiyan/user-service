<?php
/**
 * 用户创建接口
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/27
 * Time: 10:20 上午
 */

use Nos\Comm\Validator;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Http\Response;

class User_CreateController extends BaseController
{

    /**
     * 用户创建接口
     * @return string
     * @throws \Nos\Exception\CoreException
     * @throws ParamValidateFailedException
     */
    protected function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'data' => 'required|array'
        ]);
        $bool = UserModel::create($aParams['data']);
        return Response::apiSuccess();
    }
}
