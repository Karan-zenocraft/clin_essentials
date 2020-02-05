<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[TodoList]].
 *
 * @see TodoList
 */
class TodolistQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return TodoList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TodoList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
