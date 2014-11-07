

function is_completed_pic($file_path) {
    $file_handle = fopen($file_path, 'r');
    
    $file_head = fread($file_handle, 8);
    
    if (substr($file_head, 0, 2) == chr(0xFF) . chr(0xD8)) {
        // jpeg
        $status = is_completed_jpeg($file_handle);
    } elseif (substr($file_head, 0, 2) == chr(0x42) . chr(0x4D)) {
        // bmp
        $status = is_completed_bmp(filesize($file_path), substr($file_head, 2, 4)); 
    } elseif (substr($file_head, 0, 3) == chr(0x47) . chr(0x49) . chr(0x46)) {
        // gif
        $status = is_completed_gif($file_handle);
    } elseif ($file_head == chr(0x89) . chr(0x50) . chr(0x4E) . chr(0x47) . chr(0x0D) . chr(0x0A) . chr(0x1A) . chr(0x0A)) {
        // png
        $status = is_completed_png($file_handle);
    } else {
        $status = false;
    }       
    
    fclose($file_handle);
    
    return $status;
}

// jpeg gif png文件有结束符
function is_completed_jpeg($file_handle) {
    return check_end_sign($file_handle, chr(0xFF) . chr(0xD9));
}

function is_completed_gif($file_handle) {
    return check_end_sign($file_handle, chr(0x3B));
}

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

// bmp头部有字段标示文件的size
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


