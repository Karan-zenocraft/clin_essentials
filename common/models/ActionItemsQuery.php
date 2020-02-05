<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ActionItems]].
 *
 * @see ActionItems
 */
class ActionItemsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return ActionItems[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ActionItems|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
