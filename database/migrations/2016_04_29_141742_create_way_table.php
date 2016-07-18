<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// 常數
use App\Italkjob\Core\Constant\DatabaseConstant;

/**
 *
 * 玩法表
 *
**/
class CreateWayTable extends Migration {

    /**
     *
     * @return void
     */
    public function up()
    {
        //不存在是在创建
        if(! Schema::hasTable('ways')) {

            Schema::create('ways', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name',60)->nullable()->comment('玩法名称');
                $table->string('short_name',20)->nullable()->comment('玩法名称缩写');
                $table->text('number_length')->comment('号码长度');//如前三就是3个号码

                $table->boolean('has_sequence')->default('1')->comment('是否支持有序');//1有序2无序

                $table->text('way_rule')->default('')->comment('玩法规则说明');
                $table->text('description')->default('')->comment('中奖说明');

                $table->tinyInteger('status')->default(1)->comment('状态');//1:销售中0:维护中2:热销3:新上线4:24小时彩票
                
                $table->timestamps();
            });

            DB::update("ALTER TABLE ways AUTO_INCREMENT = 1;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 移除 ways 資料表
         if (Schema::hasTable('ways')) {
            Schema::drop('ways');
        }
    }

}
