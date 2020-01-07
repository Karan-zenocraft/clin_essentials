<?php

use common\models\UserRoles;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'options' => [
        'data-pjax' => 1,
    ],
]);?>
<div class="row">
    <div class="span3 style_input_width">
<?php $UserRolesDropdown = ArrayHelper::map(array("" => "") + UserRoles::find()->where("id !=" . Yii::$app->params['super_admin_role_id'] . " AND id !=" . Yii::$app->params['administrator_role_id'])->asArray()->all(), 'id', 'role_name');?>
    <?php echo $form->field($model, 'role_id')->dropDownList($UserRolesDropdown); ?>
</div>
    <div class="span3 style_input_width">

    <?=$form->field($model, 'user_name')?>
</div>
</div>
<div class="row">
<div class="span3 style_input_width">
    <?=$form->field($model, 'email')?>
</div>
<div class="span3 style_input_width">
    <?php echo $form->field($model, 'phone') ?>
</div>
</div>
<div class="row">
<div class="span3 style_input_width">
    <div class="span3 style_input_width"><?=$form->field($model, 'status')->dropDownList(Yii::$app->params['user_status']);?></div>
</div>
</div>
    <?php // echo $form->field($model, 'photo') ?>

    <?php // echo $form->field($model, 'verification_code') ?>

    <?php // echo $form->field($model, 'is_code_verified') ?>

    <?php // echo $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'auth_token') ?>

    <?php // echo $form->field($model, 'badge_count') ?>

    <?php // echo $form->field($model, 'login_type') ?>


    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'restaurant_id') ?>

    <div class="form-group">
        <?=Html::submitButton('Search', ['class' => 'btn btn-primary'])?>
        <?=Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
