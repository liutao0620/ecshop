<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends MyController {
    public function index()
    {
        $this->display();
    }
    public function top()
    {
        $this->display();
    }
    public function left()
    {
        $adminmodel=D('Admin');
        $privdata=$adminmodel->getButton();
        $this->assign('privdata',$privdata);
        $this->display();
    }
    public function drag()
    {
        $this->display();
    }
    public function main()
    {
        $this->display();
    }
     
}