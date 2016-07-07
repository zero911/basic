<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-23
 * Time: 下午3:44
 */
namespace App\Http\Controllers;

// Frame
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

// Facades
use Auth;
use Config;
use Route;
use Session;
use Tool;
use Input;
use Lang;
use Carbon;
use DB;
// Custom
use FormHelper;
use DownExcel;
use PHPExcel_Settings;
use PHPExcel_CachedObjectStorageFactory;
use Event;

// Event
use App\Events\BaseCacheEvent;

class AdminBaseController extends Controller
{
    //model的namespace
    protected $modelName;
    //模型实例
    protected $model;
    //request请求实例
    protected $request;
    //当前的controller
    protected $controller;
    //当前action
    protected $action;
    //面包屑名称
    protected $resourceName;
    //资源数据库表
    protected $resourceTable;
    //路由名称
    protected $resource;
    //友好的model
    protected $friendlyModelName;
    //需要加载错误码定义文件
    protected $errorFiles = [];
    //模板文件名称
    protected $resourceView = 'default';
    //自定义的模板文件
    protected $view = '';
    //自定义的模板文件路径
    protected $customViewPath = '';
    //自定义模板文件
    protected $customViews = [];
    //模板所有提前需要渲染的数据数组
    protected $viewVars = [];
    //分页大小
    protected static $pageSize = 20;
    //须自动准备数据的视图名称
    protected $composerViews = array(
        'view',
        'index',
        'create',
        'edit',
    );
    //functionality model
    protected $functionality = null;
    // 用于关联按钮的语言包键数组
    protected $langKeysForButtons = [];
    //视图使用的样式名
    public $viewClasses = [
        'div' => 'form-group ',
        'label' => 'col-sm-3 control-label ',
        'input_div' => 'col-sm-5 ',
        'msg_div' => 'col-sm-4 ',
        'msg_label' => 'text-danger control-label ',
        'radio_div' => 'switch ',
        'select' => 'form-control ',
        'input' => 'form-control ',
        'radio' => 'boot-checkbox',
        'date' => 'input-group date form_date ',
    ];
    protected static $aRedirectFullUrlActions = [
        'encode',
        'index',
        'settings',
        'listSearchConfig'
    ];
    //自定义验证消息
    protected $validatorMessages = [];
    //消息对象
    protected $messages = null;
    //sysConfig model 配置模型
    protected $sysConfig;
    //search config 搜索配置
    protected $searchConfig;
    //search fields 搜索字段配置
    protected $searchItems = [];
    //组装所有表单字段的数组: get/post
    protected $params = [];
    //param settings
    protected $paramSettings;
    //用于重定向的key
    protected $redirectKey;
    //采用的搜索模板Widgets
    protected $widgets = [];
    // bread crumbs
    protected $breadcrumbs = [];
    //for lang transfer
    protected $langVars = [];
    //for lang transfer, short title
    protected $langShortVars = [];
    //default lang file
    protected $defaultLangPack;
    //if is administrator
    protected $admin;
    //if its system administrator or not
    protected $isSystemAdmin = false;
    //allowed realm array
    protected $aAvailableRealm = [];
    // rights array
    protected $aRights = [];
    //Client IP
    protected $clientIP;
    //Proxy IP
    protected $proxyIP;
    //Need Right Check
    protected $needRightCheck = true;
    //当前用户可访问的功能ID列表
    protected $hasRights = null;
    // if its ajax
    protected $isAjax = false;
    //不进行权限检查的控制器列表
    protected $openControllers = ['AuthController', 'HomeController'];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->ajax = $request->ajax();
        //初始化controller和action
        $this->initCA() or abort(403);
        //todo 权限处理开始
        //获取functionality
        //初始化model
        $this->initModel();

