#!/bin/sh

LOG_COPY=/home/pi/log/copy_log.log

files='/var/www/html/tmp/s_img/*'
# cpy_dir='/media/pi/usb/img'
cpy_dir='/home/pi'

date=$(date '+%Y-%m-%d_%T')
echo -e "\nCopied datetime -> ${date}" >>$LOG_COPY

index=1
for f_path in $files; do
    cp -a -f -p $f_path $cpy_dir

    f_name=$(basename $f_path)
    f_log=$(printf "%02d:$f_name" ${index})
    echo $f_log >>$LOG_COPY
    index=$((++index))
done
