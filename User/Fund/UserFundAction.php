<?php

/**
 * @Author: Fish
 * @Date:   2019-07-2 12:42:55
 */

namespace App\Http\SingleActions\Frontend\User\Fund;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\User\Fund\FrontendUsersAccountsReport;
use Illuminate\Http\JsonResponse;

class UserFundAction
{

    protected $model;

    /**
     * @param  FrontendUsersAccountsReport  $frontendUsersAccountsReport
     */
    public function __construct(FrontendUsersAccountsReport $frontendUsersAccountsReport)
    {
        $this->model = $frontendUsersAccountsReport;
    }

    /**
     * 用户账变列表
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $contll->inputs['user_id'] = $contll->partnerUser->id;
        $fixedJoin = 1; //number of joining tables
        $withTable = 'gameMethods';
        $searchAbleFields = ['issue', 'process_time', 'type_name', 'amount', 'balance', 'method_id', 'user_id'];
        $withSearchAbleFields = ['method_id', 'lottery_name', 'method_name'];
        $orderFields = 'id';
        $orderFlow = 'desc';
        $data = $contll->generateSearchQuery($this->model, $searchAbleFields, $fixedJoin, $withTable, $withSearchAbleFields, $orderFields, $orderFlow);
        return $contll->msgOut(true, $data);
    }
}
