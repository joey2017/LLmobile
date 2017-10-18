<?php

//将对象转换成数组
if (!function_exists('objectToArray')) {
    function objectToArray($object) {
        //先编码成json字符串，再解码成数组
        return json_decode(json_encode($object), true);
    }
}

if (!function_exists('lastSql')) {
    function lastSql(){
        $sql = DB::getQueryLog();
        $query = end($sql);
        return $query;
    }
}

if (!function_exists('price')) {
    /**
     * 精确价格，只保留俩位小数
     * @param  int|float $price 价格
     * @return float
     */
    function price($price)
    {
        return sprintf("%.02f", $price);
    }
}

?>