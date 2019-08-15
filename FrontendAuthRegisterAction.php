<?php

namespace App\Http\SingleActions\Frontend;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\SystemConfiguration;
use App\Models\SystemPlatform;
use App\Models\User\FrontendUsersRegisterableLink;
use App\Models\User\FrontendLinksRegisteredUsers;
use App\Models\User\UserPublicAvatar;
use App\Models\User\FrontendUser;
use App\Models\User\Fund\FrontendUsersAccount;
use App\Models\User\FrontendUsersSpecificInfo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontendAuthRegisterAction
{
    protected $model;

    /**
     * FrontendAuthRegisterAction constructor.
     * @param FrontendUser $frontendUser
     */
    public function __construct(FrontendUser $frontendUser)
    {
        $this->model = $frontendUser;
    }

    /**
     * 用户注册
     * 0.普通注册
     * 1.人工开户注册
     * 2.链接开户注册
     * 3.扫码开户注册
     * @param FrontendApiMainController $contll
     * @param $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        //注册类型
        $registerType = $inputDatas['register_type'] ?? 0;
        $re_password = $inputDatas['re_password'] ?? '';

        if ($re_password != '' && $re_password != $inputDatas['password']) {
            return $contll->msgOut(false, [], '100008');
        }

        $typeArr = [0, 1, 2, 3];
        if (!in_array($registerType, $typeArr)) {
            $registerType = 0;
        }

        $inputDatas['vip_level'] = 0;
        $inputDatas['parent_id'] = 0;

        //0.普通注册
        if ($registerType == 0) {
            $hostPlatform = SystemConfiguration::getConfigValue('host_platform_settings');
            $hostPlatform = json_decode($hostPlatform, true);

            if (isset($hostPlatform[$inputDatas['host']])) {
                $plat = $hostPlatform[$inputDatas['host']];
                isset($plat['platform_id']) && $inputDatas['platform_id'] = $plat['platform_id'];
                isset($plat['platform_sign']) && $inputDatas['platform_sign'] = $plat['platform_sign'];
            }
        }


        //1.人工开户注册
        if ($registerType == 1) {
            $inputDatas['prize_group'] = $inputDatas['prize_group'] ?? 0;
            if ($inputDatas['prize_group'] == 0) {
                return $contll->msgOut(false, [], '100015');
            }

            //当前用户需要登录
            if (!Auth::check()) {
                return $contll->msgOut(false, [], '100019');
            }

            $inputDatas['parent_id'] = Auth::id();
            $inputDatas['platform_id'] = $contll->currentPlatformEloq->platform_id;
            $inputDatas['platform_sign'] = $contll->currentPlatformEloq->platform_sign;

            //最低开户奖金组
            $min_user_prize_group = SystemConfiguration::getConfigValue('min_user_prize_group');
            //最高开户奖金组
            $max_user_prize_group = SystemConfiguration::getConfigValue('max_user_prize_group');

            $userInfo = $contll->currentAuth->user();
            if ($userInfo->prize_group < $max_user_prize_group) {
                $max_user_prize_group = $userInfo->prize_group;
            }

            if ($inputDatas['prize_group'] < $min_user_prize_group ||
                $inputDatas['prize_group'] > $max_user_prize_group
            ) {
                return $contll->msgOut(false, [], '100016');
            }
        }

        //2.链接开户注册和扫码开户
        if ($registerType == 2 || $registerType == 3) {
            $keyword = $inputDatas['keyword'] ?? '';

            if ($keyword == '') {
                return $contll->msgOut(false, [], '100017');
            }

            $link = FrontendUsersRegisterableLink::where('keyword', $keyword)->first();

            if (is_null($link)) {
                return $contll->msgOut(false, [], '100018');
            }

            $inputDatas['prize_group'] = $link->prize_group;
            $inputDatas['parent_id'] = $link->user_id;
            $inputDatas['platform_id'] = $link->platform_id;
            $inputDatas['platform_sign'] = $link->platform_sign;
        }

        //验证平台信息是否存在
        $platform = SystemPlatform::where('platform_id', $inputDatas['platform_id'])
            ->where('platform_sign', $inputDatas['platform_sign'])
            ->first();
        if (is_null($platform)) {
            return $contll->msgOut(false, [], '100020');
        }

        $inputDatas['password'] = bcrypt($inputDatas['password']);
        $inputDatas['register_ip'] = request()->ip();
        $inputDatas['pic_path'] = UserPublicAvatar::getRandomAvatar();
        $inputDatas['sign'] = $inputDatas['platform_sign'];

        //删除不必要的数据
        unset($inputDatas['keyword']);
        unset($inputDatas['platform_sign']);
        unset($inputDatas['host']);
        unset($inputDatas['register_type']);

        //插入信息
        DB::beginTransaction();
        try {
            //附属信息
            $FrontendUsersSpecificInfo = new FrontendUsersSpecificInfo();
            $SpecificInfo = [
                'register_type' => $registerType,
            ];
            $FrontendUsersSpecificInfo = $FrontendUsersSpecificInfo->fill($SpecificInfo);
            $FrontendUsersSpecificInfo->save();
            $inputDatas['user_specific_id'] = $FrontendUsersSpecificInfo->id;
            $user = $this->model::create($inputDatas);
            $user->rid = $user->id;

            //账户信息
            $userAccountEloq = new FrontendUsersAccount();
            $userAccountData = [
                'user_id' => $user->id,
                'balance' => 0,
                'frozen' => 0,
                'status' => 1,
            ];
            $userAccountEloq = $userAccountEloq->fill($userAccountData);
            $userAccountEloq->save();
            $user->account_id = $userAccountEloq->id;
            $user->save();


            //链接开户，扫码开户
            if ($registerType == 2 || $registerType == 3) {
                $registeredUser = new FrontendLinksRegisteredUsers;
                $registeredUser->register_link_id = $link->id;
                $registeredUser->user_id = $user->id;
                $registeredUser->url = $link->url;
                $registeredUser->username = $user->username;
                $registeredUser->save();
            }

            DB::commit();
            $data['name'] = $user->username;
            return $contll->msgOut(true, $data);
        } catch (Exception $e) {
            DB::rollBack();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
