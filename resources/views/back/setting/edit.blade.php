@extends('layout.back_base')

@section('title') Zero|CMS - {{__('_basic.setting-edit')}} @stop
@section('content')
    @include('widgets.content-msgInfo')
    <h2 class="page-header">{{__('_basic.setting-edit')}}</h2>
    <form method="post" action="{{ route('setting.edit', $data->id) }}" accept-charset="utf-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="nav-tabs-custom">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">主要内容</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="tab_1">
                    <div class="form-group">
                        <label>动态设置名
                            <small class="text-red">*</small>
                            <span class="text-green small">只能全小写的英文字母与下划线（a-z_）组合</span></label>
                        <input type="text" class="form-control" name="name" autocomplete="off"
                               value="{{ Input::old('name', isset($data) ? $data->name : null) }}" placeholder="动态设置名">
                    </div>
                    <div class="form-group">
                        <label>动态设置值
                            <small class="text-red">*</small>
                        </label>
                        <input type="text" class="form-control" name="value"
                               value="{{ Input::old('value', isset($data) ? $data->value : null) }}"
                               placeholder="动态设置值">
                    </div>
                    <div class="form-group">
                        <label>动态设置分组
                            <small class="text-red">*</small>
                        </label>
                        <div class="input-group">
                            <select data-placeholder="选择动态设置分组" class="chosen-select" style="min-width:200px;"
                                    name="type_id">
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" {{ ($data->type_id === $type->id) ? 'selected':'' }}>{{ $type->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>状态
                            <small class="text-red">*</small>
                        </label>
                        <div class="input-group">
                            <input type="radio" name="status" value="0" {{ ($data->status == 0) ? 'checked' : '' }}>
                            <label class="choice" for="radiogroup">禁用</label>
                            <input type="radio" name="status" value="1" {{ ($data->status == 1) ? 'checked' : '' }}>
                            <label class="choice" for="radiogroup">启用</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>排序
                            <small class="text-red">*</small>
                            <span class="text-green small">只能数字，数字越大排序越靠前</span></label>
                        <input type="text" class="form-control" name="sort"
                               value="{{ Input::old('sort', isset($data) ? $data->sort : null) }}" maxlength="6"
                               placeholder="排序">
                    </div>
                </div><!-- /.tab-pane -->

                <button type="submit" class="btn btn-primary">修改动态设置</button>

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
