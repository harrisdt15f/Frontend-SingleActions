<?php
/**
 * @Author: Fish
 * @Date:   2019/7/5 17:11
 */

namespace App\Http\SingleActions\Frontend\User\Fund;


use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\User\UsersRechargeHistorie;
use Illuminate\Http\JsonResponse;

class UserRechargeListAction
{
    protected $model;

    /**
     * @param  UsersRechargeHistorie  $usersRechargeHistorie
     */
    public function __construct(UsersRechargeHistorie $usersRechargeHistorie)
    {
        $this->model = $usersRechargeHistorie;
    }

    /**
     * 用户充值列表
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $user = $contll->partnerUser;
        $eloqM = new UsersRechargeHistorie();
        $contll->inputs['extra_where']['method'] = 'where';
        $contll->inputs['extra_where']['key'] = 'user_id';
        $contll->inputs['extra_where']['value'] = $user->id;
        $searchAbleFields = [ 'company_order_num','created_at','amount'];
        $data = $contll->generateSearchQuery($eloqM, $searchAbleFields);
        return $contll->msgOut(true, $data);
    }
}