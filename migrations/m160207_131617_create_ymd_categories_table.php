<?php
/*
 * This file is part of the YiiModules.com
 *
 * (c) Yii2 modules open source project are hosted on <http://github.com/yiimodules/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
use yii\db\Migration;
use yii\db\Schema;

class m160207_131617_create_ymd_categories_table extends Migration
{
    public function up()
    {
        $this->createTable('ymd_categories', [
            'id'         => Schema::TYPE_PK,
            'parent_id'    => Schema::TYPE_INTEGER.' unsigned NULL',
            'name'   => Schema::TYPE_STRING . '(45) NOT NULL',
            'slug'  => Schema::TYPE_STRING . '(45) NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'image' => Schema::TYPE_STRING . '(80) NULL',
            'meta_title' => Schema::TYPE_STRING . '(80) NULL',
            'meta_keywords' => Schema::TYPE_STRING . '(150) NULL',
            'meta_description' => Schema::TYPE_STRING . '(255) NULL',
            'position' => Schema::TYPE_SMALLINT . '(10) unsigned NULL',
            'is_active' => 'tinyint(1) NULL',
            'created_at' => Schema::TYPE_INTEGER . '(11) NULL',
            'updated_at' => Schema::TYPE_INTEGER . '(11) NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('ymd_categories');
    }
}
