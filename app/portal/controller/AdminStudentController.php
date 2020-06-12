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

use cmf\controller\AdminBaseController;
use app\portal\model\PortalStudentModel;
use app\portal\model\PortalGradeModel;
use think\Db;



class AdminStudentController extends AdminBaseController
{
       
        /**
     * 学生列表
     * @adminMenu(
     *     'name'   => '学生管理',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '学生列表',
     *     'param'  => ''
     * )
     */
    public function index(){

        $params=$this->request->param();
        $gradeid = $this->request->param('grade', 0, 'intval');
        $classid = $this->request->param('class', 0, 'intval');

        $portalStudentModel = new PortalStudentModel();

        $where=['delete_time'=>'0'];
        if(!empty($gradeid)&&!empty($classid)&&$gradeid!=0&&$classid!=0)
        {
            $where = [
                'delete_time'=>'0',               
                'grade'       => $gradeid,
                'class'       => $classid
            ];
        }

        $student = $portalStudentModel->order(['grade'=>'asc','class'=>'asc','id'=>'asc'])->where($where)->paginate(10);
        $student->appends($params); 
        $page = $student->render();
        $grade =array('1' => '小学一年级','2'=>'小学二年级','3'=>'小学三年级','4'=>'小学四年级','5'=>'小学五年级','6'=>'小学六年级','7'=>'初中七年级','8'=>'初中八年级','9'=>'初中九年级');
        $class =array('1'=>'一班','2'=>'二班','3'=>'三班','4'=>'四班','5'=>'五班','6'=>'六班','7'=>'七班','8'=>'八班','9'=>'九班','10'=>'十班','11'=>'十一班','12'=>'十二班','13'=>'十三班','14'=>'十四班','15'=>'十五班');
        
        //获取年级班级设置


        $this->assign('student', $student);
        $this->assign('page', $page);

        return $this->fetch();
    }
  
    /**
     * 添加学生
     * @adminMenu(
     *     'name'   => '添加学生',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加学生',
     *     'param'  => ''
     * )
     */
    public function add()
    {

        
        return $this->fetch();
    }

    
    /**
     * 添加学生提交
     * @adminMenu(
     *     'name'   => '添加学生提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加学生提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {   
        if ($this->request->isPost()) {        
            $data = $this->request->param();             
            $student   = $data['student'];
            $result = $this->validate($student, 'PortalStudent');
               
            if ($result !== true) {
                $this->error($result);
            }

            $portalStudentModel = new PortalStudentModel();

            //是否存在该学号
            $findStudent = $portalStudentModel->where('id', $student['id'])->find();          
            if(!empty($findStudent)){
                $this->error("该学号的学生信息已经录入，请核查！");
            }
            
            //添加学生信息
            $result = $portalStudentModel->adminAddStudent($student); 

            if ($result === false) {
                $this->error('添加失败!');
            } 

            $this->success('添加成功!');
        }
    }

   
    /**
     * 编辑学生
     * @adminMenu(
     *     'name'   => '编辑学生',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑学生',
     *     'param'  => ''
     * )
     */
    public function edit()
    {  
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $student = PortalStudentModel::get($id)->toArray();
            if(empty($student)){
                $this->error("不存在该学生信息！");
            }

            $this->assign('id', $id);
            $this->assign($student);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }


    /**
     * 编辑学生提交
     * @adminMenu(
     *     'name'   => '编辑学生提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑学生提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();                      
            $student   = $data['student'];                
            $result = $this->validate($student, 'PortalStudent');
            
            if ($result !== true) {
                $this->error($result);
            }
            
            $portalStudentModel = new PortalStudentModel();
            //是否存在该学号
            $findStudent = $portalStudentModel->where('id', $student['id'])->find();          
            if(empty($findStudent)){
                $this->error("信息修改失败，不存在该学号的信息！");
            }
                       
            $result=$portalStudentModel->adminEditStudent($student);
            if ($result === false) {
                $this->error('保存失败!');
            } 
            $this->success('保存成功!');
        }
    }
   
    /**
     * 学生排序
     * @adminMenu(
     *     'name'   => '学生排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '学生排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('portal_subject'));
        $this->success("排序更新成功！", '');
    }
 
    /**
     * 学生删除
     * @adminMenu(
     *     'name'   => '学生删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '学生删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $param           = $this->request->param();
        $portalStudentModel = new PortalStudentModel();

        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result       = $portalStudentModel->where(['id' => $id])->find();
            $data         = [
                'object_id'   => $result['id'],
                'create_time' => time(),
                'table_name'  => 'portal_student',
                'name'        => $result['name'],
                'user_id'     => cmf_get_current_admin_id()
            ];
            $resultPortal = $portalStudentModel
                ->where(['id' => $id])
                ->update(['delete_time' => time()]);
            if ($resultPortal) {
                //Db::name('portal_category_post')->where(['post_id' => $id])->update(['status' => 0]);
                //Db::name('portal_tag_post')->where(['post_id' => $id])->update(['status' => 0]);
                //放进回收站
                Db::name('recycleBin')->insert($data);
            }
            $this->success("删除成功！", '');

        }

        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
            $recycle = $portalStudentModel->where(['id' => ['in', $ids]])->select();
            $result  = $portalStudentModel->where(['id' => ['in', $ids]])->update(['delete_time' => time()]);
            if ($result) {
                //Db::name('portal_category_post')->where(['post_id' => ['in', $ids]])->update(['status' => 0]);
                //Db::name('portal_tag_post')->where(['post_id' => ['in', $ids]])->update(['status' => 0]);
                foreach ($recycle as $value) {
                    $data = [
                        'object_id'   => $value['id'],
                        'create_time' => time(),
                        'table_name'  => 'portal_student',
                        'name'        => $value['name'],
                        'user_id'     => cmf_get_current_admin_id()
                    ];
                    Db::name('recycleBin')->insert($data);
                }
                $this->success("删除成功！", '');
            }
        }
    }
}
