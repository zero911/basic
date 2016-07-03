@extends('layout.back_base')
@section('title') Zero|CMS - {{__('_basic.manager-edit')}} @stop
@section('content')
    @include('widgets.content-msgInfo')
    <h2 class="page-header">新增管理员</h2>
    <form method="post" action="{{ route('admin.user.store') }}" accept-charset="utf-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="nav-tabs-custom">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">主要信息</a></li>
                <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">通联信息</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="tab_1">
                    <div class="form-group">
                        <label>登录（用户）名
                            <small class="text-red">*</small>
                            <span class="text-green small">只能5-10位英文字母与阿拉伯数字组合</span></label>
                        <input type="text" class="form-control" name="username" autocomplete="off"
                               value="{{ Input::old('username') }}" placeholder="登录名">
                    </div>
                    <div class="form-group">
                        <label>角色（用户组）
                            <small class="text-red">*</small>
                        </label>
                        <div class="input-group">
                            <select data-placeholder="选择角色（用户组）..." class="chosen-select" style="min-width:280px;"
                                    name="role">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ ($role->name === 'Demo') ? 'selected':'' }}>{{ $role->display_name }}
                                        ({{ $role->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>初始化登录密码
                            <small class="text-red">*</small>
                            <span class="text-green small">只能6-16位数字、字母和部分特殊符号（0-9a-zA-Z~@#%）组合</span></label>
                        <input type="password" class="form-control" name="password" autocomplete="off" value=""
                               placeholder="登录密码">
                    </div>
                    <div class="form-group">
                        <label>确认登录密码
                            <small class="text-red">*</small>
                        </label>
                        <input type="password" class="form-control" name="password_confirmation" autocomplete="off"
                               value="" placeholder="重复上面登录密码">
                    </div>
                    <div class="form-group">
                        <label>电子邮件
                            <small class="text-red">*</small>
                            <span class="text-green small">用于找回或重置登录密码等操作</span></label>
                        <input type="text" class="form-control" name="email" autocomplete="off"
                               value="{{ Input::old('email') }}" placeholder="电子邮件地址">
                    </div>
                    <div class="form-group">
                        <label>真实姓名
                            <small class="text-red">*</small>
                            <span class="text-green small">用于身份确认，必须为2字以上的中文</span></label>
                        <input type="text" class="form-control" name="realname" autocomplete="off"
                               value="{{ Input::old('realname') }}" placeholder="真实姓名">
                    </div>
                </div><!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    <div class="form-group">
                        <label>手机号码 <span class="text-green small">用于通讯联络，请填写国内真实的手机号码</span></label>
                        <input type="text" class="form-control" name="phone" autocomplete="off"
                               value="{{ Input::old('phone') }}" placeholder="手机号码">
                    </div>
                    <div class="form-group">
                        <label>通联地址 <span class="text-green small">用于通讯联络，请填写真实的通联地址</span></label>
                        <input type="text" class="form-control" name="address" autocomplete="off"
                               value="{{ Input::old('address') }}" placeholder="常住通联地址">
                    </div>
                </div><!-- /.tab-pane -->

                <button type="submit" class="btn btn-primary">新增管理员</button>

            </div><!-- /.tab-content -->

        </div>
    </form>
    <div id="layerPreviewPic" class="fn-hide">

    </div>

    @stop

    @section('extraPlugin')

            <!--引入iCheck组件-->
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
    <!--引入Chosen组件-->
    @include('scripts.endChosen')

    @stop


    @section('filledScript')
            <!--启用iCheck响应checkbox与radio表单控件-->
    $('input[type="radio"]').iCheck({
    radioClass: 'iradio_flat-blue',
    increaseArea: '20%' // optional
    });
@stop
