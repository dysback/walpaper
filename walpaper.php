<?php

require_once "init.php";

$home = dirname(__FILE__);
$minute = date("i");

$page = rand(0, 100);
$item = rand(0, 9);

$picture_file = "{$home}/slika.jpg";
$fortune_file = "{$home}/slika_f.jpg";
$walpaper_file = "{$home}/slika_w.jpg";
$time_file = "{$home}/slika_t" . rand(0,10000) . ".jpg";


if($minute % CHANGE_MIN == 0) {
    $r2 = json_decode(shell_exec("curl " . AJAX_URL ."?page={$page}&per_page=10"));
    $picture_link = $r2->photos[$item]->links->download;
    //$links = getLinks($r2);
    //$picture_link = $links[rand(0, count($links) - 1)];

    echo $picture_link;
    echo $picture_file;
    shell_exec("wget $picture_link -O {$picture_file}");
}

$W = (int)trim(shell_exec("echo $(xrandr --current | grep '*' | uniq | awk '{print $1}' | cut -d 'x' -f1)"));
$H = (int)trim(shell_exec("echo $(xrandr --current | grep '*' | uniq | awk '{print $1}' | cut -d 'x' -f2)"));
$size = shell_exec("identify -ping -format '%w %h' '$picture_file'");
[$iW, $iH] = explode(' ', $size);
echo "\nScreen(HxW): $H x $W";
echo "\nPicture(HxW): $iH x $iW";

$hpw = $H / $W;
$ihpw = $iH / $iW;
if($ihpw > $hpw) {
    $k = $W / $iW;
    $top = ($iH * $k - $H) / 2 + 0.3 * $H;
    $left = 0.4 * $W;

    $topH = ($iH * $k - $H) / 2 + 0.8 * $H;
    $leftH = 0.8 * $W;

} else {
    $k = $H / $iH;
    $top  = 0.3 * $H;
    $left = ($iW * $k - $W) / 2 + 0.4 * $W;

    $topH  = 0.8 * $H;
    $leftH = ($iW * $k - $W) / 2 + 0.8 * $W;
}
$top = (int)$top;
$left = (int)$left;

$topB = $top + BORDER + SHADOW_OFFSET;
$leftB = $left + BORDER  + SHADOW_OFFSET;

$topH = (int)$topH;
$leftH = (int)$leftH;
$topHB = (int)$topH + SHADOW_OFFSET;
$leftHB = (int)$leftH + SHADOW_OFFSET;
$k *= 100;

if($minute % CHANGE_MIN == 0) {
    $convert = "convert '$picture_file' -resize $k% '$picture_file'";
    echo "\n$convert";
    shell_exec($convert);

    echo "\nPicture resized (HxW): " . (int)($iH * $k / 100) . " x " . (int)($iW * $k / 100) . "\n";
    echo "\nk / Top / Left: $k: $top + $left";

    $fortune = shell_exec("fortune");
    echo "\nFortune: {$fortune}";
    $fortune = str_replace('"', "'", trim($fortune));
    $font_size = fontSize($fortune);

    $convert = "convert -size 1100x -pointsize {$font_size} -background '" . BACKGROUND_COLOR . "' -fill '" . TEXT_COLOR . "'  caption:\"{$fortune}\"  -bordercolor '" . BACKGROUND_COLOR . "' -border " . BORDER . " '{$fortune_file}'";
    echo "\n$convert\n";
    shell_exec($convert);

    $convert = "composite  -dissolve " . FORTUNE_DISOLVE . " '{$fortune_file}' '{$picture_file}' -geometry +{$left}+{$top} '{$walpaper_file}'";
    echo "\n$convert\n";
    shell_exec($convert);

    $convert = "convert '{$walpaper_file}' -size 1100x -background '#fc00' -fill '" . TEXT_COLOR . "' -pointsize {$font_size} caption:\"{$fortune}\" -geometry +{$leftB}+{$topB} -composite '{$walpaper_file}'";
    echo "\n$convert\n";
    shell_exec($convert);
}

//$date = date("l jS \of F Y H:i");
$date = date("H:i");
$convert = "convert '{$walpaper_file}' -size 1100x -background '#fc00' -fill '#0007' -pointsize " . WATCH_FONT_SIZE . " caption:\"{$date}\" -geometry +{$leftHB}+{$topHB} -composite '{$time_file}'";
echo "\n$convert\n";
shell_exec($convert);

$convert = "convert '{$time_file}' -size 1100x -background '#fc00' -fill '" . WATCH_FONT_COLOR . "' -pointsize " . WATCH_FONT_SIZE . " caption:\"{$date}\" -geometry +{$leftH}+{$topH} -composite '{$time_file}'";
echo "\n$convert\n";
shell_exec($convert);

$gsettings = "gsettings set org.gnome.desktop.background picture-uri-dark file://{$time_file}";
echo "\n{$gsettings}\n";
shell_exec($gsettings);
unlink(file_get_contents("{$home}/2remove"));
file_put_contents("{$home}/2remove", $time_file);
//shell_exec("gsettings set org.gnome.desktop.background picture-options 'none'");
shell_exec("gsettings set org.gnome.desktop.background picture-options 'centered'");

function fontSize($text) {
    $len = strlen($text);
    if($len < 30) {
        return FONT_XL;
    } elseif($len < 120) {
        return FONT_XL;
    }
    elseif($len < 300) {
        return FONT_M;
    } elseif($len < 500) {
        return FONT_S;
    }
    return FONT_XS;
}

//echo $minute . " " . $minute % $change_min;