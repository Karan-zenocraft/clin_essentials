<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags()?>
    <title><?=Html::encode($this->title)?></title>
    <?php $this->head()?>
</head>
<body>
<?php $this->beginBody()?>

<div class="wrap">
    <?php
$img = '<img src="http://zenocraft.com/clin_essentials/image/ClinEApp-logo.png" alt="ClinEssentials" width="100px">';
NavBar::begin([
    'brandLabel' => $img,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top',
    ],
]);
$menuItems = [
    /*  ['label' => 'Home', 'url' => ['/site/index']],
['label' => 'About', 'url' => ['/site/about']],
['label' => 'Contact', 'url' => ['/site/contact']],*/
];
/*if (Yii::$app->user->isGuest) {
$menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
$menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
} else {
$menuItems[] = '<li>'
. Html::beginForm(['/site/logout'], 'post')
. Html::submitButton(
'Logout (' . Yii::$app->user->identity->username . ')',
['class' => 'btn btn-link logout']
)
. Html::endForm()
. '</li>';
}*/
echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => $menuItems,
]);
NavBar::end();
?>

    <div class="container">

        <?=Alert::widget()?>
        <?=$content?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?=Html::encode(Yii::$app->name)?> <?=date('Y')?></p>

        <p class="pull-right"><?=Yii::powered()?></p>
    </div>
</footer>
<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.8.1/firebase-app.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/7.8.1/firebase-analytics.js"></script>

<script>
  // Your web app's Firebase configuration
  var firebaseConfig = {
    apiKey: "AIzaSyCaVpkmy1qvaaCrLhiQJlzQd77AT-K1OY8",
    authDomain: "ecommerce-acd47.firebaseapp.com",
    databaseURL: "https://ecommerce-acd47.firebaseio.com",
    projectId: "ecommerce-acd47",
    storageBucket: "ecommerce-acd47.appspot.com",
    messagingSenderId: "681968475052",
    appId: "1:681968475052:web:03c42f8711b5f9e8b27809",
    measurementId: "G-0KWQNS565B"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  firebase.analytics();
</script>
<?php $this->endBody()?>
</body>
</html>
<?php $this->endPage()?>
