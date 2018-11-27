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
<div class="row">
	<div class="col-xs-12">
		<?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
			<?php if (in_array($type, ['success', 'danger', 'warning', 'info'])): ?>
				<div class="alert alert-<?= $type ?>">
					<?= $message ?>
				</div>
			<?php endif ?>
		<?php endforeach ?>
	</div>
</div>