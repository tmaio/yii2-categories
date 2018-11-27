<?php
/*
 * This file is part of the YiiModules.com
 *
 * (c) Yii2 modules open source project are hosted on <http://github.com/yiimodules/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 ?>
<div class="clearfix">&nbsp;</div>
<?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('Enter name for your category.'); ?>
<?= $form->field($model, 'slug')->textInput(['maxlength' => true])->hint('Specify url name of your category (eg. home-apliances) '); ?>
<?= $form->field($model, 'description')->textarea(['rows' => 6])->widget(\yii\redactor\widgets\Redactor::className())->hint('Add description to category.'); ?>

<?= $form->field($model, 'is_active')->radioList(array('1'=>'Active','0'=>'Inactive')); ?>