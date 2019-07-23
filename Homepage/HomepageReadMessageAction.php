<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Notice\FrontendMessageNotice;
use Illuminate\Http\JsonResponse;

class HomepageReadMessageAction
{
    /**
     * 公告|站内信 已读处理
     * @param  FrontendApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        $messageELoq = FrontendMessageNotice::find($inputDatas['id']);
        if ($messageELoq->receive_user_id !== $contll->partnerUser->id) {
            return $contll->msgOut(false, [], '100401');
        }
        $messageELoq->status = FrontendMessageNotice::READ;
        $messageELoq->save();
        if ($messageELoq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $messageELoq->errors()->messages());
        }
        return $contll->msgOut(true);
    }
}
