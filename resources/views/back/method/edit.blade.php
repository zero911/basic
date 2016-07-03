@extends('layout.back_base')

@section('title') Zero|CMS - {{__('_basic.method-edit')}} @stop
@section('content')
    @include('widgets.content-msgInfo')
    <h2 class="page-header">{{__('_basic.method-edit')}}</h2>
    <form method="post" action="{{ route('method.edit', $method->id) }}" accept-charset="utf-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="nav-tabs-custom">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">主要内容</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="tab_1">
                    <div class="form-group">
                        <label>模块标识串
                            {{--<small class="text-red">*</small>--}}
                            <span class="text-green small">只能数字、字母、下划线与横杠（0-9a-zA-Z_-）组合</span></label>
                        <input type="text" class="form-control" name="method_code" autocomplete="off"
                               value="{{ Input::old('method_code',isset($method) ? $method->method_code : null) }}" placeholder="模块标识串">
                    </div>
                    <div class="form-group">
                        <label>模块名称
                            <small class="text-red">*</small>
                            <span class="text-green small">只能全小写的英文字母与下划线（a-z_）组合</span></label>
                        <input type="text" class="form-control" name="name" autocomplete="off"
                               value="{{ Input::old('name', isset($method) ? $method->name : null) }}" placeholder="模块名称">
                    </div>
                    <div class="form-group">
                        <label>模块URL地址
                            <small class="text-red">*</small>
                            <span class="text-red small">此处谨慎操作，未作容错处理。友情提示：index一定要放到第一个</span></label>
                        </label>
                        <input type="text" class="form-control" name="url"
                               value="{{ Input::old('url', isset($method) ? $method->url : null) }}"
                               placeholder="动态设置值">
                    </div>
                    <div class="form-group">
                        <label>父级模块
                            <small class="text-red">*</small>
                        </label>
                        <div class="input-group">
                            <select data-placeholder="父级模块" class="chosen-select" style="min-width:200px;" name="pid">
                                <option value="0" >顶级模块</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ ($parent->id == $method->pid) ? 'selected':'' }}>{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>状态
                            <small class="text-red">*</small>
                        </label>
                        <div class="input-group">
                            <input type="radio" name="is_actived" value="0" {{ ($method->is_actived == 0) ? 'checked' : '' }}>
                            <label class="choice" for="radiogroup">禁用</label>
                            <input type="radio" name="is_actived" value="1" {{ ($method->is_actived == 1) ? 'checked' : '' }}>
                            <label class="choice" for="radiogroup">启用</label>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->

                <button type="submit" class="btn btn-primary">{{__('_basic.method-edit')}}</button>

            </div><!-- /.tab-content -->

        </div>
    </form>

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
