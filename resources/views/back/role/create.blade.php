@extends('layout.back_base')

@section('title') Zero|CMS - {{__('_basic.role-create')}} @stop
@section('content')

    @include('widgets.content-msgInfo')

    <h2 class="page-header">{{__('_basic.role-create')}}</h2>
    <form method="post" accept-charset="utf-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="nav-tabs-custom">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">主要内容</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="tab_1">
                    <div class="form-group">
                        <label>角色(用户组)名
                            <small class="text-red">*</small>
                            <span class="text-green small">只能为英文单词，建议首字母大写</span></label>
                        <input type="text" class="form-control" name="name" autocomplete="off"
                               value="{{ Input::old('name') }}" placeholder="角色(用户组)名">
                    </div>
                    <div class="form-group">
                        <label>角色(用户组)展示名
                            <small class="text-red">*</small>
                            <span class="text-green small">展示名可以为中文</span></label>
                        <input type="text" class="form-control" name="display_name" autocomplete="off"
                               value="{{ Input::old('display_name') }}" placeholder="角色(用户组)展示名">
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<label>关联权限--}}
                            {{--<small class="text-red">*</small>--}}
                        {{--</label>--}}
                        {{--<div class="input-group">--}}
                            {{--@foreach($permissions as $per)--}}
                                {{--<input type="checkbox" name="permissions[]" value="{{ $per->id }}">--}}
                                {{--<label class="choice" for="permissions[]">{{ $per->display_name }}</label>--}}
                            {{--@endforeach--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div><!-- /.tab-pane -->

                <button type="submit" class="btn btn-primary">新增角色</button>

            </div><!-- /.tab-content -->

        </div>
    </form>

    @stop

    @section('extraPlugin')

            <!--引入iCheck组件-->
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>

    @stop


    @section('filledScript')
            <!--启用iCheck响应checkbox与radio表单控件-->
    $('input[type="checkbox"]').iCheck({
    checkboxClass: 'icheckbox_flat-blue',
    increaseArea: '20%' // optional
    });
@stop
