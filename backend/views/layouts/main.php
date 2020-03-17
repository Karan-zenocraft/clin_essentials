<?php

use backend\assets\AppAsset;
use common\assets\CommonAppAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */
AppAsset::register($this);
CommonAppAsset::register($this);
?>
<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
    <head>
        <link rel="shortcut icon" type="image/png" href=""/>
        <meta charset="<?=Yii::$app->charset?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?=Html::csrfMetaTags()?>
        <title><?=Yii::getAlias('@site_title');?>&nbsp; - &nbsp;<?=Html::encode($this->title)?></title>
        <?php $this->head()?>
    </head>
    <body>
        <?php $this->beginBody()?>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid logo-container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand logo" href="<?=\Yii::$app->urlManager->createUrl(['site/index']);?>">
                       <img src="<?php echo Yii::$app->params['root_url'] . '/common/web/img/logo.png' ?>" style="max-width: 100px!important;height: auto!important;width: 100px!important;" alt="" class="img-fluid">

                    </a>
                    <div class="nav-collapse">
                        <ul class="nav pull-right">
                            <li class="dropdown">
                                <a href="javascript:void(0);" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user"></i> <?=Yii::$app->user->identity->user_name;?>  <i class="caret"></i>

                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<?=\Yii::$app->urlManager->createUrl(['site/change-password']);?>">Change password</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a tabindex="-1" href="<?=\Yii::$app->urlManager->createUrl(['site/logout']);?>">Logout</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row-fluid">
                <?php include_once 'sidebar.php';?>
                <!--/span-->
                <div class="span9 rightsidebar" id="content">
                    <div class="row-fluid">
                        <div class="flash_message"> <?php include_once 'flash_message.php';?></div>
                        <div class="navbar">
                            <div class="navbar-inner">
                                <ul class="breadcrumb">
                                    <?=
Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    'itemTemplate' => '<li>{link} <i class="fa fa-angle-right"></i> </li> <span class="divider">/</span> ',
    'homeLink' => ['label' => 'Dashboard', 'template' => '<li><i class="fa fa-home"></i>{link}</li> <span class="divider">/</span> ', 'url' => Yii::$app->request->baseUrl . '/site/index'],
])
?>
                                    <!--i class="icon-chevron-left hide-sidebar"><a href='#' title="Hide Sidebar" rel='tooltip'>&nbsp;</a></i>
                                    <i class="icon-chevron-right show-sidebar" style="display:none;"><a href='#' title="Show Sidebar" rel='tooltip'>&nbsp;</a></i>
                                    <li>
                                        <a href="#">Dashboard</a> <span class="divider">/</span>
                                    </li>
                                    <li>
                                        <a href="#">Settings</a> <span class="divider">/</span>
                                    </li>
                                    <li class="active">Tools</li-->
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="block"><?=$content?></div>
                    </div>
                </div>
            </div>
            <hr>
            
        </div>
        <footer class="footer">
                <div class="container">
                    <p class="pull-left">&copy; <?=Yii::getAlias('@site_footer') . " " . date('Y');?></p>
                </div>
            </footer>
        <!--div class="wrap">
        <?php
/* NavBar::begin([
'brandLabel' => 'My Company',
'brandUrl' => Yii::$app->homeUrl,
'options' => [
'class' => 'navbar-inverse navbar-fixed-top',
],
]);
$menuItems = [
['label' => 'Home', 'url' => ['/site/index']],
];
if (Yii::$app->user->isGuest) {
$menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
} else {
$menuItems[] = [
'label' => 'Logout (' . Yii::$app->user->identity->first_name . ')',
'url' => ['/site/logout'],
'linkOptions' => ['data-method' => 'post']
];
}
echo Nav::widget([
'options' => ['class' => 'navbar-nav navbar-right'],
'items' => $menuItems,
]);
NavBar::end(); */
?>

            <div class="container">
        <?=
Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
])
?>

            </div>
        </div>

        <footer class="footer">
            <div class="container">
            <p class="pull-left">&copy; My Company <?=date('Y')?></p>
            <p class="pull-right"><?=Yii::powered()?></p>
            </div>
        </footer-->

        <?php $this->endBody()?>
    </body>
</html>
<?php $this->endPage()?>
