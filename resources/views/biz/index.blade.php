@extends('layouts.header')
</head>

<body>
<!--头部-->
<div class="container-fluid topbox">
    <div class="row top"><h1 style="display:none;">诚车堂汽车网</h1>
        <div class="pg-Current">
        	<a href="javascript:history.go(-1)" ><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span></a>
        </div>
        <div class="pg-Current">
        	<img src="{{asset('images/cheng.png')}}" width="30" height="30">
        </div>
        <div class="pgt">
        	<a>商家入口</a>
        </div>             
    </div>
</div>
<!--分割线-->
<div class="container-fluid line"></div>
<div class="container-fluid">
	<!--接受服务与收货-->
	<div class="row oder">
    	<div class="col-xs-12">
        	<a href="{{url('Biz/order')}}" class="oder_1">
                <span style="padding-left:0;">订单管理</span>
                <div style="float:right;">
                	<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                </div>  
            </a>  	
        </div>
    </div>
    <!--如何使用服务码-->
    <div class="row oder">
    	<div class="col-xs-12">
        	<a href="{{url('Biz/verify')}}" class="oder_1">
                <span style="padding-left:0;">服务验证</span>
                <div style="float:right;">
                	<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                </div>  
            </a>  	
        </div>
    </div> 
    <div class="row oder">
        <div class="col-xs-12">
            <a href="{{url('Biz/member')}}" class="oder_1">
                <span style="padding-left:0;">会员管理</span>
                <div style="float:right;">
                    <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                </div>  
            </a>    
        </div>
    </div>   
    <a class="btn btn-danger btn-block " href="{{url('Biz/login_out')}}" role="button" style="margin-top: 35px;">退出</a>
</div>    
@extends('layouts.footer')
