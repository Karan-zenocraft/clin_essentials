<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ClinicalStudyProtocol]].
 *
 * @see ClinicalStudyProtocol
 */
class ClinicalStudyProtocolQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return ClinicalStudyProtocol[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ClinicalStudyProtocol|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
