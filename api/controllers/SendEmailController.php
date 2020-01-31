<?php

namespace api\controllers;

/* USE COMMON MODELS */
use common\components\Common;
use common\models\ActionItems;
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
                    $getDataDateWise = SentNotes::find()->where(['DATE(created_at)' => $value['dateOnly'], 'from_user_id' => $requestParam['user_id']])->asArray()->all();
                    $amReponseParam[$key]['date'] = $value['dateOnly'];
                    $amReponseParam[$key]['datewiseData'] = $getDataDateWise;
                }
                $ssMessage = 'List of sent emails';
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
                $usersSentMailDateList = SentNotes::find()->select("DATE(created_at) dateOnly")->where(['from_user_id' => $requestParam['user_id']])->asArray()->groupBy('dateOnly')->all();
                if (!empty($usersSentMailDateList)) {
                    foreach ($usersSentMailDateList as $key => $value) {
                        $getDataDateWise = SentNotes::find()->where(['DATE(created_at)' => $value['dateOnly'], 'from_user_id' => $requestParam['user_id']])->asArray()->all();
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
                          <link rel="stylesheet" href="' . Yii::$app->params["root_url"] . '/api/web/css/todolist.css">
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
        $amRequiredParams = array('user_id', 'protocol', 'investigator', 'date', 'action_items', 'to_patient_email');
        $amParamsResult = Common::checkRequestParameterKey($amData['request_param'], $amRequiredParams);
        // If any getting error in request paramter then set error message.
        if (!empty($amParamsResult['error'])) {
            $amResponse = Common::errorResponse($amParamsResult['error']);
            Common::encodeResponseJSON($amResponse);
        }
        $requestParam = $amData['request_param'];
        $list = json_decode($requestParam['action_items']);
        // $list = $requestParam['list'];

        /*    $amRequiredParamsList = array('text', 'is_checked');

        foreach ($list as $key => $single_list) {
        $amParamsResultList = Common::checkRequestParameterKey((array) $single_list, $amRequiredParamsList);

        if (!empty($amParamsResultList['error'])) {
        $amResponse = Common::errorResponse($amParamsResultList['error']);
        Common::encodeResponseJSON($amResponse);
        }
        }*/
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
                'cssFile' => '@api/web/css/action_items.css',
                // set mPDF properties on the fly
                'options' => ['title' => "ACTION ITEMS"],
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
                    $actionItemsModel = new ActionItems();
                    $actionItemsModel->user_id = $requestParam['user_id'];
                    $actionItemsModel->investigator = $requestParam['investigator'];
                    $actionItemsModel->protocol = $requestParam['protocol'];
                    $actionItemsModel->date = $requestParam['date'];
                    $actionItemsModel->action_items = $requestParam['action_items'];
                    $actionItemsModel->patient_id = !empty($requestParam['patient_id']) ? $requestParam['patient_id'] : "";
                    $actionItemsModel->to_patient_email = $requestParam['to_patient_email'];
                    $actionItemsModel->pdf_file_name = Yii::$app->params['root_url'] . "/uploads/pdf_action_items/" . $file_name;
                    $actionItemsModel->save(false);
                    $actionItems[] = $actionItemsModel;
                }

            }

            $amReponseParam = $actionItems;
            $ssMessage = 'Action Items PDF is successfully sent through email.';
            $amResponse = Common::successResponse($ssMessage, $amReponseParam);
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
}
