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
<?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'meta_description')->textArea(['rows' => '4']) ?>

