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
use Resource\ResourceModel;

class V1_User_QueryController extends BaseController
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
        $aData  = UserModel::getListCommon($query, ['*'], $page, $length);
        // 查询业务线
        foreach ($aData['data'] as $nKey => &$aValue) {
            $aResource = ResourceModel::getById($aValue['resource_id'])['data'];
            $aValue['resource'] = $aResource[0]['full_key'];
            unset($aValue['resource_id']);
        }
        return Response::apiSuccess($aData);
    }
}
