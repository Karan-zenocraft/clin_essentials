<?php
use common\components\Common;
use common\models\UserRulesMenu;
?>
<div class="span3 leftsidebar" id="sidebar">
    <?php

//Get All sidebar menu code start.
//$amGetParentmenu = UserRulesMenu::getMenu('admin', Yii::$app->user->identity->role_id);
$amGetParentmenu = UserRulesMenu::getMenu('admin', Yii::$app->user->identity['role_id']);
//p($amGetParentmenu);
if (!empty($amGetParentmenu)) {
    ?>
    <ul class="nav nav-list bs-docs-sidenav nav-collapse">
        <?php
foreach ($amGetParentmenu as $key => $value) {
        //$class = (empty($key)) ?  "start active open" : "";  ?>
              <?php
$sidebarclass = "";
        //add last class in End of menu
        $amKeysParentMenu = array_keys($amGetParentmenu);
        if ($key === end($amKeysParentMenu)) {
            $sidebarclass = 'last';
        }

        $submenuites = (!empty($value['items'])) ? Common::array_pluck($value['items'], 'url') : array();
        if (Yii::$app->controller->id === strstr($value['url'], '/', true) || array_search(Yii::$app->controller->id, $submenuites)) {
            $sidebarclass .= ' active open';
        }?>

            <li class="<?php echo $sidebarclass; ?>">
                    <a href=<?php echo !empty($value['items']) ? 'javascript:;' : Common::createUrl($value['url']); ?>>
                    <i class="<?php //echo $value['class'];?>"></i>
                    <span class="title"><?php echo $value['label']; ?></span>
                    <span class="selected"></span>
                    <?php if (!empty($value['items'])) {?>
                    <span class="arrow open"></span>
                    <?php }?>
                    </a>
                <?php if (!empty($value['items'])) {
            ?>
                    <ul class="sub-menu">
                        <?php foreach ($value['items'] as $submenukey => $Submenuitems) {
                $class = ((Yii::$app->controller->id === strstr($Submenuitems['url'], '/', true)) && ("/" . Yii::$app->controller->action->id === strstr($Submenuitems['url'], '/', false))) ? 'active' : '';?>
                            <li class="<?php echo $class; ?>">
                                <a href=<?php echo Common::createUrl($Submenuitems['url']); ?>>
                                <i class=<?php echo $Submenuitems['class']; ?>></i>
                               <?php echo $Submenuitems['label']; ?></a>
                            </li>
                        <?php }?>
                    </ul>
                <?php }?>

            </li>

            <?php }
}
//code end
?>
    </ul>
    <!--ul class="nav nav-list bs-docs-sidenav nav-collapse collapse">
        <li class="active">
            <a href="index.html"><i class="icon-chevron-right"></i> Dashboard</a>
        </li>
        <li>
            <a href="calendar.html"><i class="icon-chevron-right"></i> Calendar</a>
        </li>
        <li>
            <a href="stats.html"><i class="icon-chevron-right"></i> Statistics (Charts)</a>
        </li>
        <li>
            <a href="form.html"><i class="icon-chevron-right"></i> Forms</a>
        </li>
        <li>
            <a href="tables.html"><i class="icon-chevron-right"></i> Tables</a>
        </li>
        <li>
            <a href="buttons.html"><i class="icon-chevron-right"></i> Buttons & Icons</a>
        </li>
        <li>
            <a href="editors.html"><i class="icon-chevron-right"></i> WYSIWYG Editors</a>
        </li>
        <li>
            <a href="interface.html"><i class="icon-chevron-right"></i> UI & Interface</a>
        </li>
        <li>
            <a href="#"><span class="badge badge-success pull-right">731</span> Orders</a>
        </li>
        <li>
            <a href="#"><span class="badge badge-success pull-right">812</span> Invoices</a>
        </li>
        <li>
            <a href="#"><span class="badge badge-info pull-right">27</span> Clients</a>
        </li>
        <li>
            <a href="#"><span class="badge badge-info pull-right">1,234</span> Users</a>
        </li>
        <li>
            <a href="#"><span class="badge badge-info pull-right">2,221</span> Messages</a>
        </li>
        <li>
            <a href="#"><span class="badge badge-info pull-right">11</span> Reports</a>
        </li>
        <li>
            <a href="#"><span class="badge badge-important pull-right">83</span> Errors</a>
        </li>
        <li>
            <a href="#"><span class="badge badge-warning pull-right">4,231</span> Logs</a>
        </li>
    </ul-->
</div>