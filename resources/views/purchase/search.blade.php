@include('layouts.header')

</head>
<body class="drawer drawer-right">
<link rel="stylesheet" href="{{asset('css/drawer.min.css')}}">
<style type="text/css">
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
.searchtab span{position: absolute; width: 30px; height: 30px; display: block; top: 2px; left: 2px; background:url({{asset('images/searchico.svg')}} no-repeat; background-size: 30px;}
.searchtab input{border:0; background:#efefef; text-indent: 2em;}
.searchbtn{width: 80px; height: 36px;}
.searchbtn button{width: 80px; height: 33px; border:0; background: #f6a915; color: #fff; border-radius: 5px;}

/*筛选按钮*/
.screen{height: 42px; clear: both; text-align: center; border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2; padding: 10px 0; position: relative;}
.screen .col-xs-4 {padding-left: 0; padding-right: 0;}
.sepline{border-left:1px solid #e2e2e2; border-right:1px solid #e2e2e2;}
.arrowico{width: 7px; height: 7px; display:inline-block;background:url({{asset('images/down-icon.svg')}} no-repeat;background-size: 7px;}
.screenico{width: 7px; height: 7px;display:inline-block; background:url({{asset('images/screen.svg')}} no-repeat; background-size: 7px;}
.sort_sele,.sort_sele2{display: none; width: 100%; position: absolute; top: 42px; left: 0; background: #fff; padding:0 15px 10px 15px; z-index: 1; text-align: left;-webkit-box-shadow: 0 1px 3px #afafaf;  -moz-box-shadow: 0 1px 3px #afafaf; box-shadow: 0 1px 3px #afafaf;}
.ptick{border-bottom: 1px solid #eee; margin: 0; line-height: 42px;}
.ptick span{display: none; width: 16px;margin-top: 14px;background:url({{asset('images/tickspan.png')}} no-repeat; height: 16px;  float: right;}
.tick span{display: inline-block; } 

/*产品列表*/
.productlist{ border-bottom: 1px solid #e8e8e8; overflow: hidden; padding-top: 12px; padding-bottom: 12px;}
.leftimg{width: 100px; height: 100px; margin-right: 10px;}
.leftimg img{width: 100%; height: 100%;}
.rightinfo h3{ line-height: 22px; margin: 3px; height: 44px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;  font-size: 14px;  word-break: break-all; font-weight: bold;}
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

.switch_btn{width: 7px; height: 7px; display: inline-block; background: url({{asset('images/down-icon.svg')}} no-repeat; background-size: 7px; margin-top: 6px;}

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

</style>

<div class="alertBg" id="msgBox" style="display:none;">
    <h4 class="alerttitle" id="alerttitle"></h4>
    <span class="vm f20" id='alertdetail'></span>
</div>

<input type="hidden" id="sort" value="0">
<input type="hidden" id="index_search" value="1">

<div class="">
    <div class="topsearch col-xs-12 box_flex">
   		<div class="flex1 searchtab">
   			<span></span>
   			<input type="text" class="form-control" id="keyword" value="{{$keyword}}" placeholder="请输入商品名称">
   		</div>
   		<div class="searchbtn">
   			<button onclick="search_goods()">搜索</button>
   		</div>
    </div>
    <div class="screen">
		<div class="col-xs-12 "><a href="javascript:;" id="comprehensive">综合排序 <span class="arrowico"></span></a></div>
		<!-- <div class="col-xs-4 sepline"><a href="javascript:;" id="classification">轮胎 <span class="arrowico"></span></a></div>
		<div class="col-xs-4 js-trigger">筛选 <span class="screenico"></span></div> -->

		<div class="sort_sele">
			<p class="ptick tick" onclick="set_sort(0,this)"><a href="javascript:;">综合排序</a><span></span></p>
			<p class="ptick" onclick="set_sort(1,this)"><a href="javascript:;">价格从低到高</a><span></span></p>
			<p class="ptick" onclick="set_sort(2,this)"><a href="javascript:;">价格从高到低</a><span></span></p>
			<p class="ptick" onclick="set_sort(3,this)"><a href="javascript:;">销量从高到低</a><span></span></p>
			<p class="ptick" onclick="set_sort(4,this)"><a href="javascript:;">销量从低到高</a><span></span></p>
			
		</div>

		<div class="sort_sele2">
			<p class="ptick tick" onclick="set_class(2,this)"><a href="javascript:;">轮胎</a><span></span></p>
			<p class="ptick" onclick="set_class(5,this)"><a href="javascript:;">轮毂</a><span></span></p>
			<p class="ptick" onclick="set_class(6,this)"><a href="javascript:;">润滑油</a><span></span></p>
			<p class="ptick" onclick="set_class(8,this)"><a href="javascript:;">电瓶</a><span></span></p>
		</div>
    </div>
	<script type="text/javascript">

</script>
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
			
		});  

		var currentpage=0;
	    ajax_get_goods();//初始化添加商品列表	


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
    		search_goods();
    	}

    

    	//搜索商品
	    function search_goods() {
	    	currentpage=0;
	    	ajax_get_goods();
	    }
	  

	    //加载商品
   		function ajax_get_goods(){
    		var keyword=$('#keyword').val(),index_search=$('#index_search').val(),sort=$('#sort').val();
            $("#load").show();
            stop=false;
            $.get("{{url('purchase/ajax_get_goods')}}",{"p":currentpage,"keyword":keyword,"index_search":index_search,"sort":sort}
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
 		

	</script>


<div id="goods_list">

</div>

</div>
<script type="text/javascript">
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
  WeixinJSBridge.call('hideToolbar');
  WeixinJSBridge.call('hideOptionMenu');
});
</script>