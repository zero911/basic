@extends('layout.back_base')
@section('title') Zero|CMS - {{__('_basic.category-create')}} @stop
@section('content')
    @include('widgets.content-msgInfo')
    <h2 class="page-header">{{__('_basic.category-create')}}</h2>
    <form method="post"  accept-charset="utf-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="type" value="category">
        <div class="nav-tabs-custom">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">主要内容</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="tab_1">
                    <div class="form-group">
                        <label>分类名称
                            <small class="text-red">*</small>
                            <span class="text-green small">简短为宜</span></label>
                        <input type="text" class="form-control" name="name" autocomplete="off"
                               value="{{ $category->name }}" placeholder="分类名称" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label>分类缩略名 <small class="text-red">*</small> <span class="text-green small">用于创建友好的链接形式</span></label>
                        <div class="input-group mono url_slug">
                            @if($category->slug)
                                <input type="text" id="slug" name="slug" autocomplete="off"
                                       value="{{ $category->slug }}" class="slug" maxlength="10"
                                       pattern="[A-z0-9_-]+" placeholder="分类缩略名">
                            @else
                                <input type="text" id="slug" name="slug" autocomplete="off"
                                       value="{{ $category->id }}" class="slug" maxlength="10"
                                       pattern="[A-z0-9_-]+" placeholder="分类缩略名">
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label>分类描述
                            <small class="text-red">*</small>
                            <span class="text-green small">建议百字以内，有助于网站SEO</span></label>
                        <textarea class="form-control" name="description" cols="45" rows="2" maxlength="200"
                                  placeholder="分类描述">{{ $category->description }}</textarea>
                    </div>
                </div><!-- /.tab-pane -->

                <button type="submit" class="btn btn-primary">修改分类</button>

            </div><!-- /.tab-content -->

        </div>
    </form>
@stop