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
        if ($inputDatas['type'] == 1) {
            $traceListsEloqs = $this->model->getUnfinishedTrace($inputDatas['lottery_traces_id'], $contll->partnerUser->id);
        } elseif ($inputDatas['type'] == 2) {
            $traceListsEloqs = $this->model::where([
                ['id', $inputDatas['lottery_trace_lists_id']],
                ['user_id', $contll->partnerUser->id],
                ['status', 0],
            ])->get();
        } else {
            return $contll->msgOut(false, [], '100314');
        }
        if ($traceListsEloqs->isEmpty()) {
            return $contll->msgOut(false, [], '100315');
        }
        DB::beginTransaction();
        $canceledNum = 0; //本次取消的期数
        $canceledAmount = 0; //本次取消的金额
        foreach ($traceListsEloqs as $item) {
            $item->status = $item::STATUS_USER_STOPED;
            $item->cancel_time = Carbon::now()->toDateTimeString();
            $item->save();
            if ($item->errors()->messages()) {
                DB::rollback();
                return $contll->msgOut(false, [], '400', $item->errors()->messages());
            }
            $canceledNum++; //本次取消的期数
            $canceledAmount += $item->total_price; //本次取消的金额
        }
        $lotteryTraceEloq = LotteryTrace::find($traceListsEloqs->first()->trace_id);
        $lotteryTraceEloq->canceled_issues += $canceledNum; //lottery_traces表 累积取消的期数
        $lotteryTraceEloq->canceled_amount += $canceledAmount; //lottery_traces表 累积取消的金额
        $lotteryTraceEloq->save();
        if ($lotteryTraceEloq->errors()->messages()) {
            DB::rollback();
            return $contll->msgOut(false, [], '400', $lotteryTraceEloq->errors()->messages());
        }
        DB::commit();
        return $contll->msgOut(true);
    }
}
