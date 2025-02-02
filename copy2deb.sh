#!/usr/bin/env bash

if [[ $1 == "" ]] ; then
    echo "Wallpaper version is required"
    echo "Usage: ./copy2deb <version>"
    exit 1
fi

cp *.php deb/wallpaper_$1/usr/local/bin/wallpaper/
cp wallpaper.sh deb/wallpaper_$1/usr/local/bin/wallpaper/
cp README.md deb/wallpaper_$1/usr/local/bin/wallpaper/
cp slika_f.jpg deb/wallpaper_$1/usr/local/bin/wallpaper/
cp slika_w.jpg deb/wallpaper_$1/usr/local/bin/wallpaper/
cp slika.jpg deb/wallpaper_$1/usr/local/bin/wallpaper/
cp murphy.csv deb/wallpaper_$1/usr/local/bin/wallpaper/

