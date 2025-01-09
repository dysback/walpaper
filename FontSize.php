<?php

$r = getScreenResolution();
define('BASE_FONT_SIZE', round($r[1] / BASE_FONT_SIZE_COEF));

define('FONT_XS', (int)round(BASE_FONT_SIZE * 1));
define('FONT_S', (int)round(BASE_FONT_SIZE * 1.25));
define('FONT_M', (int)round(BASE_FONT_SIZE * 1.5));
define('FONT_L', (int)round(BASE_FONT_SIZE * 1.875));
define('FONT_XL', (int)round(BASE_FONT_SIZE * 2.25));


enum FontSize : int {
    case XS = FONT_XS;
    case S = FONT_S;
    case M = FONT_M;
    case L = FONT_L;
    case XL = FONT_XL;
}


