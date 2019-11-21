<?php

use Common\App\AppModel;
use Nos\Comm\Validator;
use Nos\Exception\CoreException;
use Nos\Exception\ParamValidateFailedException;
use Nos\Exception\ResourceNotFoundException;
use Nos\Exception\UnauthorizedException;
use Nos\Http\Request;
use Yaf\Controller_Abstract;

abstract class BaseController extends Controller_Abstract
{

    /**
     * 已经接入用户中心的业务线
     */
    const APPS = [
        'ndp',
        'nmp'
    ];

    /**
     * 是否需要接口鉴权
     * @var bool $auth
     */
    protected $auth = true;


    /**
     * 统一接口鉴权函数
     * @return bool
     * @throws CoreException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     * @throws UnauthorizedException
     */
    protected function auth()
    {
        Validator::make($aParams = Request::all(), [
            'appId' => 'required',
            'accessToken' => 'required',
            'timestamp' => 'required'
        ]);
        $strAppId = $aParams['appId'];
        // 测试
        if ($strAppId == 'uc_all') {
            return true;
        }
        if (!in_array($strAppId, self::APPS)) {
            throw new UnauthorizedException("auth|app:{$strAppId}_was_not_registered");
        }
        $strAppSecret = AppModel::getAppSecretByAppId($strAppId);
        // 与客户端采用同样的加密算法
        $strBackAccessToken = md5($aParams['timestamp'] . $strAppId .  $strAppSecret);
        $strFrontAccessToken = $aParams['accessToken'];
        // 判断前后端的accessToken是否相等
        if ($strFrontAccessToken != $strBackAccessToken) {
            throw new UnauthorizedException("auth|app:{$strAppId}_auth_failed
            |frontAccessToken:{$aParams['accessToken']}
            |backAccessToken:{$strBackAccessToken}
            |timestamp:{$aParams['timestamp']}");
        }
        return true;
    }

    /**
     * 业务逻辑
     */
    abstract protected function indexAction();

    /**
     * 初始化
     */
    private function init()
    {
        $this->auth && $this->auth();
    }

}
