<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryNoticeList;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;

class HompageLotteryNoticeListAction
{
    /**
     * 开奖公告列表
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $lotteryNoticeEloq = FrontendAllocatedModel::select('status', 'show_num')->where('en_name', 'lottery.notice')->first();
        if ($lotteryNoticeEloq === null) {
            $lotteryNoticeEloq = FrontendAllocatedModel::createLotteryNotice();
        }
        if ($lotteryNoticeEloq->status !== 1) {
            return $contll->msgOut(false, [], '100400');
        }
        $lotterysEloq = FrontendLotteryNoticeList::select('lotteries_id', 'icon_path', 'status', 'sort', 'cn_name')->with('specificNewestOpenedIssue')->where('status', 1)->orderBy('sort', 'asc')->limit($lotteryNoticeEloq->show_num)->get();
        $data = [];
        foreach ($lotterysEloq as $lotteryEloq) {
            $issue = $lotteryEloq->specificNewestOpenedIssue->issue ?? null;
            $officialCode = $lotteryEloq->specificNewestOpenedIssue->official_code ?? null;
            $encodeTime = $lotteryEloq->specificNewestOpenedIssue->encode_time ?? null;
            $data[] = [
                'cn_name' => $lotteryEloq->cn_name,
                'lotteries_id' => $lotteryEloq->lotteries_id,
                'icon' => $lotteryEloq->icon_path,
                'issue' => $issue,
                'official_code' => $officialCode,
                'encode_time' => $encodeTime,
            ];
        }
        return $contll->msgOut(true, $data);
    }
}
