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
        $aUser = UserModel::getById((int)$nId);
        if (empty($aUser['total'])) {
            throw new OperateFailedException("email_callback|user_id:{$nId}_not_exist");
        }
        // 更新状态为已激活
        UserModel::updateById($nId, [
            'is_activate' => UserModel::ACTIVATE
        ]);
        header('Location: ' . $callbackUrl);
        exit;
    }
}
