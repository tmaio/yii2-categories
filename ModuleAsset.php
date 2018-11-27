<?php
/*
 * This file is part of the YiiModules.com
 *
 * (c) Yii2 modules open source project are hosted on <http://github.com/yiimodules/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace yiimodules\categories;
use yii\web\AssetBundle;
class ModuleAsset extends AssetBundle
{
    // the alias to your assets folder in your file system
    public $sourcePath = '@categories-assets';
    // finally your files.. 
    public $css = [
		'jstree/dist/themes/default/style.min.css',
    ];
    public $js = [
      'jstree/dist/jstree.min.js',
      'jquery.slugify.js',
    ];
    // that are the dependecies, for makeing your Asset bundle work with Yii2 framework
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}

?>