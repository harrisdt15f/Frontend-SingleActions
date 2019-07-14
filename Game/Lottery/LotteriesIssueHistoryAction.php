<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 10:32:52
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-25 10:34:18
 */
namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Game\Lottery\LotteryIssue;
use Illuminate\Http\JsonResponse;

class LotteriesIssueHistoryAction
{
    /**
     * 历史奖期
     * @param  FrontendApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        $issuesEloq = LotteryIssue::where([
            ['lottery_id', $inputDatas['lottery_sign']],
            ['status_encode', 1],
        ])->orderBy('issue', 'desc')->limit($inputDatas['count'])->get();
        $data = [];
        foreach ($issuesEloq as $issueEloq) {
            $data[] = [
                'issue_no' => $issueEloq->issue,
                'code' => $issueEloq->official_code,
            ];
        }
        return $contll->msgOut(true, $data);
    }
}
