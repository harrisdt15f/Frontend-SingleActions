<?php

namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Game\Lottery\LotteryTraceList;
use App\Models\LotteryTrace;
use Doctrine\DBAL\Schema\AbstractAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
class LotteriesStopTraceAction
{
    protected $model;
    protected $stopAllTraceType = 1;
    protected $stopOneTraceType = 2;

    /**
     * @param  LotteryTraceList $lotteryTraceList
     */
    public function __construct(LotteryTraceList $lotteryTraceList)
    {
        $this->model = $lotteryTraceList;
    }

    /**
     * 终止追号
     * @param  FrontendApiMainController $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        if ($inputDatas['type'] == $this->stopAllTraceType) {
            $traceListsEloqs = $this->model->getUnfinishedTrace($inputDatas['lottery_traces_id'], $contll->partnerUser->id);
        } elseif ($inputDatas['type'] == $this->stopOneTraceType) {
            $traceListsEloqs = $this->model::where([
                ['id', $inputDatas['lottery_trace_lists_id']],
                ['user_id', $contll->partnerUser->id],
                ['status', LotteryTraceList::STATUS_WAITING],
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
        foreach ($traceListsEloqs as $traceListsItem) {
            $traceListsItem->status = $traceListsItem::STATUS_USER_STOPED;
            $traceListsItem->cancel_time = Carbon::now()->toDateTimeString();
            $traceListsItem->save();
            if ($traceListsItem->errors()->messages()) {
                DB::rollback();
                return $contll->msgOut(false, [], '400', $traceListsItem->errors()->messages());
            }
            $canceledNum++; //本次取消的期数
            $canceledAmount += $traceListsItem->total_price; //本次取消的金额
            //帐变处理
            $user = $contll->partnerUser;
            if ($user->account()->exists()) {
                $account = $user->account;
            } else {
                return $contll->msgOut(false, [], '100313');
            }
            $params = [
                'user_id' => $user->id,
                'amount' => $traceListsItem['total_price'],
                'lottery_id' => $traceListsItem['lottery_sign'],
                'method_id' => $traceListsItem['method_sign'],
                'project_id' => $traceListsItem['project_id'],
                'issue' => $traceListsItem['issue'],
            ];
            $res = $account->operateAccount($params, 'cancel_order');
            if ($res !== true) {
                DB::rollBack();
                return $contll->msgOut(false, [], '', $res);
            }
        }
        $lotteryTraceEloq = LotteryTrace::find($traceListsEloqs->first()->trace_id);
        $lotteryTraceEloq->canceled_issues += $canceledNum; //lottery_traces表 累积取消的期数
        $lotteryTraceEloq->canceled_amount += $canceledAmount; //lottery_traces表 累积取消的金额
        if ($inputDatas['type'] == $this->stopAllTraceType) {
            $finishedTraceListsEloqs = $this->model->getFinishedTraceToRun($inputDatas['lottery_traces_id'], $contll->partnerUser->id);
            //有半状态正在运行的状态情况下，如果有不对lotteryTrace 做任何改变
            if ($finishedTraceListsEloqs->isEmpty()) {
                $finishedTraceListsEloqs = $this->model->getFinishedTrace($inputDatas['lottery_traces_id'], $contll->partnerUser->id);
                //如果用户此前有执行过追号其中一条，状态改为STATUS_FINISHED=1 完成，如果没有全部撤销变成 5 用户撤销
                $lotteryTraceEloq->status = $finishedTraceListsEloqs->isEmpty() ? LotteryTrace::STATUS_USER_DROPED : LotteryTrace::STATUS_FINISHED;
            }
        } else {
            //有半状态正在运行的状态情况下，如果有不对lotteryTrace 做任何改变
            $runningFlag = $this->model->getRuningTrace($inputDatas['lottery_trace_lists_id'], $contll->partnerUser->id);
            if ($runningFlag->isEmpty()) {
                $unFinishedTraceListsEloqs = $this->model->getUnfinishedTraceAllWating($inputDatas['lottery_trace_lists_id'], $contll->partnerUser->id);
                //如果是空代表执行结束  否则状态不变
                $lotteryTraceEloq->status = $unFinishedTraceListsEloqs->isEmpty() ? LotteryTrace::STATUS_FINISHED : $lotteryTraceEloq->status;
                $lotteryList = $this->model->getUnfinishedTraceAll($inputDatas['lottery_trace_lists_id'], $contll->partnerUser->id);
                //如果用户总共只有一条追号lottery_trace_list，那么撤销了唯一一条，lottery_trace外部状态应为5 用户撤销
                $lotteryTraceEloq->status = $lotteryList->count() == 1 ? LotteryTrace::STATUS_USER_DROPED : $lotteryTraceEloq->status;
            }
        }
        $lotteryTraceEloq->save();

        if ($lotteryTraceEloq->errors()->messages()) {
            DB::rollback();
            return $contll->msgOut(false, [], '400', $lotteryTraceEloq->errors()->messages());
        }
        DB::commit();

        return $contll->msgOut(true);
    }
}
