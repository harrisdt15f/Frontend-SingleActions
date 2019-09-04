<?php

namespace App\Http\SingleActions\Frontend\User\Fund;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\User\Fund\FrontendUsersBankCard;
use Illuminate\Http\JsonResponse;

class UserBankCardListsAction
{
    protected $model;

    /**
     * @param  FrontendUsersBankCard $frontendUsersBankCard
     */
    public function __construct(FrontendUsersBankCard $frontendUsersBankCard)
    {
        $this->model = $frontendUsersBankCard;
    }

    /**
     * 用户银行卡列表
     * @param  FrontendApiMainController $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $data = $this->model::where('user_id', $contll->partnerUser->id)
            ->get(['id',
                'user_id',
                'parent_id',
                'top_id',
                'rid',
                'bank_sign',
                'bank_name',
                'owner_name',
                'province_id',
                'card_number',
                'city_id',
                'branch',
                'status'
            ])
            ->makeHidden(['card_number'])
            ->toArray();
        return $contll->msgOut(true, $data);
    }
}
