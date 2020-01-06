<?php

namespace api\components;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\components\Common;
use yii\helpers\Json;
use \common\components\CommonController;
use \common\models\AppLicences;


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ApiCoreController extends CommonController
{

    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     */
    public $enableCsrfValidation     = false;
    public $amData                   = [];
    public $amRequiredParams         = [];
    public $amUnAuthenticatedActions = [];
    public $omUser;

    public function beforeAction($omAction)
    {

        $this->amUnAuthenticatedActions = [
            "users/sign-up",
            "users/login",
            "users/verify-referral-code",
            "users/forgot-password",
            "users/change-password",
            "users/verify-email",
        ];
        $ssControllerId                 = $omAction->controller->id;
        $ssActionId                     = $omAction->id;
        $snControlllerAndAction         = "{$ssControllerId}/{$ssActionId}";

        //CHECK IF VALID REQUEST PARAMS
        $this->amData = Common::getApiHeader();

        $this->amRequiredParams = Yii::$app->params["api_required_params"];

        if (!in_array($snControlllerAndAction, $this->amUnAuthenticatedActions))
        {
            $this->amRequiredParams[$ssControllerId][$ssActionId][] = "user_id";
        }
        Common::checkRequiredParams($this->amData, $this->amRequiredParams[$ssControllerId][$ssActionId]);
        if (!empty($this->amData["user_id"]))
        {
            $omUser = \common\models\Users::findOne(intval($this->amData["user_id"]));
            if (empty($omUser))
            {
                Common::encodeResponseJSON(Common::errorResponse("No user exists with this id"));
            }
            else
            {
                $this->omUser = $omUser;
            }
        }

        return parent::beforeAction($omAction);
    }

}
