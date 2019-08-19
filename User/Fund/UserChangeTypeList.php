<?php
/**
 * @Author: Fish
 * @Date:   2019/7/5 17:11
 */

namespace App\Http\SingleActions\Frontend\User\Fund;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\User\Fund\FrontendUsersAccountsType;
use App\Models\User\UsersRechargeHistorie;
use Illuminate\Http\JsonResponse;

class UserChangeTypeList
{
    protected $model;

    /**
     * @param  UsersRechargeHistorie  $usersRechargeHistorie
     */
    public function __construct(FrontendUsersAccountsType $frontendUsersAccountsType)
    {
        $this->model = $frontendUsersAccountsType;
    }

    /**
     * 用户充值列表
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $field = ['id', 'name', 'sign', 'in_out'];
        $datas = $this->model::getTypeList($field);
        return $contll->msgout(true, $datas);
    }
}
