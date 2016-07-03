@extends('layout.back_base')
@section('title') Zero|CMS - {{__('_basic.category')}} @stop
@section('content')
    @include('widgets.content-msgInfo')
    <a href="{{ route('category.create') }}" class="btn btn-primary margin-bottom">{{__('_basic.category-create')}}</a>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{__('_basic.category-index')}}</h3>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table table-hover table-bordered">
                <tbody>
                <!--tr-th start-->
                <tr>
                    <th>名称</th>
                    <th>操作</th>
                    <th>缩略名</th>
                    <th>文章数</th>
                </tr>
                <!--tr-th end-->

                @foreach ($datas as $cat)
                    <tr>
                        <td class="text-muted">{{ $cat->name }}</td>
                        <td>
                            <a href="{{ route('category.edit',$cat->id) }}"><i class="fa fa-fw fa-pencil" title="修改"></i></a>
                            {{--<a href="javascript:void(0);"><i class="fa fa-fw fa-link" title="查看"></i></a>--}}
                            <a href="javascript:void(0);"><i class="fa fa-fw fa-minus-circle delete_item" title="删除" data-id="{{ $cat->id }}"></i></a>
                        </td>
                        <td class="text-green">
                            @if(empty($cat->slug))
                                {{ $cat->id }}
                            @else
                                {{ $cat->slug }}
                            @endif
                        </td>
                        <td class="text-red">total:2</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div><!-- /.box-body -->

        <!--分类一般来说较少，故移除分页-->

        <!--隐藏型删除表单-->
        <form method="get" accept-charset="utf-8" id="hidden-delete-form">
            <input name="_method" type="hidden" value="delete">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>

    </div>
    @stop


    @section('filledScript')
            <!--jQuery 提交表单，实现DELETE删除资源-->
    //jQuery submit form
    $('.delete_item').click(function(){
    var action = '{{ route('category.index') }}';
    var id = $(this).data('id');
    var new_action = action + '/destroy/' + id;
    $('#hidden-delete-form').attr('action', new_action);
    $('#hidden-delete-form').submit();
    });
@stop
