<?php
namespace backend\components;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\UserRules;

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class AdminCoreController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {

        $ssControllerName = Yii::$app->controller->id . "Controller";

        $amAccessRules = array(
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'forgotPassword', 'gii','carry-forward-leaves-quota','yearly-carry-forward-leaves-for-user','monthly-carry-forward-leaves-for-user,resource-avalaible-notification'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'logout' => ['post'],
                ],
            ],
        );
        if (!\Yii::$app->user->isGuest) {
            $snRoleId = Yii::$app->user->identity->role_id;
            $omAuthActions = UserRules::findOne(['privileges_controller' => $ssControllerName, 'role_id' => $snRoleId]);

            if ($omAuthActions) {
                $amAccessRules['access']['rules'][] = array(
                    'actions' => explode(',', $omAuthActions->privileges_actions),
                    'allow' => true,
                    'roles' => ['@'],
                );
            }
        }
        //p($amAccessRules);
        return $amAccessRules;
    }
}