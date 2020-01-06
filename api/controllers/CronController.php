<?php

namespace api\controllers;

use Yii;
use yii\helpers\Json;
use yii\db\Query;
/* USE COMMON MODELS */
use common\components\Common;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\helpers\DateTime;
use common\models\Reservations;
use common\models\Devicedetails;

/**
 * MainController implements the CRUD actions for APIs.
 */
class CronController extends \yii\base\Controller
{
   public function actionCompleteReservation(){
      date_default_timezone_set(Yii::$app->params['timezone']);
      $current_date = date("Y-m-d"); 
      $current_time = date("H:i");
  /* 	$snReservationsArr = Reservations::find()->where("status IN ('".Yii::$app->params['reservation_status_value']['booked']."','".Yii::$app->params['reservation_status_value']['seated']."') AND date = '".$current_date."' AND sbooking_end_time = '".$current_time."'")->asArray()->all(); */
  $snReservationsArr = Reservations::find()->where(["status" => [Yii::$app->params['reservation_status_value']['booked'],Yii::$app->params['reservation_status_value']['seated']],"date" => $current_date,"booking_end_time" => $current_time.":00"])->all(); 
   	if(!empty($snReservationsArr)){
   		foreach ($snReservationsArr as $key => $reservation) {
               $reservation->status = Yii::$app->params['reservation_status_value']['completed'];
               $reservation->save(false);
   		}
   	  $ssMessage  = " updated successfully.";
        $amResponse = Common::successResponse( $ssMessage );
        Common::encodeResponseJSON( $amResponse );
   	}else{
         $ssMessage  = "Something went wrong! ";
        $amResponse = Common::errorResponse( $ssMessage );
        Common::encodeResponseJSON( $amResponse );
      }

   }
    public function actionSeatedReservation(){
      date_default_timezone_set(Yii::$app->params['timezone']);
      $current_date = date("Y-m-d"); 
      $current_time = date("H:i");
      
      $snReservationsArr = Reservations::find()->where(["status" => Yii::$app->params['reservation_status_value']['booked'],"date" => $current_date,"booking_start_time" => $current_time.":00"])->all(); 
      if(!empty($snReservationsArr)){
         foreach ($snReservationsArr as $key => $reservation) {
               $reservation->status = Yii::$app->params['reservation_status_value']['seated'];
               $reservation->save(false);
         }
        $ssMessage  = " updated successfully.";
        $amResponse = Common::successResponse( $ssMessage );
        Common::encodeResponseJSON( $amResponse );
      }else{
         $ssMessage  = "Something went wrong!";
        $amResponse = Common::errorResponse( $ssMessage );
        Common::encodeResponseJSON( $amResponse );
      }

   }
}
