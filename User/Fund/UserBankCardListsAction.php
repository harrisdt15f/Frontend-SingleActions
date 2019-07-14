<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 16:23:01
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 18:35:07
 */
namespace App\Http\SingleActions\Frontend\User\Fund;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\User\Fund\FrontendUsersBankCard;
use Illuminate\Http\JsonResponse;

class UserBankCardListsAction
{
    protected $model;

    /**
     * @param  FrontendUsersBankCard  $frontendUsersBankCard
     */
    public function __construct(FrontendUsersBankCard $frontendUsersBankCard)
    {
        $this->model = $frontendUsersBankCard;
    }

    /**
     * 用户银行卡列表
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $data = $this->model::select('id', 'bank_sign', 'bank_name', 'owner_name', 'card_number', 'branch', 'status', 'created_at', 'updated_at')->where('user_id', $contll->partnerUser->id)->get()->toArray();
        return $contll->msgOut(true, $data);
    }
}
