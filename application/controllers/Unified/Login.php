<?php
/**
 * 统一登录
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019/10/28
 * Time: 10:20 上午
 */

use Nos\Comm\Validator;
use Nos\Http\Request;


class Unified_LoginController extends BaseController
{

    /**
     * 业务逻辑
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'unifiedToken' => 'required'
        ]);
    }
}