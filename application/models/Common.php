<?php
/**
 * 公共基础模型
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/11/21
 * Time: 09:07 上午
 */

use Nos\Base\BaseModel;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;

class CommonModel extends BaseModel
{

    /**
     * 通用单表查询
     * @param array $aQuery 查询条件
     * @param array $aField 字段
     * @param int $page 当前页码
     * @param int $length 每页条数
     * @return array
     * @throws CoreException
     */
    public static function getListCommon(array $aQuery = [], array $aField = ['*'], int $page = 0, int $length = 0)
    {
        return static::select($aField, $aQuery, [
            'page' => $page,
            'length' => $length
        ]);
    }

    /**
     * 根据id查找
     * @param int $nId id
     * @param array $aField 查询字段
     * @param int $page 当前页码
     * @param int $length 每页条数
     * @return array
     * @throws CoreException
     */
    public static function getById(int $nId, array $aField = ['*'], int $page = 0, int $length = 0)
    {
        return self::getListCommon([
            ['id', '=', $nId]
        ], $aField, $page, $length);
    }

    /**
     * 通用新增
     * @param array $aData
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function create(array $aData)
    {
        $strTable = static::$table;
        $nId = static::insert($aData);
        if (!$nId) {
            throw new OperateFailedException("{{$strTable}}|create_batch_failed|data:" . json_encode($aData));
        }
        return $nId;
    }


    /**
     * 通用根据id修改
     * @param int $nId
     * @param array $aData
     * @return int
     * @throws CoreException
     */
    public static function updateById(int $nId, array $aData)
    {
        return self::update($aData, [
            ['id', '=', $nId]
        ]);
    }


    /**
     * 通用根据id删除
     * @param int $nId
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function deleteById(int $nId)
    {
        $strTable = static::$table;
        $nRows = static::delete([
            ['id', '=', $nId]
        ]);
        if (!$nRows) {
            throw new OperateFailedException("{$strTable}|delete_failed|id:" . $nId);
        }
        return $nRows;
    }

}
