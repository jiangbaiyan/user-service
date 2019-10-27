<?php
/**
 * 用户模型
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/27
 * Time: 10:07 上午
 */

namespace User;

use Nos\Base\BaseModel;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;

class UserModel extends BaseModel
{

    public static $table = 'user';

    /**
     * 获取用户列表
     * @param array $aQuery
     * @param int $page
     * @param int $length
     * @return array
     * @throws CoreException
     */
    public static function getList(array $aQuery, int $page, int $length)
    {
        return self::select(['*'], $aQuery, [
            'page' => $page,
            'length' => $length
        ]);
    }

    /**
     * 创建用户(支持批量)
     * $aData示例：
     * [
     *     [
     *         'name' => 'jiangbaiyan',
     *         'age'  => 15
     *     ],
     *     [
     *         'name' => 'grape',
     *         'age'  => 15
     *     ]
     * ]
     * @param array $aData
     * @return mixed
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function create(array $aData)
    {
        $nRows = self::insertBatch($aData);
        if (!$nRows) {
            throw new OperateFailedException('userModel|create_batch_failed|data:' . json_encode($aData));
        }
        return $nRows;
    }

    /**
     * 根据id删除用户(支持批量)
     * @param array $aIds 要删除的id
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function _delete(array $aIds)
    {
        $nRows = self::delete([
            ['id', 'in', $aIds]
        ]);
        if (!$nRows) {
            throw new OperateFailedException('userModel|delete_failed|ids:' . json_encode($aIds));
        }
        return $nRows;
    }


    /**
     * 根据id更新用户数据
     * @param int $nId 更新的记录id
     * @param array $aParams 更新的数据
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function updateById(int $nId, array $aParams)
    {
        $aWhere = [
            ['id', '=', $nId]
        ];
        $nRows = self::update($aParams, $aWhere);
        if (!$nRows) {
            throw new OperateFailedException('userModel|update_failed|params:' . json_encode($aParams) . '|id:' . $nId);
        }
        return $nRows;
    }
}