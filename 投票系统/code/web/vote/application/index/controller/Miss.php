<?php
namespace app\index\controller;

use think\Controller;

class Miss extends Controller
{
    public function miss()
    {
        header("https://www.baidu.com");
    }
}