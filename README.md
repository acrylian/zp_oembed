zp_oembed
=========

A [Zenphoto](http://www.zenphoto.org) plugin to embed content from various services via oEmbed using a content macro. 
An adaption of Felix Girault's OEmbed libary Essence: https://github.com/felixgirault/essence

Support for the following providers: 23hq, Bandcamp, Blip.tv, Cacoo, CanalPlus, Chirb.it, Clikthrough, CollegeHumour, 
Dailymotion, Deviantart, Dipity, Flickr, Funnyordie, Howcast, Huffduffer, Hulu, Ifixit, Imgur, Instagram, Mobypicture, 
Official.fm, Polldaddy, Qik, Revision3, Scribd, Shoudio, Sketchfab, Slideshare, SoundCloud, Ted, Twitter, Vhx, Viddler, 
Vimeo, Yfrog and Youtube.

**PHP 5.3+ required**

Usage:
--------
Place file and folder with your `/plugins` folder and enable the plugin

Enter the content macro within any supported content field like this and add the url of the service to use (here: Youtube):

`[EMBED http://www.youtube.com/watch?v=F3TP7LZ8a2w]`

On the theme the video will be directly be embeded.

The plugin has options to set the maxwidth/height of an embeded element. Since most use iFrames it is recommended to 
add the following rule to your theme's css:

`.zpoembed iframe { width: 100%; height: 40%; }` 

This way you make sure the iframe never exceeds the content your embeded it into and also sort of responsive. I decided not to have the plugin load this additionally.
Sadly there is no auto height for an iframe in css so that will roughly give a 16:9 space.
