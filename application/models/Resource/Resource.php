<?php
/**
 * 资源节点模型
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/29
 * Time: 09:07 上午
 */

namespace Resource;

use CommonModel;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Exception\ResourceNotFoundException;

class ResourceModel extends CommonModel
{

    public static $table = 'resource';


    /**
     * 通过完整节点名获取资源节点
     * @param string $strFullKey
     * @return array
     * @throws CoreException
     */
    public static function getResourceByFullKey(string $strFullKey)
    {
        return self::getListCommon([
            ['full_key', '=', $strFullKey]
        ]);
    }

    /**
     * 根据id更新资源节点
     * @param int $nId
     * @param array $aData
     * @return int|void
     * @throws CoreException
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public static function updateById(int $nId, array $aData)
    {
        if (!isset($aData['cur_key']) || !isset($aData['parent_resource_id'])) {
            throw new ParamValidateFailedException('resource_model|need_cur_key_or_parent_resource_id');
        }
        if ($aData['parent_resource_id']) {
            if ($aData['parent_resource_id'] == $nId) {
                throw new OperateFailedException("resource_model|parent_resource_id_cannot_be_cur_id|id:{$nId}");
            }
            $aResource = ResourceModel::getById($aData['parent_resource_id']);
            if (!$aResource['total']) {
                throw new ResourceNotFoundException("resource_model|parent_resource_id:{$aData['parent_resource_id']}_not_found");
            }
        }
        $aUpdate = [];
        $aUpdate['parent_resource_id'] = $aData['parent_resource_id'];
        $aUpdate['cur_key']            = $aData['cur_key'];
        $aUpdate['full_key']           = self::getFullKeyByParentResourceId($aData['parent_resource_id'], $aData['cur_key']);
        return parent::updateById($nId, $aUpdate);
    }

    /**
     * 计算完整节点名称
     * @param int $nParentResourceId
     * @param string $strCurKey
     * @return string
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function getFullKeyByParentResourceId(int $nParentResourceId, string $strCurKey)
    {
        $aQueue = [];
        // 父节点id为0，说明它是根节点
        if ($nParentResourceId == 0) {
            $strFullKey = $strCurKey;
        } else { // 否则它挂载了某个节点下
            // 递归遍历所有祖先节点，并拼接节点名
            self::findParent($nParentResourceId, $aQueue);
            // '.'为分隔符
            $strFullKey = implode('.', $aQueue) . '.' .  $strCurKey;
            $strFullKey = trim($strFullKey, '.');
        }
        return $strFullKey;
    }

    /**
     * 递归查找父节点
     * @param int $nParentResourceId
     * @param array $aQueue
     * @return bool
     * @throws CoreException
     * @throws OperateFailedException
     */
    private static function findParent(int $nParentResourceId, array &$aQueue)
    {
        // 如果没有到最顶层节点，递归的获取节点名
        if (!empty($nParentResourceId)) {
            // 查询父节点名称
            $aNode = self::getById($nParentResourceId);
            if (!$aNode['total']) {
                throw new OperateFailedException("resource|parent_node:{$nParentResourceId}_not_exists");
            }
            $aNode = $aNode['data'][0];
            $strCurKey         = $aNode['cur_key'];
            $nParentResourceId = $aNode['parent_resource_id'];
            // 将节点名放到数组的前面，这样层级高的节点就在数组的前面
            array_unshift($aQueue, $strCurKey);
            // 尾递归，处理父资源节点
            self::findParent($nParentResourceId, $aQueue);
        } else { // 父节点id为0，说明已经到了最顶层节点，push完顶层节点名称后终止递归
            return true;
        }
    }

}
