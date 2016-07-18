<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-23
 * Time: 下午3:44
 */
namespace App\Http\Controllers;

// Frame
use App\Models\Func\Functionality;
use App\Models\Func\FunctionalityParam;
use App\Models\Func\SearchConfig;
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
        $userRoleIds = Auth::user()->getUserRoles();
        $this->isSystemAdmin = in_array(Role::ADMIN, $userRoleIds);
        $this->aAvailableRealm = $this->isSystemAdmin ? [Functionality::REALM_SYSTEM, Functionality::REALM_ADMIN] : [Functionality::REALM_ADMIN];
        $this->aRights = &Role::getRightOfRoles($userRoleIds);

        //设置功能配置信息
        $this->setFunctionality();

        //权限判定
        if (!in_array($this->controller, $this->openControllers)) {
            $this->functionality or abort(404);
            $this->checkRights() or abort(403);
        }

        //初始化model
        $this->initModel();

        $this->compileResourceName();
    }

    /** [ 初始化controller和action方法 ]
     * @return bool
     */
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

    private function setFunctionality()
    {

        $this->functionality = Functionality::getByCA($this->controller, $this->action, $this->aAvailableRealm);
    }

    private function checkRights()
    {

        if (!$this->functionality || $this->functionality->disabled) return false;
        //获取functionality_params
        $this->paramSettings = FunctionalityParam::getParams($this->functionality->id);
        //request得到所有表单元素
        $this->params = trimArray($this->request->except('_token'));

        if ($this->functionality->need_search) {
            $this->getSearchConfig();
            $this->setSearchInfo();
        }

        if (!isset($enabled)) {
            $this->blockedFuncs = & $this->getBlockedFuncs();
            if ($enabled = !in_array($this->functionality->id, $this->blockedFuncs)) {
                $enabled = in_array($this->functionality->id, $this->aRights);
            }
        }

        return $enabled;

    }

    private function getSearchConfig()
    {

        if ($this->searchConfig = SearchConfig::getForm($this->functionality->search_config_id)) {
            $this->searchItems =& $this->searchConfig->getItems();
        }
    }

    protected function setSearchInfo() {
        $bNeedCalendar = SearchConfig::makeSearhFormInfo($this->searchItems, $this->params, $aSearchFields);
        $this->setVars(compact('aSearchFields', 'bNeedCalendar'));
        $this->setVars('aSearchConfig', $this->searchConfig);
        $this->addWidget('w.search');
    }

    protected function addWidget($sWidget) {
        $this->widgets[] = $sWidget;
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
        $aConditions = &$this->makeSearchConditions();
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

    /**[组建搜索条件]
     * @return array
     */
    protected function & makeSearchConditions()
    {
        $aConditions = [];
        $iCount = count($this->params);
        foreach ($this->paramSettings as $sColumn => $aParam) {
            $aFiledInfo = [];
            if (!isset($this->params[$sColumn])) {
                if ($aParam['when_limit_null'] && $iCount <= 1) {
                    $aFiledInfo[1] = null;
                } else {
                    continue;
                }
            }
            $value = isset($this->params[$sColumn]) ? $this->params[$sColumn] : null;
            if (mb_strlen($value) && !$aParam['when_limit_null']) continue;

            if (!isset($this->searchItems[$sColumn])) {
                $aConditions[$sColumn] = ['=', $value];
                continue;
            }
            $aPattSearch = ['!\$model!', '!\$\$field!', '!\$field!'];
            $aItemConfig =& $this->searchItem[$sColumn];
            $aPattReplace = [$aItemConfig['model'], $value, $aItemConfig['field']];
            $sMatchRule = preg_replace($aPattSearch, $aPattReplace, $aItemConfig['match_rule']);
            $aMatchRule = explode("\n", $sMatchRule);

            if (count($aMatchRule) > 1) {        // OR
                // todo : or
            } else {
                $aFieldInfo = array_map('trim', explode(' = ', $aMatchRule[0]));
                $aTmp = explode(' ', $aFieldInfo[0]);
                $iOperator = (count($aTmp) > 1) ? $aTmp[1] : '=';
                if (!mb_strlen($value) && $aParam['limit_when_null']) {
                    $aFieldInfo[1] = null;
                }
                list($tmp, $sField) = explode('.', $aTmp[0]);
                $sField{0} == '$' or $sColumn = $sField;
                if (isset($aConditions[$sColumn])) {
                    // TODO 原来的方式from/to的值和search_items表中的记录的顺序强关联, 考虑修改为自动从小到大排序的[from, to]数组
                    $arr = [$aConditions[$sColumn][1], $aFieldInfo[1]];
                    sort($arr);
                    // $sFrom = $aConditions[$sColumn][1];
                    // $sTo = $aFieldInfo[1];
                    $aConditions[$sColumn] = ['between', $arr];
                } else {
                    $aConditions[$sColumn] = [$iOperator, $aFieldInfo[1]];
                }
            }
        }
        return $aConditions;

    }

    public function edit($id)
    {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_model.model-missing', $this->langVars));
        }
        if ($this->request->isMethod('PUT')) {
            DB::connection()->beginTransaction();
            $bSucc = $this->saveData();
            if ($bSucc) {
                DB::commit();
                return $this->goBackToIndex('success', __('_basic.updated', $this->langVars));
            } else {
                DB::rollback();
                $this->langVars['reason'] = $this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.updat-fail', $this->langVars));
            }
        }
        $parent_id = $this->model->parent_id;
        $data = $this->model;
        $isEdit = true;
        $this->setVars(compact('data', 'isEdit', 'parent_id', 'id'));
        return $this->render();
    }

    /**
     * [create 新增记录]
     * @param  [Integer] $id [要新增记录所属的父记录id]
     * @return [Response]
     */
    public function create($id = null)
    {
        // pr($this->request->method());exit;
        if ($this->request->isMethod('POST')) {

            DB::connection()->beginTransaction();
            if ($bSucc = $this->saveData($id)) {
                DB::connection()->commit();
                Event::fire(new BaseCacheEvent($this->model));
                return $this->goBackToIndex('success', __('_basic.created', $this->langVars));
            } else {
                DB::connection()->rollback();
                $this->langVars['reason'] = &$this->model->getValidationErrorString();
                return $this->goBack('error', __('_basic.create-fail', $this->langVars));
            }
        } else {
            $data = $this->model;
            $isEdit = false;
            $this->setVars(compact('data', 'isEdit'));
            $sModelName = $this->modelName;
            list($sFirstParamName, $tmp) = each($this->paramSettings);
            !isset($sFirstParamName) or $this->setVars($sFirstParamName, $id);
            $aInitAttributes = isset($sFirstParamName) ? [$sFirstParamName => $id] : [];
            $this->setVars(compact('aInitAttributes'));
            return $this->render();
        }
    }

    /**
     * view model
     * @param int $id
     * @return bool
     */
    public function view($id)
    {
        $this->model = $this->model->find($id);
        if (!is_object($this->model)) {
            return $this->goBackToIndex('error', __('_basic.missing', $this->langVars));
        }
        $data = $this->model;
        $sModelName = $this->modelName;
        if ($sModelName::$treeable) {
            if ($this->model->parent_id) {
                if (!array_key_exists('parent', $this->model->getAttributes())) {
                    $sParentTitle = $sModelName::find($this->model->parent_id)->{$sModelName::$titleColumn};
                } else {
                    $sParentTitle = $this->model->parent;
                }
            } else {
                $sParentTitle = '(' . __('_basic.top_level', [], 3) . ')';
            }
        }
        $this->setVars(compact('data', 'sParentTitle'));
        return $this->render();
    }

    /**
     * [destroy 删除,支持批量删除]
     * @param  [Integer] $id [记录id]
     * @return [Response]
     */
    public function destroy($id = null)
    {
        // pr($id);exit;
        if (empty($id) && !isset($this->params['id'])) {
            return $this->goBackToIndex('error', __('_basic.param-error', $this->langVars));
        }
        $id or $id = $this->params['id'];
        $aIds = explode(',', $id);
        $sModelName = $this->modelName;
        $bSucc = false;
        DB::connection()->beginTransaction();
        foreach ($aIds as $id) {
            $model = $sModelName::find($id);
            if (empty($model)) {
                break;
            }
            if ($sModelName::$treeable) {
                if ($iSubCount = $model->where('parent_id', '=', $model->id)->count()) {
                    $this->langVars['reason'] = __('_basic.not-leaf', $this->langVars);
                    return redirect()->back()->with('error', __('_basic.delete-fail', $this->langVars));
                }
            }
            if (!$bSucc = $model->delete() && $this->afterDestroy($model)) {
                break;
            }
        }
        $bSucc ? DB::connection()->commit() : DB::connection()->rollback();
        $sLangKey = '_basic.' . ($bSucc ? 'deleted.' : 'delete-fail.');
        $sType = $bSucc ? 'success' : 'error';
        // pr($sType);exit;
        return $this->goBackToIndex($sType, __($sLangKey, $this->langVars));
    }

    /** [删除后操作]
     * @param $oModel object 要处理的模型对象
     * @return bool
     */
    protected function afterDestroy($oModel)
    {
        return true;
    }

    /** [表单数据保存方法]
     * @return mixed
     */
    protected function saveData()
    {
        $this->_fillModelDataFromInput();
        $aRules = $this->_makeValidateRules($this->model);
        return $this->save($aRules);
    }

    /**
     * [表单填充方法,内部调用]
     */
    protected function _fillModelDataFromInput()
    {

        $data = $this->params;
        $sModelName = $this->modelName;
        !empty($this->model->columnSetting) or $this->model->makeColumnConfigures();

        foreach ($this->model->columnSetting as $sColumn => $aSetting) {

            if ($sColumn == 'id') continue;
            if (!isset($aSetting['type'])) continue;

            switch ($aSetting['type']) {
                case 'bool':
                case 'numeric':
                case 'integer':
                    !empty($data[$sColumn]) or $data[$sColumn] = 0;
                    break;
                case 'select':
                    if (isset($data[$sColumn]) && is_array($data[$sColumn])) {
                        sort($data[$sColumn]);
                        $data[$sColumn] = implode(',', $data[$sColumn]);
                    }
            }
        }
        $this->model->fill($data);

        if ($sModelName::$treeable) {
            $this->model->parent_id or $this->model->parent_id = null;
            if ($sModelName::$foreFatherColumn) {
                $this->model->{$sModelName::$foreFatherColumn} = $this->model->setForeFather();
            }
        }
    }

    /** [自定义表单验证方法]
     * @param $oModel
     * @return mixed
     */
    protected function _makeValidateRules($oModel)
    {
        $sModel = get_class($oModel);
        $aRules = $sModel::$rules;
        return $aRules;
    }
}
