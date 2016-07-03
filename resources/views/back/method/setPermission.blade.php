@extends('layout.back_base')

@section('title') Zero|CMS - {{__('_user.permission-set')}} @stop
@section('content')

    @if(Session::has('info'))
        <div class="callout callout-info">
            <h4><i class="icon fa fa-check"></i> 提示</h4>
            <p>{{Session::get('info')}}</p>
        </div>
    @endif

    <h2 class="page-header">{{__('_user.permission-set')}}</h2>
    <form method="post" accept-charset="utf-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">主要内容</a></li>
            </ul>
            <div class="box-body table-responsive">
                <table class="table table-hover table-bordered">
                    <tbody>
                    <!--tr-th start-->
                    <tr>
                        {{--<th>选择</th>--}}
                        <th style="width: 30%;">系统模块</th>
                        <th>拥有权限</th>
                    </tr>
                    @foreach($methodPermissions as $methodPermission)
                        <tr>
                            <th class="text-green">
                                {{$methodPermission['name']}}
                            </th>
                            <td class="text-red">
                                <div class="input-group">
                                    @foreach($methodPermission['permissions'] as $per)
                                        <input type="checkbox" class="_per_checkbox" value="{{$per['id']}}"
                                               name="per_id[][{{$methodPermission['id']}}]"/>
                                        <label class="choice"  for="permissions[]">{{ $per['display_name'] }}</label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <button type="button" id="_per_select_all" class="btn btn-info">全选</button>
        <button type="button" id="_per_cancel_all" class="btn btn-info">取消</button>
        <button type="submit" class="btn btn-primary">确认设置</button>
    </form>

    @stop

    @section('extraPlugin')
            <!--引入Chosen组件-->
    @include('scripts.endChosen')
@stop
@section('filledScript')
    $('#_per_select_all').click(function(){
    $('._per_checkbox').attr('checked',true);
    });

    $('#_per_cancel_all').click(function(){
    $('._per_checkbox').attr('checked',false);
    });
@stop



