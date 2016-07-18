<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// 常數
use App\Italkjob\Core\Constant\DatabaseConstant;

/**
 *
 * 彩票种类表
 *
**/
class CreateLotteryTable extends Migration {

    /**
     *
     * @return void
     */
    public function up()
    {
        //不存在是在创建
        if(!Schema::hasTable('lottery')) {

            Schema::create('lottery', function (Blueprint $table) {
                $table->increments('id');
                $table->string('en_name',20)->unique()->nullable()->comment('英文名');
                $table->string('cn_name', 20)->default('')->comment('中文名');
                $table->tinyInteger('series_id')->comment('彩票系列id');//0:ssc;1:11选5;2;ks
                $table->tinyInteger('series_type')->default(0)->comment('彩系类型');//0数字1乐透2体育3基诺
                $table->tinyInteger('lotto_type')->default(0)->comment('乐透类型');//0:单区;1:双区
                $table->integer('sale_interval')->default(10*60)->comment('销售时间间隔');// 默认是10分钟
                $table->mediumInteger('daily_issue_count')->comment('单日奖期数');// 默认是10分钟
                $table->mediumInteger('trace_issue_count')->comment('追号奖期数');// 默认是10分钟
                $table->mediumInteger('buy_length')->comment('开奖号码长度');// 默认是10分钟
                $table->mediumInteger('valid_nums')->comment('彩票支持的合法数字');// 默认是10分钟

                $table->boolean('is_self')->default(0)->comment('是否自主');  // 是否自主彩种
                $table->boolean('is_instant')->default(0)->comment('是否即时彩');//秒秒彩是即时彩
                $table->boolean('high_frequency')->default(1)->comment('是否高频彩');
                $table->boolean('sort_winning_number')->default(0)->comment('中奖号码排序');
                $table->boolean('issue_over_midnight')->default(0)->comment('奖期跨越零点');

                $table->string('days')->default('')->comment('开奖日');//'1,2,3,4,5,6,7
                $table->tinyInteger('status')->nullable(3)->comment('彩票状态');        //0关闭1测试中2仅作为测试3正常销售4永远关闭
                $table->mediumInteger('sequence')->default(0)->comment('排序');
                $table->mediumInteger('max_bet_group')->comment('最大奖金组');

                $table->timestamp('start_sail_time');
                $table->timestamps('end_sail_time');
                $table->timestamps();
            });

            DB::update("ALTER TABLE lottery AUTO_INCREMENT = 1;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 移除 lottery 資料表
         if (Schema::hasTable('lottery')) {
            Schema::drop('lottery');
        }
    }

}
