<?php

namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Game\Lottery\LotteryIssue;
use Illuminate\Http\JsonResponse;

class LotteriesLastIssuesAction
{
    /**
     * 获取彩种上期的奖期
     * @param  FrontendApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        $lotteryIssue = LotteryIssue::getPastIssue($inputDatas['lottery_sign']);
        $data = [
            'lottery_id' => $lotteryIssue->lottery_id ?? null,
            'lottery_name' => $lotteryIssue->lottery_name ?? null,
            'official_code' => $lotteryIssue->official_code ?? null,
            'encode_time' => $lotteryIssue->encode_time ?? null,
        ];
        return $contll->msgOut(true, $data);
    }
}