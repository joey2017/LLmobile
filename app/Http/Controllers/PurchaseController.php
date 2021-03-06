<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class purchaseController extends Controller
{
    //采购主页
    public function home(){

            if(!session('account_info')){               
                session('redirect_url',url('purchase/home'));
                header("Location:".url("biz/login"));
            }else{
            
                // $this->assign('title','诚车堂-订货管理小助手！');
                $title = '诚车堂-订货管理小助手！';
                // $this->display();
                return view('purchase/home',['title'=>$title,'no_include'=>1]);
            }
        
    }

    //采购商品列表
    public function index(Request $request)
    {
            if(!session('account_info')){               
                session('redirect_url',url('purchase/index'));
                header("Location:".url("biz/login"));
            }

            $t = $request->t==0 ? 2 : intval($request->t);
            // dd($request->t);
            // $class_list=M('pms_class')->where('is_del=0')->order('sort asc')->select();
            $class_list = DB::table('pms_class')->where('is_del','0')->orderBy('sort', 'asc')->get();
            $class_list = objectToArray($class_list);
            foreach ($class_list as $k => $v) {
                if($v['pid'] == 0){
                    $cl[$v['id']]['c_name'] = $v['class_name'];
                }else{
                    $cl[$v['pid']]['item'][] = $v;
                }

                if($v['id'] == $t){
                    $class_name = $v['class_name'];
                }
            }

            // $attr_list=M('pms_attr')->field('id,attr_name,attr_val')->where('class_id=2')->order('sort desc')->select();
            $attr_list = DB::table('pms_attr')->select('id','attr_name','attr_val')->where('class_id',2)->orderBy('sort', 'desc')->get();
            $attr_list = objectToArray($attr_list);
            foreach ($attr_list as $k => $v) {
                $attr_list[$k]['attr_val'] = explode(',', $v['attr_val']);
            }
            $title = "诚车堂-订货管理小助手！";
            $no_include= 1;
            // $this->assign('title','诚车堂-订货管理小助手！');
            // $title = '诚车堂-订货管理小助手！';
            // $this->assign('t',$t);
            // $this->assign('class_name',$class_name);
            // $this->assign('class_list',$cl);
            // $this->assign('attr_list',$attr_list);
            // $this->display();
            return view('purchase/index',['title'=>$title,'t'=>$t,'class_name'=>$class_name,'class_list'=>$cl,'attr_list'=>$attr_list,'no_include'=>$no_include]);
    
    }

    //精品推荐列表
    public function ajax_get_qualitygoods(Request $request){
        // $page = intval($_REQUEST['p']);
        $page = $request->input('p');
        $limit =($page*8).",8";

        $qualitygoods = DB::select("select pg.id,pg.goods_name,pg.thumbnail,pg.price,pg.unit,pg.promotion_price,ps.name as supplier_name,pg.sales from fw_pms_goods as pg left join fw_pms_goods_attr as pga on pga.goods_id=pg.id left join fw_pms_supplier as ps on ps.id=pg.supplier_id where pg.is_sale=? and pg.is_del=? and pg.is_top=? order by pg.supplier_id asc limit ".$limit,[1,0,1]);

        $qualitygoods = objectToArray($qualitygoods);
        foreach ($qualitygoods as $k => $v) {
            if($v['promotion_price']>0){
                $qualitygoods[$k]['price']=$v['promotion_price'];
            }
        }
        $html = '';
        foreach ($qualitygoods as $key => $g) {

            $html .= '<div class="col-xs-6 tab_subset producttab">
                <div class="proinfo">
                    <a href="'.url('purchase/detail',array('id'=>$g['id'])).'" class="btn-block">
                        <img src="'.$g['thumbnail'].'!purchase">
                        <p>'.$g['goods_name'].'</p>
                        <div class="d-main">
                            <span class="price">￥:'.sprintf('%01.2f',$g['price']).'</span> 
                            <span>/'.$g['unit'].'</span>
                            <span class="pull-right">已售 '.$g['sales'].'</span>
                        </div>
                    </a>
                </div>
            </div>';
        }
    
        // $this->assign('qualitygoods',$qualitygoods);
        // echo $html=$this->fetch();
        return $html;
    }

    public function class_list(Request $request){
        $t = intval($_REQUEST['t']);
        $t = $request->input('t');
        $list=M('pms_class')->where('is_del=0')->order('sort asc')->select();
        foreach ($list as $k => $v) {
            if($v['pid']==0){
                $cl[$v['id']]['c_name']=$v['class_name'];
            }else{
                $cl[$v['pid']]['item'][]=$v;
            }
            
        }
        $this->assign('title','诚车堂-订货管理小助手！');      $this->assign('t',$t);
        $this->assign('cl',$cl);
        $this->display();
    }

    //首页搜索
    public function search(Request $request){
        // Input::get('book');
        if(!session('account_info')){           
            session('redirect_url',url('purchase/search'));
            header("Location:".url("biz/login"));
        }
        // $keyword = trim($_REQUEST['keyword']);
        $keyword = $request->input('keyword');
        // $this->assign('keyword',$keyword);
        // $this->assign('title','诚车堂-订货管理小助手！');
        // $this->display();
        $title = '诚车堂-订货管理小助手！';
        return view('purchase.search',compact('keyword','title'));
    }


    //获取商品
    public function ajax_get_goods(Request $request){

        $index_search = $request->input('index_search');
        $class_id     = $request->input('class_id')?$request->input('class_id'):2;
        $keyword      = $request->input('keyword');
        $attr         = $request->input('attr');
        $sort         = $request->input('sort');
        $page         = $request->input('page');
        $limit =($page*8).",8";

        $where = '';        
        $bind_v = '';        
        $value = '';        

        //根据关键字搜索
        if($keyword){
            $where .= " and pg.goods_name like '%".$keyword."%'";
            $bind_v .= " and pg.goods_name like '%?%'";
        }

        if(!$index_search){
            $where.=" and pg.class_id=".$class_id;
            $bind_v.=" and pg.class_id=?";
        }

        //根据属性筛选
        if($attr){
            $attr = explode(',', $attr);
            foreach ($attr as $k => $v) {
                $where .= " and FIND_IN_SET('".$v."',pga.attr_val)";
                $bind_v .= " and FIND_IN_SET(?,pga.attr_val)";
                $value .= " FIND_IN_SET('".$v."',fw_pga.attr_val) and ";
            }
            $value = substr($value,0,strlen($value)-4);
        }

        //按销量排序
        if(in_array($sort, array(0,1,2,3,4))){
            switch ($sort) {
                case 0:
                    // $sort = " order by pg.id asc";
                    $field = 'pg.id';
                    $sort  = 'asc';
                    break;
                case 1:
                    // $sort = " order by pg.price asc";
                    $field = 'pg.price';
                    $sort  = 'asc';
                    break;
                case 2:
                    // $sort = " order by pg.price desc";
                    $field = 'pg.price';
                    $sort  = 'desc';
                    break;
                case 3:
                    // $sort = " order by pg.sales desc";
                    $field = 'pg.sales';
                    $sort  = 'desc';
                    break;
                case 4:
                    // $sort = " order by pg.sales asc";
                    $field = 'pg.sales';
                    $sort  = 'asc';
                    break;
            }           
        }


        // $goods=M()->query("select pg.id,pg.goods_name,pg.thumbnail,pg.price,pg.unit,pg.promotion_price,ps.name as supplier_name,pg.sales from fw_pms_goods as pg left join fw_pms_goods_attr as pga on pga.goods_id=pg.id left join fw_pms_supplier as ps on ps.id=pg.supplier_id where pg.is_sale=1 and pg.is_merge=0 and pg.is_del=0 ".$where.$sort." limit ".$limit);
        // $goods = DB::select("select pg.id,pg.goods_name,pg.thumbnail,pg.price,pg.unit,pg.promotion_price,ps.name as supplier_name,pg.sales from fw_pms_goods as pg left join fw_pms_goods_attr as pga on pga.goods_id=pg.id left join fw_pms_supplier as ps on ps.id=pg.supplier_id where pg.is_sale=? and pg.is_merge=? and pg.is_del=? ".$bind_v.$sort." limit ".$limit);
        $goods = DB::table('pms_goods as pg')
        ->select('pg.id','pg.goods_name','pg.thumbnail','pg.price','pg.unit','pg.promotion_price','ps.name as supplier_name','pg.sales')
        ->when($keyword, function ($query) use ($keyword) {
            return $query->where('pg.goods_name','like','%'.$keyword.'%');
        })
        ->where([['pg.is_sale',1],['pg.is_merge',0],['pg.is_del',0],['pg.class_id',$class_id]])
        ->when($value, function ($query) use ($value) {
            return $query->whereRaw($value);
        })
        ->leftJoin('pms_goods_attr as pga', 'pga.goods_id', '=', 'pg.id')
        ->leftJoin('pms_supplier as ps', 'ps.id', '=', 'pg.supplier_id')
        ->orderBy($field,$sort)
        ->limit(8)
        ->get();
        $goods = objectToArray($goods);
        
        foreach ($goods as $k => $v) {
            if($v['promotion_price']>0){
                $goods[$k]['price']=$v['promotion_price'];
            }
        }

        $html = ''; 
        foreach ($goods as $g){
            $html .= '<div class="productlist  col-xs-12">';
                    $html .= '<a href="'.url('purchase/detail',array('id'=>$g['id'])).'" class="box_flex">';
                    $html .= '<div class="leftimg">';
                        $html .= '<img src="'.$g['thumbnail'].'!middle">';
                    $html .= '</div>';
                    $html .= '<div class="rightinfo flex1">';
                    $html .= '<h3>'.$g['goods_name'].'</h3>';
                    $html .= '<div style="display: inline">';
                    $html .= '<p class="text-right">'.$g['supplier_name'].'</p>';
                    $html .= '</div>';
                    $html .= '<div class="d-main">';
                    $html .= '<span class="price">￥:'.sprintf('%01.2f',$g['price']).'</span>'; 
                    $html .= '<span>/'.$g['unit'].'</span>';
                
                    $html .= '<span class="pull-right">已售 '.$g['sales'].'</span>';

                    $html .= '    </div>
                    </div>
                </a>
            </div>';
        }

        return $html;
    }

    //获取分类属性
    public function ajax_get_attr(Request $request){

        //默认轮胎id
        $class_id = $request->input('class_id')?$request->input('class_id'):2;

        $attr_list = DB::table('pms_attr')
        ->select('id','attr_name','attr_val')
        ->where('class_id',$class_id)
        ->orderBy('sort','desc')->get();
        $attr_list = objectToArray($attr_list);
        foreach ($attr_list as $k => $v) {
            $attr_list[$k]['attr_val'] = explode(',', $v['attr_val']);      
        }

        $html = '';
        foreach ($attr_list as $a){
            $html .='<div class="sift_row">
            <div class="row_title">'.$a['attr_name'].'
            <span class="fa fa-caret-down pull-right"></span>
        </div>'; 
            $html .='<div class="row_body">
                <ul class="row">';
                    foreach ($a['attr_val'] as $k => $av){
                         $html .= '<li class="col-xs-4">
                            <a href="javascript:;" class="tc_project attr_val" value="'.$a['id'].':'.$av.'">'.$av.'</a>
                        </li>';
                    }
                $html .= '        
                </ul>
            </div>
        </div>';
        }
    
        $html .= '<script type="text/javascript">

        $(document).ready(function(){  

            $(".row_title").each(function(index){
                $(this).click(function(){
                    $(this).find("span").toggleClass("fa-caret-down").toggleClass("fa-caret-up");
                    $(this).next().toggle();
                }); 
            });

        });   

        $(".row a").click(function(){ 
            $(this).toggleClass("tc_choose");
            var attr = [];
            $(".row a").filter(".attr_val").each(function(){
                if($(this).hasClass("tc_choose") && $(this).attr("value")!=0){
                    attr.push($(this).attr("value"));
                }
            });

            $("#attr_value").val(attr);

        });

        </script>';   
        return $html;
    }

    //采购商品详情
    public function detail(Request $request)
    {
        // $id=intval($_GET['id']);
        // $id = $request->input('id');
        // $request->route( 'id' );  or  $request->id;
        $id = $request->id;
        if(!session('account_info')){
            session('redirect_url',url('purchase/detail',array('id'=>$id)));
            header("Location:".url("biz/login"));
        }

        //开启日志查询
        DB::enableQueryLog();
        //详情信息
        $info = DB::table('pms_goods as pg')
        ->leftJoin('pms_goods_attr as fpga','fpga.goods_id','=','pg.id')
        ->leftJoin('pms_supplier as fps','fps.id','=','pg.supplier_id')
        ->select('pg.id','pg.goods_name','pg.unit','pg.sales','pg.thumbnail','pg.price','pg.stock','pg.promotion_price','pg.market_price','pg.detail','pg.imgs','pg.car_ids','fpga.attr_val','fpga.attr_name_val','fps.name as supplier_name','fps.qq','pg.class_id')
        ->where([['pg.is_sale','=',1],['pg.is_del','=',0],['pg.id','=',$id]])
        ->first();
        // dd(lastSql());
        $info = objectToArray($info);
        // dd($info);
        /*if(!$info){
            $this->error('商品不存在或已下架',U('purchase/index'),3);
            return redirect('home/dashboard');

        }*/

        $info['imgs']   = array_values(array_filter(explode(',',$info['imgs'])));
        
        $info['detail'] = str_replace('src="/ueditor/','src="http://www.17cct.com/ueditor/',$info['detail']);
        $attr_val=explode(',',$info['attr_name_val']);  

        foreach ($attr_val as $k => $v) {
            $value=explode('：',$v);
            if($info['class_id']==2){
                if($value[0]=='胎面宽度'){
                $last_attr=$value[1].'/';
                }
                elseif($value[0]=='扁平比')
                {
                    $last_attr.=$value[1].'R';
                }
                elseif($value[0]=='轮胎直径'){
                    $last_attr.=$value[1];
                }
            }elseif($info['class_id']==8){
                if($value[0]=='长度'){
                    $last_attr=$value[1].'*';
                }
                elseif($value[0]=='宽度')
                {
                    $last_attr.=$value[1].'*';
                }
                elseif($value[0]=='高度'){
                    $last_attr.=$value[1];
                }
            }
            $attr_names[]=$value[0];
            if(isset($value[1])){
                $attr_vals[]=$value[1];
            }else{
                $attr_vals[]='';
            }
        }
        
        if($info['class_id']==2){
            array_splice($attr_names, 1,0,array('1'=>'规格'));
            array_splice($attr_vals, 1,0,array('1'=>$last_attr));
        }elseif($info['class_id']==8){
            array_splice($attr_names, 1,0,array('1'=>'尺寸'));
            array_splice($attr_vals, 1,0,array('1'=>$last_attr));
        }
        
        //适用车型
        if($info['car_ids']){
            // $car_list = M('car')->field('id,name,parent_id,level')->where('level in(0,1,2) and id in('.$info['car_ids'].')')->select();
            $car_list = DB::table('car')->select('id','name','parent_id','level')
            ->whereIn('level',[0,1,2])
            ->whereIn('id',$info['car_ids'])
            ->get();
            //将对象转换成数组
            $car_list = get_object_vars($car_list);
            dd($car_list);
            if($car_list){
                foreach ($car_list as $k => $v) {
                    if($v['level'] == 1){
                        $car[$v['id']]['cate2'] = $v['name']; 
                    }
                    if($v['level'] == 2){
                        $car[$v['parent_id']]['cate3'][] = $v['name']; 
                    }
                }
                foreach ($car as $k => $v) {
                    $car[$k]['cate3'] = implode(' 、', $v['cate3']);
                }
            }
        }
        $cart_info = $this->get_location_cart_info();
        $cart_info = get_object_vars($cart_info);
        // dd($cart_info);
        $cart_num  = intval($cart_info['number']);
        $title     = $info['goods_name'];
        $no_include = 0;
        // $this->assign('cart_num',intval($cart_info['number']));
        // $this->assign("attr_vals",$attr_vals);
        // $this->assign("attr_names",$attr_names);
        // $this->assign("car",$car);
        // $this->assign('info',$info);
        // $this->assign('title',$info['goods_name']);
        // $this->display();
        return view('purchase/detail',compact('cart_num','attr_vals','attr_names','car','info','title','no_include'));
    }

    //加入购物车
    public function add_card(Request $request){
        $cart['goods_id'] = $request->input('goods_id');
        // $cart['goods_id']=intval($_POST['goods_id']);
        // $goods_info=M('pms_goods')->field('id,goods_name,price,supplier_id,promotion_price')->where('is_del=0 and id=0'.$cart['goods_id'])->find();
        $goods_info = DB::table('pms_goods')->select('id','goods_name','price','supplier_id','promotion_price')
        ->where([['is_del',0],['id','0'.$cart['goods_id']]])->first();
        $goods_info = get_object_vars($goods_info);
    
        if(!$goods_info){
            $result['status'] = 0;
            $result['info']   = '商品不存在或已下架';
            return json_encode($result); 
        }

        $cart['number'] = $request->input('goods_num');

        if($cart['number']<=0){
            $result['status'] = 0;
            $result['info']   = '购买商品至少为1件';
            return json_encode($result); 
        }
        
        $location_id = $this->get_location_ids();
        if(!$location_id){
            $result['status'] = -1;
            $result['info']   = '请先登录后再加入购物车';
            $result['url']    = url('biz/login');
            return json_encode($result); 
        }

        //判断商品是否已添加过在购物车
        // $goods_cart_info=M('pms_erp_cart')->field('id,number')->where('goods_id='.$cart['goods_id'].' and location_id='.$location_id)->find();
        $goods_cart_info = DB::table('pms_erp_cart')->select('id','number')
        ->where([['goods_id',$cart['goods_id']],['location_id',$location_id]])->first();
        if($goods_cart_info){           
            $goods_cart_info = get_object_vars($goods_cart_info);

            //判断商品库存
            $stock_info = $this->goods_stock_info($cart['goods_id'],$cart['number']);

            $update_cart['number'] = intval($goods_cart_info['number']) + intval($cart['number']);
            // $r=M('pms_erp_cart')->where('id='.$goods_cart_info['id'])->save($update_cart);          
            $r = DB::table('pms_erp_cart')->where('id',$goods_cart_info['id'])->update($update_cart);          
        }else{

            //判断商品库存
            $stock_info = $this->goods_stock_info($cart['goods_id'],$cart['number']);

            $cart['goods_name']  = $goods_info['goods_name'];
            $cart['price']       = $goods_info['promotion_price']>0?$goods_info['promotion_price']:$goods_info['price'];
            $cart['supplier_id'] = $goods_info['supplier_id'];
            $cart['location_id'] = $location_id;
            $cart['create_time'] = time();
            $r = DB::table('pms_erp_cart')->insert($cart);
        }       

        if($r){
            $cart_info        = $this->get_location_cart_info();
            $result['status'] = 1;
            $result['stock']  = $cart_info->number;//购物车中的商品数量
            $result['info']   = '加入购物车成功';          
        }else{
            $result['status'] = 0;
            $result['info']   = '加入购物车失败';
        }
        // $this->ajaxReturn($result);
        return json_encode($result);
    }

    //购物车管理
    public function cart(){

        $location_id = $this->get_location_ids();

        // $info = M()->query("select pg.thumbnail,pg.stock,pec.*,ps.name,pga.attr_name_val from fw_pms_erp_cart as pec left join fw_pms_goods as pg on pg.id=pec.goods_id left join fw_pms_supplier as ps on ps.id=pg.supplier_id left join fw_pms_goods_attr as pga on pga.goods_id=pg.id  where  pec.location_id=".intval($location_id));
        $info = DB::select("select pg.thumbnail,pg.stock,pec.*,ps.name,pga.attr_name_val from fw_pms_erp_cart as pec left join fw_pms_goods as pg on pg.id=pec.goods_id left join fw_pms_supplier as ps on ps.id=pg.supplier_id left join fw_pms_goods_attr as pga on pga.goods_id=pg.id where pec.location_id=?",[intval($location_id)]);
        // dd($info);
        // $info = get_object_vars($info);
        $total['price'] = 0;
        $total['count'] = 0;
        $info = objectToArray($info);
        // dd($info);
        foreach ($info as $k => $v) {
            $v['total_price']                     = $v['price']*$v['number'];
            $total['price']                       += $v['total_price'];
            $total['count']                       += $v['number'];
            $v['attr_name']                       = explode(',',$v['attr_name_val']);
            $v['change_stock']                    = intval($v['stock']);//操作库存
            $v['stock']                           = $this->goods_stock_info($v['goods_id'],0);//显示库存            
            $cart_info[$v['name']]['item'][]      = $v;
            $cart_info[$v['name']]['supplier_id'] = $v['supplier_id'];
        }

        //商品活动输出
        // $total['price']=round($total['price'],2);
        // $total['count']=intval($total['count']);
        // $this->assign('total',$total);
        // $this->assign('cart_info',$cart_info);
        // $this->assign('title','诚车堂-订货管理小助手！');
        // $this->display();
        $title = '诚车堂-订货管理小助手！';
        return view('purchase.cart',compact('total','cart_info','title'));
    }


    //修改购物车
    public function modify_cart(){
        
        $id=intval($_POST['id']);//修改记录id
        $location_id=intval($this->get_location_ids());
        $update_cart['number']=intval($_POST['number']);//修改后的数量
        $type=$_POST['type'];
        if($update_cart['number']<=0){
            $result['status']=0;
            $result['info']='购买商品至少为1件';
            ajax_return($result);
        }

        $cart_info=M('pms_erp_cart')->where('id='.$id.' and location_id='.$location_id)->find();

        if($cart_info){

            /*if($type=='a'){
                //判断商品库存
                $stock_info=$this->goods_stock_info($cart_info['goods_id'],1);

                if($stock_info<0){
                    $result['status']=0;
                    $result['info']='商品库存不足';
                    $this->ajaxReturn($result);                 
                }
            }*/

            $r=M('pms_erp_cart')->where('id='.$id)->save($update_cart);

            if($r){
                $location_cart_info=$this->get_location_cart_info();
                $result['number']=$location_cart_info['number'];
                $result['total_price']=round($location_cart_info['total_price'],2);
                $result['status']=1;
                $result['info']='修改成功';         
            }else{
                $result['status']=0;
                $result['info']='修改失败';             
            }
            $this->ajaxReturn($result);             

        }else{
            $result['status']=0;
            $result['info']='修改失败';
            $this->ajaxReturn($result); 
        }
    }

    //删除购物车
    public function delete_cart(){
        $id=intval($_POST['id']);

        if(!$id){
            $result['status']=0;
            $result['info']='请选择要删除的商品';    
            $this->ajaxReturn($result); 
        }

        $location_id=intval($this->get_location_ids());

        $r=M('pms_erp_cart')->where("id=".$id." and location_id=".$location_id)->delete();
    
        if($r){
            $cart_info=$this->get_location_cart_info();
            $result['number']=intval($cart_info['number']);
            $result['total_price']=round($cart_info['total_price'],2);
            $result['status']=1;
            $result['info']=$info.'成功';     
        }else{
            $result['status']=0;
            $result['info']=$info.'失败';     
        }

       $this->ajaxReturn($result);        
    }

    //检查订单
    public function check_order(Request $request){

        if(!session('account_info')){
            session('redirect_url',url('purchase/check_order'));
            header("Location:".url("biz/login"));
        }

        // $id_string = substr($_GET['ids'],0,-1);
        $id_string = $request->input('ids');
        // dd($ids);
        $ids = explode(',',$_GET['ids']);

        $location_id = $this->get_location_ids();
        // DB::enableQueryLog();
        // $cart_info = M('pms_erp_cart')->where('location_id='.$location_id." and id in(".$ids.")")->getField('id');      
        $cart_info = DB::table('pms_erp_cart')->whereIn('id',$ids)->where('location_id',$location_id)->select('id')->get();
        // dd(lastSql());      
        $cart_info = objectToArray($cart_info);
        // dd($cart_info);        
        if(!$cart_info){
            // $this->error('购物车还没有商品',U('purchase/index'),3);
            redirect('purchase/index');
        }

        // session(['cart_ids'=>$id_string]);
        // $request->session()->push('cart_ids', $id_string);
        $request->session()->put('cart_ids', $id_string);


        //地址
        // $address = M('pms_address')->where('is_default=1 and location_id='.$location_id)->find();       
        $address = DB::table('pms_address')->where([['is_default',1],['location_id',$location_id]])->first();

        // dd($address);       
        
        // DB::enableQueryLog(); 

        //购物车商品信息
        // $goods_info =DB::select("select pg.thumbnail,pg.price as goods_price,pg.promotion_price,pg.stock,pg.unit,pec.*,ps.name,pga.attr_name_val from fw_pms_erp_cart as pec left join fw_pms_goods as pg on pg.id=pec.goods_id left join fw_pms_supplier as ps on ps.id=pg.supplier_id left join fw_pms_goods_attr as pga on pga.goods_id=pg.id where pec.location_id=? and pec.id in(?)",[intval($location_id),$ids]);
        // dd(getLastSql());
        // dd($goods_info);
        $goods_info = DB::table('pms_erp_cart as pec')
        ->select('pg.thumbnail','pg.price as goods_price','pg.promotion_price','pg.stock','pg.unit','pec.*','ps.name','pga.attr_name_val')
        ->leftJoin('pms_goods as pg','pg.id','=','pec.goods_id')
        ->leftJoin('pms_supplier as ps','ps.id','=','pg.supplier_id')
        ->leftJoin('pms_goods_attr as pga','pga.goods_id','=','pg.id')
        ->where('pec.location_id',intval($location_id))
        ->whereIn('pec.id',$ids)
        ->get();
        $goods_info = objectToArray($goods_info);
        // dd($goods_info);
        $total['price'] = 0;
        $total['count'] = 0;
        $datas = [];
        $goods = [];
        $p = [];
        foreach ($goods_info as $k => $v) {
            //使用最新价格，如果有促销价则使用促销价
            $v['price']                             = $v['promotion_price']>0 ? $v['promotion_price'] : $v['goods_price'];
            if(isset($p[$v['supplier_id']]['total_price'])){
                $p[$v['supplier_id']]['total_price']    += $v['price']*$v['number'];
            }else{
                $p[$v['supplier_id']]['total_price'] = $v['price']*$v['number'];
            }
            $v['total_price']                       = $p[$v['supplier_id']]['total_price'];
            $v['attr_name']                         = explode(',',$v['attr_name_val']);
            $goods[$v['supplier_id']][]             = $v;
            $total['price']                         += $v['price']*$v['number'];
            $total['count']                         += $v['number'];
            $datas[$v['supplier_id']]['goods_id'][] = $v['goods_id'];
            if(isset($datas[$v['supplier_id']]['price'])&&is_array($datas[$v['supplier_id']]['price'])){
                $datas[$v['supplier_id']]['price'][] += $v['total_price'];
            }else{
                $datas[$v['supplier_id']]['price'][] = $v['total_price'];
            }
            // $datas[$v['supplier_id']]['price'][]    += $v['total_price'];

        }
        // dd($datas);
        foreach ($datas as $k => $v) {
            //供应商活动列表
            $goods[$k][0]['activity'] = $this->get_activity($v['goods_id'],$v['price'],$k);
            //能使用的优惠券列表
            $goods[$k][0]['coupon'] = $this->get_coupon($v['goods_id'],$v['price'],$k);
        }
        // dd($goods);
        // $this->assign('title','诚车堂-订货管理小助手！');
        /*$this->assign('address',$address);
        $this->assign('province_list',$province_list);
        $this->assign('goods',$goods);
        $this->assign('total',$total);
        $this->display();  */ 
        return view('purchase.check_order',compact('address','goods','total'));
        
    }


    //创建订单
    public function create_order(Request $request){

        $location_id = $this->get_location_ids();
        // dd(session('cart_ids'));
        // $ids = substr(session('cart_ids'),0,-1);
        $ids = explode(',',session('cart_ids'));
        $cart_info = DB::table('pms_erp_cart as pec')
        ->where('pec.location_id',intval($location_id))
        ->whereIn('pec.id',$ids)
        ->leftJoin('pms_goods as pg','pg.id','=','pec.goods_id')
        ->leftJoin('pms_supplier as ps','ps.id','=','pg.supplier_id')
        ->leftJoin('pms_goods_attr as pga','pga.goods_id','=','pg.id')
        ->select('pg.costs','pg.thumbnail','pg.price as goods_price','pg.promotion_price','pg.stock','pec.*','ps.name','pga.attr_name_val')
        ->get(); 
        $cart_info = objectToArray($cart_info);
        // dd($cart_info);
        if(!$cart_info){
            $result['status'] = 0;
            $result['info']   = '购物车空空如也,先去采购吧';
            return json_encode($result);
        }       

        $account_info = session('account_info');

        $address_id = $request->input('address_id');
        $address = DB::table('pms_address')->where([['id',$address_id],['location_id',$location_id]])->first();
        $create_order_price = 0;
        foreach ($cart_info as $k => $v) {
            
            //使用最新价格，如果有促销价则使用促销价
            $v['price']                             = $v['promotion_price']>0 ? $v['promotion_price'] : $v['goods_price'];
            $v['total_price']                       = $v['price']*$v['number'];//小计
            $create_order_price                     +=$v['total_price'];
            $order_info[$v['supplier_id']][]        = $v;
            $datas[$v['supplier_id']]['goods_id'][] = $v['goods_id'];
            $datas[$v['supplier_id']]['price'][]    = $v['total_price'];
        }

        if($create_order_price<=0){
            $result['status'] = 0;
            $result['info']   = '订单总价不能为0';
            return json_encode($result);
        }

        // 活动优惠或折扣id
        $act_rule_id = trim($_POST['act_rule_id']);//规则id列表,一个店铺一个活动
        // 门店优惠券id
        $coupon_ids = trim($_POST['coupon_ids']);//门店优惠券id列表,一个店铺使用一张

        //判断是否能参与活动、使用优惠券。并返回一个二维数组
        $act_data = $this->use_act_coupon($act_rule_id,$coupon_ids,$datas);
        
        //供应商id数组和留言数组
        $supplier_id = $_POST['supplier_id'];
        $remark = $_POST['remark'];

        foreach ($supplier_id as $k => $v) {
            $new_remark[$v] = $remark[$k];
        }

        $order = [];
        $all_order_id = [];
        $all_total_price = 0;
        $location_name = DB::table('supplier_location')->where('id',$location_id)->select('name')->first();
        // dd($location_name);
        //生成订单 多个店铺生成多个订单
        foreach ($order_info as $k => $v) {
            $order['order_sn']             = 'MD'.date('Ymdhis',time()).rand(10,99);
            $order['purchase_user_id']     = $account_info['id'];
            $order['location_id']          = $location_id;
            $order['location_name']        = $location_name->name;
            $order['receive_user']         = $address->name;
            $order['receive_tel']          = $address->tel;
            $order['create_time']          = time();
            $order['supplier_id']          = $k;
            $order['pay_status']           = 0;
            $order['status']               = 1;
            $order['total_original_price'] = $act_data[$k]['total_original_price'];
            $order['total_price']          = $act_data[$k]['total_price'];
            $order['act_id']               = $act_data[$k]['act_id'];
            $order['location_coupon_id']   = $act_data[$k]['location_coupon_id'];
            $order['discount_price']       = $act_data[$k]['discount_price'];
            $order['is_del']               = 0;
            $order['address']              = $address->full_address;
            $order['remark']               = $new_remark[$k];
            
            // $order_id=M('pms_order')->add($order);
            // $order_id = DB::table('pms_order')->insert($order);
            $order_id = DB::table('pms_order')->insertGetId($order);
            if($order_id){

                //将单个或多个order_id写入合并订单
                $all_order_id[] = $order_id;
                //合并订单金额
                $all_total_price += $act_data[$k]['total_price'];
                //初始化
                $order_costs = $order_nums = 0;

                //写入订单详情            
                foreach ($v as $i_k => $i_v) {
                    $order_costs             += $i_v['costs'] * $i_v['number'];
                    $order_nums              += $i_v['number'];
                    $item_data['order_id']   = $order_id;
                    $item_data['goods_name'] = $i_v['goods_name'];
                    $item_data['goods_id']   = $i_v['goods_id'];
                    $item_data['sell_price'] = $i_v['price'];
                    $item_data['num']        = $i_v['number'];
                    $item_data['thumbnail']  = $i_v['thumbnail'];
                    $item_data['attr_val']   = $i_v['attr_name_val'];                   
                    $item_result             = DB::table('pms_order_item')->insertGetId($item_data);
                }
                
                DB::select("UPDATE fw_pms_order SET `costs`=?,total_num=? WHERE `id`=?",[$order_costs,$order_nums,$order_id]);                
                if(!$item_result){
                    $result['status'] = 0;
                    $result['info']   = '订单详情创建失败';
                    return json_encode($result); 
                }

            }else{
                $result['status'] = 0;
                $result['info']   = '订单详情创建失败';
                return json_encode($result); 
            }
            
        }
        //创建合并订单
        if($all_order_id && $all_total_price){
            
            $merge_order['order_sn']         = 'JY'.date('Ymdhis',time()).rand(10,99);
            $merge_order['order_ids']        = implode(',', $all_order_id);
            $merge_order['receive_user']     = $address->name;
            $merge_order['receive_tel']      = $address->tel;
            $merge_order['location_id']      = $location_id;
            $merge_order['create_time']      = time();
            $merge_order['total_price']      = $all_total_price;
            $merge_order['pay_status']       = 0;
            $merge_order['pay_time']         = 0;
            $merge_order['means_of_payment'] = 0;
            $merge_order['is_del']           = 0;

            // $merge_order_id=M('pms_merge_order')->add($merge_order);
            $merge_order_id = DB::table('pms_merge_order')->insertGetId($merge_order);

            if($merge_order_id){
                //订单创建成功后清空购物车
                // M('pms_erp_cart')->where('location_id='.$location_id)->delete();
                DB::table('pms_erp_cart')->where('location_id',$location_id)->delete();
                $result['status']   = 1;
                $result['info']     = '订单创建成功';
                $result['order_id'] = $merge_order_id;
                return json_encode($result); 
            }else{
                $result['status'] = 0;
                $result['info']   = '订单创建失败';
                return json_encode($result); 
            }

        }else{
            $result['status'] = 0;
            $result['info']   = '订单创建失败';
            return json_encode($result); 
        }

    }

    //订单信息
    public function order(Request $request){
        // $id=intval($_REQUEST['id']);
        // $t=trim($_REQUEST['t']);
        $id = $request->input('id');
        $t  = $request->input('t');
        $location_id = $this->get_location_ids();

        if($t == 'pms_merge_order'){
            // $order_info=M('pms_merge_order')->where('id='.$id.' and pay_status=0 and system=0 and location_id='.$location_id)->find();          
            $order_info = DB::table('pms_merge_order')->where([['id',$id],['pay_status',0],['system',0],['location_id',$location_id]])->first();
            $order_info = objectToArray($order_info);
                      
        }else{
            // $order_info=M('pms_order')->where('id='.$id.' and purchase_user_id=0 and system=0 and location_id='.$location_id)->find();
            $order_info = DB::table('pms_order')->where([['id',$id],['purchase_user_id',0],['system',0],['location_id',$location_id]])->first();          

            $order_info = objectToArray($order_info);
            $order_info['pay_type'] = $this->get_pay_type($order_info->means_of_payment,$order_info->pay_type);       
        }

        if(!$order_info){
            // $this->error('无此订单信息',U('purchase/index'),3);
            redirect(url('purchase/index'));
        }
        // dd($order_info);
        return view('purchase.order',['oi'=>$order_info]);
    }

    //确认订单
    public function confirm_order(){

        $id = intval($_REQUEST['id']);
        $location_id=intval($this->get_location_ids());
        $account_info=session('account_info');

        $data['purchase_user_id']=$account_info['id'];
        $r=M('pms_order')->where('id='.$id.' and is_del=0 and location_id='.$location_id)->save($data);
        if($r){
            $result['status']=1;
            $result['msg']='订单确认成功';
        }else{
            $result['status']=0;
            $result['msg']='订单确认失败';
        }
        $this->ajaxReturn($result);
    }

    //收货
    public function receipt_goods(){
        $id=intval($_REQUEST['id']);
        $t=trim($_REQUEST['t']);
        $location_id=$this->get_location_ids();
    
        $order_info=M('pms_order as po')->join('fw_pms_supplier as ps on ps.id=po.supplier_id')->field('po.*,ps.name as supplier_name,ps.mobile as supplier_mobile')->where('po.id='.$id.' and po.system=0 and po.location_id='.$location_id)->find();
        //var_dump(M('pms_order as po')->getlastsql());
        $order_info['pay_type']=$this->get_pay_type($order_info['means_of_payment'],$order_info['pay_type']);               
        $order_info['order_status']=$this->get_order_status($order_info['status']);
        if(!$order_info){
            $this->error('无此订单信息',U('purchase/index'),3);
        }
        
        $this->assign('oi',$order_info);
        $this->display();
    }

    /**
    * 获得订单状态
    **/
    private function get_order_status($status_num){

        switch ($status_num) {
            case 1:
                $status = '未发货';
                break;
            case 2:
                $status = '已发货';
                break;
            case 3:
                $status = '未收货';
                break;
            case 4:
                $status = '已收货';
                break;
            case 5:
                $status = '已作废';
                break;  
            default:
                $status = '';
                break;
        }

        return $status;
    }


    //确认收货
    public function confirm_receipt(){

        $id = intval($_REQUEST['id']);
        $location_id=intval($this->get_location_ids());
        $account_info=session('account_info');

        $data['status']=4;
        $r=M('pms_order')->where('id='.$id.' and is_del=0 and location_id='.$location_id)->save($data);
        if($r){
            $result['status']=1;
            $result['msg']='确认收货成功';

            //给供应商发送微信消息
            $order_info = M('pms_order')->field('order_sn,location_name,supplier_id,type')->where('id='.$id.' and is_del=0 and system=0 and location_id='.$location_id)->find();
            if($order_info){

                $openid_list=M('pms_weixin')->field('open_id')->where('relation_id='.$order_info['supplier_id'].' and type=2')->select();
                if($openid_list){
                    foreach ($openid_list as $k => $v) {
                        $this->send_supplier_wx_msg($v['open_id'],$order_info['order_sn'],$order_info['location_name'],2);
                    }
                }
            }

        }else{
            $result['status']=0;
            $result['msg']='确认收货失败';
        }
        $this->ajaxReturn($result);

    }

    /**
    * 给供应商发送微信消息
    **/
    private function send_supplier_wx_msg($wxid,$order_sn,$location_name,$type,$name=''){

        if ($type == 1) {
            $first = '"'.$location_name.'"已确认订单，请按流程快速操作！';
            $status = "已确认";
        }elseif ($type == 2) {
            $first = '"'.$location_name.'"已确认收货，请知悉！';
            $status = "已收货";
        }else{
            $first = '"'.$name.'"，您好！"'.$location_name.'"给你下了一条新的订单，请及时处理！打印订单前先致电客户了解具体订单和收款情况！';
            $status = '新订单';
        }
        
        $json=array("touser"=>$wxid,
                    "template_id"=>"P4l0GDvpYTR_DmSKflGmYehFC-w8VKxMCRTk8ktvE7M",
                    "topcolor"=>"#FF0000",
                    "data"=>array('first'=>array('value'=>$first),
                                'keyword1'=>array('value'=>$order_sn),
                                'keyword2'=>array('value'=>$status),
                                'remark'=>array('value'=>'【车堂盛世】诚车堂-订货管理小助手！')
                        )
            );

        $this->send_template_info($json);

    }

    //发送模板
    private function send_template_info($json){
        
            $access_token  = $this->get_sj_acc_token();
            $get_token_url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;         
            $ch  = curl_init() ;
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,urldecode(json_encode(($json))));
            curl_setopt($ch, CURLOPT_URL,$get_token_url);           
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            $result = curl_exec($ch) ;
            curl_close($ch);
    }

    /**
    * 获取诚车堂商户版access_token
    **/
    private function get_sj_acc_token()
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb09359ac1d3f2267&secret=7e161c7930c9de1f3213dd13d6bb7a9c");
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $access_token = curl_exec($ch);
        $access_token=json_decode($access_token, true); 
        return $access_token['access_token'];   
    }

    //线下支付
    public function offline_pay(){

        $id = intval($_REQUEST['id']);
        $pay_mode = intval($_POST['pay_mode']);
        $pay_type = intval($_POST['pay_type']);
        $pay_remark = trim($_POST['pay_remark']);
            
            
        if(!in_array($pay_mode, array(3,4,5,6)))
        {           
            $result['status']=0;
            $result['msg']='支付方式不正确';
            $this->ajaxReturn($result);
        }
        $location_id=intval($this->get_location_ids());
        $order_info = M('pms_merge_order')->where('id='.$id.' and pay_status=0 and is_del=0 and system=0 and pay_time=0 and location_id='.$location_id)->find();    
        
        if($order_info && $order_info['order_ids'] && $order_info['total_price']>0){
            
            //更新合并订单状态
            $merge_update['outer_notice_sn']='线下支付';
            $merge_update['pay_time']=time();
            $merge_update['means_of_payment']=$pay_mode;
            $merge_update['pay_status']=2;
            $merge_update['pay_type']=$pay_type;
            $order_update['pay_remark']=$pay_remark;
            $r=M('pms_merge_order')->where('id='.$order_info['id'])->save($merge_update);

            //更新子订单状态
            $order_update['outer_notice_sn']='线下支付';
            $order_update['pay_time']=time();
            $order_update['means_of_payment']=$pay_mode;
            $order_update['status']=1;
            $order_update['pay_status']=1;
            $order_update['pay_type']=$pay_type;
            $order_update['pay_remark']=$pay_remark;
            $r2 =M('pms_order')->where('id in('.$order_info['order_ids'].')')->save($order_update);

            //给门店送优惠券
            $order_list =M('pms_order')->where('system=0 and id in('.$order_info['order_ids'].')')->select();

            if($order_list){
                foreach ($order_list as $k => $v) {
                    $act_ids[] = $v['act_id'];
                }

                if($act_ids){
                    //查询购物送券活动列表中的优惠券id             
                    $act_list =M('pms_activity as pa')->join('fw_pms_activity_rule as par on par.act_id=pa.id')->where("pa.act_type=3 and pa.is_del=0 and (unix_timestamp() between pa.start_time and pa.end_time) and pa.id in (".implode(',', $act_ids).")")->select();
                    if($act_list){
                        foreach ($act_list as $k => $v) {
                            $coupon_info = M('pms_coupon')->where('is_del=0 and id='.intval($v['coupon_id']).' and (unix_timestamp() between start_time and end_time) and give_num<total_num')->find();
                            if($coupon_info){
                                $location_coupon['coupon_name'] = $coupon_info['name'];
                                $location_coupon['full_money'] = $coupon_info['full_money'];
                                $location_coupon['discount_money'] = $coupon_info['discount_money'];
                                $location_coupon['num'] = 1;
                                $location_coupon['give_num'] = 1;
                                $location_coupon['coupon_id'] = $coupon_info['id'];
                                $location_coupon['goods_ids'] = $coupon_info['goods_ids'];
                                $location_coupon['start_time'] = $coupon_info['start_time'];
                                $location_coupon['end_time'] = $coupon_info['end_time'];
                                $location_coupon['supplier_id'] = $coupon_info['supplier_id'];
                                $location_coupon['add_time'] = time();
                                $location_coupon['type'] = 1;
                                $location_coupon['location_id'] = intval($this->get_location_ids());
                                M('pms_location_coupon')->add($location_coupon);
                                $coupon_ids[] = $coupon_info['id'];
                            }
                        }

                        if($coupon_ids){
                            //更新优惠券赠送数量
                            M('pms_coupon')->where('id in('.implode(',',$coupon_ids).')')->setInc('give_num');                          
                        }
                    }
                }
            }

            if($r && $r2){
                switch ($pay_mode) {
                    case 3:
                        $msg = '现金支付';
                        break;
                    case 4:
                        $msg = '刷卡支付';
                        break;
                    case 5:
                        $msg = '转账支付';
                        break;
                    case 6:
                        $msg = '月结支付';
                        break;
                    default:
                        $msg = '挂账支付';
                        break;
                }
                $result['status']=1;
                $result['msg']=$msg.'成功';
            }

        }else{
            $result['status']=0;
            $result['msg']='无此订单信息';
            
        }
        $this->ajaxReturn($result);
    }

    //支付回调页面
    public function pay_back()
    {
        $id = intval($_REQUEST['id']);
        $type=trim($_REQUEST['type']);
        $location_id=$this->get_location_ids();
        if($type == 'confirm'){
            $order=M('pms_order')->where('id='.$id.' and is_del=0 and system=0 and location_id='.$location_id)->find();

            //确认订单发送微信消息
            $openid_list=M('pms_weixin')->field('open_id')->where('relation_id='.$order['supplier_id'].' and type=2')->select();
            if($openid_list){
                foreach ($openid_list as $k => $v) {
                    $this->send_supplier_wx_msg($v['open_id'],$order['order_sn'],$order['location_name'],1);
                }
            }

        }else{
            $order=M('pms_merge_order')->where('id='.$id.' and location_id='.$location_id.' and pay_status=2 and system=0 ')->find();

            //给供应商发送门店下单微信消息
            $order_list =M('pms_order as po')->field('po.*,ps.name')->where('po.system=0 and po.id in('.$order['order_ids'].')')->join('fw_pms_supplier as ps ON ps.id=po.supplier_id')->select();

            $openid_list = array();
            foreach ($order_list as $key => $value) {
                
                $openid_list[]=M('pms_weixin')->field('open_id')->where('relation_id='.$value['supplier_id'].' and type=2')->select();
            }
            if($openid_list){
                foreach ($openid_list as $k => $v) {
                    foreach ($v as $kk => $vv) {
                        $this->send_supplier_wx_msg($vv['open_id'],$order_list[$k]['order_sn'],$order_list[$k]['location_name'],3,$order_list[$k]['name']);
                    }
                }
            }

        }

        if (!$order) {
            $this->error('无此订单信息',U('purchase/home'),3);
        }

        $order['pay_type']=$this->get_pay_type($order['means_of_payment'],$order['pay_type']);
        $this->assign('order',$order);
        // $this->assign("price",$order['total_price']);
        // $this->assign("order_sn",$order['order_sn']);
        $this->assign("title","支付结果");
        $this->display();
    }

    /**
    * 采购支付方式
    **/
    private function get_pay_type($means_of_payment,$type){
        if($type == 1){
            $pay_type = '(现金挂账结算)';
        }elseif ($type == 2) {
            $pay_type = '(月底挂账结算)';
        }elseif ($type == 3) {
            $pay_type = '(约定挂账结算)';
        }
        switch ($means_of_payment) {
            case 1:
                $msg = '支付宝';
                break;
            case 2:
                $msg = '微信';
                break;
            case 3:
                $msg = '现金支付'.$pay_type;
                break;
            case 4:
                $msg = '刷卡支付'.$pay_type;
                break;
            case 5:
                $msg = '转账支付'.$pay_type;
                break;
            case 6:
                $msg = '物流代收'.$pay_type;
                break;      
            default:
                $msg = '-';
                break;
        }

        return $msg;
    }


    //地址列表
    public function address_list(){

        $location_id = $this->get_location_ids();

        //收货地址
        $address_list =M('pms_address')->where('location_id='.$location_id)->select();

        $this->assign('ids',session('cart_ids'));
        $this->assign('address_list',$address_list);

        $this->display();
    }

    //地址添加
    public function address_add(){
        $r=intval($_GET['r']);
        
        $province_list =M('delivery_region')->where('region_level=2')->select();    

        if($r){
            $this->assign('ids',session('cart_ids'));
        }
        $this->assign('r',$r);  
        $this->assign('province_list',$province_list);
        $this->display();
    }

    //ajax添加地址
    public function ajax_address_add(){

        if($_POST['user_name'] && $_POST['province'] && $_POST['city'] && $_POST['area'] && $_POST['address'] && $_POST['tel']){

            $location_id = $this->get_location_ids();           
            $province = explode('_', trim($_POST['province']));
            $city = explode('_', trim($_POST['city']));
            $area = explode('_', trim($_POST['area']));

            $address['name'] = trim($_POST['user_name']);
            $address['province_id'] = $province[0];
            $address['city_id'] = $city[0];
            $address['area_id'] = $area[0];
            $address['detail_address'] = trim($_POST['address']);
            $address['full_address'] = $province[1].$city[1].$area[1].trim($_POST['address']);
            $address['tel'] = trim($_POST['tel']);
            $address['location_id'] = $location_id;
            $address['is_default'] = intval($_POST['is_default']);

            $address_id=M('pms_address')->add($address);            

            if($address_id){

                //如果新增的为默认地址，则其它地址修改为非默认
                if($address['is_default'] == 1){
                    $update_data['is_default']=0;
                    M('pms_address')->where('location_id='.$location_id.' and id!='.$address_id)->save($update_data);                   
                }

                $this->ajaxReturn(array('status'=>1,'msg'=>'新增地址成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'新增地址失败'));
            }   

        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>'新增地址失败'));
        }
        
    }

    //ajax修改地址
    public function address_edit(){

        $id = intval($_GET['id']);
        
        $location_id=$this->get_location_ids();

        $address_info =M('pms_address')->where('location_id='.$location_id.' and id='.$id)->find();
        

        if(!$address_info){
            $this->error('无此地址',U('purchase/address_list'),3);
        }

        //省份列表
        $province_list =M('delivery_region')->where('region_level=2')->select();

        //城市列表
        $city_list =M('delivery_region')->where('pid='.$address_info['province_id'])->select();

        //地区列表
        $area_list =M('delivery_region')->where('pid='.$address_info['city_id'])->select();

    
        foreach ($city_list as $k => $v) {
            if($v['id'] == $address_info['city_id']){
                $c_select = 'selected="true"';
            }else{
                $c_select = '';
            }
            $city_str .= '<option value="'.$v['id'].'_'.$v['name'].'" '.$c_select.'>'.$v['name'].'</option>';
        }
        foreach ($area_list as $k => $v) {
            if($v['id'] == $address_info['area_id']){
                $a_select = 'selected="true"';
            }else{
                $a_select = '';
            }
            $area_str .= '<option value="'.$v['id'].'_'.$v['name'].'" '.$a_select.'>'.$v['name'].'</option>';
        }

        $this->assign('province_list',$province_list);
        $this->assign('address',$address_info);
        $this->assign('city',$city_str);
        $this->assign('area',$area_str);

        $this->display();

    }

    //ajax修改地址保存
    public function ajax_address_save(){

        if($_POST['id'] && $_POST['user_name'] && $_POST['province'] && $_POST['city'] && $_POST['area'] && $_POST['address'] && $_POST['tel']){

            $id = intval($_POST['id']);
            $location_id = $this->get_location_ids();           
            $province = explode('_', trim($_POST['province']));
            $city = explode('_', trim($_POST['city']));
            $area = explode('_', trim($_POST['area']));

            $address['name'] = trim($_POST['user_name']);
            $address['province_id'] = $province[0];
            $address['city_id'] = $city[0];
            $address['area_id'] = $area[0];
            $address['detail_address'] = trim($_POST['address']);
            $address['full_address'] = $province[1].$city[1].$area[1].trim($_POST['address']);
            $address['tel'] = trim($_POST['tel']);
            $address['is_default'] = intval($_POST['is_default']);
            
            $result=M('pms_address')->where('id='.$id.' and location_id='.$location_id)->save($address);            
            if($result){
                //如果修改为默认地址，则其它地址修改为非默认
                if($address['is_default'] == 1){
                    $update_data['is_default']=0;
                    M('pms_address')->where('location_id='.$location_id.' and id!='.$id)->save($update_data);                   
                }
                $this->ajaxReturn(array('status'=>1,'msg'=>'修改地址成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'修改地址失败'));
            }   

        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>'修改地址失败'));
        }   

    }

    //ajax删除地址
    public function ajax_address_del(){

        $id = intval($_POST['id']);

        if(!$id){
            $this->ajaxReturn(array('status'=>0,'msg'=>'删除失败'));
        }

        $location_id=$this->get_location_ids();
        $result =M('pms_address')->where('location_id='.$location_id.' and id='.$id)->delete();

        if($result){
            $this->ajaxReturn(array('status'=>1,'msg'=>'删除成功'));
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>'删除失败'));
        }
        
    }

    //ajax设置默认地址
    public function ajax_set_default(){

        $id = intval($_POST['id']);
        

        if(!$id){
            $this->ajaxReturn(array('status'=>0,'msg'=>'设置失败'));
        }

        $location_id = $this->get_location_ids();

        //修改为默认地址
        $update_data['is_default']=1;
        $r1 = M('pms_address')->where('location_id='.$location_id.' and id='.$id)->save($update_data);

        //其它地址修改为非默认地址
        $update_data['is_default']=0;
        $r2 =M('pms_address')->where('location_id='.$location_id.' and id!='.$id)->save($update_data);

        if($r1 && $r2){
            $this->ajaxReturn(array('status'=>1,'msg'=>'设置成功'));
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>'设置失败'));
        }

    }

    //根据pid获得地区
    public function get_area(){
        $id = intval($_POST['id']);
        $type = intval($_POST['type']);

        if($id){
            $area_list =M('delivery_region')->field('id,name')->where('pid='.$id)->select();
        }
        
        if($type == 1){
            $str = '<option value="0">请选择城市</option>';
        }else{
            $str = '<option value="0">请选择地区</option>';
        }
        
        if($area_list){
            foreach ($area_list as $k => $v) {
                $str .= '<option value="'.$v['id'].'_'.$v['name'].'">'.$v['name'].'</option>';
            }
        }

        $this->ajaxReturn($str);
        
    }


        /**
    * 获得能使用的优惠券列表
    * @param    $goods_id        购买的商品id数组(按供应商分)
    * @param    $price           购买的商品售价数组(按供应商分)
    * @param    $supplier_id     供应商id
    * @return   $location_coupon 能使用的优惠券列表
    */
    private function get_coupon($goods_id,$price,$supplier_id){

        //查找本门店在该供应商的优惠券列表
        // $location_coupon =M()->query("select id,goods_ids,full_money,discount_money from fw_pms_location_coupon where location_id=".intval($this->get_location_ids())." and supplier_id=".$supplier_id." and num>0 and (unix_timestamp() between start_time and end_time)"); 
        $location_coupon = DB::select("select id,goods_ids,full_money,discount_money from fw_pms_location_coupon where location_id=? and supplier_id=? and num>0 and (unix_timestamp() between start_time and end_time)",[intval($this->get_location_ids()),$supplier_id]); 
        $location_coupon = objectToArray($location_coupon);
        // dd($location_coupon);
        if($location_coupon){
            $coupon = [];
            foreach ($location_coupon as $k => $v) {

                foreach ($goods_id as $g_k => $g_v) {
                    if($v['goods_ids'] == 0){//所有商品
                        if(isset($coupon[$v['id']]['price'])){
                            $coupon[$v['id']]['price'] += $price[$g_k];
                        }else{
                            $coupon[$v['id']]['price']= $price[$g_k];
                        }
                    }else{//部分商品
                        $had_goods_id = explode(',', $v['goods_ids']);
                        if(in_array($g_v, $had_goods_id)){
                            if(isset($coupon[$v['id']]['price'])){
                                $coupon[$v['id']]['price'] += $price[$g_k];
                            }else{
                                $coupon[$v['id']]['price']= $price[$g_k];
                            }
                        }
                    }
                }

            }

            // dd($coupon);

            foreach ($location_coupon as $k => $v) {

                if(is_array($coupon)&&!empty($coupon)){

                    if($coupon[$v['id']]['price'] >= $v['full_money']){//金额满足

                        $is_all = $v['goods_ids'] == 0 ? '全部商品':'部分商品';
                        $location_coupon[$k]['coupon_name'] = round($v['discount_money'],2)."元优惠券（".$is_all."满".round($v['full_money'],2)."元使用）";
                        
                    }else{
                        unset($location_coupon[$k]);
                    }
                }


            }

            return $location_coupon;

        }
    }

    /**
    * 获得供应商活动
    * @param    $goods_id       购买的商品id数组(按供应商分)
    * @param    $price          购买的商品售价数组(按供应商分)
    * @param    $supplier_id    供应商id
    * @return   $act_rule_list  可使用的供应商活动规则列表
    */
    private function get_activity($goods_id,$price,$supplier_id){
        
        //查找符合的有效期内的供应商活动
        // $act_list =M()->query("select id,act_name,goods_ids,act_type from fw_pms_activity where supplier_id=".$supplier_id." and is_del=0 and (unix_timestamp() between start_time and end_time)");
        $act_list =DB::select("select id,act_name,goods_ids,act_type from fw_pms_activity where supplier_id=? and is_del=? and (unix_timestamp() between start_time and end_time)",[$supplier_id,0]);
        $act_list = objectToArray($act_list);
        $act_rule_list = [];
        // dd($act_list);
        if($act_list){
            foreach ($act_list as $k => $v) {
                foreach ($goods_id as $g_k => $g_v) {
                    if($v['goods_ids'] == 0){//所有商品
                        if(isset($act[$v['id']]['price'])){
                            $act[$v['id']]['price'] += $price[$g_k];
                        }else{
                            $act[$v['id']]['price']= $price[$g_k];
                        }
                        $act[$v['id']]['act_type'] = $v['act_type'];
                        $act[$v['id']]['is_all_val'] = '全部商品';
                    }else{//部分商品
                        $had_goods_id = explode(',', $v['goods_ids']);
                        if(in_array($g_v, $had_goods_id)){
                            if(isset($act[$v['id']]['price'])){
                                $act[$v['id']]['price'] += $price[$g_k];
                            }else{
                                $act[$v['id']]['price']= $price[$g_k];
                            }
                            $act[$v['id']]['act_type'] = $v['act_type'];
                            $act[$v['id']]['is_all_val'] = '部分商品';
                        }
                    }
                }

            }
        // dd($act);
            if($act){
                foreach ($act as $k => $v) {
                    $act_rule = $this->get_act_rule($k,$v['price'],$v['act_type'],$v['is_all_val']);
                    if($act_rule){
                        foreach ($act_rule as $a_k => $a_v) {
                            $act_rule_list[] = $a_v;
                        }
                    }
                }
            }
            
        }
        // dd($act_rule_list);
        return $act_rule_list;
            
    }

    /**
    * 获得供应商活动规则列表
    * @param    $act_id             能使用的活动id
    * @param    $act_store_price    参与活动的金额
    * @param    $act_type           活动类型，1为满就减，2为满就折
    * @param    $is_all_val         全部商品/部分商品
    * @return   $act_rule_list      可使用的供应商活动规则列表
    */
    private function get_act_rule($act_id,$act_store_price,$act_type,$is_all_val){

        //金额满足能使用的规则
        if($act_type == 1 || $act_type == 2){//满减、满折
            // $act_rule_list = M('pms_activity_rule')->where('act_id='.$act_id.' and '.$act_store_price.'>=full_money');
            DB::enableQueryLog();
            $act_rule_list = DB::table('pms_activity_rule')->where([['act_id',$act_id],['full_money','<=',$act_store_price]])->get();
            // dd(getLastSql());
            $act_rule_list = objectToArray($act_rule_list);
            // dd($act_rule_list);
        }
        if($act_type == 3){//购物赠券,一个活动对应一个规则

            //规则：达到满足金额。优惠券：1.有效 2.有效期内 3.本门店领取张数合计未超过限制次数 4.赠送张数小于发行总张数
            // $act_rule_list =M()->query("select par.*,pc.discount_money as coupon_price,pc.id as coupon_id,pc.limit_num from fw_pms_activity_rule as par left join fw_pms_coupon as pc on par.discount_money=pc.id where par.act_id=".$act_id." and ".$act_store_price.">=par.full_money and pc.is_del=0 and (unix_timestamp() between pc.start_time and pc.end_time) and pc.give_num<pc.total_num"); 
            $act_rule_list = DB::select("select par.*,pc.discount_money as coupon_price,pc.id as coupon_id,pc.limit_num from fw_pms_activity_rule as par left join fw_pms_coupon as pc on par.discount_money=pc.id where par.act_id=? and ?>=par.full_money and pc.is_del=0 and (unix_timestamp() between pc.start_time and pc.end_time) and pc.give_num<pc.total_num",[$act_id,$act_store_price]); 
            $act_rule_list = objectToArray($act_rule_list);

            if($act_rule_list[0]['coupon_id']){
                //判断门店之前购物赠券(type=1)次数是否已超
                // $coupon_list =M('pms_location_coupon')->where('coupon_id='.$act_rule_list[0]['coupon_id'].' and location_id='.$this->get_location_ids().' and type=1');
                $coupon_list = DB::table('pms_location_coupon')->where([['coupon_id',$act_rule_list[0]['coupon_id']],['location_id',$this->get_location_ids()],['type',1]])->get();
                $coupon_list = objectToArray($coupon_list);
                if($coupon_list){
                    $location_coupon_num = 0;
                    foreach ($coupon_list as $k => $v) {
                        $location_coupon_num += $v['give_num'];
                    }
                    //如果门店领取的优惠券数量大于或等于限制次数，则清空
                    if($location_coupon_num >= $act_rule_list[0]['limit_num']){
                        $act_rule_list = '';
                    }

                }

            }

        }

        if(is_array($act_rule_list)&&$act_rule_list!=null){
            foreach ($act_rule_list as $k => $v) {
                $act_rule_list[$k]['act_type'] = $act_type;
                $act_rule_list[$k]['act_store_price'] = $act_store_price;
                if($act_type == 1)
                    $act_rule_list[$k]['act_name'] = $is_all_val.'满'.round($v['full_money'],2).'减'.round($v['discount_money'],2);
                if($act_type == 2)
                    $act_rule_list[$k]['act_name'] = $is_all_val.'满'.round($v['full_money'],2).'打'.round($v['discount_money'],2).'折';
                if($act_type == 3){
                    $act_rule_list[$k]['act_name'] = $is_all_val.'满'.round($v['full_money'],2).'送'.round($v['coupon_price'],2).'元优惠券';
                }
                    
            }
        }
        return $act_rule_list;
    }

    /**
    * 提交订单时判断 1.是否能使用活动规则 2.是否能使用优惠券
    * @param    $act_rule_id 活动规则id列表 逗号隔开 一个供应商一个规则
    * @param    $coupon_ids  门店优惠券列表 逗号隔开 一个供应商一张优惠券
    * @param    $datas       二维数组，键值是供应商id。包含goods_id数组和对应的price小计数组
    * @return   $new_datas   返回二维数组，键值是供应商id。包含原始总金额、总金额、优惠总金额、参与活动金额、活动id、门店优惠券id
    */
    private function use_act_coupon($act_rule_id,$coupon_ids,$datas){
        $act_list = [];
        $act = [];
        // 1.参与活动
        if($act_rule_id){
            //有效期内、有效的活动规则
            // $act_list =M()->query("select par.act_id,par.full_money,par.discount_money,pa.start_time,pa.end_time,pa.supplier_id,pa.goods_ids,pa.act_type from fw_pms_activity_rule as par left join fw_pms_activity as pa on pa.id=par.act_id where pa.is_del=0 and par.id in (".$act_rule_id.") and (unix_timestamp() between pa.start_time and pa.end_time)");
            // $act_list = DB::select("select par.act_id,par.full_money,par.discount_money,pa.start_time,pa.end_time,pa.supplier_id,pa.goods_ids,pa.act_type from fw_pms_activity_rule as par left join fw_pms_activity as pa on pa.id=par.act_id where pa.is_del=0 and par.id in (".$act_rule_id.") and (unix_timestamp() between pa.start_time and pa.end_time)");
            $act_list = DB::table('pms_activity_rule as par')
            ->whereIn('par.id',explode(',',$act_rule_id))
            ->where('pa.is_del',0)
            ->whereBetween('unix_timestamp()',array('pa.start_time','pa.end_time'))
            ->leftJoin('pms_activity as pa','pa.id','=','par.act_id')
            ->get();
            $act_list = objectToArray($act_list);
        }

        if($act_list){
            foreach ($act_list as $k => $v) {
                $act[$v['supplier_id']] = $v;
            }
        }
        // dd($act_list);
        foreach ($datas as $k => $v) {

            //初始化 活动金额、活动id、门店优惠券id、优惠金额 
            $new_datas[$k]['act_price'] = 0;
            $new_datas[$k]['act_id'] = 0;
            $new_datas[$k]['location_coupon_id'] = 0;
            $new_datas[$k]['discount_price'] = 0;
            $new_datas[$k]['total_original_price'] = 0;
            $new_datas[$k]['total_price'] = 0;

            foreach ($v['goods_id'] as $d_k => $d_v) {

                //原始总金额
                $new_datas[$k]['total_original_price'] += $v['price'][$d_k];
                //总金额(未扣除优惠)
                $new_datas[$k]['total_price'] += $v['price'][$d_k];

                if(!empty($act[$k])){//如果存在该供应商活动

                    if($act[$k]['goods_ids'] == 0){//全部商品
                        $new_datas[$k]['act_price'] += $v['price'][$d_k];
                    }else{//部分商品
                        $had_goods_id = explode(',', $act[$k]['goods_ids']);
                        if(in_array($d_v, $had_goods_id)){
                            $new_datas[$k]['act_price'] += $v['price'][$d_k];
                        }
                    }

                }

            }

        }
        // dd($new_datas);
        foreach ($datas as $k => $v) {
            if(!empty($act)){
                //判断是否能参与活动，获得优惠或折扣
                if($new_datas[$k]['act_price'] >= $act[$k]['full_money'] && $new_datas[$k]['act_price']>0 && $act[$k]['full_money']>0){

                    $new_datas[$k]['act_id'] = $act[$k]['act_id'];

                    if($act[$k]['act_type'] == 1 && $act[$k]['discount_money']>0 && $act[$k]['discount_money']<$act[$k]['full_money']){//1为满就减
                        $new_datas[$k]['discount_price'] = $act[$k]['discount_money'];//优惠金额
                        $new_datas[$k]['total_price'] -= $act[$k]['discount_money'];//总金额减去优惠金额
                    }
                    if($act[$k]['act_type'] == 2 && $act[$k]['discount_money']>0 && $act[$k]['discount_money']<10){//2为满就折
                        $new_datas[$k]['discount_price'] = $new_datas[$k]['act_price']*((10-$act[$k]['discount_money'])/10);//折扣金额
                        $new_datas[$k]['total_price'] -= $new_datas[$k]['act_price']*((10-$act[$k]['discount_money'])/10);//总金额减去折扣金额
                    }

                }
            }
        }

        //2.使用优惠券
        $coupon_list = [];
        if($coupon_ids){
            // $coupon_list = M('pms_location_coupon')->where("location_id=".intval($this->get_location_ids())." and num>0 and (unix_timestamp() between start_time and end_time) and id in (".$coupon_ids.")")->select();
            $coupon_list = DB::table('pms_location_coupon')
            ->where([["location_id",intval($this->get_location_ids())],["num",'>',0]])
            ->whereIn('id',explode(',',$coupon_ids))
            ->whereBetween('unix_timestamp()',['start_time','end_time'])
            ->get();

            $coupon_list = objectToArray($coupon_list);
        }

        $coupon = [];

        if($coupon_list){
            foreach ($coupon_list as $k => $v) {
                $coupon[$v['supplier_id']] = $v;
            }
        }

        foreach ($datas as $k => $v) {
            $new_datas[$k]['coupon_act_price'] = 0;
            foreach ($v['goods_id'] as $d_k => $d_v) {

                if(!empty($coupon[$k])){//如果存在优惠券

                    if($coupon[$k]['goods_ids'] == 0){//全部商品
                        $new_datas[$k]['coupon_act_price'] += $v['price'][$d_k];
                    }else{//部分商品
                        $coupon_goods_id = explode(',', $coupon[$k]['goods_ids']);
                        if(in_array($d_v, $coupon_goods_id)){
                            $new_datas[$k]['coupon_act_price'] += $v['price'][$d_k];
                        }
                    }

                }

            }

        }

        $new_location_coupon_id= [];

        foreach ($datas as $k => $v) {
            if(!empty($coupon)){
                // 判断是否能使用优惠券
                if($new_datas[$k]['coupon_act_price'] >= $coupon[$k]['full_money'] && $new_datas[$k]['coupon_act_price']>0 && $coupon[$k]['full_money']>0){

                    $new_datas[$k]['location_coupon_id'] = $coupon[$k]['id'];

                    if($coupon[$k]['discount_money']>0 && $coupon[$k]['discount_money']<$coupon[$k]['full_money']){
                        $new_datas[$k]['discount_price'] += $coupon[$k]['discount_money']; //(活动优惠、折扣金额)+优惠券金额
                        $new_datas[$k]['total_price'] -= $coupon[$k]['discount_money'];//总金额减去优惠券金额(已减活动金额) 
                    }
                    if($coupon[$k]['id']){
                        $new_location_coupon_id[] = $coupon[$k]['id'];//用于减优惠券数量
                    }

                }
            }
        }

        //减优惠券数量
        if($new_location_coupon_id){
            // M('pms_location_coupon')->where("id in(".implode(',', $new_location_coupon_id).")")->setDec('num');         
            DB::table('pms_location_coupon')->whereIn("id",implode(',', $new_location_coupon_id))->decrement('num');         
        }
        
        return $new_datas;

    }

    //判断商品库存
    public function goods_stock_info($goods_id,$goods_num){

        $location_id = $this->get_location_ids();

        //商品库存
        $goods_stock = DB::table('pms_goods')->where('id',$goods_id)->select('stock')->first();     
        $goods_stock = $goods_stock->stock;     
        // $goods_stock = intval(M('pms_goods')->where('id='.$goods_id)->getField('stock'));     
        
        //购物车已添加商品库存
        // $cart_stock = intval(M('pms_erp_cart')->where('goods_id='.$goods_id.' and location_id='.intval($location_id))->sum('number'));
        $cart_stock = DB::table('pms_erp_cart')->where([['goods_id',$goods_id],['location_id',intval($location_id)]])->sum('number');
    
        //库存不足返回负数
        $stock = $goods_stock-$goods_num-$cart_stock;

        return $stock;      
    }

    //获取门店购物车中的商品数量
    public function get_location_cart_info(){
        $location_id = $this->get_location_ids();     
        // $info=M()->query("select sum(number) as number,sum(price*number) as total_price from fw_pms_erp_cart where location_id=".$location_id." limit 1");
        $info = DB::select("select sum(number) as number,sum(price*number) as total_price from fw_pms_erp_cart where location_id=? limit 1",[$location_id]);
        // dd($info);
        // $info = get_object_vars($info);
        return $info[0];
    }

    //返回当前登录门店id
    public function get_location_ids(){
        $account_info = session('account_info');
        return $account_info['location_ids'][0];
    }

}
