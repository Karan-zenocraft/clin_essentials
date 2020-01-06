<?php

namespace frontend\components;

use common\models\UserRules;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class FrontCoreController extends Controller
{

    public function init()
    {
        if (!empty(Yii::$app->user->id)) {
            if (Yii::$app->user->identity->status != '1') {
                Yii::$app->user->logout();
                // $this->redirect(Yii::$app->urlManager(['site/index']));
            }
        }
        return true;
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        $ssControllerName = Yii::$app->controller->id . "Controller";

        $amAccessRules = array(
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [/*'login',*/'index', 'error', 'request-password-reset', 'signup', 'reset-password', 'email-verify', 'restaurants', 'restaurant-details', 'contact-us', 'gii'],
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
