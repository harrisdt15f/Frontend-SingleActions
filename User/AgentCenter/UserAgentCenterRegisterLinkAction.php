<?php

namespace App\Http\SingleActions\Frontend\User\AgentCenter;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\SystemConfiguration;
use App\Models\User\FrontendUsersRegisterableLink;
use Exception;
use Illuminate\Http\JsonResponse;

class UserAgentCenterRegisterLinkAction
{
    protected $model;

    /**
     * RegisterLinkAction constructor.
     * @param FrontendUsersRegisterableLink $FrontendUsersRegisterableLink
     */
    public function __construct(FrontendUsersRegisterableLink $FrontendUsersRegisterableLink)
    {
        $this->model = $FrontendUsersRegisterableLink;
    }

    /**
     * 开户链接
     * @param FrontendApiMainController $contll
     * @param $request
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        $expire = $inputDatas['expire'];
        $channel = $inputDatas['channel'];
        $prize_group = $inputDatas['prize_group'];

        //链接有效期列表
        $expire_list = configure('users_register_expire');
        $expire_list = json_decode($expire_list, true);

        if (!in_array($expire, $expire_list)) {
            return $contll->msgOut(false, [], '100600');
        }

        //最低开户奖金组
        $min_user_prize_group = configure('min_user_prize_group');
        //最高开户奖金组
        $max_user_prize_group = configure('max_user_prize_group');

        $userInfo = $contll->currentAuth->user();
        if ($userInfo->prize_group < $max_user_prize_group) {
            $max_user_prize_group = $userInfo->prize_group;
        }

        if ($prize_group < $min_user_prize_group || $prize_group > $max_user_prize_group) {
            return $contll->msgOut(false, [], '100601');
        }

        if ($userInfo->type != 2) {
            return $contll->msgOut(false, [], '100602');
        }
        
        //开户链接
        $frontUrl = configure('web_fronted_url');
        $keyword = random_int(11, 99) . substr(uniqid(), 7);
        $url = trim($frontUrl, '/') . '/register/' . $keyword;

        $addData = [
            'user_id' => $userInfo->id,
            'username' => $userInfo->username,
            'prize_group' => $prize_group,
            'type' => 0,//0链接注册1扫码注册
            'is_agent' => 0,//链接注册的用户类型：0用户1代理
            'channel' => $channel,
            'keyword' => $keyword,
            'url' => $url,
            'status' => 1,
            'platform_id' => $contll->currentPlatformEloq->platform_id,
            'platform_sign' => $contll->currentPlatformEloq->platform_sign,
        ];

        if ($expire > 0) {
            $addData['valid_days'] = $expire;
            $addData['expired_at'] = strtotime("+ {$expire} days");
        }

        try {
            $FrontendUsersRegisterableLink = new $this->model;
            $FrontendUsersRegisterableLink->fill($addData);
            $FrontendUsersRegisterableLink->save();
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }

        return $contll->msgOut(true, $FrontendUsersRegisterableLink);
    }
}
