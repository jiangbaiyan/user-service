<?php
/**
 * 邮件链接回调接口
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019-11-2
 * Time: 18:43
 */

use Nos\Comm\Validator;
use Nos\Exception\OperateFailedException;
use Nos\Http\Request;
use Nos\Http\Response;
use User\UserModel;

class Unified_CallbackController extends BaseController
{

    /**
     * 激活用户
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'id' => 'required|numeric'
        ]);
        $aUser = UserModel::getUserById($aParams['id']);
        if (empty($aUser)) {
            throw new OperateFailedException("email_callback|user_id:{$aParams['id']}_not_exist");
        }
        if (!UserModel::update([
            'is_activate' => UserModel::ACTIVATE
        ], [
            ['id', '=', $aParams['id']]
        ])) {
            throw new OperateFailedException('email_callback|update_active_status_failed');
        }
        Response::apiSuccess();
    }
}