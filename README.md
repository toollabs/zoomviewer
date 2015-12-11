# ZoomViewer
An interactive zooming image viewer for Wikimedia Commons.

## Build IIP
Clone the iip repository from https://github.com/ruven/iipsrv and build using

```
./autogen.sh
./configure
make -j 8
```

Copy `src/iipsrv.fcgi` into the `cgi_bin` directory in the zoomviewer home folder.

## Examples

Load the interactive zoomviewer for the commons file _Chicago.jpg_ using

* https://tools.wmflabs.org/zoomviewer/?f=Chicago.jpg

Use the JavaScript viewer by appending the `flash=no` parameter

* https://tools.wmflabs.org/zoomviewer/?f=Chicago.jpg&flash=no

Pull the IIIF information JSON manifest for the commons file _Chicago.jpg_ using

* https://tools.wmflabs.org/zoomviewer/iiif.php?f=Chicago.jpg
