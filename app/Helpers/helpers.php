<?php

if(!function_exists('oss_url')) {
    /**
     * 定义一个全局辅助函数用于拼接oss的图片地址
     * @param $key
     * @return string
     */
    function oss_url($key)
    {
        return config('filesystems')['disks']['oss']['bucket_url'] . $key;
    }
}
