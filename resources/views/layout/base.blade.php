<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @section('title')
            {{Config::get('sysinfo.back-index-title')}}
        @show
    </title>
    {{--    <meta name="description" content="{{ isset($description) ? $description : 'YASCMF AdminLTE' }}" />
        <meta name="keywords" content="YASCMF,AdminLTE,{{ Cache::get('website_keywords') }}" />
        <meta name="author" content="{{ Cache::get('system_author_website') }}" />--}}
    <meta name="renderer" content="webkit">{{-- 360浏览器使用webkit内核渲染页面 --}}
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"/>{{-- IE(内核)浏览器优先使用高版本内核 --}}

    @section('meta')@show

    @section('header_css')@show

    @section('header_js')@show

    @section('beforeStyle')
    @show{{-- 在内联样式之前填充一些东西 --}}

    @section('head_style')
    @show{{-- head区域内联css样式表 --}}

    @section('afterStyle')
    @show{{-- 在内联样式之后填充一些东西 --}}

</head>
<body @section('body_attr')class=""@show{{-- 给body追加类属性 --}}>

@section('beforeBody')
@show{{--在正文之后填充一些东西 --}}

@section('body')
@show{{-- 正文部分 --}}

@section('afterBody')
@show{{-- 在正文之后填充一些东西，比如js --}}

</body>
</html>