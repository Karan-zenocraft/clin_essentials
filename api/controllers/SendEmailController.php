<?php

namespace api\controllers;

/* USE COMMON MODELS */
use common\components\Common;
use common\models\Users;
use kartik\mpdf\Pdf;
use Yii;
use yii\web\Controller;

/**
 * MainController implements the CRUD actions for APIs.
 */
class SendEmailController extends \yii\base\Controller
{
    public function actionSendPdf()
    {
        $amData = Common::checkRequestType();
        $amResponse = array();
        $ssMessage = '';
        $amRequiredParams = array('user_id');
        $amParamsResult = Common::checkRequestParameterKey($amData['request_param'], $amRequiredParams);
        // If any getting error in request paramter then set error message.
        if (!empty($amParamsResult['error'])) {
            $amResponse = Common::errorResponse($amParamsResult['error']);
            Common::encodeResponseJSON($amResponse);
        }
        $requestParam = $amData['request_param'];
        //  p($requestParam);
        $notes = $requestParam['notes'];

        /*  $amRequiredParamsNotes = array('id', 'color_code', 'title', 'font_name', 'font_size', 'patient_id', 'description');

        foreach ($notes as $key => $note) {
        $amParamsResultNotes = Common::checkRequestParameterKey($note, $amRequiredParamsNotes);

        if (!empty($amParamsResultNotes['error'])) {
        $amResponse = Common::errorResponse($amParamsResultNotes['error']);
        Common::encodeResponseJSON($amResponse);
        }
        }*/
        // Check User Status
        Common::matchUserStatus($requestParam['user_id']);
        //VERIFY AUTH TOKEN
        $authToken = Common::get_header('auth_token');
        Common::checkAuthentication($authToken);

        $userModel = Users::findOne(['id' => $requestParam['user_id']]);
        if (!empty($userModel)) {
            $fromEmail = $userModel->email;
            foreach ($notes as $key => $note) {
                # code...
                $noteArr = json_decode($note);

                $html = '<html xmlns="http://www.w3.org/1999/xhtml">
                  <head style="background-color:"' . $noteArr->color_code . '>
                      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                      <h1 style="margin:0,padding:8">' . $noteArr->title . '</h1>
                  </head>
                  <body style="margin:0,padding:0">
                  <p>' . $noteArr->description . '</p>
                  </body>
                  </html>';
//            $content = $this->renderPartial('_reportView');

                // setup kartik\mpdf\Pdf component
                $pdf = new Pdf([
                    // set to use core fonts only
                    'mode' => Pdf::MODE_CORE,
                    // A4 paper format
                    'format' => Pdf::FORMAT_A4,
                    // portrait orientation
                    'orientation' => Pdf::ORIENT_PORTRAIT,
                    // stream to browser inline
                    'destination' => Pdf::DEST_FILE,
                    // your html content input
                    'content' => $html,
                    // format content from your own css file if needed or use the
                    // enhanced bootstrap css built by Krajee for mPDF formatting
                    //  'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
                    // any css to be embedded if required
                    'cssInline' => '.kv-heading-1{font-size:18px}',
                    // set mPDF properties on the fly
                    'options' => ['title' => $noteArr->title],
                    // call mPDF methods on the fly
                    'methods' => [
                        'SetHeader' => ['Clin Essentials Note'],
                        'SetFooter' => ['{PAGENO}'],
                    ],
                ]);
                $pdf->content = $html;
                $file_name = "note_" . rand(7, 100) . "_" . time() . ".pdf";
                $pdf->filename = "../../uploads/pdf_files/" . $file_name;
                return $pdf->render();
                /*       $emailformatemodel = EmailFormat::findOne(["title" => 'user_registration', "status" => '1']);
            if ($emailformatemodel) {

            //create template file
            $AreplaceString = array('{password}' => $requestParam['password'], '{username}' => $model->user_name, '{email}' => $model->email, '{email_verify_link}' => $email_verify_link);

            $body = Common::MailTemplate($AreplaceString, $emailformatemodel->body);
            $ssSubject = $emailformatemodel->subject;
            //send email for new generated password
            $ssResponse = Common::sendMail($model->email, Yii::$app->params['adminEmail'], $ssSubject, $body);

            }*/
            }
            $ssMessage = 'Successfully added PDF';
            $amResponse = Common::errorResponse($ssMessage);
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
}
