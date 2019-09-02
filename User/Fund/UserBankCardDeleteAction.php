<?php

namespace App\Http\SingleActions\Frontend\User\Fund;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\User\Fund\FrontendUsersBankCard;
use Illuminate\Http\JsonResponse;

class UserBankCardDeleteAction
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
     * 用户删除绑定银行卡
     * @param  FrontendApiMainController  $contll
     * @param  array $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, array $inputDatas): JsonResponse
    {
        $bankCardEloq = $this->model::find($inputDatas['id']);
        if ($bankCardEloq->user_id != $contll->partnerUser->id) {
            return $contll->msgOut(false, [], '100200');
        }
        if ($bankCardEloq->delete()) {
            return $contll->msgOut(true);
        } else {
            return $contll->msgOut(false, [], '100201');
        }
    }
}
