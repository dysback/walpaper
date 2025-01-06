<?php

$url= 'https://unsplash.com/wallpapers';


$r2 = shell_exec("curl $url");
$links = getLinks($r2);
$picture_link = $links[rand(0, count($links) - 1)];
echo $picture_link;
$rand = rand(0, 10000);
$picture_file = "/home/dzelenika/dev/walpaper/slika{$rand}.jpg";
$fortune_file = "/home/dzelenika/dev/walpaper/slika{$rand}f.jpg";
$walpaper_file = "/home/dzelenika/dev/walpaper/slika{$rand}w.jpg";

//echo $picture_file;
//shell_exec("/home/dzelenika/dev/walpaper/addtext.sh \"$picture_file\" \"$picture_link\"");
/*
shell_exec("wget $picture_link -O {$picture_file}");
$convert = "fortune | xargs -I{} convert -pointsize 60 -fill red -draw 'text 470,660 \"{}\" ' {$picture_file} {$picture_file}";
echo $convert;
shell_exec($convert);
shell_exec("gsettings set org.gnome.desktop.background picture-uri-dark file://{$picture_file}");
*/

shell_exec("wget $picture_link -O {$picture_file}");

$W = trim(shell_exec("echo $(xrandr --current | grep '*' | uniq | awk '{print $1}' | cut -d 'x' -f1)"));
$H = trim(shell_exec("echo $(xrandr --current | grep '*' | uniq | awk '{print $1}' | cut -d 'x' -f2)"));

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
} else {
    $k = $H / $iH;
    $top  = 0.3 * $H;
    $left = ($iW * $k - $W) / 2 + 0.4 * $W;
}
$top = (int)$top;
$left = (int)$left;

$k *= 100;
$convert = "convert '$picture_file' -resize $k% '$picture_file'";
echo "\n$convert";
shell_exec($convert);
echo "\nPicture reaized (HxW): " . (int)($iH * $k / 100) . " x " . (int)($iW * $k / 100) . "\n";
echo "\nk / Top / Left: $k: $top + $left";


$fortune = trim(shell_exec("fortune"));

$font_size = fontSize($fortune);
$convert = "convert -size 1150x -pointsize {$font_size} -background '#c003' -fill '#fffc'  caption:\"{$fortune}\"  -bordercolor '#7f0' -border 25 '{$fortune_file}'";
echo "\n$convert\n";
shell_exec($convert);

$convert = "convert '{$picture_file}' '{$fortune_file}' -geometry +{$left}+{$top} -composite '{$walpaper_file}'";
echo "\n$convert\n";
shell_exec($convert);

shell_exec("gsettings set org.gnome.desktop.background picture-uri-dark file://{$walpaper_file}");
shell_exec("gsettings set org.gnome.desktop.background picture-options 'centered'");

//$slika = new Imagick($picture_file);
//$draw = new ImagickDraw();
//$draw->annotation(20, 50, "Hello World!");
//$slika->drawImage($draw);
//file_put_contents($picture_file . "mm", $slika);
//$convert = "convert '$picture_file'  -size 960x400!  -background '#fc03' -fill '#fffc' -bordercolor '#ff0000aa' -border 25 caption:\"{$fortune}\" -geometry +{$left}+{$top} -composite '{$picture_file}'";

function getLinks(string $html) : array {
    $pos = 0;
    static $open = 'title="Download this image" href="';
    static $close = '">Download</a>';
    static $open_len = strlen($open);

    $links = [];
    while($pos = stripos($html, $open, $pos +1)) {
        echo "::$pos::";
        $end = stripos($html, $close, $pos);
        $links[] = substr($html, $pos + $open_len, $end - $pos - $open_len );
    }
    return $links;
}
/*
function getSslPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    print_r(curl_getinfo($ch));
    curl_close($ch);
    return $result;
}
*/

function fontSize($text) {
    $len = strlen($text);
    if($len < 30) {
        return 72;
    } elseif($len < 120) {
        return 60;
    }
    elseif($len < 400) {
        return 48;
    } elseif($len < 600) {
        return 40;
    }
    return 32;
}
