<?php

namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Game\Lottery\LotteryTraceList;
use App\Models\LotteryTrace;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LotteriesStopTraceAction
{
    protected $model;

    /**
     * @param  LotteryTraceList  $lotteryTraceList
     */
    public function __construct(LotteryTraceList $lotteryTraceList)
    {
        $this->model = $lotteryTraceList;
    }
    /**
     * 终止追号
     * @param  FrontendApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        $traceListsEloqs = $this->model->getUnfinishedTrace($inputDatas['id'], $contll->partnerUser->id);
        DB::beginTransaction();
        $canceledNum = 0; //取消的期数
        $canceledAmount = 0; //取消的金额
        foreach ($traceListsEloqs as $item) {
            $item->status = $item::STATUS_USER_STOPED;
            $item->cancel_time = Carbon::now()->toDateTimeString();
            $item->save();
            if ($item->errors()->messages()) {
                DB::rollback();
                return $contll->msgOut(false, [], '400', $item->errors()->messages());
            }
            $canceledNum++;
            $canceledAmount += $item->total_price;
        }
        $lotteryTraceEloq = LotteryTrace::find($inputDatas['id']);
        $lotteryTraceEloq->canceled_issues += $canceledNum;
        $lotteryTraceEloq->canceled_amount += $canceledAmount;
        $lotteryTraceEloq->save();
        if ($lotteryTraceEloq->errors()->messages()) {
            DB::rollback();
            return $contll->msgOut(false, [], '400', $lotteryTraceEloq->errors()->messages());
        }
        DB::commit();
        return $contll->msgOut(true);
    }
}
