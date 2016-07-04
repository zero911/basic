<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-28
 * Time: 下午8:33
 */

namespace App\Http\Controllers;

use App\Models\SettingType;
use Illuminate\Support\Facades\Validator;
use Cache;
use Request;
use Input;
use App\Models\SystemLogger;
use Session;

class SettingController extends AdminBaseController
{
    protected $customPath = 'back.setting';
    protected $customView = [
        'index', 'edit', 'create',
    ];
    protected $modelName = 'App\Models\Settings';
    protected $routeName = 'setting';

    public function beforeRender()
    {
        parent::beforeRender();
        $this->setVars('_title', __('_basic.setting'));
        $types = SettingType::all();
        $this->setVars('types', $types);
    }

    public function index()
    {

        if (Cache::has('setting')) {
            $datas = Cache::get('setting');
        } else {
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
            $pageSize = isset($pageSize) && is_numeric($pageSize) ? $pageSize : static::$pageSize;
            $datas = $oQuery->where('status', '=', 1)->paginate($pageSize);
            Cache::put('setting', $datas, 60 * 24 * 7);
        }
        //分页部分
        $this->setVars('settings', $datas);
        return $this->render();
    }

    public function create()
    {

        $sModel = $this->model;
        if (Request::isMethod('post')) {
            $aData = trimArray(Input::except('_token'));
            $validate = Validator::make($aData, $sModel::$rules);
            if (!$validate->passes()) {
                return $this->goBack('error', __('_basic.validate-error'));
            }
            $sModel->name = $aData['name'];
            $sModel->value = $aData['value'];
            $sModel->type_id = $aData['type_id'];
            $sModel->status = $aData['status'];
            $bSucc = $sModel->save();
            if ($bSucc) {
                Cache::put('system', $this->model->where('id', '>', 0)->get(), 60 * 24 * 7);
                SystemLogger::writeLog(Session::get('admin_user_id'),$this->request->url(),
                    $this->request->getClientIp(),$this->controller.'@'.$this->action,'创建系统设置:'.$sModel->id);
                return $this->goBackToIndex('success', __('_system.setting-create-success'));
            } else {
                return $this->goBack('error', __('_system.setting-create-error'));
            }
        }
        return $this->render();
    }

    public function edit($id)
    {
        if (!$id) {
            return $this->goBack('error', __('_basic.visit-error'));
        }
        $sModel = $this->model;
        $oSetting = $sModel::find($id);
        if (!is_object($oSetting)) {
            return $this->goBack('error', __('_basic.setting-error'));
        }
        if (Request::isMethod('post')) {

            $aData = trimArray(Input::all());
            $validate = Validator::make($aData, $sModel::$rules);
            if (!$validate->passes()) {
                return $this->goBack('error', __('_basic.validate-error'));
            }
            $oSetting->name = $aData['name'];
            $oSetting->value = $aData['value'];
            $oSetting->type_id = $aData['type_id'];
            $oSetting->status = $aData['status'];
            $oSetting->sort = $aData['sort'];
            $bSucc = $sModel->save($aData);
            if ($bSucc) {
                Cache::put('system', $this->model->where('id', '>', 0)->get(), 60 * 24 * 7);
                SystemLogger::writeLog(Session::get('admin_user_id'),$this->request->url(),
                    $this->request->getClientIp(),$this->controller.'@'.$this->action,'修改系统设置:'.$id);
                return $this->goBackToIndex('success', __('_system.setting-edit-success'));
            } else {
                return $this->goBack('error', __('_system.setting-edit-error'));
            }
        }
        $this->setVars('data', $oSetting);
        return $this->render();
    }

    public function destroy($ids)
    {
        $bSucc = $this->delete($ids);
        if ($bSucc) {
            Cache::put('setting', $this->model->where('id', '>', 0)->get(), 60 * 24 * 7);
            SystemLogger::writeLog(Session::get('admin_user_id'),$this->request->url(),
                $this->request->getClientIp(),$this->controller.'@'.$this->action,'创建单页:'.$ids);
            return $this->goBack('success', __('_system.setting-destroy-success'));
        } else {
            return $this->goBack('error', __('_system.setting-destroy-error'));
        }
    }
}