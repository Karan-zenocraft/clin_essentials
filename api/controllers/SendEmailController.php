<?php

namespace api\controllers;

/* USE COMMON MODELS */
use common\components\Common;
use common\models\EmailFormat;
use common\models\SentNotes;
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
        //$notes = json_decode(json_encode($requestParam['notes']), true);
        $notes = $requestParam['notes'];

        $amRequiredParamsNotes = array('note_id', 'color_code', 'title', 'font_name', 'font_size', 'patient_id', 'patient_email', 'description');

        foreach ($notes as $key => $note) {
            $amParamsResultNotes = Common::checkRequestParameterKey($note, $amRequiredParamsNotes);

            if (!empty($amParamsResultNotes['error'])) {
                $amResponse = Common::errorResponse($amParamsResultNotes['error']);
                Common::encodeResponseJSON($amResponse);
            }
        }
        // Check User Status
        Common::matchUserStatus($requestParam['user_id']);
        //VERIFY AUTH TOKEN
        $authToken = Common::get_header('auth_token');
        if ($authToken == "error") {
            $ssMessage = 'auth_token value can not be blank';
            $amResponse = Common::errorResponse($ssMessage);
            Common::encodeResponseJSON($amResponse);
        }
        Common::checkAuthentication($authToken, $requestParam['user_id']);

        $userModel = Users::findOne(['id' => $requestParam['user_id']]);
        if (!empty($userModel)) {
            $fromEmail = $userModel->email;
            foreach ($notes as $key => $note) {
                $html = '<!DOCTYPE html>
                        <html>

                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
                        </head>

                        <body>

                            <header style="background:' . $note['color_code'] . '">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12 p-0">
                                            <h1>' . $note['title'] . '...</h1>
                                        </div>
                                    </div>
                                </div>
                            </header>
                            <section>
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="p_id" style="font-family:' . $note['font_name'] . ';font-size:' . $note['font_size'] . 'px;">Patient ID :<span>' . $note['patient_id'] . '</span></p>
                                            <p style="font-family:' . $note['font_name'] . ';font-size: ' . $note['font_size'] . 'px">Comments : ' . $note['description'] . '</p>
                                        </div>
                                    </div>

                                </div>
                            </section>
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
                    // any css to be embedded if required
                    'cssFile' => '@api/web/css/notes.css',
                    // set mPDF properties on the fly
                    'options' => ['title' => $note['title']],
                    // call mPDF methods on the fly
                    'methods' => [
                        'SetHeader' => [''],
                        'SetFooter' => [''],
                    ],
                ]);
                $pdf->content = $html;
                $file_name = "note_" . rand(7, 100) . "_" . time() . ".pdf";
                $pdf->filename = "../../uploads/pdf_files/" . $file_name;
                echo $pdf->render();
                $emailformatemodel = EmailFormat::findOne(["title" => 'note_email', "status" => '1']);
                if ($emailformatemodel) {

                    $body = $emailformatemodel->body;
                    $ssSubject = $emailformatemodel->subject;
                    //send email for new generated password
                    $attach = !empty($file_name) && file_exists(Yii::getAlias('@root') . '/' . "uploads/pdf_files/" . $file_name) ? Yii::$app->params['root_url'] . '/' . "uploads/pdf_files/" . $file_name : "";
                    $ssResponse = Common::sendMailToUserWithAttachment($note['patient_email'], $fromEmail, $ssSubject, $body, $attach);
                    if ($ssResponse) {
                        $sentNotesModel = new SentNotes();
                        $sentNotesModel->note_id = $note['note_id'];
                        $sentNotesModel->color_code = $note['color_code'];
                        $sentNotesModel->title = $note['title'];
                        $sentNotesModel->description = $note['description'];
                        $sentNotesModel->from_user_id = $requestParam['user_id'];
                        $sentNotesModel->to_patient_id = !empty($requestParam['to_patient_id']) ? $requestParam['to_patient_id'] : "";
                        $sentNotesModel->to_patient_id = $note['patient_id'];
                        $sentNotesModel->to_email_id = $note['patient_email'];
                        $sentNotesModel->font_size = $note['font_size'];
                        $sentNotesModel->font_name = $note['font_name'];
                        $sentNotesModel->pdf_filename = Yii::$app->params['root_url'] . "/uploads/pdf_files/" . $file_name;
                        $sentNotesModel->save(false);
                        $sentNotes[] = $sentNotesModel;
                    }

                }
            }
            $amReponseParam = $sentNotes;
            $ssMessage = 'PDF is successfully sent through email.';
            $amResponse = Common::successResponse($ssMessage, $amReponseParam);
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }

    public function actionGetSentMailList()
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
        // Check User Status
        Common::matchUserStatus($requestParam['user_id']);
        //VERIFY AUTH TOKEN
        $authToken = Common::get_header('auth_token');
        if ($authToken == "error") {
            $ssMessage = 'auth_token value can not be blank';
            $amResponse = Common::errorResponse($ssMessage);
            Common::encodeResponseJSON($amResponse);
        }
        Common::checkAuthentication($authToken, $requestParam['user_id']);

        $userModel = Users::findOne(['id' => $requestParam['user_id']]);
        if (!empty($userModel)) {
            $usersSentMailDateList = SentNotes::find()->select("DATE(created_at) dateOnly")->where(['from_user_id' => $requestParam['user_id']])->asArray()->groupBy('dateOnly')->all();
            if (!empty($usersSentMailDateList)) {
                foreach ($usersSentMailDateList as $key => $value) {
                    $getDataDateWise = SentNotes::find()->where(['DATE(created_at)' => $value['dateOnly']])->asArray()->all();
                    $amReponseParam[$value['dateOnly']] = $getDataDateWise;
                }
                $ssMessage = 'List of sent emails';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);

            } else {
                $ssMessage = 'Sent Emails not found.';
                $amResponse = Common::errorResponse($ssMessage);
            }
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
}