        $this->compileResourceName();
    }

    protected function initCA()
    {
        if (!$ca = Route::getCurrentAtion()) {
            return false;
        }
        $ca = explode('@', $ca);
        $iPos = strripos($ca[0], '\\') - strlen($ca[0]) + 1;
        $sController = substr($ca[0], $iPos);
        list($this->controller, $this->action) = [$sController, $ca[1]];
        return true;
    }

    protected function initModel()
    {
        if ($sModelName = $this->modelName) {
            $this->resourceName = __('_model.' . $sModelName::$resourceName);
            $this->model = app()->make($sModelName);
            $this->resourceTable = $this->model->getTable();
            $this->friendlyModelName = Str::slug($sModelName);
            $this->langShortVars = ['resource' => null];
            $this->langVars = ['resource' => __('_model.' . Str::slug($sModelName::$resourceName))];
            $this->defaultLangPack = $sModelName::compileLangPack();
        }
    }

    protected function compileResourceName()
    {
        if ($this->resource) return true;

        if (!$sModelName = $this->modelName) return true;

        $sResouceName = $sModelName::$resourceName;
        $this->resource = Str::slug(Str::plural($sResouceName));
        return true;
    }

    protected function beforeRender()
    {

    }

    protected function compileRenderVars()
    {

    }

    /**[统一重定向到模板函数]
     * @return $this
     */
    protected function render()
    {
        $this->beforeRender();
        $this->compileRenderVars();
        if (!$this->view) {
            if (in_array($this->action, $this->customViews) && $this->customViewPath) {
                $this->resourceView = $this->customViewPath;
            }
            $this->view = $this->resourceView . '.' . $this->action;
        }
        return view($this->view)->with($this->viewVars);
    }

    /** [统一重定向返回上级函数]
     * @param $sMsgType string success/error
     * @param $sMsg string 消息内容
     * @param bool $bWithModelErrors 消息内容是否包含模型validationErrors
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    protected function goBack($sMsgType, $sMsg, $bWithModelErrors = false)
    {

        $oRedirectResponse = redirect()->back()->withInput()->with($sMsgType, $sMsg);
        !$bWithModelErrors or $oRedirectResponse = $oRedirectResponse->withErrors($this->model->validationErrors());

        return $oRedirectResponse;
    }

    /** [统一重定向返回index函数]
     * @param $sMsgType string success/error
     * @param $sMsg string 消息内容
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    protected function goBackToIndex($sMsgType, $sMsg)
    {
        $sToUrl = $this->request->session()->get($this->redictKey) or $sToUrl = route('admin.dashboard');
        return redirect()->to($sToUrl)->with($sMsgType, $sMsg);
    }

    /**[统一为模板准备数据方法]
     * @param $sKey
     * @param null $sValue
     */
    protected function setVars($sKey, $sValue = null)
    {
        if (is_array($sKey)) {
            foreach ($sKey as $key => $value) {
                $this->viewVars[$key] = $value;
            }
        } else {
            $this->viewVars[$sKey] = $sValue;
        }
    }


    public function index()
    {
        $oQuery = $this->indexQuery();
        $sModelName = $this->modelName;
        $iPageSize = isset($this->params['pagesize']) ? $this->params['pagesize'] : static::$pageSize;
        $datas = $oQuery->paginage($iPageSize);
        $this->setVars('datas', $datas);
        if ($sMainParamName = $sModelName::$mainParamColumn) {
            if (isset($aConditions[$sMainParamName])) {
                $$sMainParamName = is_array($aConditions[$sMainParamName][1]) ? $aConditions[$sMainParamName][1][0] : $aConditions[$sMainParamName][1];
            } else {
                $$sMainParamName = null;
            }
            $this->setVars(compact($sMainParamName));
        }
        return $this->render();
    }

    protected function compileParams()
    {

    }

    public function indexQuery()
    {
        $this->compileParams();
        $aConditions = & $this->makeSearchConditions();
        $oQuery = $aConditions ? $this->model->doWhere($aConditions) : $this->model;
        //是否支持软删除
        $bWithTrashed = trim($this->request->input('_withTrashed', 0));
        if ($bWithTrashed) {
            $oQuery = $oQuery->withTrashed();
        }
        //是否支持分组查询
        if ($sGroupByColumn = $this->request->input('group_by')) {
            $oQuery = $this->model->doGroupBy($oQuery, [$sGroupByColumn]);
        }
        //获取排序条件
        $aOrderSet = [];
        if ($sOrderByColumn = $this->request->input('sort_up', $this->request->input('sort_down'))) {
            $sDirection = $this->request->input('sort_up') ? 'asc' : 'desc';
            $aConditions[$sOrderByColumn] = $sDirection;
        }
        $oQuery = $this->model->doOrderBy($oQuery, $aOrderSet);
        return $oQuery;
    }

    protected function & makeSearchConditions()
    {

        $aConditions = [];

    }

    public function edit($id)
    {
        $data = $this->model->find($id);
        if (!is_object($data)) {
            return $this->goBackToIndex('error', __('_model.model-missing', $this->langVars));
        }

    }

    public function create($id = null)
    {
    }

    public function view($id)
    {
    }

    public function destroy($id)
    {
    }
}
