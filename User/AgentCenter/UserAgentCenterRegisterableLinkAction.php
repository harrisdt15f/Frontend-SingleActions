<?php

namespace App\Http\SingleActions\Frontend\User\AgentCenter;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\SystemConfiguration;
use App\Models\User\FrontendUsersRegisterableLink;
use Illuminate\Http\JsonResponse;

class UserAgentCenterRegisterableLinkAction
{
    protected $model;

    /**
     * RegisterableLinkAction constructor.
     * @param FrontendUsersRegisterableLink $FrontendUsersRegisterableLink
     */
    public function __construct(FrontendUsersRegisterableLink $FrontendUsersRegisterableLink)
    {
        $this->model = $FrontendUsersRegisterableLink;
    }

    /**
     * 开户链接
     * @param FrontendApiMainController $contll
     * @param $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $request): JsonResponse
    {
        $data = [];

        $count = $request->input('count') ?? 15;
        
        //链接有效期列表
        $data['expire_list'] = SystemConfiguration::getConfigValue('users_register_expire');
        //最低开户奖金组
        $data['min_user_prize_group'] = SystemConfiguration::getConfigValue('min_user_prize_group');
        //最高开户奖金组
        $data['max_user_prize_group'] = SystemConfiguration::getConfigValue('max_user_prize_group');

        $userInfo = $contll->currentAuth->user() ;
        if($userInfo->prize_group < $data['max_user_prize_group'] ){
            $data['max_user_prize_group'] = $userInfo->prize_group;
        }
        
        //有效开户链接
        $data['links'] = $this->model->where('status', 1)->where(
            function ($query) {
                $query->whereNull('expired_at')->orWhere('expired_at', '>=', time());
            }
        )->select('id', 'prize_group', 'valid_days', 'is_agent', 'channel', 'created_count', 'url', 'created_at')->orderBy('expired_at', 'desc')->paginate($count);
        
        return $contll->msgOut(true, $data);
    }
}
