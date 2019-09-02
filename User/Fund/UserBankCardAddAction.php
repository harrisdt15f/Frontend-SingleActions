<?php

namespace App\Http\SingleActions\Frontend\User\Fund;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\SystemConfiguration;
use App\Models\User\Fund\FrontendUsersBankCard;
use Exception;
use Illuminate\Http\JsonResponse;

class UserBankCardAddAction
{
    protected $model;
    protected $numberSign = 'binding_bankcard_number';

    /**
     * @param  FrontendUsersBankCard  $frontendUsersBankCard
     */
    public function __construct(FrontendUsersBankCard $frontendUsersBankCard)
    {
        $this->model = $frontendUsersBankCard;
    }

    /**
     * 用户添加绑定银行卡
     * @param  FrontendApiMainController  $contll
     * @param  array $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, array $inputDatas): JsonResponse
    {
        $configEloq = SystemConfiguration::where('sign', $this->numberSign)->first();
        if ($configEloq === null) {
            $configEloq = $this->createConfig();
        }
        $maxNumber = $configEloq->value;
        $nowNumber = $this->model::where('user_id', $contll->partnerUser->id)->count();
        if ($nowNumber >= $maxNumber) {
            return $contll->msgOut(false, [], '100202');
        }
        $addData = [
            'user_id' => $contll->partnerUser->id,
            'parent_id' => $contll->partnerUser->parent_id,
            'top_id' => $contll->partnerUser->top_id,
            'rid' => $contll->partnerUser->rid,
            'username' => $contll->partnerUser->username,
            'bank_sign' => $inputDatas['bank_sign'],
            'bank_name' => $inputDatas['bank_name'],
            'owner_name' => $inputDatas['owner_name'],
            'card_number' => $inputDatas['card_number'],
            'province_id' => $inputDatas['province_id'],
            'city_id' => $inputDatas['city_id'],
            'branch' => $inputDatas['branch'],
            'status' => $this->model::NATURAL_STATUS,
        ];
        try {
            $bankCardEloq = new $this->model;
            $bankCardEloq->fill($addData);
            $bankCardEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            return $contll->msgOut(false, [], $e->getCode(), $e->getMessage());
        }
    }

    /**
     * 生成 用户可绑定的银行卡数量 配置
     * @return object
     */
    public function createConfig(): object
    {
        $parent_id = SystemConfiguration::where('sign', 'system')->value('id');
        $value = 4;
        $addData = [
            'parent_id' => $parent_id,
            'pid' => 1,
            'sign' => 'binding_bankcard_number',
            'name' => '用户可绑定的银行卡数量',
            'description' => '用户可绑定的银行卡数量',
            'value' => $value,
            'status' => 1,
            'display' => 1,
        ];
        $configEloq = new SystemConfiguration();
        $configEloq->fill($addData);
        $configEloq->save();
        return $configEloq;
    }
}
