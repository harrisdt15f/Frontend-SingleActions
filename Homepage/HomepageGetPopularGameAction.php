<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Lib\Common\CacheRelated;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use App\Models\Game\ChessCards\FrontendPopularChessCardsList;
use App\Models\Game\EGame\FrontendPopularEGameList;
use Illuminate\Http\JsonResponse;

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
        //########################需要处理放到一个缓存里########################
        // $redisKey = 'popular_game';
        // if (Cache::has($redisKey)) {
        //     $data = Cache::get($redisKey);
        // } else {
        $data = [];
        $tags = 'homepage';
        //热门彩种
        $lotteryRedisKey = 'popular_lotteries';
        $data['lotteries'] = CacheRelated::getTagsCache($tags, $lotteryRedisKey);
        if ($data['lotteries'] === false) {
            $data['lotteries'] = FrontendLotteryRedirectBetList::webPopularLotteriesCache();
        }
        //热门棋牌
        $chessCardsRedisKey = 'chess_cards';
        $data['chess_cards'] = CacheRelated::getTagsCache($tags, $chessCardsRedisKey);
        if ($data['chess_cards'] === false) {
            $data['chess_cards'] = FrontendPopularChessCardsList::select('chess_card_id', 'name', 'icon')
                ->orderBy('sort', 'asc')
                ->get()
                ->toArray();
            CacheRelated::setTagsCache($tags, $chessCardsRedisKey, $data['chess_cards']);
        }
        //热门电子
        $eGameRedisKey = 'e_game';
        $data['e_game'] = CacheRelated::getTagsCache($tags, $eGameRedisKey);
        if ($data['e_game'] === false) {
            $data['e_game'] = FrontendPopularEGameList::select(
                'computer_game_id',
                'name',
                'icon'
            )->orderBy('sort', 'asc')->get()->toArray();
            CacheRelated::setTagsCache($tags, $chessCardsRedisKey, $data['e_game']);
        }
        // Cache::forever($redisKey, $data);
        // }
        return $contll->msgOut(true, $data);
    }
}
