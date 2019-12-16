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
     * @throws ResourceNotFoundException
     */
    public static function updateById(int $nId, array $aData)
    {
        $aUpdate = [];
        if (isset($aData['parent_resource_id'])) {
            if ($aData['parent_resource_id']) {
                $aData = ResourceModel::getById($aData['parent_resource_id']);
                if (!$aData['total']) {
                    throw new ResourceNotFoundException("resource_model|parent_resource_id:{$aData['parent_resource_id']}_not_found");
                }
            }
            $aUpdate['parent_resource_id'] = $aData['parent_resource_id'];
        }
        isset($aData['cur_key']) && $aUpdate['cur_key'] = $aData['cur_key'];
        return parent::updateById($nId, $aUpdate);
    }

}
