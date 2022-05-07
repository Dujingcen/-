<?php



list($str,$width,$height) = [$_GET['s'],$_GET['w'],$_GET['h']];
$font_size = 80;
$small_font=realpath('./pf_small.ttf');



$text_background = imagecreatetruecolor($width, $height);
imagesavealpha($text_background, true);
$font_bg = imagecolorallocatealpha($text_background, 255, 100, 255, 127);
imagefill($text_background, 0, 0, $font_bg);

$c = imagecreatetruecolor($width, $height);
$font_color_number  = imagecolorallocate($c, 0, 0, 0);

#获取文字各个点的坐标
$size = imagettfbbox($font_size, 0, $small_font, $str);
#计算当前文字的高度和宽度
$str_height = abs($size[3] - $size[5]);
$str_width =  $size[2] - $size[0];

//var_dump($size);die();
#计算文字的圆的直径 （文字对角线长度）

$r =getDiagonalByCoordinate($size);

$red =  imagecolorallocate($text_background,133,133,133);
imageellipse($text_background,$width/2,$height/2,$r,$r,$red);

#文字居中计算 xy

$x = ($width-$str_width)/2;
$y = ($height-$str_height)/2 + $str_height; //因为坐标计算是从文字的左下角开始

$font_zone_coordinate = imagettftext($text_background,$font_size,1, $x, $y, $font_color_number, $small_font, $str);

list($pic_x,$pic_y) = getRandCoordinate($width,$height,$font_zone_coordinate,20,40);
//imagepng($text_background, "./". date('YmdHis').".png"); #保存
# 5. 输出到浏览器
header('Content-Type: image/png');//必须声明
imagepng($text_background);//以png格式输出到浏览器

# 6. 释放图像(销毁)
imagedestroy($text_background);//释放内存

/**
 * 获取该区域的对角线长度
 * @param array $size
 * @return int
 */
function getDiagonalByCoordinate(array $size):int{

    $x = $size[2]-$size[6];
    $y = $size[3]-$size[7];

    return pow(pow($x,2)+pow($y,2),1/2);
}

/**
 * 将元素图片 随机放到 文字图片上 并返回起点坐标
 * @param int $w  文字图的宽度
 * @param int $h  文字图的高度
 * @param array $remove_zone 不能在该区域选择坐标
 * @param int $ele_width  元素图的宽度
 * @param int $ele_height  元素图的高度
 * @return array
 */
function getRandCoordinate(int $w,int $h ,array $remove_zone,int $ele_width=20,int $ele_height=40):array{
    /**
     * -----------------------------------------------$w---------------------------------------------
     * |
     * |      左上角 ($remove_zone[6],$remove_zone[7])=======右上角 ($remove_zone[4],$remove_zone[5])
     * $h                       ||                      文字                      ||
     * |      左下角 ($remove_zone[0],$remove_zone[1])=======右下角 ($remove_zone[2],$remove_zone[3])
     * |
     * |
     */

    #tep1 随机获取x的位置
    #tep2 根据x 位置 判断 y的取值范围
    #1） x 在文字区域 或则 x+ele_w 在文字区域 则表示元素图的x坐标会与 文字重叠
    # 则y坐标的取值范围为(0,$remove_zone[7]-$ele_height>0 ?($remove_zone[7]-$ele_height):0) 或者 (size[1],$h-$ele_height)
    # 确保 元素图不会超出背景图的范围
    #2）否则不与文字重叠  y 的取值范围就可以随意（0,$h-$ele_height）
    return [rand(0,$w),rand(0,$h)];
}