#!/usr/bin/env bash

export DISPLAY=:0.0
export XDG_RUNTIME_DIR="/run/user/$(id -u)"
export DBUS_SESSION_BUS_ADDRESS="unix:path=${XDG_RUNTIME_DIR}/bus"

cd /usr/local/bin/wallpaper/
/usr/bin/php /usr/local/bin/wallpaper/wallpaper.php