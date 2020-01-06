<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <!--h1><?= Html::encode($this->title) ?></h1>
    <p>Please fill out the following fields to login:</p-->
     
    <?php $form = ActiveForm::begin(['id' => 'login-form', 'options'=> ['class' => 'form-signin']]); ?>
        <h3 class="form-signin-heading">Please sign in</h3>
        <?= $form->field($model, 'username')->textInput(['placeholder'=> 'Email', 'class'=> 'input-block-level']); ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder'=> 'Password', 'class'=> 'input-block-level']) ?>
        <?= $form->field($model, 'rememberMe')->checkbox() ?>
        <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        <hr>
        Forgot your password ? <br/>

        no worries, click <a id="forget-password" href="javascript:void(0);" onclick="javascript:jQuery('#login-form').hide();jQuery('#forgot-password-form').show()"> here </a> to reset your password.

    <?php ActiveForm::end(); ?>
    <!-- BEGIN FORGOT PASSWORD FORM -->
    <?php $form = ActiveForm::begin(['id' => 'forgot-password-form', 'enableAjaxValidation'=>true , 'options'=> ['class' => 'form-signin', 'style'=> 'display: none;']]); ?>
            <h3 class="form-signin-heading">Forget Password ?</h3>
            <p>
                     Enter your e-mail address below to reset your password.
            </p>
                <?= $form->field($forgotPasswordModel, 'email', ['labelOptions'=> ['label'=> false]])->textInput(['placeholder'=> 'Email','class'=> 'input-block-level']) ?>
            <div class="form-actions">
                    <?= Html::Button('<i class="m-icon-swapleft"></i> Back', ['class' => 'btn', 'name' => 'Back-button','id'=>'back-btn','type'=>'button', 'onclick'=> 'javascript: $("#forgot-password-form").hide();$("#login-form").show();']) ?>
                    <?= Html::submitButton('Submit <i class="m-icon-swapright m-icon-white"></i>', ['class' => 'btn btn-primary pull-right', 'name' => 'forgot-button']) ?>
            </div>
    <?php ActiveForm::end(); ?>
    <!-- END FORGOT PASSWORD FORM -->    
</div>
