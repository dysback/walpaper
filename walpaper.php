<?php

require_once "init.php";

$home = __DIR__;
$minute = date("i");

$fortune_file = "{$home}/slika_f.jpg";
$walpaper_file = "{$home}/slika_w.jpg";
$time_file = "{$home}/slika_t" . rand(0,10000) . ".jpg";

if($minute % CHANGE_MIN == 0) {
    $picture_file = fetcPicture();
}

[$H, $W] = getScreenResolution();
[$iW, $iH] = getPictureDimensions($picture_file);
echo "\nScreen(HxW): $H x $W";
echo "\nPicture(HxW): $iH x $iW";

$hpw = $H / $W;
$ihpw = $iH / $iW;
if($ihpw > $hpw) {
    $k = $W / $iW;
    $top = ($iH * $k - $H) / 2 + 0.3 * $H;
    $left = 0.4 * $W;

    $topH = ($iH * $k - $H) / 2 + 0.8 * $H;
    $leftH = 0.05 * $W;

} else {
    $k = $H / $iH;
    $top  = 0.3 * $H;
    $left = ($iW * $k - $W) / 2 + 0.4 * $W;

    $topH  = 0.8 * $H;
    $leftH = ($iW * $k - $W) / 2 + 0.05 * $W;
}
$top = (int)$top + BORDER;
$left = (int)$left + BORDER;

$topB = $top - BORDER + SHADOW_OFFSET;
$leftB = $left - BORDER + SHADOW_OFFSET;

$topH = (int)$topH;
$leftH = (int)$leftH;
$topHB = (int)$topH + SHADOW_OFFSET;
$leftHB = (int)$leftH - SHADOW_OFFSET;
$k *= 100;

$fortune_width = (int)(0.58 * $W) - 1 * BORDER; 

if($minute % CHANGE_MIN == 0) {
    $convert = "convert '$picture_file' -resize $k% '$picture_file'";
    imagick("'$picture_file' -resize $k% '$picture_file'");

    echo "\nPicture resized (HxW): " . (int)($iH * $k / 100) . " x " . (int)($iW * $k / 100) . "\n";
    echo "\nk / Top / Left: $k: $top + $left";

    if(DISPLAY_FORTUNE) {
        $fortune = shell_exec("fortune");
        echo "\nFortune: {$fortune}";
        $fortune = str_replace('"', "'", trim($fortune));
        $font_size = fontSize($fortune);
    
        imagick("-size {$fortune_width}x -pointsize {$font_size} -background '" . BACKGROUND_COLOR . "' -fill '" . TEXT_COLOR . "'  caption:\"{$fortune}\"  -bordercolor '" . BACKGROUND_COLOR . "' -border " . BORDER . " '{$fortune_file}'");

        $convert = "composite  -dissolve " . FORTUNE_DISOLVE . " '{$fortune_file}' '{$picture_file}' -geometry +{$leftB}+{$topB} '{$walpaper_file}'";
        echo "\n$convert\n";
        shell_exec($convert);
    
        imagick("'{$walpaper_file}' -size {$fortune_width}x -background '#fc00' -fill '" . TEXT_COLOR . "' -pointsize {$font_size} caption:\"{$fortune}\" -geometry +{$left}+{$top} -composite '{$walpaper_file}'");
    } else {
        copy($picture_file, $walpaper_file);
    }
}

if(DISPLAY_WATCH) {
    $date = date(WATCH_FORMAT);
    imagick("-gravity NorthEast -pointsize " . WATCH_FONT_SIZE . " -fill '#0007' -annotate +{$leftHB}+{$topHB} '{$date}' '{$walpaper_file}' '{$time_file}'");
    imagick("-gravity NorthEast -pointsize " . WATCH_FONT_SIZE . " -fill '" . WATCH_FONT_COLOR . "' -annotate +{$leftH}+{$topH} '{$date}' '{$time_file}' '{$time_file}'");
}

gsettingsSet(GnomeSchema::DesktopBackground,  GnomeKey::PictureUri, "file://{$time_file}");
gsettingsSet(GnomeSchema::DesktopBackground,  GnomeKey::PictureUriDark, "file://{$time_file}");


unlink(file_get_contents("{$home}/2remove"));

file_put_contents("{$home}/2remove", $time_file);
//gsettingsSet(GnomeSchema::DesktopBackground,  GnomeKey::PictureOptions, PictureOption::None);
gsettingsSet(GnomeSchema::DesktopBackground,  GnomeKey::PictureOptions, PictureOption::Centered);

function fontSize(string $text) : int {
    $len = strlen($text);
    if($len < 30) {
        return FontSize::XL->value;
    } elseif($len < 120) {
        return FontSize::L->value;
    }
    elseif($len < 300) {
        return FontSize::M->value;
    } elseif($len < 500) {
        return FontSize::S->value;
    }
    return FontSize::XS->value;
}


function gsettingsSet(GnomeSchema $schema, GnomeKey $key, mixed $value) : string {
    if($value instanceof \BackedEnum) {
        $value = $value->value;
    }
    $gsettings = "gsettings set {$schema->value} {$key->value} {$value}";
    echo "\nGNOME: {$gsettings}";
    $response = shell_exec($gsettings);
    return $response ?? '';
}

function imagick($command) : string {
    $convert = "convert $command"; 
    echo "\n$convert";
    $response = shell_exec($convert);
    return $response ?? '';
}

function getScreenResolution() : array {
    $W = (int)trim(shell_exec("echo $(xrandr --current | grep '*' | uniq | awk '{print $1}' | cut -d 'x' -f1)"));
    $H = (int)trim(shell_exec("echo $(xrandr --current | grep '*' | uniq | awk '{print $1}' | cut -d 'x' -f2)"));
    return [$H, $W];
}

function getPictureDimensions(string $picture_file) : array {
    $size = shell_exec("identify -ping -format '%w %h' '$picture_file'");
    return explode(' ', $size);
}

function fetcPicture($picture_file = HOME . "/slika.jpg") {
    $page = rand(0, 100);
    $item = rand(0, 9);

    $r2 = json_decode(shell_exec("curl " . AJAX_URL ."?page={$page}&per_page=10"));
    $picture_link = $r2->photos[$item]->links->download;
    //$links = getLinks($r2);
    //$picture_link = $links[rand(0, count($links) - 1)];

    echo $picture_link;
    echo $picture_file;
    shell_exec("wget $picture_link -O {$picture_file}");
    return $picture_file;

}
echo $minute . " " . $minute % CHANGE_MIN;