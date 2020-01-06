<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;

//use Yii;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CommonAppAsset extends AssetBundle
{

    //public $basePath = '/yii2_advanced/common/web';
    //public $baseUrl = '/yii2_advanced/common/web';
    public $basePath;
    public $baseUrl;
    public $css = [
        'http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all',
        'plugins/easypiechart/jquery.easy-pie-chart.css',
        'css/plugins.css',
        'css/style.css',
        'css/error.css',
        'css/colorbox.css',
    ];
    public $js = [
        'plugins/easypiechart/jquery.easy-pie-chart.js',
        'js/jquery.validate.min.js',
        'js/common.js',
        'js/jquery.colorbox.js',
    ];
    public $depends = [
        //'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        $this->basePath = '@common_base';
        $this->baseUrl = '@common_base/web';
        //$this->basePath = '@app/common/';
        //$this->baseUrl = '@app/common/web';
    }

    /* public $jsOptions = array(
'position' => \yii\web\View::POS_READY
); */
}
