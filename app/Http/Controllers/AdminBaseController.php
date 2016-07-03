<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-23
 * Time: 下午3:44
 */
namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SystemLogger;
use Route;
use Session;
use DB;

class AdminBaseController extends Controller
{
    protected $modelName;
    protected $model;
    protected static $pageSize = 20;//默认pageSize;
    protected $resourceTable = '';
    protected $request;
    protected $action;//当前action
    protected $bExtraSearch = false;//页面是否支持符合查询
    protected $viewVars = [];// 分配到页面的数据数组
    protected $controller;//当前控制器
    protected $resourceName = '';
    protected $bCustomCondition = false;//页面前提条件是否开启
    protected $aCustomConditionArray = [];//页面前提条件数组,支持多个条件组合
    protected $routeName = '';
    //自定义模板部分
    protected $customView = [];//被允许的模板数组
    protected $customPath = '';//模板文件路径
    protected $view = '';//最终整个模板的全路径  path.view
    protected $bIsCached = false;
    protected $defaultRights = ['admin.home', 'auth.login', 'auth.logout', 'console.cache'];//默认拥有权限，默认将首页\登录页\清空缓存写入权限中
    //定义用于页面组装数据的数组，用户edit/create
    protected $params = [];
    //定义语言包
    protected $langVars = [];

    //系统功能配置部分
    //系统权限部分
    //系统搜索配置


    /**
     * 构造器
     * BaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->initCA() or abort(404);
        $this->initModel();
        $this->initMenus();
        $this->checkRights() or abort(403);
        $this->params = $this->request->except('_token');
    }

    /**初始化controller和action
     * @return bool
     */
    public function initCA()
    {
        if (!$ca = Route::currentRouteAction()) {
            return false;
        }
        $aCAs = explode('@', $ca);
        $iPos = strripos($aCAs[0], '\\') - strlen($aCAs[0]) + 1;
        $sController = substr($aCAs[0], $iPos);
        list($this->controller, $this->action) = [$sController, $aCAs[1]];
        return true;
    }

    /**
     *  初始化模型
     */
    protected function initModel()
    {
        if ($sModelName = $this->modelName) {
            $this->resourceName = __('_model.' . $sModelName::$resourceName);
            //modelName存在则实例化model
            $this->model = app()->make($sModelName);
            $this->langVars = ['resource' => __('_model.' . Str::slug($sModelName::$resourceName))];
        }
    }

    /**
     * 初始化菜单
     */
    protected function initMenus()
    {

        $iUserId = Session::get('admin_user_id');
        //得到菜单
        $menus = User::getRights($iUserId, true);
        if (!is_array($menus)) {
            $this->setVars('menus', null);
            $this->setVars('noRoleMsg', '当前用户无角色信息，请联系超级管理员');
        } else {
            $this->setVars('menus', $menus);
        }
    }

    /**
     * 封装给模板传值函数
     * @param $sKey
     * @param null $mValue
     */
    public function setVars($sKey, $mValue = null)
    {
        if (is_array($sKey)) {
            foreach ($sKey as $key => $value) {
                $this->setVars($key, $value);
            }
        } else {
            $this->viewVars["$sKey"] = $mValue;
        }
    }

    /**[回退到列表页面]
     * @param $sMsgType string error|success
     * @param  $sMessage string 消息内容
     * @return \Illuminate\Http\RedirectResponse
     */
    public function goBackToIndex($sMsgType, $sMessage)
    {
        //暂时写死index文件，后续需要改进
        $sToUrl = route($this->routeName . '.' . 'index');
        return redirect()->to($sToUrl)->with($sMsgType, $sMessage);
    }

    /** 定义表单提交后返回的结果
     * @param $sMsgType string error|success
     * @param $sMessage string 消息内容
     * @return \Illuminate\Http\RedirectResponse
     */
    public function goBack($sMsgType, $sMessage)
    {
        return redirect()->back()->withInput()->with($sMsgType, $sMessage);
    }

    /**
     * 定义页面跳转的需要额外传送的数据函数
     */
    public function beforeRender()
    {
    }

