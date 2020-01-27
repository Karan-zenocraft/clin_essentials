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
        $notes = json_decode(json_encode($requestParam['notes']), true);
        // p($notes[1]);

        $amRequiredParamsNotes = array('note_id', 'color_code', 'title', 'font_name', 'font_size', 'patient_id', 'description');

        foreach ($notes as $key => $note) {
            $amParamsResultNotes = Common::checkRequestParameterKey((array) json_decode($note), $amRequiredParamsNotes);

            if (!empty($amParamsResultNotes['error'])) {
                $amResponse = Common::errorResponse($amParamsResultNotes['error']);
                Common::encodeResponseJSON($amResponse);
            }
        }
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
                $html = '<!DOCTYPE html>
                <html>
                    <head>
                        <title>Enroot</title>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
                        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
                        <style>
                            html{height: 100%;background: #fff;}
                            header{background: #5f5f9c;padding: 30px 0px;border-radius: 50px 50px 0px 0px;border: 0.5px solid #ddd;}
                            header h1{color: #fff;font-family: ' . !empty($noteArr->font_name) ? $noteArr->font_name : "Poppins" . ', sans-serif;font-weight: 400;letter-spacing: 0px;text-transform: capitalize;font-size: ' . !empty($noteArr->font_size) ? $noteArr->font_size : "40" . ';line-height: ' . !empty($noteArr->font_size) ? $noteArr->font_size : "40px" . ';text-align: center;}
                             section{background: #fff;}
                            section p{color: #333;font-family: "Poppins", sans-serif;font-weight: 400;letter-spacing: 0px;text-transform: capitalize;font-size: 14px;line-height: 20px;margin: 10px 0px;}
                        </style>
                    </head>
                <body>
​
                    <header>
                        <div class="container-fluid">
                        <div class="row">
                        <div class="col-md-12 p-0">
                        <h1>Adverse Event...</h1>
                        </div>
                        </div>
                        </div>
                        </header>
                        <section>
                            <div class="container-fluid">
                            <div class="row">
                            <div class="col-md-12">
                            <p> This is the operating system that powers many Apple-based devices ranging from
                            iPhone, iPad, etc. offering multiple innovative features enhancing user experience. The
                            benefits it provides are endless, given the integration of dynamic Apple features on their
                            devices making it the second most popular OS.</p>
                                <p>This is the operating system that powers many Apple-based devices ranging from
                            iPhone, iPad, etc. offering multiple innovative features enhancing user experience. The
                            benefits it provides are endless, given the integration of dynamic Apple features on their
                            devices making it the second most popular OS.</p>
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
                echo $pdf->render();
                $emailformatemodel = EmailFormat::findOne(["title" => 'note_email', "status" => '1']);
                if ($emailformatemodel) {

                    //create template file
                    /*   $AreplaceString = array('{password}' => $requestParam['password'], '{username}' => $model->user_name, '{email}' => $model->email, '{email_verify_link}' => $email_verify_link);

                    $body = Common::MailTemplate($AreplaceString, $emailformatemodel->body);*/
                    $body = $emailformatemodel->body;
                    $ssSubject = $emailformatemodel->subject;
                    //send email for new generated password
                    $attach = !empty($file_name) && file_exists(Yii::getAlias('@root') . '/' . "uploads/pdf_files/" . $file_name) ? Yii::$app->params['root_url'] . '/' . "uploads/pdf_files/" . $file_name : "";
                    $ssResponse = Common::sendMailToUserWithAttachment($noteArr->patient_id, $fromEmail, $ssSubject, $body, $attach);
                    if ($ssResponse) {
                        $sentNotesModel = new SentNotes();
                        $sentNotesModel->note_id = $noteArr->note_id;
                        $sentNotesModel->color_code = $noteArr->color_code;
                        $sentNotesModel->title = $noteArr->title;
                        $sentNotesModel->description = $noteArr->description;
                        $sentNotesModel->from_user_id = $requestParam['user_id'];
                        $sentNotesModel->to_patient_id = !empty($requestParam['to_patient_id']) ? $requestParam['to_patient_id'] : "";
                        $sentNotesModel->to_email_id = $noteArr->patient_id;
                        $sentNotesModel->font_size = $noteArr->font_size;
                        $sentNotesModel->font_name = $noteArr->font_name;
                        $sentNotesModel->pdf_filename = $file_name;
                        $sentNotesModel->save(false);
                    }

                }
            }
            $ssMessage = 'Your Email is successfully sent.';
            $amResponse = Common::errorResponse($ssMessage);
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
}
