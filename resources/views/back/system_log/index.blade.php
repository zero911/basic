@extends('layout.back_base')
@section('title') Zero|CMS - {{__('_log.log-index')}} @stop

@section('content')
    @include('widgets.content-msgInfo')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{__('_log.log-index')}}</h3>
            <div class="box-tools">
                <form action="{{ route('log.index') }}" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control input-sm pull-right" name="s_operator_realname"
                               value="{{ Input::get('s_operator_realname') }}" style="width: 150px;"
                               placeholder="搜索操作者">
                        <input type="text" class="form-control input-sm pull-right" name="s_operator_ip"
                               value="{{ Input::get('s_operator_ip') }}" style="width: 150px;" placeholder="搜索操作者IP">
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <div class="tablebox-controls">
                <a class="btn btn-default btn-sm" href="{{route('log.exportExcel')}}"><i class="fa fa-file-excel-o" title="导出为excel文件"></i></a>
                <a class="btn btn-default btn-sm"><i class="fa fa-file-text-o" title="导出为log文本文件"></i></a>
            </div>
            <table class="table table-hover table-bordered">
                <tbody>
                <!--tr-th start-->
                <tr>
                    <th>查阅</th>
                    <th>操作类型</th>
                    <th>操作者</th>
                    <th>操作者IP</th>
                    <th>操作URL</th>
                    <th>操作内容</th>
                    <th>操作时间</th>
                </tr>
                <!--tr-th end-->

                @foreach ($datas as $sys_log)
                    <tr>
                        <td>
                            <a href="{{ route('log.view',$sys_log->id) }}"><i class="fa fa-fw fa-link" title="预览"></i></a>
                        </td>
                        <td class="text-red">{{ $userType[$sys_log->type] }}</td>
                        <td class="text-green">{{ $sys_log->user->realname }}</td>
                        <td class="text-yellow">{{ $sys_log->operator_ip }}</td>
                        <td class="overflow-hidden" title="{{ $sys_log->url }}">{{ $sys_log->url }}</td>
                        <td class="overflow-hidden"
                            title="{{ $sys_log->content }}">{{ str_limit($sys_log->content, 70, '...') }}</td>
                        <td>{{ $sys_log->created_at }}</td>
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

    </div>
@stop

