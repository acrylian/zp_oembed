zp_oembed
=========

A [Zenphoto](http://www.zenphoto.org) plugin to embed content from various services via oEmbed using a content macro. 

An adaption of [https://github.com/oscarotero/Embed]() (MIT license)

**PHP 5.5+ and cURL library required**

Usage:
--------

Put the file `zp_oembed.php` and the folder of the same name into your `/plugins` folder and enable it.

Enter the content macro within any supported content field like this and add the url of the service to use (here: Youtube):

`[EMBED http://www.youtube.com/watch?v=F3TP7LZ8a2w]`

On the theme the video will be directly be embeded. For non oembed providing sites a base HTML consisting of the page title, description and URL will be created.

Since most use iFrames it is recommended to add the following rule to your theme's css:

`.zpoembed iframe { width: 100%; height: 40%; }` 

This way you make sure the iframe never exceeds the content your embeded it into and also sort of responsive. I decided not to have the plugin load this additionally.

Sadly there is no auto height for an iframe in css so that will roughly give a 16:9 space.

Direct plugin usages in 2.x
-----------------

The content macro above works as always but since the third party library has been changed all direct usages of plugin functions from 1.x are not compatible anymore.

   $embed = zpoembed::getEmbedCode($url);
   echo $embed; 
   
These former zpOembed class methods have been removed without replacements:

- `getEmbedHTML()`
- `getReplaceEmbeds()`
- `getExtractEmbeds()`
   
You can also use the library adapted directly. 

`$embedinfo = Embed\Embed::create($url);`  

See the repository of the library on GitHub for all options.



