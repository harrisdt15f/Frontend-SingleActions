<?php

namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class LotteriesCancelBetAction
{
    /**
     * 投注撤单
     * @param  FrontendApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        $projectEloq = Project::find($inputDatas['id']);
        if ($projectEloq->user_id !== $contll->partnerUser->id) {
            return $contll->msgOut(false, [], '100314');
        }
        $projectEloq->status = Project::STATUS_DROPED;
        //#########################帐变
        $projectEloq->save();
        if ($projectEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $projectEloq->errors()->messages());
        }
        return $contll->msgOut(true);
    }
}
