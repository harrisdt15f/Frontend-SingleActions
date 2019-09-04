<?php

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
        $data = $this->model::select(
            'id',
            'bank_sign',
            'bank_name',
            'owner_name',
            'branch',
            'status',
            'created_at',
            'updated_at'
        )
            ->where('user_id', $contll->partnerUser->id)
            ->get()
            ->toArray();
        foreach ($data as &$item) {
            $item['card_number'] = $this->model::find($item['id'])->card_num;
        }
        return $contll->msgOut(true, $data);
    }
}
