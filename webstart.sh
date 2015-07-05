#!/bin/bash

prefix=$(/bin/cat /etc/wmflabs-project)
tool=$(/usr/bin/id -nu|sed -e "s/^${prefix}.//")
toolges=${prefix}.${tool}


webservice stop
qdel lighty-${tool}
qdel lighty-xdebug-${tool}
sleep 3
ssh tools-webgrid-01 "pkill -9 -U $toolges php-cgi"
ssh tools-webgrid-02 "pkill -9 -U  $toolges php-cgi"
ssh tools-webgrid-tomcat "pkill -9 -U $toolges php-cgi"
sleep 3

jsub -N lighty-${tool} -quiet -once -mem 7G -q webgrid-lighttpd ${HOME}/lighty-starter.sh ${tool} ${conf} >/dev/null
