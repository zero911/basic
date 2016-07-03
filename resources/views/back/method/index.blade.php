@extends('layout.back_base')
@section('title') Zero|CMS - {{__('_basic.method-index')}} @stop
@section('content')

    @include('widgets.content-msgInfo')
    <a href="{{ route('method.create') }}" class="btn btn-primary margin-bottom">{{__('_basic.method-create')}}</a>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{__('_basic.method-index')}}</h3>
            <div class="box-tips clearfix">
                <p>
                    <b>后台系统暂支持所有模块，主要用于权限配置</b>
                </p>
            </div>
            <div class="box-tools">
                <form method="get">
                    <div class="input-group">
                        <input type="text" class="form-control input-sm pull-right" name="s_value"
                               value="{{ Input::get('s_value') }}" style="width: 150px;" placeholder="搜索值">
                        <input type="text" class="form-control input-sm pull-right" name="s_name"
                               value="{{ Input::get('s_name') }}" style="width: 150px;" placeholder="搜索名">
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered">
                <tbody>
                <!--tr-th start-->
                <tr>
                    <th>选择</th>
                    <th>操作</th>
                    <th>模块标识串</th>
                    <th>名称</th>
                    <th>URL地址</th>
                    <th>状态</th>
                    <th>父级模块名称</th>
                    <th>更新时间</th>
                </tr>
                <!--tr-th end-->

                @foreach ($datas as $method)
                    <tr>

                        <td class="table-operation"><input type="checkbox" value="{{ $method->id }}" name="checkbox[]">
                        </td>
                        <td>
                            <a href="{{ route('method.edit',$method->id) }}"><i
                                        class="fa fa-fw fa-pencil" title="修改"></i></a>
                            <a href="javascript:void(0);"><i class="fa fa-fw fa-link" title="预览"></i></a>
                            <a href="javascript:void(0);"><i class="fa fa-fw fa-minus-circle delete_item" title="删除"
                                                             data-id="{{ $method->id }}"></i></a>
                        </td>
                        <td>{{ $method->method_code }}</td>
                        <td class="text-red">{{ $method->name }}</td>
                        <td class="text-green">{{ $method->url }}</td>
                        <td class="text-green">
                            @if($method->is_actived)
                                启用
                                @else
                                禁用
                                @endif
                        </td>
                        <td class="text-green">

                            @if($method->pid == 0)
                                顶级模块
                                @else
                                @foreach($methods as $val)
                                    @if($method->pid == $val->id)
                                        {{$val->name}}
                                        @endif
                                    @endforeach
                            @endif

                        </td>
                        <td class="text-green">{{ $method->updated_at }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div><!-- /.box-body -->
        <div class="box-footer clearfix">
            {{--{!! $settings->render() !!}--}}
            <div class="text-right">
                <ul class="pagination pagination-sm">
                    <li class=""><span>页{{$datas->currentPage()}}, 单页记录{{$datas->perPage()}}, 总计{{$datas->total()}}
                            <span></span></span>
                    </li>
                    <li>
                        <a>{!! $datas->render() !!}</a>
                    </li>
                </ul>
            </div>
        </div>

        <!--隐藏型删除表单-->
        <form method="post"  accept-charset="utf-8" id="hidden-delete-form">
            <input name="_method" type="hidden" value="delete">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>

    </div>
    @stop


    @section('extraPlugin')
            <!--引入iCheck组件-->
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
    @stop

    @section('filledScript')
            <!--启用iCheck响应checkbox与radio表单控件-->
    //Enable iCheck plugin for checkboxes
    //iCheck for checkbox and radio inputs
    $('.table-operation input[type="checkbox"]').iCheck({
    checkboxClass: 'icheckbox_flat-blue',
    radioClass: 'iradio_flat-blue'
    });

    //Enable check and uncheck all functionality
    $(".checkbox-toggle").click(function () {
    var clicks = $(this).data('clicks');
    if (clicks) {
    //Uncheck all checkboxes
    $(".table-operation input[type='checkbox']").iCheck("uncheck");
    $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
    } else {
    //Check all checkboxes
    $(".table-operation input[type='checkbox']").iCheck("check");
    $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
    }
    $(this).data("clicks", !clicks);
    });

    <!--jQuery 提交表单，实现DELETE删除资源-->
    //jQuery submit form
    $('.delete_item').click(function(){
    var action = '{{ route('method.index') }}';
    var id = $(this).data('id');
    var new_action = action + '/destroy/' + id;
    $('#hidden-delete-form').attr('action', new_action);
    $('#hidden-delete-form').submit();
    });
@stop
