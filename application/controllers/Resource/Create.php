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

class Resource_CreateController extends BaseController
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
            'parent_resource_id' => 'required|integer',
            'name' => 'required'
        ]);
        $aStack = [];
        // 拼接节点名
        $this->findParent($aParams['parent_resource_id'], $aStack);
        // '.'为分隔符
        $strFullName = implode('.', $aStack) . '.' . $aParams['name'];
        $aInsert = [
            'parent_resource_id' => $aParams['parent_resource_id'],
            'name' => $strFullName
        ];
        ResourceModel::createResource($aInsert);
        Response::apiSuccess('创建成功');
    }


    /**
     * 递归的处理当前节点的所有祖先节点
     * @param int $nParentResourceId
     * @param array $aStack
     * @return bool
     * @throws CoreException
     */
    private function findParent(int $nParentResourceId, array &$aStack)
    {
        // 如果没有到最顶层节点，递归的获取节点名
        if (!empty($nParentResourceId)) {
            // 查询父节点名称
            $aNode = ResourceModel::getResourceById($nParentResourceId);
            $strNodeName = $aNode['name'];
            $nParentResourceId = $aNode['parent_resource_id'];
            // 将节点名进栈
            array_push($aStack, $strNodeName);
            // 尾递归，处理父资源节点
            $this->findParent($nParentResourceId, $aStack);
        } else { // 父节点id为0，说明已经到了最顶层节点，push完顶层节点名称后终止递归
            $aNode = ResourceModel::getResourceById($nParentResourceId);
            $strNodeName = $aNode['name'];
            array_push($aStack, $strNodeName);
            return true;
        }
    }
}