<?php
/**
 *
 * User: ziann
 * Date: 16/7/4
 * Time: 下午1:25
 */

namespace App\Http\Controllers\Func;


use App\Http\Controllers\AdminBaseController;
use Illuminate\Support\Facades\Validator;
use Request;
use Input;
use Session;

class FunctionalityController extends AdminBaseController
{

    protected $modelName = 'App\Models\Func\Functionality';
    protected $routeName='functionalities';


    public function beforeRender()
    {
        parent::beforeRender(); // TODO: Change the autogenerated stub
        $sModel = $this->model;
        $this->setVars('aRealmTypes', $sModel::getValidTypes());

    }
}