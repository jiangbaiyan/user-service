<?php
/**
 * 统一注册
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/28
 * Time: 10:20 上午
 */
use Nos\Comm\Validator;
use Nos\Http\Request;

class Unified_RegisterController extends BaseController
{

    /**
     * 业务逻辑
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'email' => 'required|email',
            'password' => 'required',
            'data' => 'required'
        ]);
        // 拿到用户数据并写库
        // 生成JWT TOKEN
        // 存redis key为token，value为刚插入的用户id
        // 返回JWT TOKEN

    }
}