<?php

//将对象转换成数组
if (!function_exists('objectToArray')) {
    function objectToArray($object) {
        //先编码成json字符串，再解码成数组
        return json_decode(json_encode($object), true);
    }
}

?>