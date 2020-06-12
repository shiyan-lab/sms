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

use app\portal\model\PortalCategoryPostModel;
use app\portal\model\PortalCategoryModel;
use app\portal\service\PostService;
use cmf\controller\HomeBaseController;
use think\Db;

class UpdatingController extends HomeBaseController
{
    function _initialize()
    {
        $userId  = cmf_get_current_admin_id();
        if(empty($userId))
        {
            exit('请先登录！');
        }
    }
    /**
     * 数据更新
     * @adminMenu(
     *     'name'   => '数据更新',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '数据更新',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $portalCategoryModel = new PortalCategoryModel();
        $categoryTree        = $portalCategoryModel->adminCategoryTree();
        $this->assign('category_tree', $categoryTree);
        return $this->fetch();
    }

    /**
     * 生成首页
     * @adminMenu(
     *     'name'   => '生成首页',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '生成首页',
     *     'param'  => ''
     * )
     */
    public function home()
    {
        $content = $this->fetch(':index');
        file_put_contents(config('html_path').'index.html',htmlspecialchars_decode($content));
        $this->success('生成首页成功!', url('Updating/index'));
    }

    /**
     * 生成单页
     * @adminMenu(
     *     'name'   => '生成单页',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '生成单页',
     *     'param'  => ''
     * )
     */
    public function page()
    {
        //查询所有单页
        $pages = Db::name('portal_post')->where(array('post_type' => 2))->where('filename != \'\'')->select();
        foreach($pages as $key => $page)
        {
            $this->assign('page', $page);
           // file_put_contents('test.txt',$page['more'],8);
            $page['more']=json_decode($page['more'],true);
            $template = !empty($page['more']['template']) ? $page['more']['template'] : 'page';
            $content = $this->fetch(':'.$template);
            file_put_contents(config('html_path').$page['filename'],htmlspecialchars_decode($content));
        }
        $this->success('生成单页成功!', url('Updating/index'));
    }

    /**
     * 在线留言页面
     * @adminMenu(
     *     'name'   => '生成在线留言页面',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '生成在线留言页面',
     *     'param'  => ''
     * )
     */
    public function message()
    {
        //在线留言页面
        $content = file_get_contents('http://'.$_SERVER['SERVER_NAME'].'/portal/message/index');
        file_put_contents(config('html_path').'message.html',htmlspecialchars_decode($content));
        $this->success('生成在线留言页面成功!', url('Updating/index'));
    }

    /*
     * 获取所有分类id
     *用于生成全部列表
     */
    public function getAllCate()
    {
        $category = array();
        $lists = Db::name('portal_category')->where('alias != \'\'')->select();
        foreach($lists as $key => $list)
        {
            $category[] = $list['id'];
        }
        return json_encode($category);
    }
    /*
     * 获取所有分类id
     *用于生成全部内容页
     */
    public function getContentCateIds()
    {
        $categoryIds = $parent_id = array();
        $lists = Db::name('portal_category')->where('alias != \'\'')->select();
        foreach($lists as $key => $list)
        {
            $categoryIds[] = $list['id'];
            if($list['parent_id'] > 0)
            {
                $parent_id[] = $list['parent_id'];
            }
        }
        $result = array_merge(array_diff($categoryIds, $parent_id));
        return json_encode($result);
    }
    /**
     * 生成列表页
     * @adminMenu(
     *     'name'   => '生成列表页',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '生成列表页',
     *     'param'  => ''
     * )
     */
    public function listPage()
    {
        set_time_limit(0);
        $where = array();
        $cid = $this->request->param('cid');
        if(!empty($cid))
        {
            $where['id'] = $cid;
        }
        //查询出所有列表
        $list = Db::name('portal_category')->where('alias != \'\'')->where($where)->find();
        file_put_contents('test.txt',Db::name('portal_category')->getLastSql()."\n",8);
        $this->assign('category', $list);
        $template = !empty($list['list_tpl']) ? $list['list_tpl'] : 'list';
        $dir_path = config('html_path').$list['alias'];
        if(!is_dir($dir_path)){
            mkdir ($dir_path,0777,true);
        }
        $portalCategoryPostModel = new PortalCategoryPostModel();
        $join = ['__PORTAL_POST__ portal_post', 'postCategoryPost.post_id = portal_post.id'];
        //查询该分类的总条数,获得总页数
        $totalCount = $portalCategoryPostModel->alias('portal_category_post')->join($join)->where(array('portal_category_post.category_id' => $list['id'], 'portal_category_post.status' => 1))->count();
        $pageCount = ceil($totalCount/$list['page']);
        if($pageCount < 2)
        {
            $content = $this->fetch(':'.$template);
            file_put_contents($dir_path.'/index'.'.html',htmlspecialchars_decode($content));
        }else{
            for($i = 1; $i <= $pageCount; $i++)
            {
                $content = file_get_contents('http://'.$_SERVER['SERVER_NAME'].'/'.$list['alias'].'.html?page='.$i);
                $content = str_replace($list['alias'].'.html?page=',$list['alias'].'/index_',$content);
                preg_match_all("/(\/".$list['alias']."\/index_[0-9]{1,}+)/",$content,$array2);
                foreach($array2[0] as $k => $item)
                {
                    $content = str_replace($item.'"',$item.'.html"',$content);
                }
                if($i == 1)
                {
                    file_put_contents($dir_path.'/index'.'.html',$content);
                    //file_put_contents(config('html_path').$list['alias'].'.html',$content);
                }
                file_put_contents($dir_path.'/index_'.$i.'.html',$content);
            }
        }
        $this->success($list['name'].'分类列表生成成功!');
    }