    /**
     * 封装页面跳转方法
     * @return mixed
     */
    public function render()
    {
        $this->beforeRender();
        if (!$this->view) {
            if (in_array($this->action, $this->customView) && $this->customPath) {
                $this->view = $this->customPath . '.' . $this->action;
            }
        }
//        pr($this->viewVars);exit;
        return view($this->view)->with($this->viewVars);
    }

    /**首页方法
     * @return mixed
     */
    public function index()
    {
        //is_null($this->request->input()) || count($this->request->input()) < 2
        if (is_null($this->request) || !$this->bExtraSearch) {
//            pr($this->model);exit;
            $oQuery = $this->model->where('id', '>', 0);
            //若存在页面自定义条件检索则追加where
            $oQuery = $this->bCustomCondition ? $this->model->doWhere($this->aCustomConditionArray) : $oQuery;
        } else {
            $aData = trimArray($this->request->except('_token'));
//            组装数组格式
            $aData = $this->makeSearchCondition($aData);
            $oQuery = $this->indexQuery($aData);
            $pageSize = $this->request->input('pageSize');
        }
        //分页部分
        $pageSize = isset($pageSize) && is_numeric($pageSize) ? $pageSize : static::$pageSize;
        $datas = $oQuery->paginate($pageSize);
        $this->setVars('datas', $datas);
//        pr($datas);exit;
        return $this->render();
    }

    /**
     * 保存数据
     * @param $id int [默认空处理创建逻辑，非空处理edit逻辑]
     * @return array
     */
    protected function saveData($id = null)
    {
//        pr($this->params);
        //遍历表单数据避免出现多维数组
        $_params=trimArray($this->params);
        foreach($_params as $key=> $par){
            if(is_array($par)){
                $_params[$key]=implode(',',$par);
            }else{
                $_params[$key]=e($par);
            }
        }
//        pr($_params);
        //数据填充
        $sModel = $id ? $this->model->find($id)->fill($_params) : $this->model->fill($_params);
        $aRules = $this->makeValidateRules($sModel);
//        pr($sModel->toArray());exit;
        $bSucc = $sModel->save($aRules);
        return ['is_success'=>$bSucc, 'model'=>$sModel];
    }

    /** [构造表单验证条件]
     * @param $oModel
     * @return mixed
     */
    protected function makeValidateRules($oModel)
    {
        $sClassName = get_class($oModel);
        return $sClassName::$rules;
    }

    /**
     * [封装的create函数]
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function create()
    {
        if ($this->request->isMethod('post')) {
            DB::connection()->beginTransaction();
            $aResult = $this->saveData();
            if ($aResult['is_success']) {
                DB::connection()->commit();
                SystemLogger::writeLog(Session::get('admin_user_id'), $this->request->url(),
                    $this->request->getClientIp(), $this->controller . '@' . $this->action, '创建' . $this->langVars['resource'] . ':' . $aResult['model']->id);
                return $this->goBackToIndex('succss', __('_basic.created', $this->langVars));
            } else {
                DB::connection()->rollback();
                return $this->goBack('error', __('_basic.create-failed', $this->langVars));
            }
        }
        return $this->render();
    }

    /** [封装update方法]
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function edit($id)
    {
        if (!$id) {
            return $this->goBackToIndex('error',__('_basic.param-error', $this->langVars));
        }
        $_model = $this->model->find($id);
        if (!is_object($_model)) {
            return $this->goBackToIndex('error', __('_basic.model-exist'));
        }
        if ($this->request->isMethod('post')) {
            DB::connection()->beginTransaction();
            $aResult = $this->saveData($id);
            if ($aResult['is_success']) {
                DB::connection()->commit();
                SystemLogger::writeLog(Session::get('admin_user_id'), $this->request->url(),
                    $this->request->getClientIp(), $this->controller . '@' . $this->action, '编辑' . $this->langVars['resource'] . ':' . $id);
                return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
            } else {
                DB::connection()->rollback();
                return $this->goBack('error', __('_basic.update-failed', $this->langVars));
            }
        }
        $this->setVars('data', $_model);
        return $this->render();
    }

    /** [根据主键删除模型,支持单个,多个字符串或数组传入删除]
     * @param $ids
     * @return mixed
     */
    public function destroy($ids = null)
    {
        if (!$ids && !isset($this->params['id'])) {
            return $this->goBackToIndex('error', __('_basic.param-error', $this->langVars));
        }
        $ids or $ids = $this->params['id'];
        $ids = explode(',', $ids);
        $bSucc = false;
        DB::connection()->beginTransaction();
        foreach ($ids as $id) {
            $sModel = $this->model->find($id);
            if (!is_object($sModel)) break;
            if (!$bSucc = $sModel->delete()) break;
        }
        $bSucc ? DB::connection()->commit() : DB::connection()->rollback();
        $sLangKey = '_basic.' . ($bSucc ? 'deleted.' : 'delete-failed.');
        $sType = $bSucc ? 'success' : 'error';
        if($bSucc){
            SystemLogger::writeLog(Session::get('admin_user_id'), $this->request->url(),
                $this->request->getClientIp(), $this->controller . '@' . $this->action, '删除' . $this->langVars['resource'] . ':' . json_encode($ids));
        }
        return $this->goBackToIndex($sType, $sLangKey);
    }

