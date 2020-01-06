<?php

namespace common\models;

class UserRulesMenu extends \common\models\base\UserRulesMenuBase
{
	const MENU_ACTIVE_STATUS = 1;
	

	/***
	Get dynamic menu for frontend and backend
	{role_id} = set role id for who login.
	{category} = where is the show menu (Front top , front bottom , front middle , admin sidebar)
	{parent_id} = Get List of submenu then pass parent id.
	*/
    public static function getMenu($category , $role_id){
        $Menutree = array() ;
    	$amGetParentmenu = self::createTree($category , $role_id , '0');
    	foreach ($amGetParentmenu as $key => $row) {
            $amParentMenu = array('label' => $row->label, 'url' => $row->url, 'id' => [$row->id], 'parent_id' => $row->parent_id , 'class'=> $row->class);
    		$amSubMenu = self::createTree($category , $role_id ,$row->id);
            if(!empty($amSubMenu)){
                $Submenuitems = array();
                foreach ($amSubMenu as $submenukey => $submenuvalue) {
                    $Submenuitems[] = array('label' => $submenuvalue->label, 'url' => $submenuvalue->url, 'id' => $submenuvalue->id, 'parent_id' => $submenuvalue->parent_id, 'class'=> $submenuvalue->class);
                }
                $amParentMenu['items'] = $Submenuitems;
            }
            $Menutree[] = $amParentMenu;
    	}
        return  $Menutree;
    }

/***
create tree structure for menu.
*/
    public static function createTree($category ,$role_id ,$parent_id){
    	$condition = [ 'status'=>self::MENU_ACTIVE_STATUS , 'category' => $category,'user_rules.role_id' => $role_id,'parent_id'=>$parent_id];
    	$tree = UserRulesMenu::find()->joinWith('userRules')->where($condition)->orderBy('position')->all();
    	return $tree;
    }
}
