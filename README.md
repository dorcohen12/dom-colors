Live demo -
https://dcohen.net/dom-colors/

Tested on php 8.2.
Required extensions =>
  - GD
  - JSON
  - Sessions

Funtions [analyzeColorsPNG, analyzeColorsJPG] are made using chatGPT.
While it uses GD extension, i've tried to work with other functions that read the file contents and headers, while the function worked fine with BMP file extensions, with PNG/JPG it had unexpected and wrong results.
I've tried to perform a client side conversion of JPG/PNG to BMP through the client side, but the BMP file extension has different abilities than JPG/PNG which would affect the dominant colors results.
