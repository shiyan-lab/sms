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

class PortalMessageValidate extends Validate
{
    protected $rule = [
        // 用|分开
        'name'       => 'require|chsAlpha',
        'phone'     => 'require',
        'msg' => 'require|max:500'
    ];

    protected $message = [
        'name.require'       => "姓名不能为空！",
        'name.chsAlpha'       => "姓名只能是汉字、字母！",
        'phone.require'     => "号码不能为空!",
        'msg.require' => '留言内容不能为空',
        'msg.max' => '留言长度不能超过500'
    ];


}