<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class HomepageRankingAction
{
    protected $model;

    /**
     * @param  Project  $projectModel
     */
    public function __construct(Project $projectModel)
    {
        $this->model = $projectModel;
    }

    /**
     * 首页中奖排行榜
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        // $rankingEloq = FrontendAllocatedModel::select('status', 'show_num')->where('en_name', 'winning.ranking')->first();
        // if ($rankingEloq === null || $rankingEloq->status !== 1) {
        //     return $contll->msgOut(false, [], '100400');
        // }
        // if (Cache::has('homepage_ranking')) {
        //     $rankingData = Cache::get('homepage_ranking');
        // } else {
        //     $rankingData = $this->model::select('username', 'lottery_sign', 'bonus')->where('bonus', '>', '0')->orderBy('bonus', 'DESC')->limit($rankingEloq->show_num)->get()->toArray();
        //     $expiresAt = Carbon::now()->addHours(1); //缓存1小时
        //     Cache::put('homepage_ranking', $rankingData, $expiresAt);
        // }

        //先使用假数据展示
        //###########################################
        $rankingData = [
            ['username' => 'pitiless', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/1.jpg', 'lottery_sign' => '重庆时时彩', 'bonus' => '1980000.0000'],
            ['username' => 'memorial', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/2.jpg', 'lottery_sign' => '中兴11选5', 'bonus' => '1970000.0000'],
            ['username' => 'hickey', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/3.jpg', 'lottery_sign' => '新疆时时彩', 'bonus' => '1870000.0000'],
            ['username' => 'miracle', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/4.jpg', 'lottery_sign' => '黑龙江时时彩', 'bonus' => '1866666.0000'],
            ['username' => 'belief', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/5.jpg', 'lottery_sign' => '香港六合彩', 'bonus' => '1823488.0000'],
            ['username' => 'unique', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/6.jpg', 'lottery_sign' => '中兴PK10', 'bonus' => '1796000.0000'],
            ['username' => 'allure Love', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/7.jpg', 'lottery_sign' => '北京PK10', 'bonus' => '1790000.0000'],
            ['username' => 'nightmare', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/8.jpg', 'lottery_sign' => '上海时时乐', 'bonus' => '1600000.0000'],
            ['username' => 'suffocate', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/9.jpg', 'lottery_sign' => '福彩3D', 'bonus' => '1480000.0000'],
            ['username' => 'ambiguous', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/10.jpg', 'lottery_sign' => '安徽快3', 'bonus' => '1366666.0000'],
            ['username' => 'Waitz', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/11.jpg', 'lottery_sign' => '中兴11选5', 'bonus' => '1200000.0000'],
            ['username' => 'each other', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/12.jpg', 'lottery_sign' => '广东11选5', 'bonus' => '1119000.0000'],
            ['username' => 'poppies', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/13.jpg', 'lottery_sign' => '重庆时时彩', 'bonus' => '1080808.0000'],
            ['username' => 'beta', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/14.jpg', 'lottery_sign' => '中兴1分彩', 'bonus' => '980000.0000'],
            ['username' => 'bInsomnia', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/15.jpg', 'lottery_sign' => '新疆时时彩', 'bonus' => '978000.0000'],
            ['username' => 'pluto', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/16.jpg', 'lottery_sign' => '重庆时时彩', 'bonus' => '978000.0000'],
            ['username' => 'dot', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/17.jpg', 'lottery_sign' => '黑龙江时时彩', 'bonus' => '960000.0000'],
            ['username' => 'silly', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/18.jpg', 'lottery_sign' => '河南快3', 'bonus' => '958000.0000'],
            ['username' => 'liquor', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/19.jpg', 'lottery_sign' => '福彩3D', 'bonus' => '958000.0000'],
            ['username' => 'wex159357', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/20.jpg', 'lottery_sign' => '北京PK10', 'bonus' => '956000.0000'],
            ['username' => 'Eddie', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/21.jpg', 'lottery_sign' => '上海时时乐', 'bonus' => '950000.0000'],
            ['username' => 'Fernanda', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/22.jpg', 'lottery_sign' => '香港六合彩', 'bonus' => '938880.0000'],
            ['username' => 'Hamilton', 'user_icon' => '/uploaded_files/aa_1/user_aa_1/23.jpg', 'lottery_sign' => '中兴11选5', 'bonus' => '916600.0000'],
        ];
        return $contll->msgOut(true, $rankingData);
    }
}
