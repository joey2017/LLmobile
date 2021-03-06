@include('layouts.header')
<link rel="stylesheet" href="{{asset('font-awesome/css/font-awesome.min.css')}}">
</head>


<body class="drawer drawer-right">
<link rel="stylesheet" href="{{asset('css/drawer.min.css')}}">
<style type="text/css">

/*导航*/
.sidebarbox{ height: 100%; overflow:auto; }
.metismenu,.metismenu ul{margin:0; padding: 0; list-style: none;}
.metismenu a{padding: 12px 10px; display: block; border-bottom: 1px solid rgb(218, 218, 218); }
.metismenu ul a{text-indent: 15px; background-color: #eee;}

.metismenu .arrow{float:right;line-height:1.42857}[dir=rtl] .metismenu .arrow{float:left}.metismenu .glyphicon.arrow:before{content:"\e079"}.metismenu .active>a>.glyphicon.arrow:before{content:"\e114"}.metismenu .fa.arrow:before{content:"\f104"}.metismenu .active>a>.fa.arrow:before{content:"\f107"}.metismenu .ion.arrow:before{content:"\f3d2"}.metismenu .active>a>.ion.arrow:before{content:"\f3d0"}.metismenu .plus-times{float:right}[dir=rtl] .metismenu .plus-times{float:left}.metismenu .fa.plus-times:before{content:"\f067"}.metismenu .active>a>.fa.plus-times{-webkit-transform:rotate(45deg);-ms-transform:rotate(45deg);-o-transform:rotate(45deg);transform:rotate(45deg)}.metismenu .plus-minus{float:right}[dir=rtl] .metismenu .plus-minus{float:left}.metismenu .fa.plus-minus:before{content:"\f067"}.metismenu .active>a>.fa.plus-minus:before{content:"\f068"}.metismenu .collapse{display:none}.metismenu .collapse.in{display:block}.metismenu .collapsing{position:relative;height:0;overflow:hidden;-webkit-transition-timing-function:ease;-o-transition-timing-function:ease;transition-timing-function:ease;-webkit-transition-duration:.35s;-o-transition-duration:.35s;transition-duration:.35s;-webkit-transition-property:height,visibility;-o-transition-property:height,visibility;transition-property:height,visibility}

/*布局样式重置*/
.tab_parent{padding-left: 15px;}
.tab_subset{margin:0; padding: 0 15px 0 0;}
a{color: #333;}
a:focus,a:active, a:hover{color: #333; text-decoration: none;}
.box_flex{font-size:14px; display: -webkit-box; display: -moz-box; display: -webkit-flex; display: -moz-flex; display: -ms-flexbox; display: flex;}
.flex1{ -webkit-box-flex: 1; -moz-box-flex: 1; -webkit-flex: 1; -ms-flex: 1; flex: 1;}

.drawer-overlay{overflow: hidden;}
.topsearch{height: 34px; margin:10px 0;}

/*搜索条*/
.searchtab{position: relative; width: 100%; height: 36px; border-radius: 5px; margin-right:10px; }
.searchtab span{position: absolute; width: 30px; height: 30px; display: block; top: 2px; left: 2px; background:url({{asset('images/searchico.svg')}}) no-repeat; background-size: 30px;}
.searchtab input{border:0; background:#efefef; text-indent: 2em;}
.searchbtn{width: 80px; height: 36px;}
.searchbtn button{width: 80px; height: 33px; border:0; background: #f6a915; color: #fff; border-radius: 5px;}

/*筛选按钮*/
.screen{height: 42px; clear: both; text-align: center; border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2; padding: 10px 0; position: relative;}
.screen .col-xs-4 {padding-left: 0; padding-right: 0;}
.sepline{border-left:1px solid #e2e2e2; border-right:1px solid #e2e2e2;}
.arrowico{width: 7px; height: 7px; display:inline-block;background:url({{asset('images/down-icon.svg')}}) no-repeat;background-size: 7px;}
.screenico{width: 7px; height: 7px;display:inline-block; background:url({{asset('images/screen.svg')}}) no-repeat; background-size: 7px;}
.sort_sele,.sort_sele2{display: none; width: 100%; position: absolute; top: 42px; left: 0; background: #fff; padding:0 15px 10px 15px; z-index: 1; text-align: left;-webkit-box-shadow: 0 1px 3px #afafaf;  -moz-box-shadow: 0 1px 3px #afafaf; box-shadow: 0 1px 3px #afafaf;}
.ptick{border-bottom: 1px solid #eee; margin: 0; line-height: 42px;}
.ptick span{display: none; width: 16px;margin-top: 14px;background:url({{asset('images/tickspan.png')}}) no-repeat; height: 16px;  float: right;}
.tick span{display: inline-block; } 

/*产品列表*/
.productlist{ border-bottom: 1px solid #e8e8e8; overflow: hidden; padding-top: 12px; padding-bottom: 12px;}
.leftimg{width: 100px; height: 100px; margin-right: 10px;}
.leftimg img{width: 100%; height: 100%;}
.rightinfo h3{ line-height: 18px; margin: 3px; height: 44px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;  font-size: 14px;  word-break: break-all; font-weight: bold;}
.rightinfo .text-right{font-size: 12px; color: #a9a9a9;}
.price{ color: #eb5211;font-size: 20px;}
.d-main{line-height: 28px;}
.d-main .pull-right{font-size: 12px; color: #717171;}

/*右侧筛选弹框*/
.sift_row{padding: 0 4% 10px; border-bottom: 1px solid #e7e7e7; overflow: hidden;}
.row_title{ padding: 15px 0;}

.row_body ul{list-style: none; margin:0; padding: 0; }
.row_body ul li{ padding: 0 10px 10px 0;   text-align: center;}

.tc_project{display: block; padding: 5px 0;  border-radius: 3px; border: 1px solid #ddd; font-size: 12px;overflow: hidden;
    display: -webkit-box;  -webkit-line-clamp: 1;  -webkit-box-orient: vertical;  height: 28px; line-height: 19px;}
.tc_choose{background: #e60012; border:1px #e60012 solid; color: #fff !important;}

.switch_btn{width: 7px; height: 7px; display: inline-block; background: url({{asset('images/down-icon.svg')}}) no-repeat; background-size: 7px; margin-top: 6px;}

.spanrotate{-webkit-transform: rotateZ(180deg);
            -moz-transform: rotateZ(180deg);
            -o-transform: rotateZ(180deg);
            -ms-transform: rotateZ(180deg);
            transform: rotateZ(180deg);}

.sift_bottom{position:absolute; bottom: 0; right: 0; width: 100%;}
.sift-btn{ width: 50%; height: 48px; background: #FFF; line-height: 48px; float: left; text-align: center; border-top: 1px solid #e7e7e7;
}
.sift-btn-ok{  color: #fff; background: #ff5000; border-top-color: #e7e7e7;}
.drawer-default nav{padding-bottom: 47px;}

.no_record{height: 24px;  padding-top: 205px;  text-align: center;  background: url(http://s.17cct.com/v5/images/erp/empty.png) no-repeat center 20px;  background-size: 180px 180px;}


/*购物车按钮*/
.purchase_index{position: fixed; bottom: 20px; left: 20px;  width: 36px; height: 36px; background: rgba(0,0,0,0.5);
    filter: alpha(opacity=50);border-radius: 22px;}
.purchase_index a{background: url({{asset('images/purchase_index.svg')}}) no-repeat center;background-size: 30px;   width: 36px; height: 36px;}


</style>

<div class="alertBg" id="msgBox" style="display:none;">
    <h4 class="alerttitle" id="alerttitle"></h4>
    <span class="vm f20" id='alertdetail'></span>
</div>

<div class="drawer-main drawer-default">

	<nav role="navigation" id="attr_list" style="height:auto;">
		@foreach ($attr_list as $a)
			<div class="sift_row">
					<div class="row_title">
						{{$a['attr_name']}}
						<span class="switch_btn pull-right"></span>
					</div>
					<div class="row_body">
						<ul class="row" >
								<li class="col-xs-4">
									<a href="javascript:;" class="tc_project tc_choose" value="0">不限</a>
								</li>
							@foreach ($a['attr_val'] as $k => $av)
								<li class="col-xs-4">
									<a href="javascript:;" class="tc_project" value="{{$a['id']}}:{{$av}}">{{$av}}</a>
								</li>
							@endforeach					
						</ul>
					</div>
				</div>
		@endforeach
	</nav>

	<div class="sift_bottom">
		<div class="sift-btn" id="reset_attr">
			重置
		</div>
		<div class="sift-btn sift-btn-ok" onclick="$('.drawer').drawer('close');ajax_get_goods();">
			确定
		</div>
	</div>
</div>

<input type="hidden" id="class_id" value="{{$t}}">
<input type="hidden" id="sort" value="0">
<input type="hidden" id="attr_value" value="">

<div class="">
    <div class="topsearch col-xs-12 box_flex">
   		<div class="flex1 searchtab">
   			<span></span>
   			<input type="text" class="form-control" id="keyword" value="" placeholder="请输入商品名称">
   		</div>
   		<div class="searchbtn">
   			<button onclick="search_goods(2)">搜索</button>
   		</div>
    </div>
    <div class="screen">
		<div class="col-xs-4 "><a href="javascript:;" id="comprehensive">综合排序 <span class="arrowico"></span></a></div>
		<div class="col-xs-4 sepline"><a href="javascript:;" id="classification">{{$class_name}} <span class="arrowico"></span></a></div>
		<div class="col-xs-4 js-trigger">筛选 <span class="screenico"></span></div>

		<div class="sort_sele">
			<p class="ptick tick" onclick="set_sort(0,this)"><a href="javascript:;">综合排序</a><span></span></p>
			<p class="ptick" onclick="set_sort(1,this)"><a href="javascript:;">价格从低到高</a><span></span></p>
			<p class="ptick" onclick="set_sort(2,this)"><a href="javascript:;">价格从高到低</a><span></span></p>
			<p class="ptick" onclick="set_sort(3,this)"><a href="javascript:;">销量从高到低</a><span></span></p>
			<p class="ptick" onclick="set_sort(4,this)"><a href="javascript:;">销量从低到高</a><span></span></p>
		</div>

		<div class="sort_sele2">
	    	<ul class="metismenu " id="menu">
	        @foreach ($class_list as $c)
	          	<li>
	            	<a href="#">
	              		<i class="fa fa-circle-o"></i>
	              		{{$c['c_name']}}
	              		<span class="fa arrow fa-fw"></span>
	            	</a>
	            	<ul aria-expanded="false" class="collapse">
	                  @foreach ($c['item'] as $ci)
	                		<li @if ($ci['id'] == $t)class="select"@endif>
	                  			<a href="{{url('purchase/index',array('t'=>$ci['id']))}}" class="present">{{$ci['class_name']}} <i class="fa fa-angle-right pull-right"></i></a>
	                		</li>
	                  @endforeach             	
	            	</ul>
	          	</li>
	        @endforeach
	        </ul>
	    	
	    	<!-- 导航插件 -->
	    	<script type="text/javascript" src="{{asset('js/metisMenu.js')}}"></script>
	    	<script>
	    		$(function () {
	    	    	$('#menu').metisMenu();
	    	  	});
	    	</script>

		</div>
    </div>

    <script type="text/javascript">
		$(document).ready(function(){  
			$('#comprehensive').click(function(){
		        $(".sort_sele").slideToggle(200);
		        $(".sort_sele2").hide();
		    });

		    $('#classification').click(function(){
		        $(".sort_sele2").slideToggle(200);
		        $(".sort_sele").hide();
		    });	  

	    	$(".row_title").click(function(){
				$(this).children().toggleClass('spanrotate');			
			});		


			$('.drawer').drawer();
			$('.js-trigger').click(function(){
				$(".sort_sele2").hide();
				$(".sort_sele").hide();
			 	$('.drawer').drawer("open");
			});
		});  

		//查找子类商品并选中
		$('.select').parent().parent().addClass('active');

		var currentpage=0;
	    ajax_get_goods();//初始化添加商品列表
	    ajax_get_attr();//初始化添加属性列表	


	    //滚动加载
    	$(window).scroll(function(){ 
            totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop()); 
            if($(document).height() <= totalheight){ 
                if(stop==true){ 
                	MsgBox('正在加载...');
                	currentpage++;
                    ajax_get_goods();
                } 
            } 
        });

    	//设置排序
    	function set_sort(val,_this){
    		$('#sort').val(val);
    		$('#comprehensive').html($(_this).find('a').html()+'<span class="arrowico"></span>');
    		$(_this).addClass('tick').siblings().removeClass('tick');
    		$(".sort_sele").slideToggle(200);
    		search_goods(1);
    	}

    	//选分类
    	function set_class(val,_this){
    		$('#classification').html(($(_this).find('a').html())+'<span class="arrowico"></span>');
    		$('#class_id').val(val);
    		$('#attr_value').val('');
    		$('#keyword').val('');
    		$(_this).addClass('tick').siblings().removeClass('tick');
    		$(".sort_sele2").slideToggle(200);
    		search_goods(1);
    		ajax_get_attr();
    	}

    	//搜索商品
	    function search_goods(type) {
	    	if(type==2&&$('#keyword').val()==''){
	    		MsgBox('请输入关键词搜索');
	    		return false;
	    	}
	    	currentpage=0;
	    	ajax_get_goods();
	    }

	    //属性重置
	    $('#reset_attr').click(function(){
 			$('#attr_value').val('');
 			ajax_get_attr(); 			
 			ajax_get_goods();
 		})

	    //加载商品
   		function ajax_get_goods(){
    		var keyword=$('#keyword').val(),attr_value=$('#attr_value').val(),sort=$('#sort').val(),class_id=$('#class_id').val();
            $("#load").show();
            stop=false;
            $.get("{{url('purchase/ajax_get_goods')}}",{"p":currentpage,"keyword":keyword,"attr":attr_value,"sort":sort,"class_id":class_id}
          	,function(html){
                  if(html!=""){ 
                    if(currentpage==0) {
                        $("#goods_list").html(html);
                    }
                    else {
                       $("#goods_list").append(html);                                               
                    }
                    stop=true;                 	
                  }else{
                  	MsgBox('已加载全部数据');
                  	if(currentpage==0){
                  		$("#goods_list").html('<div class="no_record col-sm-12">暂无数据</div>');
                  	}                  	
                  }                
                 $("#load").hide();  
            });
 		}
 		
 		//加载属性
 		function ajax_get_attr(){
 			var class_id=$('#class_id').val();
		    $.get("{{url('purchase/ajax_get_attr')}}",{"class_id":class_id}
		  	,function(html){
		          if(html!=""){               
		                $("#attr_list").html(html);               
		          }else{
		          	MsgBox('该分类未添加有属性');
		          }
		    });
 		}	

	</script>

<div id="goods_list">
    
</div>

<!--底栏-->
@include('layouts.purchase_bottom')

</div>
<script type="text/javascript">
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
  WeixinJSBridge.call('hideToolbar');
  WeixinJSBridge.call('hideOptionMenu');
});
</script>
<script src="{{asset('js/iscroll.js')}}"></script>
<script src="{{asset('js/jquery.drawer.min.js')}}"></script>