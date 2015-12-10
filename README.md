# ZoomViewer
An interactive zooming image viewer for Wikimedia Commons.

## Build IIP
Download iip from https://github.com/ruven/iipsrv and build using

```
./autogen.sh
./configure
make -j 8
```

Copy `src/iipsrv.fcgi` into the `cgi_bin` directory in the zoomviewer home folder.
