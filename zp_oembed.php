<?php
/**
 * A plugin to embed content from various services via oEmbed using a content macro.
 * An adaption of Felix Girault's OEmbed libary Essence: https://github.com/felixgirault/essence
 *
 * Support for the following providers: 23hq, Bandcamp, Blip.tv, Cacoo, CanalPlus, Chirb.it, Clikthrough, CollegeHumour, 
 * Dailymotion, Deviantart, Dipity, Flickr, Funnyordie, Howcast, Huffduffer, Hulu, Ifixit, Imgur, Instagram, Mobypicture, 
 * Official.fm, Polldaddy, Qik, Revision3, Scribd, Shoudio, Sketchfab, Slideshare, SoundCloud, Ted, Twitter, Vhx, Viddler, Vimeo, 
 * Yfrog and Youtube.
 *
 * Usage:
 * Enter the macro within any supported content field like this and add the url of the service to use (here: Youtube):
 * <var>[EMBED http://www.youtube.com/watch?v=F3TP7LZ8a2w]</var>
 * On the theme the video will be directly be embeded.
 *
 * The plugin has options to set the maxwidth/height of an embeded element. Since most use iFrames
 * it is recommended to add the following rule to your theme's css:
 * .zpoembed iframe { width: 100%; height: 40%; }  
 * This way you make sure the iframe never exceeds the content your embeded it into and also sort of responsive.
 * Sadly there is no auto height for an iframe in css so that will roughly give a 16:9 space.
 *
 * @license GPL v3 
 * @author Malte Müller (acrylian)
 *
 * @package plugins
 * @subpackage misc
 */
$plugin_is_filter = 9|THEME_PLUGIN|ADMIN_PLUGIN;
$plugin_description = gettext('A plugin to embed content from various services by URL using OEmbed.');
$plugin_author = 'Malte Müller (acrylian)';
$plugin_version = '1.4.5';
$option_interface = 'zpoembed';

zp_register_filter('content_macro','zpoembed::macro');

require_once(SERVERPATH.'/'.USER_PLUGIN_FOLDER.'/zp_oembed/bootstrap.php');
global $essence;
/* Tried to do this within the class constructor to avoid the global
but got errors probably because of the namespaces */
$essence =  new fg\Essence\Essence(); 

class zpoembed {
	/**
	 * class instantiation function
	 */
	function __construct() {
		setOptionDefault('zpoembed_maxwidth',640);
		setOptionDefault('zpoembed_maxwidth',480);
	}
	
	function getOptionsSupported() {
				$options = array(
							gettext('Maxwidth of the embed') => array('key' => 'zpoembed_maxwidth', 'type' => OPTION_TYPE_TEXTBOX,
										'order' => 0,
										'desc' => NULL),
							gettext('Maxheight of the embed') => array('key' => 'zpoembed_maxheight', 'type' => OPTION_TYPE_TEXTBOX,
										'order' => 1,
										'desc' => NULL)
			);
		return $options;
	}
	
	static function macro($macros) {
		$macros['EMBED'] = array(
				'class'=>'expression',
				'params'=> array('string'),
				'value'=>'zpoembed::getEmbedHTML($1);',
				'owner'=>'oembed',
				'desc'=>gettext('Pass the url of the service to embed the content.')
				);
		return $macros;
	}

	static function getEmbedHTML($url) {
		global $essence;
		$maxwidth = getOption('zpoembed_maxwidth');
		$maxheight = getOption('zpoembed_maxheight');
		if(empty($maxwidth)) $maxwidth = 640;
		if(empty($maxheight)) $maxheight = 480;
		$media = $essence->embed($url,
    	array(
        'maxwidth' => $maxwidth,
        'maxheight' => $maxheight
    	));
		if($media) {
			return '<div class="zpoembed">'.$media->html.'</div>';
		}
		return NULL;
	}
	
	static function getReplaceEmbeds($text) {
		global $essence;
		$replace = $essence->replace($text,'<div class="zpoembed">%html%</div>');
		if($replace) {
			return $replace;
		}
		return NULL;
	}
	
	static function getExtractEmbeds($url) {
		global $essence;
		$urls = $essence->extract($url);
		if(is_array($urls)) {
			$media = $essence->embedAll($urls);
			return $media; 
		}
		return NULL;
	}

} // class end
?>