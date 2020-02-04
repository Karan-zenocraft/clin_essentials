<?php

namespace api\controllers;

/* USE COMMON MODELS */
use common\components\Common;
use common\models\ActionItems;
use common\models\ClinicalStudyProtocol;
use common\models\EmailFormat;
use common\models\SentNotes;
use common\models\TodoList;
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
                $note_color = (!empty($note['note_id'] && ($note['note_id'] == "3"))) ? "#76777A" : "#FFFFFF";
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
                                            <h1 style="color:' . $note_color . '">' . $note['title'] . '...</h1>
                                        </div>
                                    </div>
                                </div>
                            </header>
                            <section>
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="p_id" style="font-family:' . $note['font_name'] . ';font-size:' . $note['font_size'] . 'px;">Patient <span style="text-transform:uppercase">id:</span><span>' . ' ' . $note['patient_id'] . '</span></p>
                                            <p style="font-family:' . $note['font_name'] . ';font-size: ' . $note['font_size'] . 'px">Notes : ' . $note['description'] . '</p>
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
                        $sentNotesModel->user_id = $requestParam['user_id'];
                        $sentNotesModel->patient_id = $note['patient_id'];
                        $sentNotesModel->patient_email = $note['patient_email'];
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
            $usersSentMailDateList = SentNotes::find()->select("DATE(created_at) dateOnly")->where(['user_id' => $requestParam['user_id']])->asArray()->groupBy('dateOnly')->all();

            if (!empty($usersSentMailDateList)) {
                foreach ($usersSentMailDateList as $key => $value) {
                    $getDataDateWise = SentNotes::find()->where(['DATE(created_at)' => $value['dateOnly'], 'user_id' => $requestParam['user_id']])->asArray()->all();
                    $amReponseParam[$key]['date'] = $value['dateOnly'];
                    $amReponseParam[$key]['datewiseData'] = $getDataDateWise;
                }
                $ssMessage = 'List of sent emails.';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);

            } else {
                $amReponseParam = [];
                $ssMessage = 'Sent Emails not found.';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);
            }
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }

    public function actionDeleteOrArchiveNote()
    {
        $amData = Common::checkRequestType();
        $amResponse = array();
        $ssMessage = '';
        $amRequiredParams = array('user_id', 'id', 'action');
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
            $note = SentNotes::find()->where(['id' => $requestParam['id']])->one();
            $amReponseParam = [];
            if (!empty($note)) {
                if (Yii::$app->params['action'][$requestParam['action']] == "delete") {
                    $note->delete();
                    $ssMessage = 'Note deleted successfully.';
                    $amResponse = Common::successResponse($ssMessage, $amReponseParam);
                } else if (Yii::$app->params['action'][$requestParam['action']] == "archive") {
                    $note->is_archive = "1";
                    $note->save(false);
                    $ssMessage = 'Note archived successfully.';
                } else if (Yii::$app->params['action'][$requestParam['action']] == "un_archive") {
                    $note->is_archive = "0";
                    $note->save(false);
                    $ssMessage = 'Note un archived successfully.';
                }
                $usersSentMailDateList = SentNotes::find()->select("DATE(created_at) dateOnly")->where(['user_id' => $requestParam['user_id']])->asArray()->groupBy('dateOnly')->all();
                if (!empty($usersSentMailDateList)) {
                    foreach ($usersSentMailDateList as $key => $value) {
                        $getDataDateWise = SentNotes::find()->where(['DATE(created_at)' => $value['dateOnly'], 'user_id' => $requestParam['user_id']])->asArray()->all();
                        $amReponseParam[$key]['date'] = $value['dateOnly'];
                        $amReponseParam[$key]['datewiseData'] = $getDataDateWise;
                    }

                }

                $amResponse = Common::successResponse($ssMessage, $amReponseParam);

            } else {
                $ssMessage = 'Invalid note id';
                $amResponse = Common::errorResponse($ssMessage);
            }
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
    public function actionSendPdfToDoList()
    {
        $amData = Common::checkRequestType();
        $amResponse = array();
        $ssMessage = '';
        $amRequiredParams = array('user_id', 'protocol', 'investigator', 'date', 'to_patient_email');
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
        $amRequiredParamsList = array('text', 'is_cheked');

        if (!empty($requestParam['list'])) {
            $list = $requestParam['list'];
            foreach ($list as $key => $single_list) {
                $amParamsResultList = Common::checkRequestParameterKey($single_list, $amRequiredParamsList);

                if (!empty($amParamsResultList['error'])) {
                    $amResponse = Common::errorResponse($amParamsResultList['error']);
                    Common::encodeResponseJSON($amResponse);
                }
            }
            $list_arr = '<div class="row"><div class="col-md-12 SectionList"><nav><ul>';

            foreach ($list as $key => $single_list) {
                $checked = ($single_list['is_cheked'] == 1) ? "checked" : "unchecked";
                $list_arr .= '<li><input type="checkbox" id="list" checked="' . $checked . '"><label for="list"><span>' . $single_list['text'] . '</span></label> </li>';
            }
            $list_arr = $list_arr . "</ul></nav></div></div>";
            // $list = $requestParam['list'];

            $userModel = Users::findOne(['id' => $requestParam['user_id']]);
            $logo = Yii::$app->params['root_url'] . "/uploads/images/logo-orange.png";
            if (!empty($userModel)) {
                $fromEmail = $userModel->email;
                $html = '<!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
                    </head>
                    <body>
                        <header>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12 p-0">
                                        <h1>VISIT TO DO LIST</h1>
                                    </div>
                                </div>
                            </div>
                        </header>
                        <section>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="Box">
                                            <p class="p_id">' . $requestParam["protocol"] . '</p>
                                            <p>PROTOCOL</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="Box">
                                            <p class="p_id">' . $requestParam["investigator"] . '</p>
                                            <p>INVESTIGATOR</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="Box">
                                            <p class="p_id">' . $requestParam["date"] . '</p>
                                            <p>DATE</p>
                                        </div>
                                    </div>
                                </div>' . $list_arr . '
                            </div>
                        </section>
                        <footer>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12 d-flex align-items-center justify-content-between">
                                        <div class="Left" style="position: relative;width: 100%;">
                                            <p style="position: absolute;right: 0;top: 5px;font-size: 11px;line-height: 14px;font-weight: bold;letter-spacing: 1px;color: #333333b8;font-family:FrutingerBQRoman; "class="BottomText">Resources and Tools for Clinical Research Professionals</p>
                                        </div>


                                        <div class="Logo" style="display:flex;align-items:center;justify-content:center">
                                      <hr style="display: block;margin-top: 0.5em;margin-left: auto;margin-right: auto; border-style: inset;border-width: 1px;width:80%;position:absolute;left:auto;bottom:0;right:0">
                                            <img src="' . $logo . '" width="auto" max-height="80" alt="" class="img-fluid" style="float:right;">
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </footer>
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
                    //   'cssFile' => '@api/web/css/todolist.css',
                    // set mPDF properties on the fly
                    'options' => ['title' => "VISIT TO DO LIST"],
                    // call mPDF methods on the fly
                    'methods' => [
                        'SetHeader' => [''],
                        'SetFooter' => [''],
                    ],
                ]);
                $pdf->content = $html;
                $file_name = "note_" . rand(7, 100) . "_" . time() . ".pdf";
                $pdf->filename = "../../uploads/pdf_todolist/" . $file_name;
                echo $pdf->render();
                $emailformatemodel = EmailFormat::findOne(["title" => 'todolist_email', "status" => '1']);
                if ($emailformatemodel) {

                    $body = $emailformatemodel->body;
                    $ssSubject = $emailformatemodel->subject;
                    //send email for new generated password
                    $attach = !empty($file_name) && file_exists(Yii::getAlias('@root') . '/' . "uploads/pdf_todolist/" . $file_name) ? Yii::$app->params['root_url'] . '/' . "uploads/pdf_todolist/" . $file_name : "";
                    $ssResponse = Common::sendMailToUserWithAttachment($requestParam['to_patient_email'], $fromEmail, $ssSubject, $body, $attach);
                    if ($ssResponse) {
                        $toDoListModel = new TodoList();
                        $toDoListModel->user_id = $requestParam['user_id'];
                        $toDoListModel->investigator = $requestParam['investigator'];
                        $toDoListModel->protocol = $requestParam['protocol'];
                        $toDoListModel->date = $requestParam['date'];
                        $toDoListModel->list = $requestParam['list'];
                        $toDoListModel->patient_id = !empty($requestParam['patient_id']) ? $requestParam['patient_id'] : "";
                        $toDoListModel->to_patient_email = $requestParam['to_patient_email'];
                        $toDoListModel->pdf_file_name = Yii::$app->params['root_url'] . "/uploads/pdf_todolist/" . $file_name;
                        $toDoListModel->save(false);
                        $toDoList[] = $toDoListModel;
                    }

                }

                $amReponseParam = $toDoList;
                $ssMessage = 'To do list PDF is successfully sent through email.';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);
            } else {
                $ssMessage = 'Invalid user_id';
                $amResponse = Common::errorResponse($ssMessage);
            }
        } else {
            $ssMessage = 'List can not be blank';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);

    }

    public function actionSendPdfActionItems()
    {
        $amData = Common::checkRequestType();
        $amResponse = array();
        $ssMessage = '';
        $amRequiredParams = array('user_id', 'protocol', 'investigator', 'date', 'to_patient_email');
        $amParamsResult = Common::checkRequestParameterKey($amData['request_param'], $amRequiredParams);
        // If any getting error in request paramter then set error message.
        if (!empty($amParamsResult['error'])) {
            $amResponse = Common::errorResponse($amParamsResult['error']);
            Common::encodeResponseJSON($amResponse);
        }
        $requestParam = $amData['request_param'];
        Common::matchUserStatus($requestParam['user_id']);
        //VERIFY AUTH TOKEN
        $authToken = Common::get_header('auth_token');
        if ($authToken == "error") {
            $ssMessage = 'auth_token value can not be blank';
            $amResponse = Common::errorResponse($ssMessage);
            Common::encodeResponseJSON($amResponse);
        }
        Common::checkAuthentication($authToken, $requestParam['user_id']);
        $amRequiredParamsList = array('item', 'by_date', 'is_cheked');

        if (!empty($requestParam['action_items'])) {
            $action_items = $requestParam['action_items'];
            foreach ($action_items as $key => $single_item) {
                $amParamsResultList = Common::checkRequestParameterKey($single_item, $amRequiredParamsList);

                if (!empty($amParamsResultList['error'])) {
                    $amResponse = Common::errorResponse($amParamsResultList['error']);
                    Common::encodeResponseJSON($amResponse);
                }
            }
            $list_arr = '<div class="row"><div class="col-md-12 SectionList"><nav><ul>';

            foreach ($action_items as $key => $single_item) {
                $checked = ($single_item['is_cheked'] == 1) ? "checked" : "unchecked";
                $list_arr .= '<li><label for="list"><span>' . $single_item['item'] . '</span><span>' . $single_item['by_date'] . '</span></label><input type="checkbox" id="list" checked="' . $checked . '"></li>';
            }
            $list_arr = $list_arr . "</ul></nav></div></div>";
            // $list = $requestParam['list'];

            $userModel = Users::findOne(['id' => $requestParam['user_id']]);
            $logo = Yii::$app->params['root_url'] . "/uploads/images/logo-orange.png";
            if (!empty($userModel)) {
                $fromEmail = $userModel->email;
                $html = '<!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
                    </head>
                    <body>
                        <header>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12 p-0">
                                        <h1>Action Items</h1>
                                    </div>
                                </div>
                            </div>
                        </header>
                        <section>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="Box">
                                            <p class="p_id">' . $requestParam["protocol"] . '</p>
                                            <p>PROTOCOL</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="Box">
                                            <p class="p_id">' . $requestParam["investigator"] . '</p>
                                            <p>INVESTIGATOR</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="Box">
                                            <p class="p_id">' . $requestParam["date"] . '</p>
                                            <p>DATE</p>
                                        </div>
                                    </div>
                                </div>' . $list_arr . '
                            </div>
                        </section>
                        <footer>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12 d-flex align-items-center justify-content-between">
                                        <div class="Left" style="position: relative;width: 100%;">
                                            <p style="position: absolute;right: 0;top: 5px;font-size: 11px;line-height: 14px;font-weight: bold;letter-spacing: 1px;color: #333333b8;font-family:FrutingerBQRoman; "class="BottomText">Resources and Tools for Clinical Research Professionals</p>
                                        </div>


                                        <div class="Logo" style="display:flex;align-items:center;justify-content:center">
                                      <hr style="display: block;margin-top: 0.5em;margin-left: auto;margin-right: auto; border-style: inset;border-width: 1px;width:80%;position:absolute;left:auto;bottom:0;right:0">
                                            <img src="' . $logo . '" width="auto" max-height="80" alt="" class="img-fluid" style="float:right;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </footer>
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
                    'cssFile' => '@api/web/css/todolist.css',
                    // set mPDF properties on the fly
                    'options' => ['title' => "VISIT TO DO LIST"],
                    // call mPDF methods on the fly
                    'methods' => [
                        'SetHeader' => [''],
                        'SetFooter' => [''],
                    ],
                ]);
                $pdf->content = $html;
                $file_name = "note_" . rand(7, 100) . "_" . time() . ".pdf";
                $pdf->filename = "../../uploads/pdf_action_items/" . $file_name;
                echo $pdf->render();
                $emailformatemodel = EmailFormat::findOne(["title" => 'action_items_email', "status" => '1']);
                if ($emailformatemodel) {

                    $body = $emailformatemodel->body;
                    $ssSubject = $emailformatemodel->subject;
                    //send email for new generated password
                    $attach = !empty($file_name) && file_exists(Yii::getAlias('@root') . '/' . "uploads/pdf_action_items/" . $file_name) ? Yii::$app->params['root_url'] . '/' . "uploads/pdf_action_items/" . $file_name : "";
                    $ssResponse = Common::sendMailToUserWithAttachment($requestParam['to_patient_email'], $fromEmail, $ssSubject, $body, $attach);
                    if ($ssResponse) {
                        $toDoListModel = new ActionItems();
                        $toDoListModel->user_id = $requestParam['user_id'];
                        $toDoListModel->investigator = $requestParam['investigator'];
                        $toDoListModel->protocol = $requestParam['protocol'];
                        $toDoListModel->date = $requestParam['date'];
                        $toDoListModel->action_items = $requestParam['action_items'];
                        $toDoListModel->patient_id = !empty($requestParam['patient_id']) ? $requestParam['patient_id'] : "";
                        $toDoListModel->to_patient_email = $requestParam['to_patient_email'];
                        $toDoListModel->pdf_file_name = Yii::$app->params['root_url'] . "/uploads/pdf_action_items/" . $file_name;
                        $toDoListModel->save(false);
                        $toDoList[] = $toDoListModel;
                    }

                }

                $amReponseParam = $toDoList;
                $ssMessage = 'To do list PDF is successfully sent through email.';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);
            } else {
                $ssMessage = 'Invalid user_id';
                $amResponse = Common::errorResponse($ssMessage);
            }
        } else {
            $ssMessage = 'Action Items can not be blank';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);

    }

    public function actionSendPdfClinicalStudyProtocol()
    {
        $amData = Common::checkRequestType();
        $amResponse = array();
        $ssMessage = '';
        $amRequiredParams = array('user_id', 'my_notes', 'to_patient_email');
        $amParamsResult = Common::checkRequestParameterKey($amData['request_param'], $amRequiredParams);
        // If any getting error in request paramter then set error message.
        if (!empty($amParamsResult['error'])) {
            $amResponse = Common::errorResponse($amParamsResult['error']);
            Common::encodeResponseJSON($amResponse);
        }
        $requestParam = $amData['request_param'];
        Common::matchUserStatus($requestParam['user_id']);
        //VERIFY AUTH TOKEN
        $authToken = Common::get_header('auth_token');
        if ($authToken == "error") {
            $ssMessage = 'auth_token value can not be blank';
            $amResponse = Common::errorResponse($ssMessage);
            Common::encodeResponseJSON($amResponse);
        }
        Common::checkAuthentication($authToken, $requestParam['user_id']);
        $amRequiredParamsList = array('text', 'is_checked');

        if (!empty($requestParam['protocol_array'])) {
            $protocol_array = $requestParam['protocol_array'];
            foreach ($protocol_array as $key => $single_item) {
                $amParamsResultList = Common::checkRequestParameterKey($single_item, $amRequiredParamsList);

                if (!empty($amParamsResultList['error'])) {
                    $amResponse = Common::errorResponse($amParamsResultList['error']);
                    Common::encodeResponseJSON($amResponse);
                }
            }
            /*   $list_arr = "";
            foreach ($protocol_array as $key => $single_item) {
            $checked = ($single_item['is_checked'] == 1) ? "checked" : "unchecked";
            $list_arr .= "<div class='row'><div class='col-md-6'>'" . $single_item['is_checked'] . "'<span>'" . $single_item['text'] . "'</span></div>";

            }*/
            $list_arr = '<div class="row"><div class="col-md-12 SectionList"><nav><ul>';

            foreach ($protocol_array as $key => $single_item) {
                $checked = ($single_item['is_checked'] == 1) ? true : false;
                $list_arr .= '<li><input type="checkbox" style="color:#ff6a0c" name="list" value="' . $key . '" checked=' . $checked . '><span style="padding-left:60;">' . $single_item['text'] . '</span></li>';
            }
            $list_arr = $list_arr . "</ul></nav></div></div>";
            // $list = $requestParam['list'];

            $userModel = Users::findOne(['id' => $requestParam['user_id']]);
            $logo = Yii::$app->params['root_url'] . "/uploads/images/logo-orange.png";
            if (!empty($userModel)) {
                $fromEmail = $userModel->email;
                $html = '<!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
                    </head>
                    <body>
                        <header>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12 p-0">
                                        <h1>Reviewing a Clinical Study Protocol</h1>
                                    </div>
                                </div>
                            </div>
                        </header>
                        <section>
                            <div class="container-fluid">
                             ' . $list_arr . '
                            </div>
                        </section>
                        <footer>
                        <h2>MY NOTES</h2>' . $requestParam['my_notes'] . '
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12 d-flex align-items-center justify-content-between">
                                        <div class="Left" style="position: relative;width: 100%;">
                                            <p style="position: absolute;right: 0;top: 5px;font-size: 11px;line-height: 14px;font-weight: bold;letter-spacing: 1px;color: #333333b8;font-family:FrutingerBQRoman; "class="BottomText">Resources and Tools for Clinical Research Professionals</p>
                                        </div>


                                        <div class="Logo" style="display:flex;align-items:center;justify-content:center">
                                      <hr style="display: block;margin-top: 0.5em;margin-left: auto;margin-right: auto; border-style: inset;border-width: 1px;width:80%;position:absolute;left:auto;bottom:0;right:0">
                                            <img src="' . $logo . '" width="auto" max-height="80" alt="" class="img-fluid" style="float:right;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </footer>
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
                    'cssFile' => '@api/web/css/todolist.css',
                    // set mPDF properties on the fly
                    'options' => ['title' => "Reviewing a Clinical Study Protocol"],
                    // call mPDF methods on the fly
                    'methods' => [
                        'SetHeader' => [''],
                        'SetFooter' => [''],
                    ],
                ]);
                $pdf->content = $html;
                $file_name = "note_" . rand(7, 100) . "_" . time() . ".pdf";
                $pdf->filename = "../../uploads/pdf_clinical_study_protocol/" . $file_name;
                echo $pdf->render();
                $emailformatemodel = EmailFormat::findOne(["title" => 'critical_study_protocol', "status" => '1']);
                if ($emailformatemodel) {

                    $body = $emailformatemodel->body;
                    $ssSubject = $emailformatemodel->subject;
                    //send email for new generated password
                    $attach = !empty($file_name) && file_exists(Yii::getAlias('@root') . '/' . "uploads/pdf_clinical_study_protocol/" . $file_name) ? Yii::$app->params['root_url'] . '/' . "uploads/pdf_clinical_study_protocol/" . $file_name : "";
                    $ssResponse = Common::sendMailToUserWithAttachment($requestParam['to_patient_email'], $fromEmail, $ssSubject, $body, $attach);
                    if ($ssResponse) {
                        $clinicalStudyModel = new ClinicalStudyProtocol();
                        $clinicalStudyModel->user_id = $requestParam['user_id'];
                        $clinicalStudyModel->my_notes = $requestParam['my_notes'];
                        $clinicalStudyModel->protocol_array = $requestParam['protocol_array'];
                        $clinicalStudyModel->patient_id = !empty($requestParam['patient_id']) ? $requestParam['patient_id'] : "";
                        $clinicalStudyModel->to_patient_email = $requestParam['to_patient_email'];
                        $clinicalStudyModel->pdf_file_name = Yii::$app->params['root_url'] . "/uploads/pdf_clinical_study_protocol/" . $file_name;
                        $clinicalStudyModel->save(false);
                        $clinicalStudyProtocolArr[] = $clinicalStudyModel;
                    }

                }

                $amReponseParam = $clinicalStudyProtocolArr;
                $ssMessage = 'Clinical Study Protocol PDF is successfully sent through email.';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);
            } else {
                $ssMessage = 'Invalid user_id';
                $amResponse = Common::errorResponse($ssMessage);
            }
        } else {
            $ssMessage = 'protocol_array can not be blank';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);

    }
    public function actionSaveNote()
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

                $sentNotesModel = new SentNotes();
                $sentNotesModel->note_id = $note['note_id'];
                $sentNotesModel->color_code = $note['color_code'];
                $sentNotesModel->title = $note['title'];
                $sentNotesModel->description = $note['description'];
                $sentNotesModel->user_id = $requestParam['user_id'];
                $sentNotesModel->patient_id = $note['patient_id'];
                $sentNotesModel->patient_email = $note['patient_email'];
                $sentNotesModel->font_size = $note['font_size'];
                $sentNotesModel->font_name = $note['font_name'];
                $sentNotesModel->mail_sent = Yii::$app->params['mail_sent']['false'];
                $sentNotesModel->save(false);
                $sentNotes[] = $sentNotesModel;

            }
            $amReponseParam = $sentNotes;
            $ssMessage = 'Your note is successfully saved.';
            $amResponse = Common::successResponse($ssMessage, $amReponseParam);
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
    public function actionGetSaveNotesList()
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
            $usersSentMailDateList = SentNotes::find()->where(['user_id' => $requestParam['user_id'], "mail_sent" => "0"])->asArray()->all();
            if (!empty($usersSentMailDateList)) {
                array_walk($usersSentMailDateList, function ($arr) use (&$amResponseData) {
                    $ttt = $arr;
                    $ttt['font_size'] = (int) $ttt['font_size'];
                    unset($ttt['pdf_filename']);
                    unset($ttt['is_archive']);
                    unset($ttt['created_at']);
                    unset($ttt['updated_at']);
                    $amResponseData[] = $ttt;
                    return $amResponseData;
                });
                $amReponseParam = $amResponseData;
                $ssMessage = 'List of save notes';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);

            } else {
                $amReponseParam = [];
                $ssMessage = 'Save Notes not found.';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);
            }
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
    public function actionDeleteSavedNote()
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
        if (!empty($requestParam['id'])) {
            $userModel = Users::findOne(['id' => $requestParam['user_id']]);
            if (!empty($userModel)) {
                $idArr = $requestParam['id'];
                $success = "";
                foreach ($idArr as $key => $single_id) {
                    $note = SentNotes::find()->where(['id' => $single_id, "user_id" => $requestParam['user_id'], "mail_sent" => '0'])->one();
                    if (empty($note)) {
                        $ssMessage = 'Invalid id';
                        $success = "0";
                        $amResponse = Common::errorResponse($ssMessage);
                        Common::encodeResponseJSON($amResponse);
                    } else {
                        $note->delete();
                        $success = "1";
                    }
                }
                if ($success != "0") {
                    $notesArr = SentNotes::find()->where(["user_id" => $requestParam['user_id'], "mail_sent" => '0'])->asArray()->all();

                    if (!empty($notesArr)) {
                        array_walk($notesArr, function ($arr) use (&$amResponseData) {
                            $ttt = $arr;
                            $ttt['font_size'] = (int) $ttt['font_size'];
                            unset($ttt['pdf_filename']);
                            unset($ttt['is_archive']);
                            unset($ttt['created_at']);
                            unset($ttt['updated_at']);
                            $amResponseData[] = $ttt;
                            return $amResponseData;
                        });
                        $amReponseParam = $amResponseData;
                    } else {
                        $amReponseParam = [];
                    }
                    $ssMessage = 'Note deleted successfully.';
                    $amResponse = Common::successResponse($ssMessage, $amReponseParam);
                }
            } else {
                $ssMessage = 'Invalid user_id';
                $amResponse = Common::errorResponse($ssMessage);
            }
        } else {
            $ssMessage = 'id can not be blank';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
}
