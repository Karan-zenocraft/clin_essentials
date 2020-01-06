<?php

namespace common\models;

class UserRoles extends \common\models\base\UserRolesBase
{
    public static function getRole($role_id,$field_name = false){
    	$userrolesmodel = UserRoles::findOne($role_id);
    	if(!empty($userrolesmodel)){
    		if(!empty($field_name)){
    			return $userrolesmodel->$field_name;
    		}else{
    			return $userrolesmodel;
    		}
    	}
    }
}
