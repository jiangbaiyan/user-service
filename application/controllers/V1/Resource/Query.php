<?php
/**
 * 资源查询接口
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/11/21
 * Time: 09:28 上午
 */

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Http\Response;
use Resource\ResourceModel;

class V1_Resource_QueryController extends BaseController
{

    /**
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'page'   => 'numeric',
            'length' => 'numeric',
            'query'  => 'array'
        ]);
        $page   = $aParams['page'] ?? 0;
        $length = $aParams['length'] ?? 0;
        $query  = $aParams['query'] ?? [];
        $aData  = ResourceModel::getListCommon($query, ['*'], $page, $length);
        return Response::apiSuccess($aData);
    }
}
