<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[SentNotes]].
 *
 * @see SentNotes
 */
class SentNotesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return SentNotes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SentNotes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
