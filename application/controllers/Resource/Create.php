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
            'key' => 'required'
        ]);
        $aQueue = [];
        $nParentResourceId = $aParams['parent_resource_id'];
        $strCurKey  = $aParams['key'];
        // 父节点id为0，说明它是根节点
        if ($nParentResourceId == 0) {
            $strFullKey = $strCurKey;
        } else { // 否则它挂载了某个节点下
            // 递归遍历所有祖先节点，并拼接节点名
            $this->findParent($nParentResourceId, $aQueue);
            // '.'为分隔符
            $strFullKey = implode('.', $aQueue) . '.' .  $strCurKey;
            $strFullKey = trim($strFullKey, '.');
        }
        // 查询节点名是否已经存在
        if (ResourceModel::getResourceByFullKey($strFullKey)) {
            throw new OperateFailedException('resource|resource_key_exists');
        }
        $aInsert = [
            'parent_resource_id' => $nParentResourceId,
            'cur_key'  => $strCurKey,
            'full_key' => $strFullKey
        ];
        ResourceModel::createResource($aInsert);
        Response::apiSuccess();
    }


    /**
     * 递归处理当前节点的所有祖先节点
     * @param int $nParentResourceId
     * @param array $aQueue
     * @return bool
     * @throws CoreException
     * @throws OperateFailedException
     */
    private function findParent(int $nParentResourceId, array &$aQueue)
    {
        // 如果没有到最顶层节点，递归的获取节点名
        if (!empty($nParentResourceId)) {
            // 查询父节点名称
            $aNode = ResourceModel::getResourceById($nParentResourceId);
            if (!$aNode) {
                throw new OperateFailedException("resource|parent_node:{$nParentResourceId}_not_exists");
            }
            $strCurKey = $aNode['cur_key'];
            $nParentResourceId = $aNode['parent_resource_id'];
            // 将节点名放到数组的前面，这样层级高的节点就在数组的前面
            array_unshift($aQueue, $strCurKey);
            // 尾递归，处理父资源节点
            $this->findParent($nParentResourceId, $aQueue);
        } else { // 父节点id为0，说明已经到了最顶层节点，push完顶层节点名称后终止递归
            return true;
        }
    }
}