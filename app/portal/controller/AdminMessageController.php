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
namespace app\portal\controller;

use app\portal\model\MessageModel;
use app\portal\model\PortalMessageModel;
use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\ThemeModel;

class AdminMessageController extends AdminBaseController
{
    /**
     * 留言列表
     * @adminMenu(
     *     'name'   => '留言列表',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '留言列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $post_id = $this->request->param('id', 0, 'intval');
        $model = new PortalMessageModel();
        $datas = $model->order('id desc')->where(['post_id'=>$post_id])->paginate();
        $this->assign("datas", $datas);
        $this->assign('page', $datas->render());
        return $this->fetch();
    }


    public function delete()
    {
        $param           = $this->request->param();
        $portalMessageModel = new PortalMessageModel();

        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result       = $portalMessageModel->where(['id' => $id])->find();
            if ($result) {
                $portalMessageModel
                    ->where(['id' => $id])
                    ->delete();
                $this->success("删除成功！", URL('/AdminStudent/delete'));
            }


        }

        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
            $portalMessageModel->where(['id' => ['in', $ids]])->delete();
            $this->success("删除成功！",URL('/AdminStudent/delete'));
        }
    }
}
