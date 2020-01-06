<?php
use backend\assets\AppAsset;
use common\assets\CommonAppAsset;
use yii\helpers\Html;

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
    <title><?=Html::encode($this->title)?></title>
    <?php $this->head()?>
</head>
<body id="login">
    <?php $this->beginBody()?>
        <div class="container">
         <div class="flash_message"> <?php include_once 'flash_message.php';?></div>
            <?=$content?>
        </div>
    <?php $this->endBody()?>
</body>
</html>
<?php $this->endPage()?>