    /**
     * 生成内容页
     * @adminMenu(
     *     'name'   => '生成内容页',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '生成内容页',
     *     'param'  => ''
     * )
     */
    public function contentPage()
    {
        set_time_limit(0);
        $where = array();
        $ids = $this->request->param('ids/a');
        if(!empty($ids))
        {
            $where['portal_post.id'] = ['in', $ids];
        }
        $cid = $this->request->param('cid');
        if((empty($cid) || $cid == 0 ) && empty($ids))
        {
            $this->error('参数错误！');
        }
        $child = Db::name('portal_category')->where(array('parent_id' => $cid))->find();
        if(!empty($child))
        {
            $this->success('ok!');
        }
        $where['portal_category_post.category_id'] = $cid;
        $category = array();
        $lists = Db::name('portal_category')->where('alias != \'\'')->select();
        foreach($lists as $key => $list)
        {
            $category[$list['id']] = $list;
        }
        $portalCategoryPostModel = new PortalCategoryPostModel();
        $join = [['__PORTAL_POST__ portal_post', 'portal_category_post.post_id = portal_post.id']];
        $postService         = new PostService();
        //查询所有文章
        $data = $portalCategoryPostModel->alias('portal_category_post')->field('portal_category_post.category_id,portal_post.*')->join($join)->where(array('portal_category_post.status' => 1,'portal_post.post_status' => 1, 'portal_post.delete_time' => 0))->where($where)->select();
        if(!empty($data))
        {
            foreach($data as $key => $item)
            {
                $article    = $postService->publishedArticle($item['id'], $item['category_id']);
                if (!empty($article)) {
                    $prevArticle = $postService->publishedPrevArticle($item['id'], $item['category_id']);
                    $nextArticle = $postService->publishedNextArticle($item['id'], $item['category_id']);
                    $this->assign('category', $category[$item['category_id']]);
                    $this->assign('article', $article);
                    $this->assign('prev_article', $prevArticle);
                    $this->assign('next_article', $nextArticle);
                    $dir_path = config('html_path').$category[$item['category_id']]['alias'];
                    $template = !empty($item['more']['template']) ? $item['more']['template'] : $category[$item['category_id']]['one_tpl'];
                    $content = $this->fetch(':'.$template);
                    file_put_contents($dir_path.'/'.$item['id'].'.html',$content);
                }
            }
        }
        if(!empty($ids))
        {
            $this->success('生成内容页成功!', url('AdminArticle/index'));
        }
        $this->success($category[$cid]['name'].'分类内容页生成成功!');
    }
}