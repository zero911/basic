@extends('layout.back_base')
@section('title') Zero|CMS - {{__('_basic.role-index')}} @stop
@section('content')
    @include('widgets.content-msgInfo')
    <a href="{{ route('role.create') }}" class="btn btn-primary margin-bottom">新增角色</a>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{__('_basic.role-index')}}</h3>
            <div class="box-tips clearfix">
                <p class="text-red">
                    请在超级管理员协助下完成新增修改与删除角色（用户组）操作。
                </p>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered">
                <tbody>
                <!--tr-th start-->
                <tr>
                    <th>编号</th>
                    <th>角色（用户组）名</th>
                    <th>角色展示名</th>
                    <th>创建日期</th>
                    <th>更新日期</th>
                    <th>操作</th>
                </tr>
                <!--tr-th end-->

                @foreach ($datas as $role)
                    <tr>
                        <td class="text-muted">{{ $role->id }}</td>
                        <td class="text-green">{{ $role->name }}</td>
                        <td class="text-red">{{ $role->display_name }}</td>
                        <td>{{ $role->created_at }}</td>
                        <td>{{ $role->updated_at }}</td>
                        <td>
                            <a href="{{ route('role.edit',$role->id) }}"><i class="fa fa-fw fa-pencil"  title="修改"></i></a>
                            <a href="javascript:void(0);"><i class="fa fa-fw fa-link" title="预览"></i></a>
                            <a href="javascript:void(0);"><i class="fa fa-fw fa-minus-circle delete_item" title="删除" data-id="{{ $role->id }}"></i></a>
                            <a href="{{route('method.setPermission',$role->id)}}">设置权限</a>
                            <a href="{{route('method.viewPermission',$role->id)}}">查看权限</a>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div><!-- /.box-body -->

        <!--隐藏型删除表单-->
        <form method="get"  accept-charset="utf-8" id="hidden-delete-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>

    </div>
    @stop


    @section('filledScript')
            <!--jQuery 提交表单，实现DELETE删除资源-->
    //jQuery submit form
    $('.delete_item').click(function(){
    var action = '{{ route('role.index') }}';
    var id = $(this).data('id');
    var new_action = action + '/destroy/' + id;
    $('#hidden-delete-form').attr('action', new_action);
    $('#hidden-delete-form').submit();
    });
@stop
