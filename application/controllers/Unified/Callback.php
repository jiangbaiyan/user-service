<?php
/**
 * 邮件链接回调接口
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019-11-2
 * Time: 18:43
 */

use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Http\Request;
use User\UserModel;

class Unified_CallbackController extends BaseController
{

    public $auth = false;

    /**
     * 激活用户
     * @throws OperateFailedException
     * @throws CoreException
     * @throws ParamValidateFailedException
     */
    public function indexAction()
    {
        Validator::make($aParams = Request::all(), [
            'data' => 'required'
        ]);
        $strData = base64_decode($aParams['data']);
        list($nId, $callbackUrl) = explode('_', $strData);
        $aUser = UserModel::getUserById($nId);
        if (empty($aUser)) {
            throw new OperateFailedException("email_callback|user_id:{$nId}_not_exist");
        }
        if (!UserModel::update([
            'is_activate' => UserModel::ACTIVATE
        ], [
            ['id', '=', $nId]
        ])) {
            throw new OperateFailedException('email_callback|update_active_status_failed');
        }
        header('Location: ' . $callbackUrl);
        exit;
    }
}