<?php

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = 'Update Users: ' . $model->user_name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="users-update">

    <?=$this->render('_form', [
    'model' => $model,
    'UserRolesDropdown' => $UserRolesDropdown,

])?>

</div>
