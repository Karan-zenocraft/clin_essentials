<?php
namespace common\components;

use common\models\EmailFormat;
use common\models\LeaveQuota;
use common\models\Milestones;
use common\models\ProblemSets;
use common\models\Projects;
use common\models\Questions;
use common\models\RestaurantFloors;
use common\models\Restaurants;
use common\models\SystemConfig;
use common\models\Tasks;
use common\models\Timesheet;
use common\models\Users;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class Common
{

    public static $amPostData;
    /**
     * function: sendMail()
     * For send apple push notification.
     *
     * @param string  $ssToEmail
     * @param string  $asFromEmail
     * @param string  $ssSubject
     * @param string  $ssBody
     * @param template $attach
     */
    public static function sendMailApproveReject($ssToEmail, $asFromEmail, $ssSubject, $ssBody, $attach = false, $snSuperAdminEmail = '', $snHREmail = '', $snOtherEmailAddress = '')
    {
        if (!empty($snOtherEmailAddress) && !empty($snHREmail)) {
            $result = Yii::$app->mail->compose()
                ->setFrom([$asFromEmail])
                ->setTo($ssToEmail)
                ->setCc([$snSuperAdminEmail => "Project Manager", $snHREmail, $snOtherEmailAddress])
                ->setSubject($ssSubject)
                ->setHtmlBody($ssBody)
                ->send();
            return true;
        } else if (empty($snHREmail) && !empty($snOtherEmailAddress)) {
            $result = Yii::$app->mail->compose()
                ->setFrom([$asFromEmail])
                ->setTo($ssToEmail)
                ->setCc([$snSuperAdminEmail => "Project Manager", $snOtherEmailAddress])
                ->setSubject($ssSubject)
                ->setHtmlBody($ssBody)
                ->send();
            return true;
        } else {
            $result = Yii::$app->mail->compose()
                ->setFrom([$asFromEmail])
                ->setTo($ssToEmail)
                ->setCc([$snSuperAdminEmail => "Project Manager", $snHREmail])
                ->setSubject($ssSubject)
                ->setHtmlBody($ssBody)
                ->send();
            return true;
        }
    }

    public static function sendMail($ssToEmail, $asFromEmail, $ssSubject, $ssBody, $attach = false, $snSuperAdminEmail = '')
    {
        $result = Yii::$app->mail->compose()
            ->setFrom([$asFromEmail])
            ->setTo($ssToEmail)
            ->setSubject($ssSubject)
            ->setHtmlBody($ssBody)
            ->send();
        return true;
    }

    public static function sendMailToUser($ssToEmail, $asFromEmail, $ssSubject, $ssBody, $attach = false)
    {
        $result = Yii::$app->mail->compose()
            ->setFrom([$asFromEmail => "Clian Essentials Admin"])
            ->setTo($ssToEmail)
            ->setSubject($ssSubject)
            ->setHtmlBody($ssBody)
            ->send();
        return true;
    }

    public static function sendMailToUserWithAttachment($ssToEmail, $asFromEmail, $ssSubject, $ssBody, $attach = false)
    {
        $result = Yii::$app->mail->compose()
            ->setFrom([$asFromEmail])
            ->setTo($ssToEmail)
            ->setSubject($ssSubject)
            ->setHtmlBody($ssBody)
            ->attach($attach)
            ->send();
        return true;
    }

    /**
     * function: MailTemplate()
     * For send mail.
     *
     * @param Array   $replaceString
     * @param string  $Url
     */
    public static function MailTemplate($replaceString, $body)
    {
        $logo_front_url = Yii::$app->params['site_url'] . Yii::$app->request->baseUrl;
        $logo_img_url = Yii::$app->params['site_url'] . Yii::$app->request->baseUrl . "/img/Chiefs_rs_logo.png";
        $logo_url = (!empty(Yii::$app->urlManagerFrontEnd)) ? Yii::$app->urlManagerFrontEnd->createUrl('site/index') : '';
        /* $url = '';
        $url_logo = ''; */
        if (!empty($replaceString)) {
            foreach ($replaceString as $key => $value) {
                $replacekey[] = $key;
                $replacevalue[] = $value;
            }
        }

        $replacekey[] = '{logo_front_url}';
        $replacekey[] = '{logo_img_url}';
        $replacekey[] = '{logo_url}';
        $replacevalue[] = $logo_front_url;
        $replacevalue[] = $logo_img_url;
        $replacevalue[] = $logo_url;
        /* p($replacekey,0);
        p($replacevalue,0); */
        $result = str_replace(
            $replacekey, $replacevalue, $body
        );
        //  p($result);
        return $result;
    }

    /**
     * Generate randome 7 character password.
     */
    public static function generatePassword()
    {
        $length = 7;
        $strength = 0;
        $vowels = 'aeuy';
        $consonants = 'bdghjmnpqrstvz';
        if ($strength & 1) {
            $consonants .= 'BDGHJLMNPQRSTVWXZ';
        }
        if ($strength & 2) {
            $vowels .= "AEUY";
        }
        if ($strength & 4) {
            $consonants .= '23456789';
        }
        if ($strength & 8) {
            $consonants .= '@#$%';
        }

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        return $password;
    }

    public static function array_pluck($arr, $toPlucked)
    {
        return array_map(function ($arr) use ($toPlucked) {
            return $arr["$toPlucked"];
        }, $arr);
    }

    /**
     * Convert Html to pdf
     *
     * @param html    $html
     * @return pdf
     */
    public static function ConvertArrayExcel($results, $rolename)
    {
        //$pdf = Yii::$app->pdf->PDF('/usr/bin/wkhtmltopdf');

        $fileName = $rolename . 'info.xls';
        header("Content-type: application/vnd.ms-excel; name='excel'");
        header('Content-Description: File Transfer');
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Pragma: public");
        $fh = fopen('php://output', 'w');

        $headerDisplayed = false;

        foreach ($results as $data) {

            // Add a header row if it hasn't been added yet
            if (!$headerDisplayed) {
                // Use the keys from $data as the titles
                fputcsv($fh, array_keys($data));
                $headerDisplayed = true;
            }
            // Put the data into the stream
            fputcsv($fh, $data);
        }
        // Close the file
        fclose($fh);

        // Make sure nothing else is sent, our file is done
        return true;
    }

    /**
     * Array To CSV - Download CSV File
     *
     * @param array   $results
     * @return CSV File
     */
    public static function ConvertArrayCsv($results, $rolename)
    {
        $fileName = $rolename . 'info.csv';
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; charset=ISO-8859-2;filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");
        $fh = fopen('php://output', 'w');

        $headerDisplayed = false;

        foreach ($results as $data) {

            // Add a header row if it hasn't been added yet
            if (!$headerDisplayed) {
                // Use the keys from $data as the titles
                fputcsv($fh, array_keys($data));
                $headerDisplayed = true;
            }
            // Put the data into the stream
            fputcsv($fh, $data);
        }
        // Close the file
        fclose($fh);

        // Make sure nothing else is sent, our file is done
        return true;
    }
    /*
     * Set designing for view milestones button
     */

    public static function template_view_gallery_button($url, $model, $title, $flag = false)
    {
        if ($flag == 1) {
            return Html::a('<i class="icon-glass icon-white"></i>', $url, [
                'title' => Yii::t('yii', $title),
                'class' => 'btn btn-primary btn-small',
                //'target' => '_blanck'
            ]);
        }
        if ($flag == 2) {
            return Html::a('<i class="icon-camera icon-white"></i>', $url, [
                'title' => Yii::t('yii', $title),
                'class' => 'btn btn-primary btn-small',
                //'target' => '_blanck'
            ]);
        }
        if ($flag == 3) {
            return Html::a('<i class="icon-picture icon-white"></i>', $url, [
                'title' => Yii::t('yii', $title),
                'class' => 'btn btn-primary btn-small',
                //'target' => '_blanck'
            ]);
        }
        if ($flag == 4) {
            return Html::a('<i class="icon-list icon-white"></i>', $url, [
                'title' => Yii::t('yii', $title),
                'class' => 'btn btn-primary btn-small',
                //'target' => '_blanck'
            ]);
        }
    }
    /*
     * Set designing for view tasks button
     */

    public static function template_view_tasks_button($url, $model)
    {
        return Html::a('<i class="fa fa-external-link"></i> View Tasks', $url, [
            'title' => Yii::t('yii', 'View'),
            'class' => 'btn btn-primary',
            //'target' => '_blanck'
        ]);
    }
    /*
     * Set designing for view timesheets button
     */

    public static function template_view_timesheets_button($url, $model)
    {
        return Html::a('<i class="fa fa-external-link"></i> View Timesheets', $url, [
            'title' => Yii::t('yii', 'View'),
            'class' => 'btn btn-primary',
            //'target' => '_blanck'
        ]);
    }
    /*
     * Set designing for Grideview update permission button
     */

    public static function template_update_permission_button($url, $model, $flag)
    {

        return Html::a('<i class="icon-time icon-white"></i> ', $url, [
            'title' => Yii::t('yii', "Edit Restaurant's working Hours"),
            'class' => 'btn btn-primary btn-small colorbox_popup',
            'onClick' => 'javascript:openColorBox(1090,820);',
        ]);

    }
    /*
     * Set designing for Grideview update button
     */

    public static function template_update_button($url, $model, $flag = false)
    {
        if ($flag == 2) {
            return Html::a('<i class="icon-pencil icon-white"></i>', $url, [
                'title' => Yii::t('yii', 'Edit'),
                'class' => '',
            ]);
        }
        if ($flag == 1) {
            return Html::a('<i class="icon-pencil icon-white"></i>', $url, [
                'title' => Yii::t('yii', 'Edit'),
                'class' => 'btn btn-primary btn-small',
            ]);
        }
        return Html::a('<i class="icon-pencil icon-white"></i> Edit', $url, [
            'title' => Yii::t('yii', 'Edit'),
            'class' => 'btn btn-primary',
        ]);
    }
    public static function template_update_tag_button($url, $model, $flag = false)
    {
        if ($flag == 1) {
            return Html::a('<i class="icon-pencil icon-white"></i>', $url, [
                'title' => Yii::t('yii', 'Edit'),
                'class' => 'btn btn-primary btn-small colorbox_popup',
                'onclick' => 'javascript:openColorBox(420,580);',
            ]);
        }
        if ($flag == 2) {
            return Html::a('<i class="icon-pencil icon-white"></i>', $url, [
                'title' => Yii::t('yii', 'Edit'),
                'class' => 'btn btn-primary btn-small colorbox_popup',
                'onclick' => 'javascript:openColorBox(420,400);',
            ]);
        }
        if ($flag == 3) {
            return Html::a('<i class="icon-pencil icon-white"></i>', $url, [
                'title' => Yii::t('yii', 'Edit'),
                'class' => 'btn btn-primary btn-small colorbox_popup',
                'onclick' => 'javascript:openColorBox(400,600);',
            ]);
        }
        return Html::a('<i class="icon-pencil icon-white"></i> Edit', $url, [
            'title' => Yii::t('yii', 'Edit'),
            'class' => 'btn btn-primary',
        ]);
    }

    /*
     * Set designing for Grideview delete button
     */

    public static function template_delete_button($url, $model, $confirmmessage = false, $flag = false)
    {
        $confirmmessage = $confirmmessage ?: "Are you sure you want to delete it?";
        if ($flag == 1) {
            return Html::a('<i class="icon-remove icon-white"></i>', $url, [
                'title' => Yii::t('yii', 'Delete'),
                'class' => 'btn btn-danger btn-small deleteGlobalButton',
                'data-confirm' => $confirmmessage,
                "data-method" => "post",
                "data-pjax" => "0",
            ]);
        }
        return Html::a('<i class="icon-remove icon-white"></i> Delete', $url, [
            'title' => Yii::t('yii', 'Delete'),
            'class' => 'btn btn-danger deleteGlobalButton',
            'data-confirm' => $confirmmessage,
            "data-method" => "post",
            "data-pjax" => "0",
        ]);
    }

    public static function template_update_leave_button($url, $model)
    {
        if ($model->leave_type == Yii::$app->params['full_day_leave_type']) {
            if ($model->leave_status == Yii::$app->params['requested_leave_status']) {
                if ($model->end_date >= date("Y-m-d")) {
                    return Html::a('<i class="icon-pencil icon-white"></i> Update', $url, [
                        'title' => Yii::t('yii', 'Update'),
                        'class' => 'btn btn-primary',
                    ]);

                } else {
                    return '';
                }
            }
        } else {
            if ($model->leave_status == Yii::$app->params['requested_leave_status']) {
                if ($model->start_date >= date("Y-m-d")) {
                    return Html::a('<i class="icon-pencil icon-white"></i> Update', $url, [
                        'title' => Yii::t('yii', 'Update'),
                        'class' => 'btn btn-primary',
                    ]);

                } else {
                    return '';
                }
            }

        }

    }
    public static function template_delete_leave_button($url, $model, $confirmmessage = false)
    {
        if ($model->leave_type == Yii::$app->params['full_day_leave_type']) {
            if ($model->end_date >= date("Y-m-d")) {
                if ($model->leave_status == Yii::$app->params['requested_leave_status'] || $model->leave_status == Yii::$app->params['approved_leave_status']) {
                    $confirmmessage = $confirmmessage ?: "Are you sure you want to cancel leave this application?";
                    return Html::a('<i class="icon-remove icon-white"></i> Cancel', $url, [
                        'title' => Yii::t('yii', 'Cancel'),
                        'class' => 'btn btn-danger deleteGlobalButton',
                        'data-confirm' => $confirmmessage,
                        "data-method" => "post",
                        "data-pjax" => "0",
                    ]);
                } else {
                    return '';
                }
            }
        } else {
            if ($model->start_date >= date("Y-m-d")) {
                if ($model->leave_status == Yii::$app->params['requested_leave_status'] || $model->leave_status == Yii::$app->params['approved_leave_status']) {
                    $confirmmessage = $confirmmessage ?: "Are you sure you want to cancel leave this application?";
                    return Html::a('<i class="icon-remove icon-white"></i> Cancel', $url, [
                        'title' => Yii::t('yii', 'Cancel'),
                        'class' => 'btn btn-danger deleteGlobalButton',
                        'data-confirm' => $confirmmessage,
                        "data-method" => "post",
                        "data-pjax" => "0",
                    ]);
                } else {
                    return '';
                }
            }

        }
    }
    /*
     * Set designing for Grideview approve button
     */

    public static function template_approve_button($url, $model)
    {
        if ($model->leave_status == Yii::$app->params['requested_leave_status'] || $model->leave_status == Yii::$app->params['rejected_leave_status']) {
            if ($model->tl_user_id == Yii::$app->user->id) {
                return Html::a('<i class="icon-ok icon-white"></i> Approve', $url, [
                    'title' => Yii::t('yii', 'Approve'),
                    'class' => 'btn btn-small btn-primary colorbox_popup',
                    'onClick' => 'javascript:openColorBox(600,370);',
                ]);
            }
        } else {
            return '';
        }
    }

    /*
     * Set designing for Grideview reject button
     */

    public static function template_reject_button($url, $model, $confirmmessage = false)
    {
        /* if ( $model->start_date >= date( "Y-m-d" ) ) {*/
        if ($model->leave_status == Yii::$app->params['requested_leave_status'] || $model->leave_status == Yii::$app->params['approved_leave_status']) {
            if ($model->tl_user_id == Yii::$app->user->id) {
                $confirmmessage = $confirmmessage ?: "Are you sure you want to reject the leave application?";
                return Html::a('<i class="icon-remove icon-white"></i> Reject', $url, [
                    'title' => Yii::t('yii', 'Reject'),
                    'class' => 'btn btn-small btn-danger deleteGlobalButton reject_leave',
                    //'data-confirm' => $confirmmessage,
                    "data-method" => "post",
                    "data-pjax" => "0",
                ]);
            }
        } else {
            return '';
        }
        /*}*/
    }

    /*
     * Set designing for Grideview view button
     */

    public static function template_view_button($url, $model)
    {
        return Html::a('<i class="fa fa-external-link"></i> View', $url, [
            'title' => Yii::t('yii', 'View'),
            'class' => 'btn yellow margin-bottom-5',
            'target' => '_blanck',
        ]);
    }
    /*
     * Set designing for Grideview Download button
     */

    public static function template_download_document_button($url, $model, $title)
    {
        return Html::a('<i class="icon-download-alt icon-white"></i>' . $title, $url, [
            'title' => Yii::t('yii', $title),
            'class' => 'btn btn-primary view_milestones',
        ]);
    }
    //VIEW DOCUMENT BUTTON//
    public static function template_view_document_button($url, $model, $title)
    {
        return Html::a($title, $url, [
            'title' => Yii::t('yii', $title),
            'class' => 'btn btn-primary view_milestones',
            'target' => '_blanck',
        ]);
    }

    //Change Status.
    public static function template_user_status_change_button($model, $labelname, $status, $message, $sendMail = false)
    {
        // set classname for decline user status
        if ($status == '2') {
            $labelicon = 'fa-times';
            $className = 'grey-cascade';
        }
        // set classname for Approved user status
        if ($status == '1') {
            $labelicon = 'fa-check';
            $className = 'green';
        }
        // set redirect url
        $url = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        // set confirmation message
        $confirmmessage = 'Are you sure you want to make it ' . $labelname . '?';

        return Html::a('<i class="fa ' . $labelicon . '"></i> ' . $labelname, Yii::$app->urlManager->createUrl(['users/change-user-status', 'id' => $model->id, 'url' => $url, 'message' => $message, 'status' => $status, 'sendmail' => '1']), [
            'title' => Yii::t('yii', $labelname),
            // 'class' => 'btn yellow margin-bottom-5 confirmOkButton',
            'data-confirm' => $confirmmessage,
            "data-method" => "post",
            "data-pjax" => "0",
            'class' => 'btn margin-bottom-5 confirmOkButton ' . $className,
        ]);
    }

    /*
     * Change teacher status for Active or Inactive.
     */

    public static function template_status_button($model, $message)
    {
        $id = $model->id;
        $modelname = $model->className();
        $url = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        if ($model->status == '1') {
            $confirmmessage = "Are you sure you want to make it In-Active ?";
            return Html::a('<i class="fa fa-times"></i> In-Active', Yii::$app->urlManager->createUrl(['site/change-status', 'id' => $id, 'modelname' => $modelname, 'url' => $url, 'message' => $message, 'sendmail' => '1']), [
                'title' => Yii::t('yii', 'In-Active'),
                'data-confirm' => $confirmmessage,
                "data-method" => "post",
                "data-pjax" => "0",
                'class' => 'btn grey-cascade margin-bottom-5 confirmOkButton',
            ]);
        } else {
            $confirmmessage = "Are you sure you want to make it Active ?";
            return Html::a('<i class="fa fa-check"></i> Active', Yii::$app->urlManager->createUrl(['site/change-status', 'id' => $id, 'modelname' => $modelname, 'url' => $url, 'message' => $message]), [
                'title' => Yii::t('yii', 'Active'),
                'data-confirm' => $confirmmessage,
                "data-method" => "post",
                "data-pjax" => "0",
                'class' => 'btn green margin-bottom-5 confirmOkButton',
            ]);
        }
    }

    /*
     * Change teacher status for Approved or declined.
     */

    public static function template_acceptance_status_button($model, $message, $sendmail = false)
    {
        $id = $model->id;
        $modelname = $model->className();
        $url = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        if ($model->status == '1') {
            $confirmmessage = "Are you sure you want to make it Decline ?";
            return Html::a('<i class="fa fa-times"></i> Decline', Yii::$app->urlManager->createUrl(['users/change-user-status', 'id' => $model->id, 'url' => $url, 'message' => $message, 'status' => '2', 'sendmail' => $sendmail]), ['title' => Yii::t('yii', 'Decline'),
                'data-confirm' => $confirmmessage,
                "data-method" => "post",
                "data-pjax" => "0",
                'class' => 'btn grey-cascade margin-bottom-5 confirmOkButton',
            ]);
        } else {
            $confirmmessage = "Are you sure you want to make it Approve ?";
            return Html::a('<i class="fa fa-check"></i> Approve', Yii::$app->urlManager->createUrl(['users/change-user-status', 'id' => $id, 'modelname' => $modelname, 'url' => $url, 'message' => $message, 'status' => '1', 'sendmail' => $sendmail]), [
                'title' => Yii::t('yii', 'Approve'),
                'data-confirm' => $confirmmessage,
                "data-method" => "post",
                "data-pjax" => "0",
                'class' => 'btn green margin-bottom-5 confirmOkButton',
            ]);
        }
    }

    /*
     * Set designing for view timesheets button
     */

    public static function get_score_card($url, $model)
    {
        return Html::a('<i class="fa fa-external-link"></i> View Score Cards', $url, [
            'title' => Yii::t('yii', 'View'),
            'class' => 'btn btn-primary btn-small',
            //'target' => '_blanck'
        ]);
    }

    /*
     * Set designing for view timesheets button
     */

    public static function get_project_history($url, $model, $flag = false)
    {
        if ($flag == 1) {
            return Html::a('<i class="icon-list-alt icon-white"></i>', $url, [
                'title' => Yii::t('yii', 'View Project History'),
                'class' => 'btn btn-primary btn-small',
                //'target' => '_blanck'
            ]);
        }
        return Html::a('<i class="fa fa-external-link"></i> View Project History', $url, [
            'title' => Yii::t('yii', 'View'),
            'class' => 'btn btn-primary btn-small',
            //'target' => '_blanck'
        ]);
    }

    /**
     * This function give system config by name
     */
    public static function getSystemConfig($name)
    {
        $systemConfigModel = new SystemConfig();
        $systemConfigData = $systemConfigModel->findOne(['name' => $name]);
        return $systemConfigData->value;
    }

    public static function createUrl($url, $param = false)
    {

        return ($url != "#") ? Yii::$app->urlManager->createUrl([$url]) : 'javascript:void(0);';
    }

    /**
     * function: closeColorBox()
     * For close color box popup window.
     *
     * @param string  $ssCloseScript
     */
    public static function closeColorBox()
    {
        /* $ssCloseScript = "<script src='" . Yii::$app->request->baseUrl . "/js/jquery.js'></script>";
        $ssCloseScript .= "<script src='" . Yii::$app->request->baseUrl . "/js/colorbox/jquery.colorbox.js'></script>";
         */
        $ssCloseScript = "<script type='text/javascript'>parent.jQuery.colorbox.close(); parent.window.location.reload(true);</script>";
        return $ssCloseScript;
    }

    public static function generateUniqueNumber($ssType)
    {
        switch ($ssType) {
            case 'QUESTION':
                $lastQuestionsId = Questions::find()->max('id');
                //Following generates duplicate unique numbers thats why commented
                //$snTotalQuestion = Questions::find()->count();
                //return $snTotalQuestion + 1;
                return $lastQuestionsId + 1;
                break;
            case 'PROBLEM_SET':
                $lastProblemSetsId = ProblemSets::find()->max('id');
                //Following generates duplicate unique numbers thats why commented
                //$totalProblemSets = ProblemSets::find()->count();
                //return $totalProblemSets + 1;
                return $lastProblemSetsId + 1;
                break;
        }
    }

    /*
     * $keyname =  compare key name
     */
    public static function FilterMultidimensionalArray($array, $Keyname, $value)
    {
        return array_filter($array, function ($arrayValue) use ($value, $Keyname) {return $arrayValue[$Keyname] == $value;});
    }
    /*
     * find items in multidimetional array based of the compare key and value passed.
     * $array : Multidimetional array.
     * findWhat : find key name in array.
     * value : compare value with provided key name.
     */
    public static function FindItems($array, $findwhat, $value, $found = array())
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = find_items($v, $findwhat, $value, $found);
                if ($result === true) {
                    $found[] = $v;
                } else {
                    $found = $result;
                }
            } else {
                if ($k == $findwhat && $v == $value) {
                    return true;
                }
            }
        }
        return $found;

    }
    public static function dateDiff($d1, $d2)
    {

        $date1 = strtotime($d1);
        $date2 = strtotime($d2);
        $seconds = $date2 - $date1;
        $weeks = floor($seconds / 604800);
        $seconds -= $weeks * 604800;
        $days = floor($seconds / 86400);

        $seconds -= $days * 86400;
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        /* $months=round(($date1-$date2) / 60 / 60 / 24 / 30);
        $years=round(($date1-$date2) /(60*60*24*365));
         */
        if (!empty($days)) {
            return $days;
        } elseif (!empty($hours)) {
            return $hours . ' Hours';
        } elseif (!empty($minutes)) {
            return $minutes . ' Minutes';
        } else {
            return $seconds . ' Seconds';
        }

        /*            $diffArr=array("Seconds"=>$seconds,
    "minutes"=>$minutes,
    "Hours"=>$hours,
    "Days"=>$days,
    "Weeks"=>$weeks,
    "Months"=>$months,
    "Years"=>$years
    ) ;
    return $diffArr;*/
    }

    /*
     * Return the number of days between the two dates:
     */
    /* public static function dateDiff ($date1, $date2) {
    return round(abs(strtotime($date1)-strtotime($date2))/86400);
    } */

    public static function buildTree(array $elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = self::buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    /*
     * Set designing for Grideview update status button
     */

    public static function update_status_button($url, $model, $title, $flag)
    {
        if ($flag == false) {
            if ($model->status != Yii::$app->params['milestone_status_array']['QA-Pending'] && $model->status != Yii::$app->params['milestone_status_array']['QA-Approved']) {
                return Html::a($title, $url, [
                    'title' => Yii::t('yii', $title),
                    'class' => 'colorbox_popup float_left',
                    'onClick' => 'javascript:openColorBox(600,300);',
                ]);
            }
        } else {
            if ($model->milestone->status != Yii::$app->params['milestone_status_array']['QA-Pending'] && $model->milestone->status != Yii::$app->params['milestone_status_array']['QA-Approved']) {
                return Html::a($title, $url, [
                    'title' => Yii::t('yii', $title),
                    'class' => 'colorbox_popup float_left',
                    'onClick' => 'javascript:openColorBox(600,300);',
                ]);
            }

        }
    }

    /*
     * Set designing for Grideview update status button
     */

    public static function send_for_qa_button($url, $model, $title, $flag)
    {
        if ($flag == true) {
            if ($model->status == Yii::$app->params['milestone_status_array']['Completed']) {
                return Html::a($title, $url, [
                    'title' => Yii::t('yii', $title),
                    'class' => 'colorbox_popup float_left btn btn-primary',
                    'onClick' => 'javascript:openColorBox(750,500);',
                ]);

            }
        } else {
            if ($model->status == Yii::$app->params['milestone_status_array']['QA-Pending']) {
                return Html::a($title, $url, [
                    'title' => Yii::t('yii', $title),
                    'class' => 'colorbox_popup float_left btn btn-primary',
                    'onClick' => 'javascript:openColorBox(700,450);',
                ]);

            }

        }
    }

    public static function add_total_leaves_button($url, $model, $flag)
    {
        if ($flag == 1) {
            return Html::a('<i class="icon-plus-sign icon-white"></i>', $url, [
                'title' => Yii::t('yii', 'Add total leaves'),
                'class' => 'btn btn-primary btn-small colorbox_popup float_left',
                'onClick' => 'javascript:openColorBox(600,300);',
            ]);
        }
        return Html::a('Add total leaves', $url, [
            'title' => Yii::t('yii', 'Add total leaves'),
            'class' => 'btn btn-primary btn-small colorbox_popup float_left',
            'onClick' => 'javascript:openColorBox(600,300);',
        ]);
    }

    public static function add_technical_skills($url, $model, $flag)
    {
        if ($flag == 1) {
            return Html::a('<i class="icon-wrench icon-white"></i>', $url, [
                'title' => Yii::t('yii', 'Add Technical Skills'),
                'class' => 'btn btn-primary btn-small colorbox_popup float_left',
                'onClick' => 'javascript:openColorBox(700,600);',
            ]);
        }
        return Html::a('Add/Edit Technical Skills', $url, [
            'title' => Yii::t('yii', 'Add Technical Skills'),
            'class' => 'btn btn-primary btn-small colorbox_popup float_left',
            'onClick' => 'javascript:openColorBox(700,600);',
        ]);
    }

    /*
     * Set designing for Grideview column length
     */
    public static function get_substr($string, $length)
    {
        return (strlen($string) > $length) ? substr($string, 0, $length) . '...' : $string;
    }
    /*
     * Set designing for Grideview update permission button
     */
    public static function display_hours($hours = '')
    {

        if (!empty($hours)) {
            $snHours = explode('.', number_format($hours, 2));
            $temp_hour = current($snHours);
            $temp_minute = (count($snHours) > 1) ? end($snHours) : 0;
            if ($temp_minute > 59) {
                $temp_hour = $temp_hour + 1;
                $temp_minute = $temp_minute - 60;
            }
            $snHour = ($temp_hour == 0) ? '' : $temp_hour . " hr ";
            $snMinute = ($temp_minute == 0) ? '' : $temp_minute . " min";
            return $snHour . " " . $snMinute;
        } else {
            return '0 hr';
        }
        //return str_replace(".",":",number_format($data->hours,2));
    }

    public static function get_tasks_hours($mid = '', $tid = '')
    {

        $snTaskId = isset($tid) ? $tid : 0;
        $snMilestoneId = isset($mid) ? $mid : 0;
        $consumedHours = 0;
        // GET TASKS //
        if ($snTaskId != 0) {
            $amTasksDetails = Tasks::find()->where(['id' => $snTaskId])->one();
            if (!empty($amTasksDetails)) {
                $consumedHours = Timesheet::consumedHours(array('task_id' => $amTasksDetails->id));
            }
        } else {
            $amMilestonesDetails = Milestones::find()->where(['id' => $snMilestoneId])->one();
            if (!empty($amMilestonesDetails)) {
                $consumedHours = Timesheet::consumedHours(array('milestone_id' => $amMilestonesDetails->id));
            }
        }
        $snHours = explode('.', $consumedHours);
        $snConsumedHours = $snHours[0] . '.' . $snHours[1];
        return Common::display_hours($snConsumedHours);
    }
    //THIS FUNCTIONS RETURNS SUM OF TIMES//
    public static function sum_the_time($times)
    {

        $seconds = 0;
        foreach ($times as $time) {
            list($hour, $minute, $second) = explode('.', $time);
            $seconds += $hour * 3600;
            $seconds += $minute * 60;
            $seconds += $second;
        }
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        //p($hours.'.'. $minutes.'.'. $seconds);
        // return "{$hours}:{$minutes}:{$seconds}";
        return sprintf('%02d.%02d.%02d', $hours, $minutes, $seconds);
    }
    //THIS FUNCTIONS FINDS DIFFERENCE BETWEEN TWO TIMES//
    public static function subtract_the_time($firstTime, $secondTime)
    {
        $firstTimeSeconds = $secondTimeSeconds = 0;
        $times[] = $firstTime;
        $times[] = $secondTime;

        list($hour, $minute, $second) = explode('.', $firstTime);
        $firstTimeSeconds += $hour * 3600;
        $firstTimeSeconds += $minute * 60;
        $firstTimeSeconds += $second;

        list($hour, $minute, $second) = explode('.', $secondTime);
        $secondTimeSeconds += $hour * 3600;
        $secondTimeSeconds += $minute * 60;
        $secondTimeSeconds += $second;

        $diffInSeconds = $firstTimeSeconds - $secondTimeSeconds;
        $temp = $diffInSeconds;
        if ($diffInSeconds < 0) {
            $diffInSeconds = (-1) * $diffInSeconds;
        }
        $H = floor($diffInSeconds / 3600);
        $i = ($diffInSeconds / 60) % 60;
        $s = $diffInSeconds % 60;
        if ($temp < 0) {
            return '-' . sprintf("%02d:%02d:%02d", $H, $i, $s);
        } else {
            return sprintf("%02d:%02d:%02d", $H, $i, $s);

        }

        //gmdate("H:i:s",$diffInSeconds);
    }

    public static function display_consumed_hours($consumedHours = '')
    {

        $snHours = explode('.', $consumedHours);
        $snConsumedHours = $snHours[0] . '.' . $snHours[1];
        return Common::display_hours($snConsumedHours);

    }
    /*THIS FUNCTION IS FOR FULL DAY TYPE LEAVE REQUEST
    -CREATES ENTRY IN LEAVE QUOTA TABLE
    -MANAGE TOTAL LEAVES AND PREVIOUS MOTN BALANCE AT CREATE & UPDATE LEAVE TIME
     */
    public static function full_day_type_leave_request($model)
    {
        $snRequesterEmail = Users::find()->where(['id' => Yii::$app->user->id])->one();
        $snTeamLeaderEmail = Users::find()->where(['id' => $model->tl_user_id])->one();
        $snPMEmail = Yii::$app->params['ProjectManagerEmail'];
        $snUserName = $snRequesterEmail->user_name . ' ' . $snRequesterEmail->last_name;
        // $PrevPendingLeaveEnd = $PrevPendingLeaveStart = '';

        $snStartDateYear = date("Y", strtotime($model->start_date));
        $snEndDateYear = date("Y", strtotime($model->end_date));
        $snStartDateMonth = date("m", strtotime($model->start_date));
        $snEndDateMonth = date("m", strtotime($model->end_date));
        $snPrevMonthStart = Common::get_previous_month($model->start_date);
        $omLeavesQuotaPrevMonthStart = LeaveQuota::find()->where(['month' => $snPrevMonthStart, 'year' => $snStartDateYear])->one();
        $PrevPendingLeaveStart = !empty($omLeavesQuotaPrevMonthStart) ? $omLeavesQuotaPrevMonthStart->total_leaves : 0;
        $snPrevMonthEnd = Common::get_previous_month($model->end_date);
        $omLeavesQuotaPrevMonthEnd = LeaveQuota::find()->where(['month' => $snPrevMonthEnd, 'year' => $snStartDateYear])->one();
        $PrevPendingLeaveEnd = !empty($omLeavesQuotaPrevMonthEnd) ? $omLeavesQuotaPrevMonthEnd->total_leaves : 0;
        $omLeavesQuotaStartMonth = LeaveQuota::find()->where(['month' => $snStartDateMonth, 'year' => $snStartDateYear])->one();
        if (empty($omLeavesQuotaStartMonth)) {
            $omLeaveQuota = new LeaveQuota();
            $omLeaveQuota->year = $snStartDateYear;
            $omLeaveQuota->month = $snStartDateMonth;
            $omLeaveQuota->total_leaves = Common::get_users_total_count();
            $omLeaveQuota->previous_month_bal = !empty($PrevPendingLeaveStart) ? $PrevPendingLeaveStart : 0;
            $omLeaveQuota->save(false);
        } else {
            $omLeavesQuotaStartMonth->previous_month_bal = $PrevPendingLeaveStart;
            $omLeavesQuotaStartMonth->save(false);

        }
        $omLeavesQuotaEndMonth = LeaveQuota::find()->where(['month' => $snEndDateMonth, 'year' => $snEndDateYear])->one();
        if (empty($omLeavesQuotaEndMonth)) {
            $omLeaveQuota = new LeaveQuota();
            $omLeaveQuota->year = $snEndDateYear;
            $omLeaveQuota->month = $snEndDateMonth;
            $omLeaveQuota->total_leaves = Common::get_users_total_count();
            $omLeaveQuota->previous_month_bal = $PrevPendingLeaveEnd;
            $omLeaveQuota->save(false);
        } else {
            $omLeavesQuotaEndMonth->previous_month_bal = $PrevPendingLeaveEnd;
            $omLeavesQuotaEndMonth->save(false);

        }
        $emailformatemodel = EmailFormat::findOne(["title" => 'leave_application_request_full_day', "status" => '1']);
        if ($emailformatemodel) {

            //create template file
            $AreplaceString = array('{user_name}' => $snUserName, '{start_date}' => $model->start_date, '{end_date}' => $model->end_date, '{no_of_days}' => $model->no_of_days, '{reason}' => $model->reason, '{leave_type}' => Yii::$app->params['leave_type_display'][$model->leave_type]);
            $body = Common::MailTemplate($AreplaceString, $emailformatemodel->body);

            //send email for new generated password
            $snMailStatus = Common::sendMail($snTeamLeaderEmail->email, $snRequesterEmail->email, $emailformatemodel->subject, $body, false, $snPMEmail);
            return $snMailStatus;
        }

    }
    /*THIS FUNCTION IS FOR HALF DAY TYPE LEAVE REQUEST
    -CREATES ENTRY IN LEAVE QUOTA TABLE
    -MANAGE TOTAL LEAVES AND PREVIOUS MOTN BALANCE AT CREATE & UPDATE LEAVE TIME
     */
    public static function half_day_type_leave_request($model)
    {
        $snRequesterEmail = Users::find()->where(['id' => Yii::$app->user->id])->one();
        $snTeamLeaderEmail = Users::find()->where(['id' => $model->tl_user_id])->one();
        $PrevPendingLeave = '';
        $snPMEmail = Yii::$app->params['ProjectManagerEmail'];
        $snUserName = $snRequesterEmail->user_name . ' ' . $snRequesterEmail->last_name;

        $snStartDateYear = date("Y", strtotime($model->start_date));
        $snStartDateMonth = date("m", strtotime($model->start_date));
        $snPrevMonth = Common::get_previous_month($model->start_date);
        $omLeavesQuotaPrevMonth = LeaveQuota::find()->where(['month' => $snPrevMonth, 'year' => $snStartDateYear])->one();
        $PrevPendingLeave = !empty($omLeavesQuotaPrevMonth) ? $omLeavesQuotaPrevMonth->total_leaves : 0;

        $omLeavesQuotaStartMonth = LeaveQuota::find()->where(['month' => $snStartDateMonth, 'year' => $snStartDateYear])->one();
        if (empty($omLeavesQuotaStartMonth)) {
            $omLeaveQuota = new LeaveQuota();
            $omLeaveQuota->year = $snStartDateYear;
            $omLeaveQuota->month = $snStartDateMonth;
            $omLeaveQuota->total_leaves = Common::get_users_total_count();
            $omLeaveQuota->previous_month_bal = !empty($PrevPendingLeave) ? $PrevPendingLeave : 0;
            $omLeaveQuota->save(false);
        } else {
            $omLeavesQuotaStartMonth->previous_month_bal = $PrevPendingLeave;
            $omLeavesQuotaStartMonth->save(false);
        }
        $emailformatemodel = EmailFormat::findOne(["title" => 'leave_application_request_half_day', "status" => '1']);
        if ($emailformatemodel) {

            //create template file
            $AreplaceString = array('{user_name}' => $snUserName, '{start_date}' => $model->start_date/*, '{end_date}' => $model->end_date,'{no_of_days}' => $model->no_of_days*/, '{reason}' => $model->reason, '{leave_type}' => Yii::$app->params['leave_type_display'][$model->leave_type]);
            $body = Common::MailTemplate($AreplaceString, $emailformatemodel->body);

            //send email for new generated password
            $snMailStatus = Common::sendMail($snTeamLeaderEmail->email, $snRequesterEmail->email, $emailformatemodel->subject, $body, false, $snPMEmail);
            return $snMailStatus;
        }
    }
    //THIS FUNCTION RETURNS TOTAL USERS COUNT//
    public static function get_users_total_count()
    {
        $snCountUsers = Users::find()->where(['status' => 1, 'company' => "INHERITX"])->count();
        return !empty($snCountUsers) ? $snCountUsers : 0;
    }

    public static function get_user_name($id)
    {
        $omUsers = Users::find()->where(['id' => $id])->one();
        return !empty($omUsers) ? $omUsers->name : "-";
    }
    //THIS FUNCTION RETURNS USER ROLE OF USER//
    public static function get_user_role($id, $flag)
    {
        $omUsers = Users::find()->where(['id' => $id])->one();
        if (!empty($flag) && $flag = "1") {
            return !empty($omUsers) ? $omUsers : '-';
        } else {
            return !empty($omUsers) ? $omUsers->role_id : '-';

        }
    }

    //THIS FUNCTION RETURNS TEAM LEADERS ARRAY//
    /*  public static function get_team_leaders_name($flag=false) {
    if ( $flag ) {

    $arrTeamLeads = ArrayHelper::map( Users::find()->where( "role_id = '".Yii::$app->params['userroles']['admin']."' AND id != '".Yii::$app->user->id."' AND company = 'INHERITX' AND status = '1'" )->orderBy( 'user_name' )->asArray()->all(), 'id', function( $user ) {
    return $user['user_name'].' '.$user['last_name'];
    } );
    }else {
    $arrTeamLeads = ArrayHelper::map( Users::find()->where( "(role_id = '".Yii::$app->params['userroles']['admin']."' OR role_id = '".Yii::$app->params['userroles']['hr_admin']."' OR role_id = '".Yii::$app->params['userroles']['sales_admin']."') AND id != '".Yii::$app->user->id."' AND company = 'INHERITX' AND status = '1'" )->orderBy( 'user_name' )->asArray()->all(), 'id', function( $user ) {
    return $user['user_name'].' '.$user['last_name'];
    } );
    }
    return $arrTeamLeads;
    }*/

    //THIS FUNCTION GET PREVIOUS MONTH OF GIVEN MONTH//
    public static function get_previous_month($date)
    {
        $datestring = '' . $date . ' first day of last month';
        $dt = date_create($datestring);
        return $dt->format('m');
    }
    //THIS FUNCTION GENERATE RANDOM FILE NAME//
    public function random_string($length)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }
    //THIS FUNCTION RETURNS TEAM LEADERS ARRAY//
    /*  public static function get_team_leaders() {
    $arrTeamLeads = ArrayHelper::map( Users::find()->where( "role_id = '".Yii::$app->params['userroles']['admin']."' AND company = 'INHERITX' AND status = '1'" )->orderBy( 'user_name' )->asArray()->all(), 'id', function( $user ) {
    return $user['user_name'].' '.$user['last_name'];
    } );

    return $arrTeamLeads;
    }*/

    //THIS FUNCTION RETURNS USERS ARRAY BY ROLE ID//
    public static function get_users_by_role_id($first_role_id = '', $second_role_id = '', $third_role_id = '')
    {
        if ($second_role_id == '' && $third_role_id == '') {
            $arrUsers = ArrayHelper::map(Users::find()->where("role_id = '" . $first_role_id . "' AND company = 'INHERITX' AND status = '1'")->orderBy('user_name')->asArray()->all(), 'id', function ($user) {
                return $user['user_name'] . ' ' . $user['last_name'];
            });
        } else if ($third_role_id == '') {
            $arrUsers = ArrayHelper::map(Users::find()->where("(role_id = '" . $first_role_id . "' OR role_id = '" . $second_role_id . "') AND company = 'INHERITX' AND status = '1'")->orderBy('user_name')->asArray()->all(), 'id', function ($user) {
                return $user['user_name'] . ' ' . $user['last_name'];
            });
        } else {
            $arrUsers = ArrayHelper::map(Users::find()->where("(role_id = '" . $first_role_id . "' OR role_id = '" . $second_role_id . "' OR role_id = '" . $third_role_id . "') AND id != '" . Yii::$app->user->id . "' AND company = 'INHERITX' AND status = '1'")->orderBy('user_name')->asArray()->all(), 'id', function ($user) {
                return $user['user_name'] . ' ' . $user['last_name'];
            });
        }
        return $arrUsers;
    }

    //GET NAME BY ID AND MODEL//
    public static function get_name_by_id($id = '', $flag = '')
    {

        if ($flag == "Questions") {
            $snQuestionDetail = Questions::find()->where(['id' => $id])->one();
            $question = $snQuestionDetail->question;
            return !empty($question) ? $question : '';
        }
        if ($flag == "RestaurantFloors") {
            $snRestaurantsDetail = RestaurantFloors::find()->where(['id' => $id])->one();
            $name = $snRestaurantsDetail->name;
            return !empty($name) ? $name : '';

        }
        if ($flag == "Users") {
            $snUserDetails = Users::find()->where(['id' => $id])->one();
            return $snUserDetails;

        }
    }
    //THIS FUNCTIONS FINDS ESTIMATED HOURS BY MILESTONE ID//
    public static function get_estimated_hours($milestone_id, $flag = '')
    {
        $snHours = array();
        $oModelTasks = Tasks::TaskDropDownArr($milestone_id, $flag = true);
        foreach ($oModelTasks as $tasks) {
            $snHour = number_format((double) $tasks['actual_hours'], 2);
            $snHours[] = $snHour . '.0';
        }
        $snEstimatedHours = Common::sum_the_time($snHours);
        if (!empty($snEstimatedHours)) {
            $snHours = explode('.', $snEstimatedHours);
            $snConsumedHours = $snHours[0] . '.' . $snHours[1];
            if ($flag == true) {
                return $snConsumedHours;
            } else {
                return Common::display_hours($snConsumedHours);

            }

        }
    }
    public static function get_score_card_details1($snUserId)
    {
        $snScoreCardDetails = array();
        if (empty($snUserId)) {
            $snUserId = Yii::$app->user->id;
        }
        $arrAssignedProjects = Projects::ProjectDropDownArr($flag = false, $id = $snUserId, $temp = 'temp');

        //$snScoreCardDetails['assigned_projects'] = $arrAssignedProjects;
        $snScoreCardDetails = [];
        if (!empty($arrAssignedProjects)) {

            foreach ($arrAssignedProjects as $project) {

                foreach ($project['milestones'] as $key => $milestone) {
                    $snEstimatedHours = Common::get_estimated_hours($milestone['id'], $flag = true);
                    $snScoreCardDetails['estimated_hours'] = $snEstimatedHours;
                    $snScoreCardDetails['consumed_hours'] = Timesheet::consumedHours(['milestone_id' => $milestone['id'], 'user_id' => $snUserId]);
                    $snScoreCardDetails['normal_hours'] = Timesheet::consumedHours(['milestone_id' => $milestone['id'], 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['Normal']]);
                    $snScoreCardDetails['bug_hours'] = Timesheet::consumedHours(['milestone_id' => $milestone['id'], 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['Bug']]);
                    $snScoreCardDetails['cr_hours'] = Timesheet::consumedHours(['milestone_id' => $milestone['id'], 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['CR']]);
                    $snScoreCardDetails['suggestion_hours'] = Timesheet::consumedHours(['milestone_id' => $milestone['id'], 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['Suggestions']]);
                    $snScoreCardDetails['feedback_hours'] = Timesheet::consumedHours(['milestone_id' => $milestone['id'], 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['Feedback']]);
                    $snScoreCardDetails['support_hours'] = Timesheet::consumedHours("milestone_id = " . $milestone['id'] . " AND user_id != " . $snUserId . " AND task_type =" . Yii::$app->params['TASK_TYPE_VALUE']['Support']);

                    $snTotalBillableHoursArr[] = number_format((double) $snScoreCardDetails['estimated_hours'], 2) . '.0';
                    $snTotalBillableHoursArr[] = number_format((double) $snScoreCardDetails['cr_hours'], 2) . '.0';
                    $snScoreCardDetails['total_billable_hours'] = Common::sum_the_time($snTotalBillableHoursArr);

                    $snProfilLossFirstArr[] = number_format((double) $snScoreCardDetails['estimated_hours'], 2) . '.0';
                    $snProfilLossFirstArr[] = number_format((double) $snScoreCardDetails['feedback_hours'], 2) . '.0';
                    $snProfilLossFirstArr[] = number_format((double) $snScoreCardDetails['suggestion_hours'], 2) . '.0';

                    $snProfilLossSecondArr[] = number_format((double) $snScoreCardDetails['normal_hours'], 2) . '.0';
                    $snProfilLossSecondArr[] = number_format((double) $snScoreCardDetails['support_hours'], 2) . '.0';
                    $snProfilLossSecondArr[] = number_format((double) $snScoreCardDetails['bug_hours'], 2) . '.0';

                    $snScoreCardDetails['profit_hours/loss_hours'] = Common::subtract_the_time(Common::sum_the_time($snProfilLossFirstArr), Common::sum_the_time($snProfilLossSecondArr));
                }

            }

            return $snScoreCardDetails;
        }

    }
    //THIS FUNCTIONS FINDS SCORE CARD DETAILS BY USER ID AND MILESTONE ID//
    public static function get_score_card_details($snUserId = '', $snMilestoneId = '')
    {

        //DIFFERENT HOURS CALCULATIONS//
        $snEstimatedHours = Common::get_estimated_hours($snMilestoneId, $flag = true);
        $snScoreCardDetails['estimated_hours'] = $snEstimatedHours;
        $snScoreCardDetails['consumed_hours'] = Timesheet::consumedHours(['milestone_id' => $snMilestoneId, 'user_id' => $snUserId]);
        $snScoreCardDetails['normal_hours'] = Timesheet::consumedHours(['milestone_id' => $snMilestoneId, 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['Normal']]);
        $snScoreCardDetails['bug_hours'] = Timesheet::consumedHours(['milestone_id' => $snMilestoneId, 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['Bug']]);
        $snScoreCardDetails['cr_hours'] = Timesheet::consumedHours(['milestone_id' => $snMilestoneId, 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['CR']]);
        $snScoreCardDetails['suggestion_hours'] = Timesheet::consumedHours(['milestone_id' => $snMilestoneId, 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['Suggestions']]);
        $snScoreCardDetails['feedback_hours'] = Timesheet::consumedHours(['milestone_id' => $snMilestoneId, 'user_id' => $snUserId, 'task_type' => Yii::$app->params['TASK_TYPE_VALUE']['Feedback']]);
        $snScoreCardDetails['support_hours'] = Timesheet::consumedHours("milestone_id = " . $snMilestoneId . " AND user_id != " . $snUserId . " AND task_type =" . Yii::$app->params['TASK_TYPE_VALUE']['Support']);

        //CHANGING FORMAT OF ESTIMATED AND CR HOURS FOR GETTING SUM FOR TOTAL BILLABLE HOURS//
        $snTotalBillableHoursArr[] = number_format((double) $snScoreCardDetails['estimated_hours'], 2) . '.0';
        $snTotalBillableHoursArr[] = number_format((double) $snScoreCardDetails['cr_hours'], 2) . '.0';
        $snScoreCardDetails['total_billable_hours'] = Common::sum_the_time($snTotalBillableHoursArr);

        //CHANGING FORMAT OF HOURS FOR GETTING SUM AND DIFFERENCE FOR TOTAL PROFIT LOSS HOURS//
        $snProfilLossFirstArr[] = number_format((double) $snScoreCardDetails['estimated_hours'], 2) . '.0';
        $snProfilLossFirstArr[] = number_format((double) $snScoreCardDetails['feedback_hours'], 2) . '.0';
        $snProfilLossFirstArr[] = number_format((double) $snScoreCardDetails['suggestion_hours'], 2) . '.0';

        $snProfilLossSecondArr[] = number_format((double) $snScoreCardDetails['normal_hours'], 2) . '.0';
        $snProfilLossSecondArr[] = number_format((double) $snScoreCardDetails['support_hours'], 2) . '.0';
        $snProfilLossSecondArr[] = number_format((double) $snScoreCardDetails['bug_hours'], 2) . '.0';

        $snScoreCardDetails['profit_hours/loss_hours'] = Common::subtract_the_time(Common::sum_the_time($snProfilLossFirstArr), Common::sum_the_time($snProfilLossSecondArr));
        return $snScoreCardDetails;
    }
    public static function get_time_diff($time)
    {
        $startTimeStamp = strtotime(date('Y-m-d'));
        $endTimeStamp = strtotime($time);

        $timeDiff = $endTimeStamp - $startTimeStamp;

        $numberDays = $timeDiff / 86400; // 86400 seconds in one day

        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        return $numberDays;

    }

    //GET CSS CLASS ACCORDING TO DATE DIFFERENCE//

    public static function get_css_class_by_date_diff($date1)
    {
        $dateDiff = Common::get_time_diff($date1);
        $color = '';
        if ($dateDiff > 7) {
            $color = 'pink'; //RED COLOR
        }
        if ($dateDiff <= 7) {
            $color = 'lightyellow'; //YELLOW COLOR
        }
        if ($dateDiff < 0) {
            $color = 'lightgreen'; //GREEN COLOR
        }
        return $color;

    }
    //GET ESTIMATED HOURS OF PROJECT BY MILESTONE WISE TOTAL//

    public static function get_estimated_hours_by_milestones($project_id)
    {

        $snMilestones = Milestones::find()->where(['project_id' => $project_id])->asArray()->all();
        $snEstimatedHours = [];
        if (!empty($snMilestones)) {
            foreach ($snMilestones as $key => $value) {
                $snHour = number_format((double) $value['actual_hours'], 2);
                $snEstimatedHours[] = $snHour . '.0';
            }
        }
        $snTotalHours = Common::sum_the_time($snEstimatedHours);
        if ($snTotalHours > 0) {
            $snHours = explode('.', $snTotalHours);
            $snTotalEstimatedHours = $snHours[0] . '.' . $snHours[1];
            return Common::display_hours($snTotalEstimatedHours);

        } else {
            return '0 hr';
        }
    }

    //GET CSS CLASS ACCORDING TO HOURS DIFFERENCE//

    public static function get_css_class_by_hours_diff($total_hours, $project_id)
    {
        $snConsumedHours = Timesheet::consumedHours("project_id = $project_id");
        $color = '';
        if ($snConsumedHours > $total_hours) {
            $color = 'pink'; //RED COLOR
        } else {
            $color = 'white';
        }

        return $color;

    }
    //GET CSS CLASS ACCORDING TO HOURS DIFFERENCE//

    public static function get_date_diff_in_years($date)
    {
        // p($date,0);

        $date1 = new \DateTime($date);
        $date2 = new \DateTime(date("Y-m-d"));
        $interval = $date1->diff($date2);
        $years = ($interval->y > 0) ? $interval->y . " Years" : "";
        $months = ($interval->m > 0) ? $interval->m . " Months" : "";
        return !empty($date) ? $years . " " . $months : "-";
    }

    //GET CSS CLASS ACCORDING TO HOURS DIFFERENCE//
    public static function get_date_diff_in_days($date)
    {
        // p($date,0);

        $date1 = new \DateTime($date);
        $date2 = new \DateTime(date("Y-m-d"));
        $interval = $date1->diff($date2);
        $years = ($interval->y > 0) ? $interval->y . " Years" : "";
        $months = ($interval->m > 0) ? $interval->m . " Months" : "";
        $days_interval = ($interval->d > 0) ? $interval->d : "";
        if (!empty($date) && ($years > 0 || $months > 0)) {
            $days = "-";
        } else {

            $days = ($days_interval >= 0) ? $days_interval : '-';
        }
        return $days;
    }

    //THIS FUNCTIONS FINDS TOTAL HOURS BY PROJECT ID//
    public static function get_project_total_hours_by_milestones($project_id, $flag = '')
    {
        $snHours = array();
        $oModelMilestones = Milestones::MilestoneDropDownArr($project_id, $flag = true);
        foreach ($oModelMilestones as $milestones) {
            $snHour = number_format((double) $milestones['actual_hours'], 2);
            $snHours[] = $snHour . '.0';
        }
        $snEstimatedHours = Common::sum_the_time($snHours);
        if (!empty($snEstimatedHours)) {
            $snHours = explode('.', $snEstimatedHours);
            $snConsumedHours = $snHours[0] . '.' . $snHours[1];
            if ($flag == true) {
                return $snConsumedHours;
            } else {
                return Common::display_hours($snConsumedHours);

            }

        }
    }

    public static function getApiHeader($ssStatus = 200)
    {

        $ssContentType = "application/json";
        if (!empty($_REQUEST['id']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ssStatusHeader = 'HTTP/1.1 ' . $ssStatus . ' ';
            header($ssStatusHeader);
            header('Content-type: ' . $ssContentType);
            $ssPostData = file_get_contents("php://input");

            if (!empty($ssPostData)) {
                static::$amPostData = Json::decode($ssPostData);
                if (empty(static::$amPostData)) {
                    $amResponse = Common::errorResponse("You have passed null JSON");
                    Common::encodeResponseJSON($amResponse);
                }
            } else {
                static::$amPostData = Json::decode(Yii::$app->request->post("json"));
                if (empty(static::$amPostData)) {
                    static::$amPostData = Yii::$app->request->post();
                }
            }

            return static::$amPostData;
        } else {
            $amResponse = Common::errorResponse("Request Type must be POST");
            Common::encodeResponseJSON($amResponse);
        }
    }

    public static function checkRequiredParams($amData, $amRequiredParams)
    {
        $amError = array();
        if (empty($amData)) {
            $amError['error'] = 'Invalid request parameters';
        } else {
            $amDataAll = $amData; // self::arrayFlatten($amData);
            foreach ($amRequiredParams as $value) {
                if (!isset($amDataAll[$value])) {
                    $amError['error'] = $value . " can't be blank";
                    return $amError;
                }
            }
        }

        return $amError;
    }

    public static function errorResponse($ssErrorMessage)
    {
        $amResponse = array('success' => "0", 'message' => $ssErrorMessage);
        return $amResponse;
    }

    /**
     * function: encodeResponseJSON()
     * For generate random number
     *
     * @param array   $amResponse
     * @return object JSON
     */
    public static function encodeResponseJSON($amResponse)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->data = $amResponse;

        /* header('Content-type:application/json');
        echo Json::encode($amResponse);*/
        Yii::$app->end();
    }
    public static function checkRequestType($ssFlag = 'AES', $ssStatus = 200, $ssBody = '', $ssContentType = 'text/json')
    {
        $amFileData = $amData = array();
        if (isset($_POST) && isset($_FILES) && !empty($_FILES)) {

            $amData = $_POST;

            if (!empty($amData['json'])) {
                $amData = json::decode($amData['json'], true);
            }
            $amFileData = $_FILES;
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $ssStatusHeader = 'HTTP/1.1 ' . $ssStatus . ' ';
            header($ssStatusHeader);

            header("'Content-type:.$ssContentType; charset=utf-8'");
            $amPostReq = file_get_contents("php://input");

            // p($amPostReq);
            $amData = json::decode($amPostReq, true);

            if (!empty($_POST) && empty($amData)) {
                $amData = $_POST;
            }
            if (!empty($amData['json'])) {
                $amData = json::decode($amData['json'], true);
            }
        }

        return array("request_param" => $amData, "file_param" => $amFileData);
    }

    public static function checkRequestParameterKey($amData, $amRequiredParams)
    {
        $amError = array();
        if (empty($amData)) {
            $amError['error'] = 'Invalid request parameters';
        } else {
            $amDataAll = self::arrayFlatten($amData);
            foreach ($amRequiredParams as $value) {
                if (!isset($amDataAll[$value])) {
                    $amError['error'] = $value . " can't be blank";
                    return $amError;
                }
            }
        }

        /*   if(empty($amError)){
        //When user_id key exists into request parameter then i will find user into database.
        if(array_key_exists("user_id",$amData)){
        $EmnogoCriteria = new Users;
        $EmnogoCriteria->_id = Common::SetMongoObject($amData['user_id']);
        if(($model = AppUsers::model()->find($EmnogoCriteria)) === NULL){
        $amError['error'] = "UserId doesn't exists!";
        }else{
        $amError['userinformation'] = $model;
        }
        }
        } */
        return $amError;
    }
    public static function arrayFlatten($array)
    {
        if (!is_array($array)) {
            return false;
        }
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::arrayFlatten($value));
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
    public static function generateToken($snUserId)
    {
        return md5(time() . $snUserId);
    }

    public static function successResponse($ssSuccessMessage, $amReponseParam = [])
    {
        $amResponse = ['success' => "200", 'message' => $ssSuccessMessage, 'data' => $amReponseParam];
        return $amResponse;
    }
    public static function successResponseBlank($ssSuccessMessage, $amReponseParam = [])
    {
        $amResponse = ['success' => "0", 'message' => $ssSuccessMessage, 'data' => $amReponseParam];
        return $amResponse;
    }

    public static function checkAuthentication($authToken)
    {
        $valid = 0;

        $chkAuthentication = Users::findAll(["auth_token" => $authToken]);
        if (!empty($chkAuthentication)) {
            foreach ($chkAuthentication as $value) {
                if ($value->auth_token == $authToken) {
                    $valid = 1;
                }
            }
        } else {
            $valid = 0;
        }

        if ($valid != 1) {
            // FOR GENERATE ERROR RESPONSE IF TOKEN NOT VALID
            $errormessage['success'] = '401';
            $errormessage['message'] = 'Auth token not valid.';
            $errormessage['data'] = array();
            Common::encodeResponseJSON($errormessage);
        }

        return;
    }
    public static function negativeResponse($ssErrorMessage)
    {
        $amResponse = array('success' => -1, 'message' => $ssErrorMessage);
        return $amResponse;
    }

    public static function get_header($pHeaderKey)
    {
        $test = getallheaders();
        if (array_key_exists($pHeaderKey, $test)) {
            $headerValue = $test[$pHeaderKey];
        }
        return $headerValue;
    }
    public static function template_cancel_button($url, $model, $confirmmessage = false, $flag = false)
    {
        $confirmmessage = $confirmmessage ?: "Are you sure you want to cancel it?";
        if ($flag == 2) {
            return Html::a('<i class="fa fa-trash"></i>', $url, [
                'title' => Yii::t('yii', 'Delete'),
                'class' => 'deleteGlobalButton',
                'data-confirm' => $confirmmessage,
                "data-method" => "post",
                "data-pjax" => "0",
            ]);
        }
        return Html::a('<i class="fa fa-window-close"></i>', $url, [
            'title' => Yii::t('yii', 'Cancel Booking'),
            'class' => 'deleteGlobalButton',
            'data-confirm' => $confirmmessage,
            "data-method" => "post",
            "data-pjax" => "0",
        ]);
    }

    public static function checkRequestParameterKeyArray($amData, $amRequiredParams)
    {
        $amError = array();
        if (empty($amData)) {
            $amError['error'] = 'Invalid request parameters';
        } else {
            foreach ($amRequiredParams as $value) {
                if (!isset($amData[$value])) {
                    $amError['error'] = $value . " can't be blank";
                    return $amError;
                }
            }
        }
        return $amError;
    }
    public static function matchUserStatus($id)
    {

        if (($model = Users::findOne($id)) !== null) {
            if ($model->status == Yii::$app->params['user_status_value']['in_active']) {
                $ssMessage = 'User has been deactivated by admin.';
                $WholeMealData = Common::negativeResponse($ssMessage);
                Common::encodeResponseJSON($WholeMealData);
            }
        } else {
            $ssMessage = 'User is not available';
            $WholeMealData = Common::negativeResponse($ssMessage);
            Common::encodeResponseJSON($WholeMealData);
        }
    }
    public static function checkRestaurantStatus($restaurant_id)
    {
        $valid = 0;

        $restaurant = Restaurants::findOne($restaurant_id);

        if ($restaurant->status == Yii::$app->params['user_status_value']['active']) {
            $valid = 1;
        } else {
            $valid = 0;
        }

        if ($valid != 1) {
            // FOR GENERATE ERROR RESPONSE IF TOKEN NOT VALID
            $errormessage['success'] = '401';
            $errormessage['message'] = 'Restaurant is not active today.';
            $errormessage['data'] = array();
            Common::encodeResponseJSON($errormessage);
        }

        return;
    }
    public static function checkRestaurantIsDeleted($restaurant_id)
    {
        $valid = 0;

        $restaurant = Restaurants::findOne($restaurant_id);

        if ($restaurant->is_deleted == Yii::$app->params['delete_status']['no']) {
            $valid = 1;
        } else {
            $valid = 0;
        }

        if ($valid != 1) {
            // FOR GENERATE ERROR RESPONSE IF TOKEN NOT VALID
            $errormessage['success'] = '401';
            $errormessage['message'] = 'Restaurant is deleted.';
            $errormessage['data'] = array();
            Common::encodeResponseJSON($errormessage);
        }

        return;
    }

    // Send Push Notification to Members - using iphone
    public static function SendNotification($arr)
    {
        $deviceid = $arr['device_token'];
        //$pemfielname        = 'apns-dev-minime.pem';
        $pemfielname = Yii::$app->params['push_notification_pem_file'];
        $snbadge_count_user = Users::find()->where('id = "' . $arr['user_id'] . '"')->one();
        if (!empty($snbadge_count_user)) {
            if ($snbadge_count_user) {
                $badge_count = $snbadge_count_user->badge_count + 1;
                $snbadge_count_user->badge_count = $badge_count;
                $snbadge_count_user->save(false);
            } else {
                $badge_count = 0;
            }
            $snbadge_count_user->save(false);
        }
        $amAPNSRequest = array('apns_host' => /* 'ssl://gateway.push.apple.com:2195', */'ssl://gateway.sandbox.push.apple.com:2195',
            'apsn_certificate' => $pemfielname,
            'apns_pass_pharse' => '',
            'ssMessage' => $arr['message'],
            'Badge' => $badge_count,
            'notification_type' => $arr['notification_type'],
            'sound' => 'default',
        );

        Common::sendAPNS($deviceid, $amAPNSRequest);
    }

    public static function sendAPNS($ssDeviceToken, $amAPNSReques, $ssTags = 201)
    {

        //p($amAPNSReques);
        $ssApnsHost = $amAPNSReques['apns_host'];
        $ssApnsCert = $amAPNSReques['apsn_certificate'];
        $ssPassPhrase = $amAPNSReques['apns_pass_pharse'];
        $ssBadgeCount = $amAPNSReques['Badge'];
        $passphrase = '';
        $ssCertifiateFilePath = Yii::getAlias('@webroot') . "/" . $ssApnsCert;
        //p($ssCertifiateFilePath);
        if (file_exists($ssCertifiateFilePath)) {
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $ssCertifiateFilePath);
            // Open a connection to the APNS server
            $oFp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195' /* 'ssl://gateway.push.apple.com:2195' */, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            if ($oFp) {
                try
                {
                    // Create the payload body
                    $amBody['aps'] = array(
                        'alert' => $amAPNSReques['ssMessage'],
                        'tag' => $ssTags,
                        'badge' => $amAPNSReques['Badge'],
                        'notification_type' => $amAPNSReques['notification_type'],
                        'sound' => $amAPNSReques['sound']);
                    // Encode the payload as JSON
                    $amEncodePayload = json_encode($amBody);
                    // Build the binary notification
                    $smEncodeMsg = chr(0) . pack('n', 32) . pack('H*', $ssDeviceToken) . pack('n', strlen($amEncodePayload)) . $amEncodePayload;
                    // Send it to the server
                    $oResult = fwrite($oFp, $smEncodeMsg, strlen($smEncodeMsg));
                    fclose($oFp);
                } catch (Exception $e) {
                    //echo 'Caught exception: '.  $e->getMessage(). "\n";
                }
            }
        }
    }
    public static function time_elapsed_string($datetime, $full = false)
    {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function getLastMondaySaturday($day)
    {
        $date = "";
        if ($day == "monday") {
            if (date('D') != 'Mon') {
                //take the last monday
                $staticstart = date('Y-m-d', strtotime('last Monday'));

            } else {
                $staticstart = date('Y-m-d');
            }
            $date = $staticstart;
        } else {
            if (date('D') != 'Sat') {
                $staticfinish = date('Y-m-d', strtotime('next Saturday'));
            } else {

                $staticfinish = date('Y-m-d');
            }
            $date = $staticfinish;
        }
        return $date;
    }

    public static function getMpNames($mp)
    {
        if (!empty($mp)) {
            $mpArr = explode(",", $mp);
            $i = 0;
            $mp_name = "";
            foreach ($mpArr as $key => $mp) {
                $mp_name .= Common::get_user_name($mp);
                $i++;
                if ($i != count($mpArr)) {
                    $mp_name .= ",";
                }
            }
            return $mp_name;
        }
    }
    public static function get_remaining_questions_per_week($user_id)
    {
        $monday = Common::getLastMondaySaturday("monday");
        $saturday = Common::getLastMondaySaturday("saturday");
        $query = Questions::find()->where(['user_agent_id' => $user_id, "status" => Yii::$app->params['user_status_value']['active'], "is_delete" => 0]);
        $query->andWhere(['between', 'created_at', $monday, $saturday]);
        $questionCount = $query->count();
        $limit = 10;
        return ($limit - $questionCount);
    }
    public static function template_view_answers_button($url, $model, $title, $flag = false)
    {
        if ($flag == 1) {
            return Html::a('<i class="icon-calendar icon-white"></i>', $url, [
                'title' => Yii::t('yii', $title),
                'class' => 'btn btn-primary btn-small',
                //'target' => '_blanck'
            ]);
        }
        if ($flag == 2) {
            return Html::a('<i class="icon-comment icon-white"></i>', $url, [
                'title' => Yii::t('yii', $title),
                'class' => 'btn btn-primary btn-small',
                //'target' => '_blanck'
            ]);
        }
        return Html::a('<i class="fa fa-external-link"></i>' . $title, $url, [
            'title' => Yii::t('yii', $title),
            'class' => 'btn btn-primary',
            //'target' => '_blanck'
        ]);
    }
    public static function clean_special_characters($string)
    {
        /*$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[()-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.*/
        return preg_replace('/[^A-Za-z0-9]/', "", $string);
    }
}
