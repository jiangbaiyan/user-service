<?php
/**
 * 资源节点删除接口
 * @copyright Copyright (c) 2019 自动化
 * @author jiangbaiyan<jiangbaiyan@jd.com>
 * @version v1.0
 *
 */

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Http\Response;
use Resource\ResourceModel;

class V1_Resource_DeleteController extends BaseController
{

    /**
     * @return string
     * @throws CoreException
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    protected function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'id' => 'required'
        ]);
        ResourceModel::deleteById($aParams['id']);
        return Response::apiSuccess();
    }
}
