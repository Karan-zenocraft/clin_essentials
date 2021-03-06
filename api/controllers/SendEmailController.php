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

        $amRequiredParamsNotes = array('note_id', 'color_code', 'title', 'font_name', 'font_size', 'patient_id', 'patient_email', 'description', 'pdf_password', 'sent_mail_time');
        $checkedboxs = Yii::$app->params['root_url'] . "/uploads/images/icon_checked.jpg";
        $uncheckeds = Yii::$app->params['root_url'] . "/uploads/images/icon_unchecked.jpg";
        foreach ($notes as $key => $note) {

            $amParamsResultNotes = Common::checkRequestParameterKey($note, $amRequiredParamsNotes);

            if (!empty($amParamsResultNotes['error'])) {
                $amResponse = Common::errorResponse($amParamsResultNotes['error']);
                Common::encodeResponseJSON($amResponse);
            }
            if (($note['note_id'] == "3") || ($note['note_id'] == "6")) {
                if ($checked = $note['late_entry'] == 1) {
                    $list_array = '<img src="' . $checkedboxs . '" alt="" style="height:12px;width:12px"><span>   Late Entry</span>';

                } else if ($unchecked = $note['late_entry'] == 0) {

                    $list_array = '<img src="' . $uncheckeds . '" alt="" style="height:12px;width:12px"><span>   Late Entry</span>';
                }
            } else {
                $list_array = "";
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
                if (!empty($note['note_id'] && ($note['note_id'] == "9"))) {
                    if (empty($note['please_complete_data'])) {
                        $ssMessage = 'please_complete_data can not be blank.';
                        $amResponse = Common::errorResponse($ssMessage);
                        Common::encodeResponseJSON($amResponse);
                    } else {
                        $please_complete_data = $note['please_complete_data'];
                        $section0_checked = ($please_complete_data[0]['is_checked'] == "1") ? $checkedboxs : $uncheckeds;
                        $section0_text = $please_complete_data[0]['text'];
                        $section1_checked = ($please_complete_data[1]['is_checked'] == "1") ? $checkedboxs : $uncheckeds;
                        $section1_text = $please_complete_data[1]['text'];
                        $section2_checked = ($please_complete_data[2]['is_checked'] == "1") ? $checkedboxs : $uncheckeds;
                        $section2_text = $please_complete_data[2]['text'];
                        $section3_checked = ($please_complete_data[3]['is_checked'] == "1") ? $checkedboxs : $uncheckeds;
                        $section3_text = $please_complete_data[3]['text'];
                        $section4_checked = ($please_complete_data[4]['is_checked'] == "1") ? $checkedboxs : $uncheckeds;
                        $section4_text = $please_complete_data[4]['text'];
                        $section5_checked = ($please_complete_data[5]['is_checked'] == "1") ? $checkedboxs : $uncheckeds;
                        $section5_text = $please_complete_data[5]['text'];
                        $section6_checked = ($please_complete_data[6]['is_checked'] == "1") ? $checkedboxs : $uncheckeds;
                        $section6_text = $please_complete_data[6]['text'];
                        $section7_checked = ($please_complete_data[7]['is_checked'] == "1") ? $checkedboxs : $uncheckeds;
                        $section7_text = $please_complete_data[7]['text'];
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




                                      <table width="100%" cellpadding="15px" cellspacing="0" border="0" align="center">
                <tr>
                <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
                <tr>

                    <td>
<table width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
                <tr>
<td class="p_id" style="font-family:' . $note['font_name'] . ';font-size:' . $note['font_size'] . 'px;">  ' . $list_array . ' </td>

<td class="p_id" style="font-family:' . $note['font_name'] . ';font-size:' . $note['font_size'] . 'px;">Patient <span style="text-transform:uppercase">id:</span><span>' . ' ' . $note['patient_id'] . '</span></td>

 </tr>
          </table>

                    </td>


                    <td>

                    <table width="20%" cellpadding="0" cellspacing="0" border="0" align="right">
                <tr>

                            <td valign="middle"  height="20px" width="20px"><img  src="' . $section0_checked . '" alt="" style="height:20px;width:20px"></td>

                            <td valign="middle" width="10px" height="20px"></td>

                            <td valign="middle"  style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #333;font-family: FRUTBL_;">' . $section0_text . '</td>

</tr>
          </table>
                    <td>
            </tr>
          </table>


<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
          <tr>
             <td valign="middle" height="15px"></td>
            </tr>
                    <tr>
                            <td valign="middle" align="center" height="20px" width="20px"><img src="' . $section1_checked . '" alt="" style="height:20px;width:20px"></td>

                            <td width="10px" height="20px"></td>

                            <td style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #333;font-family: FRUTBL_;">' . $section1_text . '</td>

            </tr>
            <tr>
             <td valign="middle" height="15px"></td>
            </tr>
                    <tr>
                            <td valign="middle" align="center" height="20px" width="20px"><img src="' . $section2_checked . '" alt="" style="height:20px;width:20px"></td>

                            <td width="10px" height="20px"></td>

                            <td style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #333;font-family: FRUTBL_;">' . $section2_text . '</td>

            </tr>
            <tr>
             <td valign="middle" height="15px"></td>
            </tr>
                    <tr>
                            <td valign="middle" align="center" height="20px" width="20px"><img src="' . $section3_checked . '" alt="" style="height:20px;width:20px"></td>

                            <td width="10px" height="20px"></td>

                            <td style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #333;font-family: FRUTBL_;">' . $section3_text . '</td>

            </tr>
            <tr>
             <td valign="middle" height="15px"></td>
            </tr>
                    <tr>
                            <td valign="middle" align="center" height="20px" width="20px"><img src="' . $section4_checked . '" alt="" style="height:20px;width:20px"></td>

                            <td width="10px" height="20px"></td>

                            <td style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #333;font-family: FRUTBL_;">' . $section4_text . '</td>

            </tr>
            <tr>
             <td valign="middle" height="15px"></td>
            </tr>
                    <tr>
                            <td valign="middle" align="center" height="20px" width="20px"><img src="' . $section5_checked . '" alt="" style="height:20px;width:20px"></td>

                            <td width="10px" height="20px"></td>

                            <td style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #333;font-family: FRUTBL_;">' . $section5_text . '</td>

            </tr>
            <tr>
             <td valign="middle" height="15px"></td>
            </tr>
                    <tr>
                            <td valign="middle" align="center" height="20px" width="20px"><img src="' . $section6_checked . '" alt="" style="height:20px;width:20px"></td>

                            <td width="10px" height="20px"></td>

                            <td style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #333;font-family: FRUTBL_;">' . $section6_text . '</td>

            </tr>
            <tr>
                            <td valign="middle" align="left" height="12"></td>
            </tr>
            <td valign="middle" height="15px"></td>
                <tr>
                            <td valign="middle" align="center" height="20px" width="20px"><img src="' . $section7_checked . '" alt="" style="height:20px;width:20px"></td>
                            <td width="10px" height="20px"></td>
                            <td  style="border-bottom: 1px solid #d1d3d5;font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #333;font-family: FRUTBL_;">' . $section7_text . '</td>
                            <td style=" font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #333;font-family: FRUTBL_;">Section </td>
            </tr>
            <tr>
                            <td valign="middle" align="left" height="12"></td>
            </tr>
            </table>
                  </tr>
                </td>
</table>
                                       <!---REMOVE OLD--->
                                        </div>
                                    </div>

                                </div>
                            </section>
                        </body>
                        </html>';
                    }
                } else {
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

                                       <p class="p_id" style="font-family:' . $note['font_name'] . ';font-size:' . $note['font_size'] . 'px;">  ' . $list_array . ' </p>

                                            <p class="p_id" style="font-family:' . $note['font_name'] . ';font-size:' . $note['font_size'] . 'px;">Patient <span style="text-transform:uppercase">id:</span><span>' . ' ' . $note['patient_id'] . '</span></p>
                                            <p style="font-family:' . $note['font_name'] . ';font-size: ' . $note['font_size'] . 'px">Notes: ' . $note['description'] . '</p>
                                        </div>
                                    </div>

                                </div>
                            </section>
                        </body>
                        </html>';
                }
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
                $pdf->getApi()->setProtection(array(), $note['pdf_password']);
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
                        $sentNotesModel->pdf_password = md5($note['pdf_password']);
                        $sentNotesModel->font_size = $note['font_size'];
                        $sentNotesModel->font_name = $note['font_name'];
                        $sentNotesModel->late_entry = !empty($note['late_entry']) ? $note['late_entry'] : "0";
                        $sentNotesModel->please_complete_data = (!empty($note['please_complete_data']) && ($note['note_id'] == '9')) ? $note['please_complete_data'] : [];
                        $sentNotesModel->pdf_filename = Yii::$app->params['root_url'] . "/uploads/pdf_files/" . $file_name;
                        $sentNotesModel->sent_mail_time = $note['sent_mail_time'];
                        $sentNotesModel->save(false);
                        $sentNotes[] = $sentNotesModel;
                    }

                }
            }
            $amReponseParam = $sentNotes;
            $ssMessage = (count($notes) > 1) ? 'CRA Note(s) PDF is successfully sent.' : 'CRA Note PDF is successfully sent.';
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
            $usersSentMailDateList = SentNotes::find()->select("DATE(created_at) dateOnly")->where(['user_id' => $requestParam['user_id'], 'mail_sent' => Yii::$app->params['mail_sent']['true']])->asArray()->groupBy('dateOnly')->all();

            if (!empty($usersSentMailDateList)) {
                foreach ($usersSentMailDateList as $key => $value) {
                    $getDataDateWise = SentNotes::find()->where(['DATE(created_at)' => $value['dateOnly'], 'user_id' => $requestParam['user_id']])->asArray()->all();
                    array_walk($getDataDateWise, function ($arr) use (&$amResponseData) {
                        $ttt = $arr;
                        $ttt['patient_id'] = !empty($ttt['patient_id']) ? $ttt['patient_id'] : "";
                        $ttt['sent_mail_time'] = !empty($ttt['sent_mail_time']) ? $ttt['sent_mail_time'] : $ttt['created_at'];
                        $ttt['please_complete_data'] = !empty($ttt['please_complete_data']) ? json_decode($ttt['please_complete_data']) : [];
                        $amResponseData[] = $ttt;
                        return $amResponseData;
                    });
                    $amReponseParam[$key]['date'] = $value['dateOnly'];
                    $amReponseParam[$key]['datewiseData'] = $amResponseData;
                }
                $ssMessage = 'List of sent emails.';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);

            } else {
                $amReponseParam = [];
                $ssMessage = 'CRA Notes not found.';
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
                        array_walk($getDataDateWise, function ($arr) use (&$amResponseData) {
                            $ttt = $arr;
                            $ttt['patient_id'] = !empty($ttt['patient_id']) ? $ttt['patient_id'] : "";
                            $ttt['sent_mail_time'] = !empty($ttt['sent_mail_time']) ? $ttt['sent_mail_time'] : $ttt['created_at'];
                            $ttt['please_complete_data'] = !empty($ttt['please_complete_data']) ? json_decode($ttt['please_complete_data']) : [];
                            $amResponseData[] = $ttt;
                            return $amResponseData;
                        });
                        $amReponseParam[$key]['datewiseData'] = $amResponseData;
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
        $amRequiredParams = array('user_id', 'protocol', 'investigator', 'date', 'to_patient_email', 'pdf_password', 'sent_mail_time');
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

            $list_arr = '<table width="100%" cellpadding="2px" cellspacing="2px" border="0" align="center">';
            foreach ($list as $key => $single_list) {
                $checked = ($single_list['is_cheked'] == 1) ? "checked" : "unchecked";
                $list_arr .= '<tr>
                        <td valign="middle" width="24px">
                        <table  style="float:left;" width="24px"  cellpadding="0" cellspacing="0" border="0" align="center" height="24px">
                            <tr>
                            <td valign="middle" align="left" style="height:20px;width:20px;border: 2px solid #ff6a0c;"></td>
                            </tr>
                        </table>
                        </td>

                        <td width="15px">
                        <table style="float:left;"  width="15px" cellpadding="0" cellspacing="0" border="0" align="center" height="24px">
                        <tr>
                        <td></td>
                        </tr>
                        </table>
                        </td>

                        <td valign="middle" align="left" style="border-bottom: 1px solid #d1d3d5;font-size: 13px;line-height: 20px;word-break:break-all;
                        letter-spacing: 1px;font-weight: lighter;font-family: "FrutingerBQRoman";color: #333;width: 100%;">' . $single_list['text'] . '
                        </td>

                    </tr>


                    <tr>
                    <td valign="middle" align="left" height="10px">
                    </td>
                    </tr>';
            }
            $list_arr = $list_arr . " </table>";
            // $list = $requestParam['list'];
            $userModel = Users::findOne(['id' => $requestParam['user_id']]);
            $logo = Yii::$app->params['root_url'] . "/uploads/images/logo-orange.png";
            if (!empty($userModel)) {
                $fromEmail = $userModel->email;
                $html = '<!DOCTYPE html>
                    <html style="height:100%">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">

                    </head>
                    <body style="height:100%">
<!--table 1-->
    <table align="center" cellpadding="0px" cellspacing="0px" border="0" style="width: 100%;height: 100%;">
        <tr>
            <td valign="top">

                <!--table 1.1-->
    <table align="center" cellpadding="0px" cellspacing="0px" border="0" style="width: 100%;">
        <tr>
            <td>
                <!--table 1.2-->
             <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: #ff6a0c">
                    <tr>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                                <tr>
                                    <td valign="middle" align="left" height="20"></td>
                                </tr>
                                <tr>
                                    <td valign="bottom" align="center" style="font-size: 50px;letter-spacing: 2px;color: #fff;line-height: 35px;font-family: "FrutingerBQRoman";font-weight: 700;">
                                        VISIT TO DO LIST
                                    </td>
                                </tr>

                                <tr>
                                    <td valign="middle" align="left" height="20"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--/table 1.2-->

                <!--table 1.3-->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: #fff">
                    <tr>
                        <td valign="middle" align="left" height="60"></td>
                    </tr>
                </table>
                <!--/table 1.3-->

                <!--table 1.4-->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: #fff">

                    <tr>
                    <td width="33.33%" valign="bottom">
                      <table width="80%" cellpadding="0" cellspacing="0" border="0" align="left">
                       <tr>
                        <td valign="top" align="center" style="color: #333;letter-spacing: 2px;text-transform: capitalize;border-bottom: 1px solid #76767a;height: 22px;word-break: break-all;">


                           <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr align="center">
                                <td>
                            ' . $requestParam["protocol"] . '

                                    </td>
                               </tr>
                               </table>
                           </td>
                        </tr>
                        <tr>
                        <td valign="bottom" align="center" style="color: #76767a;font-family: "Helvetica";font-weight: 600;letter-spacing: 2px;text-transform: capitalize;font-size: 15px;line-height: 26px;height: 22px;word-break: break-all;">PROTOCOL</td>
                        </tr>
                    </table>
                    </td>

                        <td width="33.33%" valign="bottom">
                      <table width="80%" cellpadding="0" cellspacing="0" border="0" align="center">
                       <tr>

                        <td valign="top" align="center" style="color: #333;letter-spacing: 2px;text-transform: capitalize;border-bottom: 1px solid #76767a;height: 22px;word-break: break-all;">


                           <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr align="center">
                                <td>
                           ' . $requestParam["investigator"] . '

                                    </td>
                               </tr>
                               </table>
                           </td>
                     </tr>
                        <tr>
                        <td valign="bottom" align="center" style="color: #76767a;font-family: "Helvetica";font-weight: 600;letter-spacing: 2px;text-transform: capitalize;font-size: 15px;line-height: 26px;height: 22px;word-break: break-all;">INVESTIGATOR</td>
                        </tr>
                    </table>
                    </td>


                        <td width="33.33%" valign="bottom">
                      <table width="80%" cellpadding="0" cellspacing="0" border="0" align="right">
                       <tr>

                        <td valign="top" align="center" style="color: #333;letter-spacing: 2px;text-transform: capitalize;border-bottom: 1px solid #76767a;height: 22px;word-break: break-all;">


                           <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr align="center">
                                <td>
                           ' . $requestParam["date"] . '

                                    </td>
                               </tr>
                               </table>
                           </td>
                        </tr>
                        <tr>
                        <td valign="bottom" align="center" style="color: #76767a;font-family: "Helvetica";font-weight: 600;letter-spacing: 2px;text-transform: capitalize;font-size: 15px;line-height: 26px;height: 22px;word-break: break-all;">DATE</td>
                        </tr>
                    </table>
                    </td>
                    </tr>
                </table>
                <!--/table 1.4-->
                <!--table 1.5-->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: #fff">
                    <tr>
                        <td valign="middle" align="left" height="70"></td>
                    </tr>
                </table>
                <!--/table 1.5-->
                <!--table 1.6-->
                ' . $list_arr . '
                <!--/table 1.6-->
            </td>
        </tr>
    </table>
     <!--/table 1.1-->
            </td>
        </tr>
    </table>
    <!--/table 1-->
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
                        'SetFooter' => ['
                        <div class="Footer"><p style="margin-top:2px;margin-right:75px;">Resources and Tools for Clinical Research Professionals</p><div class="Logo"><img src="' . $logo . '" alt="" style="z-index:99999;overflow:hidden;height: 70px;width: auto;margin-top:-60px;"></div>
                        </div>
                        '],
                    ],
                ]);
                $pdf->content = $html;
                $file_name = "note_" . rand(7, 100) . "_" . time() . ".pdf";
                $pdf->filename = "../../uploads/pdf_todolist/" . $file_name;
                $pdf->getApi()->setProtection(array(), $requestParam['pdf_password']);
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
                        $toDoListModel->pdf_password = md5($requestParam['pdf_password']);
                        $toDoListModel->pdf_file_name = Yii::$app->params['root_url'] . "/uploads/pdf_todolist/" . $file_name;
                        $toDoListModel->sent_mail_time = $requestParam['sent_mail_time'];
                        $toDoListModel->save(false);
                        $toDoList[] = $toDoListModel;
                    }
                }
                $amReponseParam = $toDoList;
                $ssMessage = 'Visit To do list PDF is successfully sent.';
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
        $amRequiredParams = array('user_id', 'protocol', 'investigator', 'date', 'to_patient_email', 'pdf_password', 'sent_mail_time');
        $amParamsResult = Common::checkRequestParameterKey($amData['request_param'], $amRequiredParams);
        // If any getting error in request paramter then set error message.
        if (!empty($amParamsResult['error'])) {
            $amResponse = Common::errorResponse($amParamsResult['error']);
            Common::encodeResponseJSON($amResponse);
        }
        $requestParam = $amData['request_param'];
        $dateinformate = $requestParam["date"];
        $date = date("d", strtotime($dateinformate));
        $month = date("m", strtotime($dateinformate));
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
            $list_arr = '<table width="100%" cellpadding="0px" cellspacing="0px" border="0" align="center">';
            $i = 1;
            foreach ($action_items as $key => $single_item) {

                $index = $i++;
                $checked = ($single_item['is_cheked'] == 1) ? "checked" : "unchecked";
                $list_arr .= '<tr>
        <td width="86.13%" style="border-right: 2px solid #008997;">

            <table width="100%" cellpadding="0px" cellspacing="0px" border="0" align="left" valign="top">
                <tr>

                    <td width="10px" valign="top">
                        <table border="0" align="left" valign="top">
                            <tr>


                                <td valign="top" align="left" style="color: #008997;font-size: 14px;line-height: 20px;font-weight: 600;letter-spacing: 1px;font-family: FrutingerBQRoman;">' . $index . '.</td>
                            </tr>
                        </table>


                    </td>



                    <td valign="center" width="500px" align="left" style="font-size: 14px;line-height: 20px;font-weight: 400;letter-spacing: 1px;font-family: FrutingerBQRoman;">


                        <table border="0" align="left" valign="bottom" width="100%">
                            <tr>
                                <td valign="bottom" style="font-size: 14px;border-bottom: 1px solid #5a5a5a;line-height: 20px;font-weight: 400;letter-spacing: 1px;font-family: FrutingerBQRoman;width: 100%;">' . $single_item['item'] . ' </td>

                            </tr>
                        </table>





                    </td>





                    <td valign="bottom" align="right" style="font-size: 14px;line-height: 20px;font-weight: 400;letter-spacing: 1px;font-family: FrutingerBQRoman;">

                        <table border="0" align="left" valign="bottom">
                            <tr>
                                <td valign="bottom" style="font-size: 14px;border-bottom: 1px solid #5a5a5a;line-height: 20px;font-weight: 400;letter-spacing: 1px;font-family: FrutingerBQRoman;">' . $date . '</td>
                                <td valign="bottom">/</td>
                                <td valign="bottom" style="font-size: 14px;border-bottom: 1px solid #5a5a5a;line-height: 20px;font-weight: 400;letter-spacing: 1px;font-family: FrutingerBQRoman;">' . $month . '</td>
                            </tr>
                        </table>

                    </td>




                </tr>
            </table>

        </td>

        <td align="center" valign="middle">

            <table cellpadding="0px" cellspacing="0px" border="0" align="center">
                <tr>

                    <td height="20px" width="20px" valign="middle" align="center" style="border:2px solid #008997;"> </td>



                </tr>



            </table>
        </td>

    </tr>';
            }
            $list_arr = $list_arr . " </table>";
            // $list = $requestParam['list'];

            $userModel = Users::findOne(['id' => $requestParam['user_id']]);
            $logo = Yii::$app->params['root_url'] . "/uploads/images/logo-orange.png";
            if (!empty($userModel)) {
                $fromEmail = $userModel->email;
                $html = '<!DOCTYPE html>
                    <html style="height:100%">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">

                    </head>
                    <body style="height:100%">
<!--table 1-->
    <table align="center" cellpadding="0px" cellspacing="0px" border="0" style="width: 100%;height: 100%;">
        <tr>
            <td valign="top">

                <!--table 1.1-->
    <table align="center" cellpadding="0px" cellspacing="0px" border="0" style="width: 100%;">
        <tr>
            <td>
                <!--table 1.2-->
             <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: #008997">
                    <tr>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                                <tr>
                                    <td valign="middle" align="left" height="20"></td>
                                </tr>
                                <tr>
                                    <td valign="bottom" align="center" style="font-size: 50px;letter-spacing: 2px;color: #fff;line-height: 35px;font-family: "FrutingerBQRoman";font-weight: 700;">
                                     ACTION ITEMS
                                    </td>
                                </tr>

                                <tr>
                                    <td valign="middle" align="left" height="20"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--/table 1.2-->

                <!--table 1.3-->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: #fff">
                    <tr>
                        <td valign="middle" align="left" height="60"></td>
                    </tr>
                </table>
                <!--/table 1.3-->

                <!--table 1.4-->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: #fff">

                    <tr>
                    <td width="33.33%" valign="bottom">
                      <table width="80%" cellpadding="0" cellspacing="0" border="0" align="left">
                       <tr>
                        <td valign="top" align="center" style="color: #333;letter-spacing: 2px;text-transform: capitalize;border-bottom: 1px solid #5a5a5a;height: 22px;word-break: break-all;">


                           <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr align="center">
                                <td>
                            ' . $requestParam["protocol"] . '

                                    </td>
                               </tr>
                               </table>
                           </td>
                        </tr>
                        <tr>
                        <td valign="bottom" align="center" style="color: #5a5a5a;font-family: "Helvetica";font-weight: 600;letter-spacing: 2px;text-transform: capitalize;font-size: 15px;line-height: 26px;height: 22px;word-break: break-all;">PROTOCOL</td>
                        </tr>
                    </table>
                    </td>

                        <td width="33.33%" valign="bottom">
                      <table width="80%" cellpadding="0" cellspacing="0" border="0" align="center">
                       <tr>

                        <td valign="top" align="center" style="color: #333;letter-spacing: 2px;text-transform: capitalize;border-bottom: 1px solid #5a5a5a;height: 22px;word-break: break-all;">


                           <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr align="center">
                                <td>
                           ' . $requestParam["investigator"] . '

                                    </td>
                               </tr>
                               </table>
                           </td>
                     </tr>
                        <tr>
                        <td valign="bottom" align="center" style="color: #5a5a5a;font-family: "Helvetica";font-weight: 600;letter-spacing: 2px;text-transform: capitalize;font-size: 15px;line-height: 26px;height: 22px;word-break: break-all;">INVESTIGATOR</td>
                        </tr>
                    </table>
                    </td>


                        <td width="33.33%" valign="bottom">
                      <table width="80%" cellpadding="0" cellspacing="0" border="0" align="right">
                       <tr>

                        <td valign="top" align="center" style="color: #333;letter-spacing: 2px;text-transform: capitalize;border-bottom: 1px solid #5a5a5a;height: 22px;word-break: break-all;">


                           <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr align="center">
                                <td>
                           ' . $requestParam["date"] . '

                                    </td>
                               </tr>
                               </table>
                           </td>
                        </tr>
                        <tr>
                        <td valign="bottom" align="center" style="color: #5a5a5a;font-family: "Helvetica";font-weight: 600;letter-spacing: 2px;text-transform: capitalize;font-size: 15px;line-height: 26px;height: 22px;word-break: break-all;">DATE</td>
                        </tr>
                    </table>
                    </td>
                    </tr>
                </table>
                <!--/table 1.4-->

                <!--table 1.5-->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: #fff">
                    <tr>
                        <td valign="middle" align="left" height="40"></td>
                    </tr>
                </table>
                <!--/table 1.5-->

<!--table 1.5.1-->

                            <table width="100%" cellpadding="5px" cellspacing="0px" border="0" align="center">
                            <tr>
                                <td  valign="middle" align="center" style="background: #008997;float: left;font-size: 14px;line-height: 20px;font-weight: 600;color: #fff;letter-spacing: 1px;font-family: FrutingerBQRoman;padding-right: 25px;padding-left: 25px;">Action Item : </td>
                                <td width="400px" valign="middle" align="center"></td>

                                <td  valign="middle" align="center" style="background: #008997;float: right;font-size: 14px;line-height: 20px;font-weight: 600;color: #fff;letter-spacing: 1px;font-family: FrutingerBQRoman;padding-right: 25px;padding-left: 25px;">Action Due : </td>
                            </tr>

                            <tr>
                            <td height="20px" valign="middle" align="center"></td>
                            <tr>

                            </table>
                            <!--/table 1.5.1-->


                            <!--table 1.5.2-->


                            <table width="100%" cellpadding="0px" cellspacing="0px" border="0" align="center">


                                <tr>

                                <td width="79.6%">


                                </td>




                            <td valign="middle" align="center" style="float: right;color: #008997;font-size: 14px;line-height: 20px;font-weight: 400;letter-spacing: 1px;font-family: FrutingerBQRoman;display: flex;align-items: center;justify-content: center;height: 40px;text-align:center">By <br> Date</td>

                            <td valign="middle" align="center" style="float: right;color: #008997;font-size: 14px;line-height: 20px;font-weight: 400;letter-spacing: 1px;font-family: FrutingerBQRoman;height: 40px;
                            border-left: 2px solid #008997;text-align:center">Prior to the <br> Next Visit</td>
                                </tr>


                            </table>


                            <!--/table 1.5.2-->



                            <!--table 1.5.3-->


                            <!----here------->
                             ' . $list_arr . '


                            <!--/table 1.5.3-->

                <!--table 1.6-->

                <!--/table 1.6-->
            </td>
        </tr>
    </table>
     <!--/table 1.1-->
            </td>
        </tr>
    </table>
    <!--/table 1-->
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
                    'options' => ['title' => "VISIT TO DO LIST"],
                    // call mPDF methods on the fly
                    'methods' => [
                        'SetHeader' => [''],
                        'SetFooter' => ['

                        <div class="Footer"><p style="margin-top:2px;margin-right:75px;">Resources and Tools for Clinical Research Professionals</p><div class="Logo"><img src="' . $logo . '" alt="" style="z-index:99999;overflow:hidden;height: 70px;width: auto;margin-top:-60px;"></div>
                        </div>


                        '],
                    ],
                ]);
                $pdf->content = $html;
                $file_name = "note_" . rand(7, 100) . "_" . time() . ".pdf";
                $pdf->filename = "../../uploads/pdf_action_items/" . $file_name;
                $pdf->getApi()->setProtection(array(), $requestParam['pdf_password']);
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
                        $toDoListModel->pdf_password = !empty($requestParam['pdf_password']) ? md5($requestParam['pdf_password']) : "";
                        $toDoListModel->to_patient_email = $requestParam['to_patient_email'];
                        $toDoListModel->pdf_file_name = Yii::$app->params['root_url'] . "/uploads/pdf_action_items/" . $file_name;
                        $toDoListModel->sent_mail_time = $requestParam['sent_mail_time'];
                        $toDoListModel->save(false);
                        $toDoList[] = $toDoListModel;
                    }

                }

                $amReponseParam = $toDoList;
                $ssMessage = 'Action Item PDF is successfully sent.';
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
        $amRequiredParams = array('user_id', 'my_notes', 'to_patient_email', 'pdf_password');
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

            $checkedbox = Yii::$app->params['root_url'] . "/uploads/images/icon_checked.jpg";
            $unchecked = Yii::$app->params['root_url'] . "/uploads/images/icon_unchecked.jpg";

            $list_arr = '<tr>
                            <td valign="middle" align="left">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">';
            foreach ($protocol_array as $key => $single_item) {
//                $checked = ($single_item['is_checked'] == 1) ? true : false;

                if ($checked = $single_item['is_checked'] == 1) {
                    $list_arr .= '<tr>
                            <td valign="middle" align="center" height="20px" width="20px"><img src="' . $checkedbox . '" alt="" style="height:20px;width:20px"></td>
                            <td width="10px" height="20px"></td>
                            <td style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #f37420;font-family: FRUTBL_;">' . $single_item['text'] . ' :</td>
                            </tr>
                            <tr>
                            <td valign="middle" align="left" height="12"></td>
                            </tr>';

                } else if ($checked = $single_item['is_checked'] == 0) {

                    $list_arr .= '<tr>
                            <td valign="middle" align="center" height="20px" width="20px"><img src="' . $unchecked . '" alt="" style="height:20px;width:20px"></td>
                            <td width="10px" height="20px"></td>
                            <td style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: 400;color: #f37420;font-family: FRUTBL_;">' . $single_item['text'] . ' :</td>
                            </tr>
                            <tr>
                            <td valign="middle" align="left" height="12"></td>
                            </tr>';
                }
            }
            $list_arr = $list_arr . "</table>

                            </td>
                            </tr>";
            // $list = $requestParam['list'];
            $userModel = Users::findOne(['id' => $requestParam['user_id']]);
            $logo = Yii::$app->params['root_url'] . "/uploads/images/logo-orange.png";
            $header = Yii::$app->params['root_url'] . "/uploads/images/pdf_header2.png";
            $text = Yii::$app->params['root_url'] . "/uploads/images/text4.jpg";
            $footer = Yii::$app->params['root_url'] . "/uploads/images/footer.jpg";

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

                        <!--table 1-->
    <table align="center" cellpadding="0px" cellspacing="0px" border="0" style="width: 100%;height: 100%;">
        <tr>
            <td valign="top">
                <!--table 1.1-->
                <table align="center" cellpadding="0px" cellspacing="0px" border="0" style="width: 100%;">
                    <tr>
                        <td>
                            <!--table 1.2-->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: url(' . $header . ');background-repeat: no-repeat;background-size: contain;background-position: center;height: auto;">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                                                <tbody>
                                                    <tr>
                                                        <td valign="middle" align="left" height="122px" style="width: 100%;"></td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--/table 1.2-->
                            <!--table 1.3-->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background: #fff">
                                <tr>
                                    <td valign="middle" align="left" height="20"></td>
                                </tr>
                            </table>
                            <!--/table 1.3-->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr>
                            <td valign="middle" align="center" style="font-size: 16px;line-height: 22px;letter-spacing: 1px;font-weight: 400;color: #10436e;font-family: "FRUTBL_";">You have just been assigned to a new study. How should you prepare and get up to speed</td>
                            </tr>
                            <tr>
                            <td valign="middle" align="center" style="font-size: 16px;line-height: 22px;letter-spacing: 1px;font-weight: 400;color: #10436e;font-family: "FRUTBL_";"> What key information do you need to know about the protocol?</td>
                            </tr>
                            <tr>
                            <td valign="middle" align="left" height="20"></td>
                            </tr>
                            <tr>
                            <td valign="middle" align="center" style="font-size: 10px;line-height: 16px;letter-spacing: 1px;font-weight: 400;color: #10436e;font-family: "FRUTBL_";"> As you start, review these sections and either jot down main points here or flag the section in the protocol for easy future reference. Us</td>
                            </tr>
                            <tr>
                            <td valign="middle" align="center" style="font-size: 10px;line-height: 16px;letter-spacing: 1px;font-weight: 400;color: #10436e;font-family: "FRUTBL_";"> the check boxes so you know which areas you have reviewed. Once you have completed this outline of key sections, go back and review</td>
                            </tr>
                            <tr>
                            <td valign="middle" align="center" style="font-size: 10px;line-height: 16px;letter-spacing: 1px;font-weight: 400;color: #10436e;font-family: "FRUTBL_";"> the entire protocol from start to finish and add any study specific nuances to the “My Notes” section on the next page.</td>
                            </tr>
                              <tr>
                            <td valign="middle" align="left" height="20"></td>
                            </tr>
                                <tr>
                            <td valign="middle" align="center" style="font-size: 10px;line-height: 16px;letter-spacing: 1px;font-weight: 400;color: #10436e;font-family: "FRUTBL_";"> As a bonus tip, also review the informed consent template for this study so you can learn about the study from the patients prospective.</td>
                            </tr>
                            <tr>
                            <td valign="middle" align="left" height="30"></td>
                            </tr>
                              ' . $list_arr . '

                         </tr>
                            <tr>
                            <td valign="middle" align="left" height="20"></td>
                            </tr>
                            <tr>
                            <td align="center" valign="middle" width="100%">
                            <table width="70%" cellpadding="5px" cellspacing="0" border="0" align="center" style="background:#d1d2d4;border-radius:30px;background: url(' . $text . ');background-repeat: no-repeat;background-size: contain;background-position: center;height: auto;">
                            <tr>
                            <td align="center" valign="middle" height="70px">
                            </td>
                            </tr>
                            </table>
                            </td>
                            </tr>
                            <tr>
                            <td align="center" valign="middle" height="20px">
                            </td>
                            </tr>
                             <tr>
                            <td align="center" valign="middle" style="font-size: 14px;line-height: 20px;letter-spacing: 1px;font-weight: bold;color: #f37420;font-family: FRUTBL_;">
                            My Notes
                            </td>
                            </tr>
                                 <tr>
                            <td align="center" valign="middle" style="height:10px;">
                            </td>
                            </tr>
                             <tr>
                            <td align="left" valign="middle" style="font-size: 12px;line-height: 15px;letter-spacing: 1px;font-weight: bold;color: #5A5A5A;font-family: FrutingerBQRoman;">' . $requestParam['my_notes'] . '
                            </td>
                            </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--/table 1.1-->
            </td>
        </tr>
    </table>
    <!--/table 1-->

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
                    'cssFile' => '@api/web/css/clinical_study.css',
                    // set mPDF properties on the fly
                    'options' => ['title' => "Reviewing a Clinical Study Protocol"],
                    // call mPDF methods on the fly
                    'methods' => [
                        'SetHeader' => [''],
                        'SetFooter' => ['
                        <div class="Footer"></div>

                        ', ],
                    ],
                ]);
                $pdf->content = $html;
                $file_name = "note_" . rand(7, 100) . "_" . time() . ".pdf";
                $pdf->filename = "../../uploads/pdf_clinical_study_protocol/" . $file_name;
                $pdf->getApi()->setProtection(array(), $requestParam['pdf_password']);
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
                        $clinicalStudyModel->pdf_password = !empty($requestParam['pdf_password']) ? md5($requestParam['pdf_password']) : "";

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
                $sentNotesModel->late_entry = !empty($note['late_entry']) ? $note['late_entry'] : "0";
                $sentNotesModel->please_complete_data = (!empty($note['please_complete_data']) && ($note['note_id'] == '9')) ? $note['please_complete_data'] : [];
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
                    $ttt['patient_id'] = !empty($ttt['patient_id']) ? $ttt['patient_id'] : "";
                    $ttt['please_complete_data'] = !empty($ttt['please_complete_data']) ? json_decode($ttt['please_complete_data']) : [];
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
                            $ttt['patient_id'] = !empty($ttt['patient_id']) ? $ttt['patient_id'] : "";
                            $ttt['sent_mail_time'] = !empty($ttt['sent_mail_time']) ? $ttt['sent_mail_time'] : $ttt['created_at'];
                            $ttt['please_complete_data'] = !empty($ttt['please_complete_data']) ? json_decode($ttt['please_complete_data']) : [];
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

    public function actionGetActionItemsList()
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
            $usersSentActionItems = ActionItems::find()->select("DATE(created_at) dateOnly")->where(['user_id' => $requestParam['user_id']])->asArray()->groupBy('dateOnly')->all();
            if (!empty($usersSentActionItems)) {
                foreach ($usersSentActionItems as $key => $value) {
                    $getDataDateWise = ActionItems::find()->where(['DATE(created_at)' => $value['dateOnly'], 'user_id' => $requestParam['user_id']])->asArray()->all();
                    $amReponseParam[$key]['date'] = $value['dateOnly'];
                    array_walk($getDataDateWise, function ($arr) use (&$amResponseData) {
                        $ttt = $arr;
                        $ttt['action_items'] = json_decode($ttt['action_items']);
                        $ttt['patient_id'] = !empty($ttt['patient_id']) ? $ttt['patient_id'] : "";
                        $ttt['pdf_password'] = !empty($ttt['pdf_password']) ? $ttt['pdf_password'] : "";
                        $ttt['sent_mail_time'] = !empty($ttt['sent_mail_time']) ? $ttt['sent_mail_time'] : $ttt['created_at'];

                        $amResponseData[] = $ttt;
                        return $amResponseData;
                    });
                    $amReponseParam[$key]['datewiseData'] = $amResponseData;
                    $ssMessage = 'Action Items List.';
                    $amResponse = Common::successResponse($ssMessage, $amReponseParam);
                }
            } else {
                $amReponseParam = [];
                $ssMessage = 'Action Items not found.';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);
            }
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }

    public function actionGetToDoList()
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
            $usersSentToDoLIst = TodoList::find()->select("DATE(created_at) dateOnly")->where(['user_id' => $requestParam['user_id']])->asArray()->groupBy('dateOnly')->all();
            if (!empty($usersSentToDoLIst)) {
                foreach ($usersSentToDoLIst as $key => $value) {
                    $getDataDateWise = TodoList::find()->where(['DATE(created_at)' => $value['dateOnly'], 'user_id' => $requestParam['user_id']])->asArray()->all();
                    $amReponseParam[$key]['date'] = $value['dateOnly'];
                    array_walk($getDataDateWise, function ($arr) use (&$amResponseData) {
                        $ttt = $arr;
                        $ttt['list'] = json_decode($ttt['list']);
                        $ttt['patient_id'] = !empty($ttt['patient_id']) ? $ttt['patient_id'] : "";
                        $ttt['pdf_password'] = !empty($ttt['pdf_password']) ? $ttt['pdf_password'] : "";
                        $ttt['sent_mail_time'] = !empty($ttt['sent_mail_time']) ? $ttt['sent_mail_time'] : $ttt['created_at'];

                        $amResponseData[] = $ttt;
                        return $amResponseData;
                    });
                    $amReponseParam[$key]['datewiseData'] = $amResponseData;

                    $ssMessage = 'To Do List';
                    $amResponse = Common::successResponse($ssMessage, $amReponseParam);
                }

            } else {
                $amReponseParam = [];
                $ssMessage = 'To Do List not found.';
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);
            }
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }

    public function actionDeleteOrArchiveToDoList()
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
            $todolist = TodoList::find()->where(['id' => $requestParam['id']])->one();
            $amReponseParam = [];
            if (!empty($todolist)) {
                if (Yii::$app->params['action'][$requestParam['action']] == "delete") {
                    $todolist->delete();
                    $ssMessage = 'To do list deleted successfully.';
                    $amResponse = Common::successResponse($ssMessage, $amReponseParam);
                } else if (Yii::$app->params['action'][$requestParam['action']] == "archive") {
                    $todolist->is_archive = "1";
                    $todolist->save(false);
                    $ssMessage = 'To do list archived successfully.';
                } else if (Yii::$app->params['action'][$requestParam['action']] == "un_archive") {
                    $todolist->is_archive = "0";
                    $todolist->save(false);
                    $ssMessage = 'To do list un archived successfully.';
                }
                $usersSentToDoLIst = TodoList::find()->select("DATE(created_at) dateOnly")->where(['user_id' => $requestParam['user_id']])->asArray()->groupBy('dateOnly')->all();
                if (!empty($usersSentToDoLIst)) {
                    foreach ($usersSentToDoLIst as $key => $value) {
                        $getDataDateWise = TodoList::find()->where(['DATE(created_at)' => $value['dateOnly'], 'user_id' => $requestParam['user_id']])->asArray()->all();
                        $amReponseParam[$key]['date'] = $value['dateOnly'];
                        array_walk($getDataDateWise, function ($arr) use (&$amResponseData) {
                            $ttt = $arr;
                            $ttt['list'] = json_decode($ttt['list']);
                            $ttt['patient_id'] = !empty($ttt['patient_id']) ? $ttt['patient_id'] : "";
                            $ttt['sent_mail_time'] = !empty($ttt['sent_mail_time']) ? $ttt['sent_mail_time'] : $ttt['created_at'];
                            $ttt['pdf_password'] = !empty($ttt['pdf_password']) ? $ttt['pdf_password'] : "";
                            $amResponseData[] = $ttt;
                            return $amResponseData;
                        });
                        $amReponseParam[$key]['datewiseData'] = $amResponseData;
                    }

                }

                $amResponse = Common::successResponse($ssMessage, $amReponseParam);

            } else {
                $ssMessage = 'Invalid to do list id';
                $amResponse = Common::errorResponse($ssMessage);
            }
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }

    public function actionDeleteOrArchiveActionItems()
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
            $actionItems = ActionItems::find()->where(['id' => $requestParam['id']])->one();
            $amReponseParam = [];
            if (!empty($actionItems)) {
                if (Yii::$app->params['action'][$requestParam['action']] == "delete") {
                    $actionItems->delete();
                    $ssMessage = 'Action Item deleted successfully.';
                    $amResponse = Common::successResponse($ssMessage, $amReponseParam);
                } else if (Yii::$app->params['action'][$requestParam['action']] == "archive") {
                    $actionItems->is_archive = "1";
                    $actionItems->save(false);
                    $ssMessage = 'Action Item archived successfully.';
                } else if (Yii::$app->params['action'][$requestParam['action']] == "un_archive") {
                    $actionItems->is_archive = "0";
                    $actionItems->save(false);
                    $ssMessage = 'Action Item un archived successfully.';
                }
                $usersSentActionItems = ActionItems::find()->select("DATE(created_at) dateOnly")->where(['user_id' => $requestParam['user_id']])->asArray()->groupBy('dateOnly')->all();
                if (!empty($usersSentActionItems)) {
                    foreach ($usersSentActionItems as $key => $value) {
                        $getDataDateWise = ActionItems::find()->where(['DATE(created_at)' => $value['dateOnly'], 'user_id' => $requestParam['user_id']])->asArray()->all();
                        $amReponseParam[$key]['date'] = $value['dateOnly'];
                        array_walk($getDataDateWise, function ($arr) use (&$amResponseData) {
                            $ttt = $arr;
                            $ttt['action_items'] = json_decode($ttt['action_items']);
                            $ttt['patient_id'] = !empty($ttt['patient_id']) ? $ttt['patient_id'] : "";
                            $ttt['sent_mail_time'] = !empty($ttt['sent_mail_time']) ? $ttt['sent_mail_time'] : $ttt['created_at'];
                            $ttt['pdf_password'] = !empty($ttt['pdf_password']) ? $ttt['pdf_password'] : "";
                            $amResponseData[] = $ttt;
                            return $amResponseData;
                        });
                        $amReponseParam[$key]['datewiseData'] = $amResponseData;
                    }
                }
                $amResponse = Common::successResponse($ssMessage, $amReponseParam);

            } else {
                $ssMessage = 'Invalid action items id';
                $amResponse = Common::errorResponse($ssMessage);
            }
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
}