    /** [浏览单条记录详细]
     * @param $id int
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function view($id)
    {
        if (!$id) {
            return $this->goBackToIndex('error', __('_basic.visit-error'));
        }
        $sModel = $this->model;
        $_model = $sModel::find($id);
        if (!is_object($_model)) {
            return $this->goBackToIndex('error', __('_basic.model-exist'));
        }
//        pr($oArticle);exit;
        $this->setVars('data', $_model);
        return $this->render();
    }

    /**页面搜索条件同一处理方法
     * @param $aData
     * @return mixed
     */
    public function indexQuery($aData)
    {
        $this->bExtraSearch && is_array($aData) ? $oQuery = $this->model->doWhere($aData) : $oQuery = $this->model;
        $aOrderSet = [];//获取排序条件
        if ($sOderColumn = $this->request->input('sort_up', $this->request->input('sort_down'))) {
            //  如果sort_up不存在则取sort_down字段
            $sSortType = $this->request->input('sort_up') ? 'asc' : 'desc';
            $aOrderSet[$sOderColumn] = $sSortType;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);
        return $oQuery;
    }

    /**[组装查询字段的数组]
     * @param $aData array 查询的字段数组
     * @return array 改装后的数组
     */
    private function makeSearchCondition($aData)
    {
        $aConditionData = [];
        foreach ($aData as $key => $data) {
            if ($data != '') {//过滤空字段
                if ($key == 'created_from' || $key == 'updated_from') {
                    $aConditionData[$key] = ['>=', strtotime($data)];
                } elseif ($key == 'created_to' || $key == 'updated_to') {
                    $aConditionData[$key] = ['<=', strtotime($data)];
                } else {
                    $aConditionData[$key] = ['=', $data];
                }
            }
        }
        return $aConditionData;
    }

    protected function checkRights()
    {

        $user_id = Session::get('admin_user_id');
        $aMethod = User::getRights($user_id, false);
//        pr($aMethod);exit;
        //用户无角色或者无权限直接false
        if (!is_array($aMethod) || count($aMethod) < 1) return false;
        //得到当前路由名称
        $sCurrentRouteName = Route::currentRouteName();
        //定义返回权限的数组，并合并默认权限
        $aResult = $this->defaultRights;
        foreach ($aMethod as $method) {
            $aResult[] = $method['url'];
            if (isset($method['kids']) && $method['kids']) {
                foreach ($method['kids'] as $item) {
                    $aUrl = explode(',', $item['url']);
                    foreach ($aUrl as $url) {
                        $aResult[] = $url;
                    }
                }
            }
        }
        //判断权限部分
        return in_array($sCurrentRouteName, $aResult) ? true : false;
    }
}
