<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use app\portal\model\PortalMessageModel;
use cmf\controller\HomeBaseController;
use think\Env;


class MessageController extends HomeBaseController
{
    /**
     * 联系方式
     *
     * @param $tel
     * @return tel
     */
    function checkTel($tel)
    {
        $isMob="/^1[3-8]{1}[0-9]{9}$/";
        $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        if(!preg_match($isMob,$tel) && !preg_match($isTel,$tel))
        {
            $this->error("电话格式错误！");
        }
        return $tel;
    }
    /*
     * 表单提交--在线预约
     */
    public function add()
    {
        header("Access-Control-Allow-Origin:*");
        file_put_contents('test.txt','123');
        $model = new PortalMessageModel();
        $data = $this->request->post();
        $isMob="/^1[3-8]{1}[0-9]{9}$/";
        $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        if(!preg_match($isMob,$data['phone']) && !preg_match($isTel,$data['phone']))
        {
 //          $this->error('电话格式错误!');
 //          return '电话格式错误！';
            return json_encode(array('msg' => '电话格式错误!'));
        }

        $result = $this->validate($data, 'PortalMessage');

        if ($result !== true) {
//            $this->error($result);
//            return $result;
            return json_encode(array('msg' => $result));
        }

        if ($this->request->isPost()) {
            $data['createtime'] = time();
            $result=$model->allowField(true)->save($data);
            if ($result==true) {
//                $this->success('预约成功!');
//                return '预约成功！';
                return json_encode(array('msg' => '预约成功!'));
            } else {
//                $this->error('预约失败!');
//                return '预约失败！';
                return json_encode(array('msg' => '预约失败!'));
            }
        } else{
          //  file_put_contents('test.txt','2222222222'."\r\n",8);
        }
    }


    public function  messageCount()
    {
        $post_id = $this->request->param('post_id', 0, 'intval');
        if($post_id!=0)
        {
            $model = new PortalMessageModel();
            $count = $model->field('count(id) as id')->where('post_id','eq',$post_id)->find();
            file_put_contents('test.txt',json_encode($count)."\r\n",8);
            return json_encode(array('msg' => $count['id']));
        }
        else
        {
            return json_encode(array('msg' => '0'));
        }
    }

    public function index()
    {
        $post_id = $this->request->param('id', 0, 'intval');
        $this->assign('post_id',$post_id);
        return $this->fetch(':message');
    }
}
