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

}
