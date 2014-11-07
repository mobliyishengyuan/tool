<?php

function is_completed_pic($file_path) {
    $file_handle = fopen($file_path, 'r');
    
    $file_head = fread($file_handle, 8);
    
    if (substr($file_head, 0, 2) == chr(0xFF) . chr(0xD8)) {
        // jpeg
        // 前两个字节为0x FF D8。第三四自己为0xFF E0，第七到第十一个字节为0x 4A 46 49 46 00。即JFIF
        // http://wenku.baidu.com/view/bb08bad376eeaeaad1f330f0.html
        $status = is_completed_jpeg($file_handle);
    } elseif (substr($file_head, 0, 2) == chr(0x42) . chr(0x4D)) {
        // bmp
        // windows下bmp的前两个字节为0x424D，对应的值为”BM”。
        // http://wenku.baidu.com/link?url=3hLZYGNKBBNDUWatjIDaHpyZo-LMNbR3A8LNzKw-kIbkxeY09G8eoDMwuG8o4RTz2gOqmhIg2ExhPyUOmh8iahkM7HLFBTn5g7CIYRqzXum
        $status = is_completed_bmp(filesize($file_path), substr($file_head, 2, 4)); 
    } elseif (substr($file_head, 0, 3) == chr(0x47) . chr(0x49) . chr(0x46)) {
        // gif
        // 前三个字节为0x474946，对应的值为”GIF”。
        // http://wenku.baidu.com/view/2c0feaa6f524ccbff121841d.html
        $status = is_completed_gif($file_handle);
    } elseif ($file_head == chr(0x89) . chr(0x50) . chr(0x4E) . chr(0x47) . chr(0x0D) . chr(0x0A) . chr(0x1A) . chr(0x0A)) {
        // png
        // 前8个字节为0x 89 50 4E 47 0D 0A 1A 0A。
        // http://wenku.baidu.com/view/ce54b54669eae009581bec5f.html
        $status = is_completed_png($file_handle);
    } else {
        $status = false;
    }       
    
    fclose($file_handle);
    
    return $status;
}

// jpeg gif png文件有结束符，但是在结束符后可能会有\r\n\0这些额外字符。也可能添加其他字符。
// 如现有一些攻击方式就是在图片文件后加html代码的字符。
// jpeg文件结尾标示为0xFFD9
function is_completed_jpeg($file_handle) {
    return check_end_sign($file_handle, chr(0xFF) . chr(0xD9));
}

// gif文件结束块是0x3B。
function is_completed_gif($file_handle) {
    return check_end_sign($file_handle, chr(0x3B));
}

// png结尾字符为0x 00 00 00 00 49 45 4E 44 AE 42 60 82。
function is_completed_png($file_handle) {
    return check_end_sign($file_handle
        , chr(0x00). chr(0x00) . chr(0x00) . chr(0x00) 
        . chr(0x49) . chr(0x45) .chr(0x4E) . chr(0x44)
        . chr(0xAE) . chr(0x42) . chr(0x60) . chr(0x82));
}

function check_end_sign($file_handle, $end_sign) {
    $end_sign_len = strlen($end_sign);
    $end_byte_num = 2 + $end_sign_len;

    fseek($file_handle, - $end_byte_num, SEEK_END);
    $file_end = fread($file_handle, $end_byte_num);

    if (substr($file_end, 0, $end_sign_len) == $end_sign
        || substr($file_end, 2, $end_sign_len) == $end_sign) {
        return true;
    }

    return false;
}

// bmp的第三到六字节为该文件大小，使用的是小端方式存储，如文件中内容0x36EE0200，实际对应的是0x0002EE36，也就是文件大小为192054
function is_completed_bmp($file_size, $file_size_sign) {
    $format_file_size_sign = '';

    for ($i = strlen($file_size_sign) - 1;$i >= 0; $i--) { 
        $format_file_size_sign .= sprintf("%02d", dechex(ord($file_size_sign[$i])));
    }       

    $format_file_size = hexdec($format_file_size_sign);

    if ($format_file_size > $file_size) {
        return false;
    }       

    return true;
}


