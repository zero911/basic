<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16/3/26
 * Time: 下午7:07
 */
namespace App\Http\Controllers;
use Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;
use Cache;
use Session;

class AdminController extends AdminBaseController
{
    protected $view='back.upload.create';
    protected $validatorMessages = array(
        'picture.image'   => '文件类型不允许,请上传常规的图片(bmp|gif|jpg|png)文件',
        'picture.max'    => '文件过大,文件大小不得超出2MB',
    );
    public function getUpload()
    {
        return $this->render();
    }
    /**
     * 上传图像文件
     * 允许上传的文件为 image mime
     * 上传逻辑直接放在控制器里予以处理，你也可剥离出一些代码到其它类里
     *
     * @return Response
     */
    public function postUpload()
    {
        if ($this->request->ajax()) {
            $json = [
                'status' => 0,
                'info' => '失败原因为：<span class="text_error">不存在待上传的文件</span>',
                'operation'=>'上传图片',
                'url' => '',
            ];
            if ($this->request->hasFile('picture')) {
                //
                $file = $this->request->file('picture');
                $data = $this->request->all();
                $rules = [
                    //'picture'    => 'image|max:2048',
                    'picture'    => 'max:2048',
                ];
                $messages = $this->validatorMessages;
                $validator = Validator::make($data, $rules, $messages);
                if ($validator->passes()) {
                    $realPath = $file->getRealPath();
                    $destPath = Config::get('sysConfig.article-pic-path');
                    $savePath = $destPath.''.date('Ymd', time());
                    is_dir($savePath) or mkdir($savePath);  //如果不存在则创建目录
                    $name = $file->getClientOriginalName();
                    $ext = $file->getClientOriginalExtension();
                    //----------
                    // 因本人生产服务器禁用掉fileinfo扩展特性，故无法通过框架自身Validation 'image'表单验证文件MIME类型，如果您的服务器开启fileinfo扩展可直接使用 'picture' => 'image|max:2048'验证规则
                    // 这里根据客户端上传文件扩展名来验证，存在一定的安全隐患，请将上传目录执行权限去掉
                    //----------
                    $check_ext = in_array($ext, array('jpg', 'png', 'gif', 'bmp'), true);
                    if ($check_ext) {
                        $uniqid = uniqid().'_'.date('s');
                        $oFile = $uniqid.'o.'.$ext;
                        $rFile = $uniqid.'rw300.'.$ext;
                        $fullfilename = url('').'/'.$savePath.'/'.$oFile;  //原始完整路径
                        if ($file->isValid()) {
                            $uploadSuccess = $file->move($savePath, $oFile);  //移动文件
                            $user = user('object');
                            $file = [
                                'original_file_name' => $name,  //添加文件操作信息，原始文件名
                                'uploaded_full_file_name' => $fullfilename,  //添加文件操作信息，上传之后存储在服务器上的原始完整路径
                            ];
//                            event(new UserUpload(user('object'), $file));  //触发上传文件事件
                            $oFilePath = $savePath.'/'.$oFile;
                            $rFilePath = $savePath.'/'.$rFile;
                            $json = array_replace($json, ['status' => 1, 'info' => $fullfilename]);
                        } else {
                            $json = array_replace($json, ['status' => 0, 'info' => '失败原因为：<span class="text_error">文件校验失败</span>']);
                        }
                    } else {
                        $json = array_replace($json, ['status' => 0, 'info' => '失败原因为：<span class="text_error">文件类型不允许,请上传常规的图片（bmp|gif|jpg|png）文件</span>']);
                    }
                } else {
                    $json = format_json_message($validator->messages(), $json);
                }
            }
            return response()->json($json);
        } else {
            //非ajax请求抛出异常
            return view('back.exceptions.jump', ['exception' => '非法请求，不予处理！']);
        }
    }

    public function resetCache(){

        Cache::forget('syscfg');
        Cache::forget('setting');
        Cache::forget('permission_method');

        $iUserId = Session::get('admin_user_id');
        //得到菜单
        $menus=User::getRights($iUserId,true);
        if(!is_array($menus)){
            return view('back.console.cache',['menus'=>null,'success'=>'重建缓存成功!']);
        }
        return view('back.console.cache',['menus'=>$menus,'success'=>'重建缓存成功!']);
    }
}