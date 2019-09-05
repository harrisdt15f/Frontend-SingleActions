<?php

namespace App\Http\SingleActions\Frontend\User\Fund;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\SystemConfiguration;
use App\Models\User\Fund\FrontendUsersBankCard;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\Integer;

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
        if ($this->addBankVerifiy($contll, $inputDatas) !== null) {
            return $this->addBankVerifiy($contll, $inputDatas);
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
            'status' => $this->model::INITIALIZATION_STATUS,
        ];
        try {
            $bankCardEloq = $this->model;
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

    /**
     * @param FrontendApiMainController $contll
     * @param array $inputDatas
     * @return JsonResponse
     */
    public function addBankVerifiy(FrontendApiMainController $contll, array $inputDatas)
    {
        $userIdNum = $this->model::where('user_id', $contll->partnerUser->id)->count();
        $ownerNameNum = $this->model::where([['owner_name', $inputDatas['owner_name']],['user_id',$contll->partnerUser->id]])->count();
        //检验当前用户资金密码是否设置
        $funPassword = $contll->partnerUser->fund_password;
        if (!isset($funPassword)) {
            return $contll->msgOut(false, [], '100205');
        }
        //检验当前用户添加的拥有者是否存在
        if ($userIdNum > 0) {
            if ($ownerNameNum === 0) {
                return $contll->msgOut(false, [], '100204');
            }
        }
        //检验当前用户银行卡数量是否大于等于1
        if ($userIdNum >= 1) {
            if ($this->twoAddVerifiy($contll, $ownerNameNum, $inputDatas) !== null) {
                return $this->twoAddVerifiy($contll, $ownerNameNum, $inputDatas);
            }
        }
        //检验当前用户添加的银行卡号是否存在
        $cardNumber = $this->model::where([['card_number', $inputDatas['card_number']],['user_id',$contll->partnerUser->id]])->count();
        if (!empty($cardNumber)) {
            return $contll->msgOut(false, [], '100203');
        }
    }

    /**
     * 二次添加检验拥有者与资金密码
     * @param FrontendApiMainController $contll
     * @param int $ownerNameNum
     * @return JsonResponse
     */
    public function twoAddVerifiy(FrontendApiMainController $contll, $ownerNameNum, $inputDatas)
    {
        //检验资金密码与拥有者
        if (empty($inputDatas['fund_password'])) {
            return $contll->msgOut(false, [], '100208');
        }
        if (!Hash::check($inputDatas['fund_password'], $contll->partnerUser->fund_password)) {
            return $contll->msgOut(false, [], '100206');
        }
        if ($ownerNameNum === 0) {
            return $contll->msgOut(false, [], '100207');
        }
    }
}
