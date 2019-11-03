<?php
/**
 * 资源节点模型
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/29
 * Time: 09:07 上午
 */

namespace Resource;

use Nos\Base\BaseModel;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;

class ResourceModel extends BaseModel
{

    public static $table = 'resource';

    /**
     * 查询资源节点
     * @param array $aQuery
     * @param int $page
     * @param int $length
     * @return array
     * @throws CoreException
     */
    public static function getResource(array $aQuery, int $page = 0, int $length = 0)
    {
        return self::select(['*'], $aQuery, [
            'page' => $page,
            'length' => $length
        ]);
    }

    /**
     * 通过完整节点名获取资源节点
     * @param string $strFullKey
     * @return array
     * @throws CoreException
     */
    public static function getResourceByFullKey(string $strFullKey)
    {
        return self::getResource([
            ['full_key', '=', $strFullKey]
        ]);
    }

    /**
     * 通过id获取资源节点
     * @param int $nId
     * @return array
     * @throws CoreException
     */
    public static function getResourceById(int $nId)
    {
        return self::getResource([
            ['id', '=', $nId]
        ]);
    }

    /**
     * 创建资源节点
     * $aData示例：
     * [
     *    'parent_resource_id' => '0',
     *    'name' => 'ndp'
     * ]
     * @param array $aData
     * @return mixed
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function createResource(array $aData)
    {
        $nRows = self::insert($aData);
        if (!$nRows) {
            throw new OperateFailedException('resourceModel|create_failed|data:' . json_encode($aData));
        }
        return $nRows;
    }

    /**
     * 删除资源节点
     * @param array $aQuery
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function deleteResource(array $aQuery)
    {
        $nRows = self::delete($aQuery);
        if (!$nRows) {
            throw new OperateFailedException('resourceModel|delete_failed|query:' . json_encode($aQuery));
        }
        return $nRows;
    }


    /**
     * 更新资源节点
     * @param array $aQuery 要更新的记录行
     * @param array $aData 更新的数据
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function updateResource(array $aQuery, array $aData)
    {
        $nRows = self::update($aData, $aQuery);
        if (!$nRows) {
            throw new OperateFailedException('resourceModel|update_failed|params:' . json_encode($aData) . '|query:' . json_encode($aQuery));
        }
        return $nRows;
    }
}