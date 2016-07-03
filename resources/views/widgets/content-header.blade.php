{{--后台右侧顶部beader--}}
<h1>
    控制面板
    <small>
        @if(isset($_title))
            {{$_title}}
        @else
            概述
        @endif
    </small>
</h1>
<ol class="breadcrumb">
    <li><a href="http://g.yascmf.cn/admin"><i class="fa fa-dashboard"></i> 主页</a></li>
    <li class="active">控制面板 -
        @if(isset($_title))
            {{$_title}}
        @else
            概述
        @endif
    </li>
</ol>
