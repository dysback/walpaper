
## AWESOME :p GNOME wallpaper changer

### Description
Lorem ipsum

### What's new
Lorem ipsum
  
### @todo (for 0.5 and further versions)
- 0.5 (preffered)
  - package as gnome extension
  - improve documentation (README and install shell output)
  - set default wallpapper before firsts ctontab jobs
  - uninstall script (remove crontab, purge files, kill process ...)
- further
  - offline wallpapers
  - wallpaper cache-ing
  - additional image sources
  - additional content
    - weather and forecasts
    - joke
    - various system info (disk usage, current user, ...
    - sport match result
    - unread mail alert ...
    - system updates ...
  - gui configuration

### Installation
Lorem Ipsum
### Configuration
Lorem Ipsum

### 4 Maintainers
#### Building package:

./copy2deb.sh <package_version>

example:

./copy2deb.sh 0.4

directory wallpaper_<package_version> should exist

**Build Package**

dpkg-deb --build deb/wallpaper_<package_version>

example

dpkg-deb --build deb/wallpaper_0.4


**Install package**

sudo -E dpkg -i deb/wallpaper_<package_version>.deb 

example:

sudo -E dpkg -i deb/wallpaper_0.4.deb 



***
by **dy**