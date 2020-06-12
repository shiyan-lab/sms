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
namespace app\user\model;

use think\Model;
use think\db;

class PortalMessageModel extends Model
{
    public function lists()
    {

        $userId        = cmf_get_current_user_id();
        $user = new UserModel();
        $user = $user->where(['id'=>$userId])->find();
        $tempCourses =$this->where('userid','eq',0)->where('post_id','>',0)->where('phone','eq',$user['mobile'])
            ->select();
        if(!empty($tempCourses))
        {
            $userNew =[];
            $userNew['userid']=$userId;
            $this->field(true)
                ->where('userid','eq',0)->where('post_id','>',0)->where('phone','eq',$user['mobile'])
                ->setField($userNew);
        }
        $courses     = $this->where('userid',$userId)->order('id desc')->paginate(10);
        $data['page']  = $courses->render();
        $data['lists'] = $courses->items();
        return $data;
    }
}