<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Change Password');
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- block -->
   <div class="change_password_form">
       <div class="navbar navbar-inner block-header">
           <div class="muted pull-left"><?= Html::encode($this->title) ?></div>
       </div>
       <div class="block-content collapse in">
           <div class="span12">
                   <!-- BEGIN FORM-->
                   <?php $form = ActiveForm::begin(['enableAjaxValidation'=>true, 'options'=> ['class'=> 'form-horizontal']]); ?>
                        <?= $form->field($model, 'currentPassword', ['template' => '<div class="control-group">{label}<div class="controls">{input}<div class="help-block"></div></div></div>'])->passwordInput(['maxlength' => 255, 'class'=> 'span6 m-wrap']) ?>
                        <?= $form->field($model, 'newPassword', ['template' => '<div class="control-group">{label}<div class="controls">{input}<div class="help-block"></div></div></div>'])->passwordInput(['maxlength' => 255, 'class'=> 'span6 m-wrap']) ?>
                        <?= $form->field($model, 'retypePassword', ['template' => '<div class="control-group">{label}<div class="controls">{input}<div class="help-block"></div></div></div>'])->passwordInput(['rows' => 6, 'class'=> 'span6 m-wrap']) ?>
                        <?php /* $form->field($model, 'status', ['template' => '<div class="control-group">{label}<div class="controls">{input}</div></div>'])->textInput() ?>
                        <?= $form->field($model, 'created_at')->textInput() ?>
                        <?= $form->field($model, 'updated_at')->textInput() */ ?>
                        <div class="form-actions">
                            <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->urlManager->createUrl(['site/index']), ['class' => 'btn default']) ?>
                            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
                        </div>
                   <!-- END FORM-->
                   <?php ActiveForm::end(); ?>
           </div>
       </div>
   </div>
   <!-- /block -->
