<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// 常數
use App\Italkjob\Core\Constant\DatabaseConstant;

/**
 *
 * 彩票系列表
 *
**/
class CreateSeriesTable extends Migration {

    /**
     *
     * @return void
     */
    public function up()
    {
        //不存在是在创建
        if(!Schema::hasTable('series')) {

            Schema::create('series', function (Blueprint $table) {
                $table->increments('id');
                $table->string('en_name',40)->nullable()->comment('彩系英文名字');
                $table->string('cn_name',40)->nullable()->comment('彩系中文名字');
                $table->tinyInteger('series_type')->default(0)->comment('彩系类型');//0数字1乐透2体育3基诺
                $table->tinyInteger('status')->default(1)->comment('状态');//1销售中0维护中

                $table->timestamps();
            });

            DB::update("ALTER TABLE series AUTO_INCREMENT = 1;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 移除 series 資料表
         if (Schema::hasTable('series')) {
            Schema::drop('series');
        }
    }

}
