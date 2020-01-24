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
        // Check User Status
        Common::matchUserStatus($requestParam['user_id']);
        //VERIFY AUTH TOKEN
        $authToken = Common::get_header('auth_token');
        Common::checkAuthentication($authToken);

        $userModel = Users::findOne(['id' => $requestParam['user_id']]);
        if (!empty($userModel)) {
            $html = '<html xmlns="http://www.w3.org/1999/xhtml">
                  <head>
                      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                  </head>
                  <body>
                  <p>Simple Content</p><p>Simple Content</p><p>Simple Content</p>
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
                'options' => ['title' => 'Krajee Report Title'],
                // call mPDF methods on the fly
                'methods' => [
                    'SetHeader' => ['Clin Essentials Note'],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);
            $pdf->content = $html;
            $file = "note_" . rand(7, 100) . "_" . time() . ".pdf";
            $pdf->filename = "../../uploads/pdf_files/" . $file;

            //$pdf->destination = __DIR__ . "../../../uploads/pdf_files/" . $pdf->filename;
            return $pdf->render();
            // $file = $pdf->render();
            /*  ob_clean();

            $pdf1 = $pdf->Output($pdf->filename, 'F');*/

            //  $pdf1->saveAs(__DIR__ . "../../../uploads/pdf_files/" . $pdf->filename);
            $ssMessage = 'Successfully added PDF';
            $amResponse = Common::errorResponse($ssMessage);

            // return the pdf output as per the destination setting

            // return the pdf output as per the destination setting

            // create PDF file from HTML content :
            /* Yii::$app->html2pdf
        ->convert($html)
        ->saveAs(__DIR__ . "../../../uploads/pdf_files/");
         */
        } else {
            $ssMessage = 'Invalid user_id';
            $amResponse = Common::errorResponse($ssMessage);
        }
        Common::encodeResponseJSON($amResponse);
    }
}
