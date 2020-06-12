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

use app\portal\service\ApiService;
use cmf\controller\HomeBaseController;
use app\portal\model\PortalCategoryModel;
use app\portal\model\PortalCategoryPostModel;
use app\portal\model\PortalPostModel;
use think\Db;

class ImportController extends HomeBaseController
{
    /*
     * 织梦数据导入
     */
    public function zhimeng()
    {
        $param = $this->request->param();
        if(!empty($param))
        {
            $portal_post =new PortalPostModel();
            $portal_category_post =new PortalCategoryPostModel();
            $classid = $param['classid'];//织梦栏目id
            $cid = $param['cid'];//cmf分类id
//            $portal_category = new PortalCategoryModel();
//            $parent = $portal_category->where(array('id' => $cid))->field('parent_id')->find();
//            $parent_id = $parent['parent_id'];
            $db2 = [
                // 数据库类型
                'type'     => 'mysql',
                // 服务器地址
                'hostname' => 'localhost',
                // 数据库名
                'database' => 'nyyzfx',
                // 用户名
                'username' => 'root',
                // 密码
                'password' => 'root',
                // 端口
                'hostport' => '3306',
                // 数据库编码默认采用utf8
                'charset'  => 'utf8',
                // 数据库表前缀
                'prefix'   => 'dede_',
                "authcode" => 'b1z2fSoQkRIZchY1dW',
                //#COOKIE_PREFIX#
            ];
            $dgprefix = 'dede_';
            $sql = "select sortrank,pubdate,senddate,click,title,litpic,description,body from {$dgprefix}archives join {$dgprefix}addonarticle on {$dgprefix}archives.id = {$dgprefix}addonarticle.aid where {$dgprefix}archives.typeid = {$classid} and {$dgprefix}archives.ismake = 1 order by {$dgprefix}archives.id desc";
            $result = Db::connect($db2)->query($sql);
            file_put_contents('testd.txt', $sql."\n\n",FILE_APPEND);
            foreach ($result as $key => $value)
            {
                $body = str_replace('div','p',$value['body']);
                $more = array('audio' => '','video' => '', 'thumbnail' => $value['litpic'],'template' => 'article');
                $data = array(
                    'user_id' => 1,
                    'post_hits' => $value['click'],
                    'create_time' => $value['sortrank'],
                    'update_time' => $value['pubdate'],
                    'published_time' => $value['senddate'],
                    'post_title' => $value['title'],
                    'post_excerpt' => $value['description'],
                    'thumbnail' => $value['litpic'],
                    'post_content' => htmlspecialchars(stripslashes($body)),
                    'more' => json_encode($more)
                );
                $portal_post->insert($data);
                $id = $portal_post->getLastInsID();
                $portal_category_post->insert(array('post_id' => $id, 'category_id' => $cid));
//                if($parent_id > 0)
//                {
//                    $portal_category_post->insert(array('post_id' => $id, 'category_id' => $parent_id));
//                }
            }
            echo 'ok!';
        }else{
            return $this->fetch();
        }
    }
    /*
    * 帝国数据导入
    */
    public function diguo()
    {
        $param = $this->request->param();
        if(!empty($param))
        {
            $portal_post =new PortalPostModel();
            $portal_category_post =new PortalCategoryPostModel();
            $classid = $param['classid'];//帝国栏目id
            $cid = $param['cid'];//cmf分类id
//            $portal_category = new PortalCategoryModel();
//            $parent = $portal_category->where(array('id' => $cid))->field('parent_id')->find();
//            $parent_id = $parent['parent_id'];
            $db2 = [
                // 数据库类型
                'type'     => 'mysql',
                // 服务器地址
                'hostname' => 'localhost',
                // 数据库名
                'database' => 'nysjsbyy',
                // 用户名
                'username' => 'root',
                // 密码
                'password' => 'root',
                // 端口
                'hostport' => '3306',
                // 数据库编码默认采用utf8
                'charset'  => 'utf8',
                // 数据库表前缀
                'prefix'   => 'hd_',
                "authcode" => 'b1z2fSoQkRIZchY1dW',
                //#COOKIE_PREFIX#
            ];
            $dgprefix = 'handu_';
            $sql = "select onclick,truetime,lastdotime,titleurl,title,newstime,titlepic,ftitle,smalltext,writer,befrom,newstext from {$dgprefix}ecms_news join {$dgprefix}ecms_news_data_1 on {$dgprefix}ecms_news.id = {$dgprefix}ecms_news_data_1.id where {$dgprefix}ecms_news.classid = {$classid} order by {$dgprefix}ecms_news.id desc";
            $result = Db::connect($db2)->query($sql);
            foreach ($result as $key => $value)
            {
                $more = array('audio' => '','video' => '', 'thumbnail' => $value['titlepic'],'template' => 'article');
                $data = array(
                    'user_id' => 1,
                    'post_hits' => $value['onclick'],
                    'create_time' => $value['truetime'],
                    'update_time' => $value['lastdotime'],
                    'published_time' => $value['newstime'],
                    'post_title' => $value['title'],
                    'post_excerpt' => $value['smalltext'],
                    'post_source' => $value['befrom'],
                    'thumbnail' => $value['titlepic'],
                    'post_content' => htmlspecialchars(stripslashes($value['newstext'])),
                    'more' => json_encode($more)
                );
                $portal_post->insert($data);
                $id = $portal_post->getLastInsID();
                $portal_category_post->insert(array('post_id' => $id, 'category_id' => $cid));
//                if($parent_id > 0)
//                {
//                    $portal_category_post->insert(array('post_id' => $id, 'category_id' => $parent_id));
//                }
            }
            echo 'ok!';
        }else{
            return $this->fetch();
        }
    }
    /*
     * 为父级分类增加子类文章
     */
    public function addCate()
    {
        $portal_category_post =new PortalCategoryPostModel();
        $cate = $portal_category_post->where(array('category_id' => array('in', [2,3,4,5])))->select();
        foreach($cate as $key => $item)
        {
            $portal_category_post->insert(array('post_id' => $item['post_id'], 'category_id' => 1));
        }
        echo 'ok!';
    }
}
