<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// 常數
use App\Italkjob\Core\Constant\DatabaseConstant;

/**
 *
 * 奖期表
 *
**/
class CreateIssueTable extends Migration {

    /**
     *
     * @return void
     */
    public function up()
    {
        //不存在是在创建
        if(!Schema::hasTable('issue')) {

            Schema::create('issue', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('lottery_id')->nullable()->comment('彩票名称id');
                $table->string('issue_num',20)->nullable()->comment('奖期');
                $table->mediumInteger('winning_number')->comment('开奖号码');
                $table->integer('record_author')->comment('录号者');

                $table->tinyInteger('status')->default(0)->comment('状态');//0销售中1录号中2录号完成3录号失败

                $table->timestamp('open_time')->comment('开始时间');
                $table->timestamp('stop_time')->comment('截至时间');
                $table->timestamp('official_time')->comment('官方开奖时间');
                $table->timestamp('record_time')->comment('录号时间');
                $table->timestamps();
            });

            DB::update("ALTER TABLE issue AUTO_INCREMENT = 1;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 移除 issue 資料表
         if (Schema::hasTable('issue')) {
            Schema::drop('issue');
        }
    }

}
