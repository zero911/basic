@extends('layout.back_base')
@section('title') Zero|CMS - 403 @stop

@section('content')
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> 警告！</h4>
        <p>无权限访问</p>
    </div>
@stop
