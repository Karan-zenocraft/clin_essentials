<style type="text/css">
img{
height: 43px !important;
width: 43px !important;
}
</style><?php

use common\components\Common;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ' Manage Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index email-format-index">
        <div class="email-format-index">
    <div class="navbar navbar-inner block-header">
        <div class="muted pull-left">Search Here</div>
    </div>
        <div class="block-content collapse in">
        <div class="users-form span12">

     <?=Html::a(Yii::t('app', '<i class="icon-filter icon-white"></i> Filter'), "javascript:void(0);", ['class' => 'btn btn-primary open_search']);?>
     <?php if (!empty($_REQUEST['UsersSearch']) || (!empty($_GET['temp']) && $_GET['temp'] == "clear")) {?>
        <div class="userss-serach common_search">
         <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
<?php } else {?>
    <div class="users-serach common_search">
         <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    <?php }?>
</div>
</div>
</div>
    <div class="navbar navbar-inner block-header">
        <div class="muted pull-left"><?=Html::encode($this->title)?></div>
        <div class="pull-right">
            <?=Html::a(Yii::t('app', '<i class="icon-plus"></i> Add User'), ['create'], ['class' => 'btn btn-success'])?>
            <?=Html::a(Yii::t('app', '<i class="icon-refresh"></i> Reset'), Yii::$app->urlManager->createUrl(['users/index']), ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>
    <div class="block-content">
        <div class="goodtable">

    <?php Pjax::begin();?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => null,
    //'layout' => "<div class='table-scrollable'>{items}</div>\n<div class='col-md-5 col-sm-12'><div class='row1'>{summary}</div></div>\n<div class='col-md-7 col-sm-12'><div class='row'>{pager}</div></div>",
    'layout' => "<div class='table-scrollable'>{items}</div>\n<div class='margin-top-10'>{summary}</div>\n<div class='dataTables_paginate paging_bootstrap pagination'>{pager}</div>",
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
//        'id',
        [
            'attribute' => 'role_id',
            'header' => 'Role',
            //  'filter' => $UserRolesDropdown,
            'filterOptions' => ["style" => "width:17%;"],
            'headerOptions' => ["style" => "width:17%;"],
            'contentOptions' => ["style" => "width:17%;"],
            'value' => function ($data) use ($UserRolesDropdown) {
                return !empty($UserRolesDropdown[$data->role_id]) ? $UserRolesDropdown[$data->role_id] : '';
            },
        ],
        'user_name',
        'email:email',
        'phone',
        [
            'attribute' => 'photo',
            'format' => 'image',
            'value' => function ($data) {
                if (!empty($data->photo)) {
                    $photo = Yii::$app->params['root_url'] . '/' . "uploads/profile_pictures/" . $data->photo;
                } else {
                    $photo = "-";
                }
                return $photo;
            },
        ],
        //'verification_code',
        //'is_code_verified',
        //'password_reset_token:ntext',
        //'auth_token',
        //'badge_count',
        //'login_type',
        [
            'attribute' => 'status',
            'filterOptions' => ["style" => "width:13%;"],
            'headerOptions' => ["style" => "width:13%;"],
            'contentOptions' => ["style" => "width:13%;"],
            'value' => function ($data) {
                return Yii::$app->params['user_status'][$data->status];
            },
        ],
        //'created_at',
        //'updated_at',
        //'restaurant_id',

        [
            'header' => 'Actions',
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ["style" => "width:40%;"],
            'contentOptions' => ["style" => "width:40%;"],
            'template' => '{update}{delete}',
            'buttons' => [
                'update' => function ($url, $model) {
                    $flag = 1;
                    return Common::template_update_button($url, $model, $flag);
                },
                'delete' => function ($url, $model) {
                    $flag = 1;
                    $confirmmessage = "Are you sure you want to delete this user?";
                    return Common::template_delete_button($url, $model, $confirmmessage, $flag);
                },

            ],
        ],
    ],
]);?>

    <?php Pjax::end();?>
        </div>
    </div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $('.users-serach').hide();
        $('.open_search').click(function(){
            $('.users-serach').toggle();
        });
    });

</script>