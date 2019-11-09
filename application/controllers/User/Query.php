<?php
/**
 * 用户查询接口
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/27
 * Time: 10:05 上午
 */

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Http\Response;
use User\UserModel;

class User_QueryController extends BaseController
{

    /**
     * 查询用户列表
     * @return string
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'page'   => 'integer',
            'length' => 'integer',
            'query'   => 'array'
        ]);
        $page   = $aParams['page'] ?? 0;
        $length = $aParams['length'] ?? 0;
        $query  = $aParams['query'] ?? [];
        $aData  = UserModel::getUser($query, ['*'], $page, $length);
        return Response::apiSuccess($aData);
    }
}