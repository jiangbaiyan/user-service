<?php
/**
 * 资源节点接口
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/29
 * Time: 09:12 上午
 */

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use Nos\Http\Response;
use Resource\ResourceModel;

class V1_Resource_CreateController extends BaseController
{

    /**
     * 创建资源节点
     * @throws CoreException
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'parent_resource_id' => 'required|numeric',
            'cur_key'            => 'required'
        ]);

        $nParentResourceId = $aParams['parent_resource_id'];
        $strCurKey         = $aParams['cur_key'];
        $strFullKey = ResourceModel::getFullKeyByParentResourceId($nParentResourceId, $strCurKey);
        // 查询节点名是否已经存在
        if (ResourceModel::getResourceByFullKey($strFullKey)['total']) {
            throw new OperateFailedException('resource|resource_key_exists');
        }
        $aInsert = [
            'parent_resource_id' => $nParentResourceId,
            'cur_key'            => $strCurKey,
            'full_key'           => $strFullKey,
            'app_secret'         => md5($nParentResourceId . $strFullKey)
        ];
        ResourceModel::create($aInsert);
        return Response::apiSuccess();
    }

}
