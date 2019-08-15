<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use App\Models\Game\ChessCards\FrontendPopularChessCardsList;
use App\Models\Game\EGame\FrontendPopularEGameList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepageGetPopularGameAction
{
    protected $model;

    /**
     * @param  FrontendAllocatedModel  $frontendAllocatedModel
     */
    public function __construct(FrontendAllocatedModel $frontendAllocatedModel)
    {
        $this->model = $frontendAllocatedModel;
    }

    /**
     * 获取热门游戏列表
     * @param FrontendApiMainController $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $data = [];
        //########################需要处理放到一个缓存里########################
        // $redisKey = 'popular_game';
        // if (Cache::has($redisKey)) {
        //     $data = Cache::get($redisKey);
        // } else {
        //热门彩种
        $lotteryRedisKey = 'popular_lotteries';
        if (Cache::has($lotteryRedisKey)) {
            $data['lotteries'] = Cache::get($lotteryRedisKey);
        } else {
            $data['lotteries'] = FrontendLotteryRedirectBetList::webPopularLotteriesCache();
        }
        //热门棋牌
        $chessCardsRedisKey = 'chess_cards';
        if (Cache::has($chessCardsRedisKey)) {
            $data['chess_cards'] = Cache::get($chessCardsRedisKey);
        } else {
            $data['chess_cards'] = FrontendPopularChessCardsList::select(
                'chess_card_id',
                'name',
                'icon'
            )->orderBy('sort', 'asc')->get()->toArray();
            Cache::forever($chessCardsRedisKey, $data['chess_cards']);
        }
        //热门电子
        $eGameRedisKey = 'e_game';
        if (Cache::has($eGameRedisKey)) {
            $data['e_game'] = Cache::get($eGameRedisKey);
        } else {
            $data['e_game'] = FrontendPopularEGameList::select(
                'computer_game_id',
                'name',
                'icon'
            )->orderBy('sort', 'asc')->get()->toArray();
        }
        // Cache::forever($redisKey, $data);
        // }
        return $contll->msgOut(true, $data);
    }
}
