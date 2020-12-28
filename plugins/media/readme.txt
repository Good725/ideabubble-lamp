
INSTALL:
1. define correct SHARED_MEDIA_LINBRARY_PATH in Model_Sharedmedia
2. give permissions to this file storage directory : chmod a+w /home/webtest/techtest/www-media/media/
3. run database script : install-media-uploader


Used external jQuery plugins:
1. jQuery plugin for html5 dragging files from desktop to browser http://www.github.com/weixiyen/jquery-filedrop
2. jQuery Image Cropping Plugin http://deepliquid.com/content/Jcrop.html


TODO list
--------------
- appropriate handling for failed uploads
- "ok" message for successful upload
- clear progressbar before next upload
- delete functionality
- install script and automatically set permissions on install?
- gallery paging
- SHARED_MEDIA_LIBRARY_PATH to some standard config
- rename files in library to form "$hash.$ext" - to prevent name collisions
- cleanup preview files
