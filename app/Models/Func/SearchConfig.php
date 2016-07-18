<?php

namespace App\Models\Func;

use App\Models\BaseModel;

class SearchConfig extends BaseModel
{

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    protected $table = 'yascmf_search_config';
    protected $fillable = [
        'name',
        'form_name',
        'row_size',
        'realm',
    ];
    public static $resourceName = 'label';
    public static $rules = [

        'name' => 'required|alpha_dash:2,40',
        'form_name' => 'alpha_dash:2,40',
        'row_size' => 'integer',
        'realm' => 'in:1,2',
    ];

    const REALM_ADMIN = 1;
    const REALM_USER = 2;
    const REALM_SYSTEM = 3;

    public static $realms = [
        self::REALM_ADMIN => 'admin',
        self::REALM_USER => 'user',
        self::REALM_SYSTEM => 'system',
    ];

    public static function getForm($iFunctionalityId, $bAdmin = true)
    {
        $realms = static::$realms;
        $iGiveUpRealm = $bAdmin ? self::REALM_USER : self::REALM_ADMIN;
        unset($realms[$iGiveUpRealm], $iGiveUpRealm);

        $oFunctionality = Functionality::find($iFunctionalityId);
        $realms = array_keys($realms);
        if (! $oSearchConfig = static::find($oFunctionality->search_config_id, ['id', 'name', 'form_name', 'row_size', 'realm'])) {
            return null;
        }
        return in_array($oSearchConfig->realm, $realms) ? $oSearchConfig : null;

    }
    /**
     * get all items
     * @return Array
     */
    public function & getItems(){
        $data = & SearchItem::getItemList($this->id);
        return $data;
    }

    public static function makeSearhFormInfo(& $aSearchItems, & $aParams, & $aSearchFields) {
        $bNeedCalendar = false;
        $aSearchFields = [];
        foreach ($aSearchItems as $aFieldInfo) {
            $aData = & $aFieldInfo;
            $sField = & $aData['field'];
            $aSearchFields[$sField] = array(
                'type' => $aData['type'],
                'label' => strtolower($aData['label']),
                'value' => isset($aParams[$sField]) ? $aParams[$sField] : ''
            );
            switch ($aData['type']) {
                case 'select':
                    $aSearchFields[$sField]['options'] = SearchItem::explainSource($aData['source']);
                    $aSearchFields[$sField]['div']     = false;
                    $aSearchFields[$sField]['empty']   = $aData['empty'] ? ($aData['empty_text'] ? $aData['empty_text'] : true) : false;
                    $aSearchFields[$sField]['is_date'] = false;
                    break;
                case 'date':
                    $aSearchFields[$sField]['type'] = 'text';
                    if (isset($aParams[$sField])) {
                        $aSearchFields[$sField]['value'] = $aParams[$sField];
                    } else {
                        $aSearchFields[$sField]['value'] = $aData['default_value'] == 'CURRENT_DATE' ? date('Y-m-d') : $aData['default_value'];
                    }
                    $aSearchFields[$sField]['dateFormat'] = 'YMD';
                    $aSearchFields[$sField]['maxYear']    = date('Y');
                    $aSearchFields[$sField]['div']        = $aData['div'];
                    $aSearchFields[$sField]['empty']      = $aData['empty_text'];
                    $aSearchFields[$sField]['is_date']    = true;
                    $bNeedCalendar = true;
                    break;
                case 'datetime':
                    $aSearchFields[$sField]['type'] = 'text';
                    if (isset($aParams[$sField])) {
                        $aSearchFields[$sField]['value'] = $aParams[$sField];
                    } else {
                        $aSearchFields[$sField]['value'] = $aData['default_value'] == 'CURRENT_DATE' ? date('Y-m-d 00:00:00') : $aData['default_value'];
                    }
                    $aSearchFields[$sField]['dateFormat'] = 'YMDHIS';
                    $aSearchFields[$sField]['maxYear']    = date('Y');
                    $aSearchFields[$sField]['div']        = $aData['div'];
                    $aSearchFields[$sField]['empty']      = $aData['empty_text'];
                    $aSearchFields[$sField]['is_date']    = true;
                    $bNeedCalendar = true;
                    break;
                default :
                    $aSearchFields[$sField]['type'] = 'text';
                    $aSearchFields[$sField]['is_date'] = false;
            }
        }
        return $bNeedCalendar;
    }
}