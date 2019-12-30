<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        echo  "nihao!";
    }

    public function phpInfo()
    {
        phpinfo();
    }
}