<?php

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-create email-format-create">

    <?=$this->render('_form', [
    'model' => $model,
    'UserRolesDropdown' => $UserRolesDropdown,

])?>

</div>
