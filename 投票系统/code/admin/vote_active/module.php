<?php

defined('IN_IA') or exit('Access Denied');

class Applet_testModule extends WeModule 
{
    public function settingsDisplay()
    {
        // 声明为全局才可以访问到.
        global $_W, $_GPC;
        // 模板中需要用到 "tpl" 表单控件函数的话, 记得一定要调用此方法.
        load()->func('tpl');
        //这里来展示设置项表单
        include $this->template('setting');
    }
}