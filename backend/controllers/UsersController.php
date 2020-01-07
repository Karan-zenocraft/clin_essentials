<?php

namespace backend\controllers;

use backend\components\AdminCoreController;
use common\components\Common;
use common\models\EmailFormat;
use common\models\UserRoles;
use common\models\Users;
use common\models\UsersSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends AdminCoreController
{
    /**
     * {@inheritdoc}
     */
    /*   public function behaviors()
    {
    return [
    'verbs' => [
    'class' => VerbFilter::className(),
    'actions' => [
    'delete' => ['POST'],
    ],
    ],
    ];
    }*/

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $UserRolesDropdown = ArrayHelper::map(UserRoles::find()->where("id !=" . Yii::$app->params['userroles']['super_admin'])->asArray()->all(), 'id', 'role_name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'UserRolesDropdown' => $UserRolesDropdown,
        ]);
    }

    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();
        $UserRolesDropdown = ArrayHelper::map(UserRoles::find()->where("id !=" . Yii::$app->params['userroles']['admin'] . " AND id !=" . Yii::$app->params['userroles']['super_admin'])->asArray()->all(), 'id', 'role_name');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->password = md5($_REQUEST['Users']['password']);
            $file = \yii\web\UploadedFile::getInstance($model, 'photo');
            if (!empty($file)) {
                $file_name = $file->basename . "_" . uniqid() . "." . $file->extension;
                //p(trim($file_name));
                $file_filter = str_replace(" ", "", $file_name);
                $model->photo = $file_filter;
                $file->saveAs(Yii::getAlias('@root') . '/uploads/' . $file_filter);
            }
            $model->generateAuthKey();
            $email_verify_link = Yii::$app->params['root_url'] . '/site/email-verify?verify=' . base64_encode($model->verification_code) . '&e=' . base64_encode($model->email);
            $model->save(false);
            $emailformatemodel = EmailFormat::findOne(["title" => 'backend_registration', "status" => '1']);
            if ($emailformatemodel) {

                //create template file
                $AreplaceString = array('{password}' => $_REQUEST['Users']['password'], '{username}' => $model->user_name, '{email}' => $model->email, '{role}' => $model->role->role_name);

                $body = Common::MailTemplate($AreplaceString, $emailformatemodel->body);
                $ssSubject = $emailformatemodel->subject;
                //send email for new generated password
                $ssResponse = Common::sendMail($model->email, Yii::$app->params['adminEmail'], $ssSubject, $body);

            }
            return $this->redirect(['users/index']);

        }

        return $this->render('create', [
            'model' => $model,
            'UserRolesDropdown' => $UserRolesDropdown,

        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old_image = $model->photo;
        $UserRolesDropdown = ArrayHelper::map(UserRoles::find()->where("id !=" . Yii::$app->params['userroles']['admin'] . " AND id !=" . Yii::$app->params['userroles']['super_admin'])->asArray()->all(), 'id', 'role_name');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $file = \yii\web\UploadedFile::getInstance($model, 'photo');
            if (!empty($file)) {
                $delete = $model->oldAttributes['photo'];
                $file_name = $file->basename . "_" . uniqid() . "." . $file->extension;
                $file_filter = str_replace(" ", "", $file_name);
                if (!empty($old_image) && file_exists(Yii::getAlias('@root') . '/uploads/' . $old_image)) {
                    unlink(Yii::getAlias('@root') . '/uploads/' . $old_image);
                }
                $file->saveAs(Yii::getAlias('@root') . '/uploads/' . $file_filter, false);
                $model->photo = $file_filter;
            } else {
                $model->photo = $old_image;
            }
            $model->save();
            return $this->redirect(['users/index']);
        }

        return $this->render('update', [
            'model' => $model,
            'UserRolesDropdown' => $UserRolesDropdown,

        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
