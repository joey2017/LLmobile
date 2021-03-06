@include('layouts.header')

</head>

<style type="text/css">
    .work_order_top{padding:5px 15px; color: #fff; font-size: 25px; position:relative;}
    .work_order_top a{color: #fff;}

    /*筛选框*/
    .screen_btn{ font-size: 16px;}
    .screen_btn span,.refreshico{display: inline-block; width: 24px; height: 19px;vertical-align: middle;}
    .screen_btn span{background: url({{asset('images/bossico.svg')}}) no-repeat 6px 0; background-size: 35px;}
    .refreshico{background: url({{asset('images/bossico.svg')}}) no-repeat -23px -1px; background-size: 48px;}

    .screenbox{ position: absolute; width: 100%; padding:10px; top: 50px; left: 0; color: #4C4C4C; font-size: 14px; background:#fff; display: none; border-bottom: 1px solid #eee;}
    .screenbox dl{margin-bottom: 3px;}
    .screenbox dt{margin-right: 10px;line-height: 30px;}
    .screenbox dd{padding-left: 40px;}
    .screenbox a{padding:4px 10px;display: inline-block; border:1px solid #eee; color:#4C4C4C; margin-bottom: 5px;}
    .screen_cur{background: #FF962A;border:1px solid #FF962A !important; color: #fff !important;}

    /*主要数据*/
    .main_data{ background: #ff3e3e; padding: 15px 0; overflow: hidden; color: #fff; }
    .data_tab{position: relative; padding: 30px 0 0 0; width: 150px; margin: 15px auto; height: 150px; border-radius: 50%; border: 1px solid #ff6d6d; background-color: #f33636;}
    .data_tab h4{color: #ffe400; font-size: 24px;font-weight: bold;}
    .data_tab a,.datah a{color: #fff; display:block;}
    .box-flex{ display: -webkit-box; display: -moz-box; display: -webkit-flex; display: -moz-flex; display: -ms-flexbox;  display: flex; }
    .flex1{-webkit-box-flex: 1;-moz-box-flex: 1;-webkit-flex: 1;-ms-flex: 1;flex: 1;}
    .data_b_r{border-right: 1px solid #ff7575;}
    .datah{padding-top: 5px;}
    .datah p,.turnover p{font-size: 12px;}
    .datah h5{font-size: 16px; color: #ffe400; font-weight: bold;}
    .turnover h5{font-size: 20px; color: #ff3e3e; font-weight: bold;}
    .data_rb{border-right:1px solid #eee;}

    .data_btn span{display: block;width: 50px;height: 50px; margin: 15px auto 7px auto; border-radius: 50%;}

    .da_btn8{ background: url({{asset('images/bossico2.svg')}}) no-repeat -160px -56px;background-size: 230px;background-color:#88AFF3;}
    .da_btn9{ background: url({{asset('images/bossico2.svg')}}) no-repeat -2px -104px;background-size: 230px;background-color:#F3B088;}
    .da_btn10{ background: url({{asset('images/bossico2.svg')}}) no-repeat -55px -103px;background-size: 230px;background-color:#9B88CA;}
    .da_btn15{ background: url({{asset('images/authorize.svg')}}) no-repeat 8px 7px;background-size: 34px;background-color:#34c566;}
    /*.da_btn13{ background: url({{asset('images/purchase_order.svg')}}) no-repeat 8px 7px;background-size: 34px;background-color:#7ec7f7;}*/

</style>


<!--头部-->

<div class="container-fluid topbox">
    <div class="row top">
        <div class="pg-Current">
            <a href="" ><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span></a>
        </div>
        <div class="pgt"><a>返回</a></div>
    </div>
</div>

<!--服务券-->
<div class="box-flex text-center">
    <div class="flex1 data_btn data_rb">
        <a href="{{url('biz/authorize',array('id'=>$n_location_id))}}">
            <span class="da_btn15"></span>
            <p>推送授权</p>
        </a>
    </div>
    <div class="flex1 data_btn data_rb">
        <a href="{{url('biz/myagent',array('id'=>$n_location_id))}}">
            <span class="da_btn10"></span>
            <p>代理推广</p>
        </a>
    </div>
    <div class="flex1 data_btn data_rb">
        <a href="{{url('biz/income',array('id'=>$n_location_id))}}">
            <span class="da_btn8"></span>
            <p>代理收入</p>
        </a>
    </div>
    <!-- <div class="flex1 data_btn data_rb">
        <a href="{{url('biz/purchase_order',array('id'=>$n_location_id))}}">
            <span class="da_btn13"></span>
            <p>采购订单</p>
        </a>
    </div> -->
    <div class="flex1 data_btn data_rb">
        <a href="{{url('biz/location',array('id'=>$n_location_id))}}">
            <span class="da_btn9"></span>
            <p>发展门店</p>
        </a>
    </div>
</div>
<!--分割线-->
<div class="container-fluid line" ></div>

</body>
</html>
