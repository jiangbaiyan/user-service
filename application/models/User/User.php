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
     * 查询用户列表
     * @param array $aQuery
     * @param int $page
     * @param int $length
     * @return array
     * @throws CoreException
     */
    public static function getUser(array $aQuery, int $page, int $length)
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
    public static function createUser(array $aData)
    {
        $nRows = self::insertBatch($aData);
        if (!$nRows) {
            throw new OperateFailedException('userModel|create_batch_failed|data:' . json_encode($aData));
        }
        return $nRows;
    }

    /**
     * 删除用户
     * @param array $aQuery
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function deleteUser(array $aQuery)
    {
        $nRows = self::delete($aQuery);
        if (!$nRows) {
            throw new OperateFailedException('userModel|delete_failed|query:' . json_encode($aQuery));
        }
        return $nRows;
    }


    /**
     * 根据id更新用户数据
     * @param array $aQuery 要更新的记录行
     * @param array $aData 更新的数据
     * @return int
     * @throws CoreException
     * @throws OperateFailedException
     */
    public static function updateUser(array $aQuery, array $aData)
    {
        $nRows = self::update($aData, $aQuery);
        if (!$nRows) {
            throw new OperateFailedException('userModel|update_failed|params:' . json_encode($aData) . '|query:' . json_encode($aQuery));
        }
        return $nRows;
    }
}