@extends('layout.back_base')
@section('title') Zero|CMS - {{__('_basic.cache-reload')}} @stop

@section('content')
    @if(isset($success))
        <div class="callout callout-info">
          <h4><i class="icon fa fa-check"></i> 提示</h4>
          <p>{{$success}}</p>
        </div>
    @endif
@stop
