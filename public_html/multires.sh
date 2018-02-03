#!/bin/bash
cd `dirname $0`/cache

MD5=$2
FILE=$3

# fetch the full res file
TMP=${MD5}.jpg
MD51=`echo $MD5 | cut -c1`
MD52=`echo $MD5 | cut -c1-2`
wget -O "$TMP" "https://upload.wikimedia.org/wikipedia/commons/${MD51}/${MD52}/${FILE}"


# generate multiresolution pyramid
MULTI=${DIR}${MD5}
/usr/bin/vips im_vips2tiff "$TMP" "$TIFF":jpeg:75,tile:256x256,pyramid
