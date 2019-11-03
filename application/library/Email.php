<?php
/**
 * 邮件工具类
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2019-11-2
 * Time: 11:51
 */

use Nos\Comm\Config;
use Nos\Comm\Pool;
use Nos\Exception\CoreException;
use Nos\Exception\OperateFailedException;

class Email
{

    private static $strSendFrom = '';

    /**
     * 发送邮件
     * @param string $strSendTo 发送对象
     * @param string $strTitle 邮件标题
     * @param string $strContent 邮件内容
     * @return int 成功返回1
     * @throws OperateFailedException
     */
    public static function send(string $strSendTo, string $strTitle, string $strContent)
    {
        try {
            $objMailer = self::getMailerInstance();
            $objMessage = new Swift_Message($strTitle);
            $objMessage->setFrom(self::$strSendFrom)
                ->setTo($strSendTo)
                ->setBody($strContent);
            return $objMailer->send($objMessage);
        } catch (\Exception $e) {
            throw new OperateFailedException('mail|send_mail_failed|msg:' . $e->getMessage());
        }
    }


    /**
     * 获取transport实例
     * @return array|mixed|Swift_SmtpTransport
     * @throws CoreException
     */
    private static function getMailerInstance()
    {
        $aMailConfig = Config::get('mail.ini');
        $strHost     = $aMailConfig['host'];
        $strPort     = $aMailConfig['port'];
        $strUserName = $aMailConfig['username'];
        $strPassword = $aMailConfig['password'];
        $strPoolKey = $strHost . $strPort . $strUserName . $strPassword;
        $objMailer = Pool::get($strPoolKey);
        if (empty($objMailer)) {
            $objTransport = new Swift_SmtpTransport($strHost, $strPort, 'ssl');
            $objTransport->setUsername($strUserName)->setPassword($strPassword);
            $objMailer = new Swift_Mailer($objTransport);
            Pool::set($strPoolKey, $objMailer);
        }
        self::$strSendFrom = $strUserName;
        return $objMailer;
    }
}