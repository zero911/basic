@extends('layout.back_base')

@section('title') Zero|CMS - {{__('_user.permission-create')}} @stop
@section('content')

    @include('widgets.content-msgInfo')
    <h2 class="page-header">{{__('_user.permission-create')}}</h2>
    <form method="post" accept-charset="utf-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">主要内容</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="tab_1">
                    <div class="form-group">
                        <label>权限标示串
                            {{--<small class="text-red">*</small>--}}
                            <span class="text-green small">只能数字、字母、下划线与横杠（0-9a-zA-Z_-）组合</span></label>
                        <input type="text" class="form-control" name="name" autocomplete="off"
                               value="{{ Input::old('name') }}" placeholder="权限标示串">
                    </div>
                    <div class="form-group">
                        <label>权限名称
                            <small class="text-red">*</small>
                            <span class="text-green small">建议中文</span>
                        </label>
                        <input type="text" class="form-control" name="display_name"
                               value="{{ Input::old('display_name') }}"
                               placeholder="模块名称">
                    </div>
                    <div class="form-group">
                        <label>权限描述
                            <small class="text-red">*</small>
                            <span class="text-green small">建议中文</span>
                        </label>
                        <input type="text" class="form-control" name="description"
                               value="{{ Input::old('description') }}"
                               placeholder="权限描述">
                    </div>
                    <div class="form-group">
                        <label>权限类型
                            <small class="text-red">*</small>
                        </label>
                        <div class="input-group">
                            <select data-placeholder="权限类型" class="chosen-select" style="min-width:200px;" name="type">
                                @foreach($types as $key => $type)
                                    <option value="{{$key}}">{{__('_user.'.$type)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--                    <div class="form-group">
                                            <label>关联模块
                                                <small class="text-red">*</small>
                                            </label>
                                            <div class="input-group">
                                                @foreach($methods as $method)
                                                    <input type="checkbox" name="permissions[]" value="{{ $method->id }}">
                                                    <label class="choice" for="permissions[]">{{ $method->name }}</label>
                                                @endforeach
                                            </div>
                                        </div>--}}
                    {{--                    <div class="form-group">
                                            <label>激活状态
                                                <small class="text-red">*</small>
                                            </label>
                                            <div class="input-group">
                                                <input type="radio" name="is_actived" value="1" checked>
                                                <label class="choice" for="radiogroup">启用</label>
                                                <input type="radio" name="is_actived" value="0">
                                                <label class="choice" for="radiogroup">禁用</label>
                                            </div>
                                        </div>--}}
                </div><!-- /.tab-pane -->

                <button type="submit" class="btn btn-primary">{{__('_user.permission-create')}}</button>

            </div><!-- /.tab-content -->

        </div>
    </form>

    @stop

    @section('extraPlugin')
            <!--引入Chosen组件-->
    @include('scripts.endChosen')

@stop



