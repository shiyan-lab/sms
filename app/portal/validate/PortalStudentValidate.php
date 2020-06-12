<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\portal\validate;

use think\Validate;

class PortalStudentValidate extends Validate
{
    protected $rule = [
        'id'  => 'require',
        'name'  => 'require|chs',        
        'mobile' => 'require',
        'class' => 'require',
    ];
    protected $message = [
        'id.require' => '学号不能为空',
        'id.integer' => '学号必须由数字组成',
        'name.require'=>'姓名不能为空', 
        'name.chs'=>'姓名必须为汉字',
        'mobile.require' => '手机号码不能为空',
        'class.require' => '科目不能为空',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];

   
}