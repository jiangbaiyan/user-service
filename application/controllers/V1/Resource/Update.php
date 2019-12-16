<?php
/**
 * 资源节点更新接口
 * @copyright Copyright (c) 2019 自动化
 * @author jiangbaiyan<jiangbaiyan@jd.com>
 * @version v1.0
 *
 */

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Exception\ResourceNotFoundException;
use Nos\Http\Request;
use Nos\Http\Response;
use Resource\ResourceModel;

class V1_Resource_UpdateController extends BaseController
{

    /**
     * @throws CoreException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'id'   => 'required|numeric',
            'data' => 'required',
        ]);
        ResourceModel::updateById($aParams['id'], $aParams['data']);
        return Response::apiSuccess();
    }
}
