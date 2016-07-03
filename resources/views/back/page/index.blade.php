@extends('layout.back_base')
@section('title') Zero|CMS - {{__('_basic.page-index')}} @stop
@section('content')

    {{--表单提交的提示语--}}
    @include('widgets.content-msgInfo')
    {{--撰写新文章button--}}
    <a href="{{route('article.create')}}" class="btn btn-primary margin-bottom">撰写</a>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{__('_basic.page')}}</h3>
            <div class="box-tools">
                <form action="http://g.yascmf.cn/admin/article" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control input-sm pull-right" name="s_title" value=""
                               style="width: 150px;" placeholder="搜索文章标题">
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <div class="tablebox-controls">
                <!-- Check all button -->
                <button class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o" title="全选/反全选"></i>
                </button>
                <button class="btn btn-default btn-sm"><i class="fa fa-trash-o" title="删除"></i></button>
                <button class="btn btn-default btn-sm"><i class="fa fa-refresh" title="刷新"></i></button>
            </div>
            <table class="table table-hover table-bordered">
                <tbody>
                <!--tr-th start-->
                <tr>
                    <th>选择</th>
                    <th>操作</th>
                    <th>标题</th>
                    <th>Slug</th>
                    <th>最后修改时间</th>
                </tr>
                <!--tr-th end-->
                @foreach($datas as $data)
                    <tr>
                        <td class="table-operation">
                            <div class="icheckbox_flat-blue" aria-checked="false" aria-disabled="false"
                                 style="position: relative;"><input type="checkbox" value="1" name="checkbox[]"
                                                                    style="position: absolute; opacity: 0;">
                                <ins class="iCheck-helper"
                                     style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                            </div>
                        </td>
                        <td>
                            <a href="{{route('page.edit',$data->id)}}"><i class="fa fa-fw fa-pencil" title="修改"></i></a>
                            <a href="{{route('page.view',$data->id)}}"><i class="fa fa-fw fa-link"
                                                                             title="预览"></i></a>
                            <a href="javascript:void(0);"><i class="fa fa-fw fa-minus-circle delete_item" title="删除"
                                                             data-id="1"></i></a>
                        </td>
                        <td class="text-muted">{{$data->title}}</td>
                        <td class="text-green">
                            @if(empty($data->slug))
                                {{ $data->id }}
                            @else
                                {{ $data->slug }}
                            @endif
                        </td>
                        <td>{{$data->updated_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
        <div class="box-footer clearfix">
        </div>
        <!--隐藏型删除表单-->
        <form method="post" action="{{route('article.destroy')}}" accept-charset="utf-8" id="hidden-delete-form">
            <input name="_method" type="hidden" value="delete">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
        </form>

    </div>
@stop