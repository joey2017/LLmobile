@foreach ($goods as $g)
	<div class="productlist  col-xs-12">
		@if ($type == 1)
			<a href="{{url('supWarehouse/detail',array('id'=>$g['id']))}}" class="box_flex">
		<else />
			<a href="{{url('purchase/detail',array('id'=>$g['id']))}}" class="box_flex">
		@endif
	    	<div class="leftimg">
	    		<img src="{{$g['thumbnail']}}!middle">
	    	</div>
	    	<div class="rightinfo flex1">
	    		<h3>{{$g['goods_name']}}</h3>
	    		<div style="display: inline">
		    		<p class="text-right">{{$g['supplier_name']}}</p>
	    		</div>
	    		<div class="d-main">
	    			<span class="price">￥:{{price($g['price'])}}</span> 
	    			<span>/{{$g['unit']}}</span>
	    			@if ($type == 1)
	    			<span class="pull-right">数量 <span style="color: #eb5211;font-size: 14px;">{{$g['stock']}}</span></span>
	    			<else />
	    			<span class="pull-right">已售 {{$g['sales']}}</span>
	    			@endif
	    		</div>
	    	</div>
	    </a>
	</div>
@endforeach

