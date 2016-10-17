<?php
/**
 * A plugin to embed content from various services via oEmbed using a content macro.
 * An adaption of Felix Girault's OEmbed libary Essence: https://github.com/felixgirault/essence (FreeBSD license)
 *
 * Support for the following providers: 23hq, Bandcamp, Blip.tv, Cacoo, CanalPlus, Chirb.it, Clikthrough, CollegeHumour, 
 * Dailymotion, Deviantart, Dipity, Flickr, Funnyordie, Howcast, Huffduffer, Hulu, Ifixit, Imgur, Instagram, Mobypicture, 
 * Official.fm, Polldaddy, Qik, Revision3, Scribd, Shoudio, Sketchfab, Slideshare, SoundCloud, Ted, Twitter, Vhx, Viddler, Vimeo, 
 * Yfrog and Youtube and more
 *
 * Usage:
 * Enter the macro within any supported content field like this and add the url of the service to use (here: Youtube):
 * <var>[EMBED http://www.youtube.com/watch?v=F3TP7LZ8a2w]</var>
 * On the theme the video will be directly be embeded.
 * 
 * You can also use the zpoembed class methods directly:
 * <code>
 * $zpoemned = new zpoembed;
 * $embed = $zpoembed->getEmbedHTML($url);
 * </code>
 *
 * The plugin has options to set the maxwidth/height of an embeded element. Those are for the width and height attributes of the mostly iframe. 
 * Percentage values are not possibe so this bites with responsive behaviour. Therefore your theme's css needs to be setup by adding this:
 * <code>.zpoembed > * { width: 100%; }</code>
 *
 * PHP 5.4+ required.
 *
 * @author Malte Müller (acrylian) <info@maltem.de>
 * @copyright 2014 Malte Müller
 * @license GPL v3 or later
 * @package plugins
 * @subpackage misc
 */
$plugin_is_filter = 9|THEME_PLUGIN|ADMIN_PLUGIN;
$plugin_description = gettext('A plugin to embed content from various services by URL using OEmbed. PHP 5.4+ required.');
$plugin_author = 'Malte Müller (acrylian)';
$plugin_version = '1.0.4';
$plugin_disable = (version_compare(PHP_VERSION, '5.4') >= 0) ? false : gettext('zp_oembed requires PHP 5.4 or greater.');
$option_interface = 'zpoembedOptions';

zp_register_filter('content_macro','zpoembed::macro');

require_once(SERVERPATH.'/'.USER_PLUGIN_FOLDER.'/zp_oembed/bootstrap.php');

/**
 * zpoembed options class
 */
class zpoembedOptions {
	
	
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
										'desc' => gettext('Max width of the embed. Numbers only, any appended px etc. will cause an error. Responsive behaviour must be added by the theme css by adding .zpoembed > * { width: 100%; }.')),
							gettext('Maxheight of the embed') => array('key' => 'zpoembed_maxheight', 'type' => OPTION_TYPE_TEXTBOX,
										'order' => 1,
										'desc' => gettext('Max width of the embed. Numbers only, any appended px etc. will cause an error.'))
			);
		return $options;
	}
	
	
}

/**
 * zpoembed class
 */
class zpoembed {
	
	public $essence = '';
	
	function __construct() {
		$this->essence =  Essence\Essence::instance(); 
	}
	
	/**
	 * Internal function to provide the content macro 
	 * 
	 * @param type $macros
	 * @return type
	 */
	static function macro($macros) {
		$macros['EMBED'] = array(
				'class'=>'function',
				'params'=> array('string'),
				'value'=>'zpoembed::getMacroEmbedHTML',
				'owner'=>'oembed',
				'desc'=>gettext('Pass the url of the service to embed the content.')
				);
		return $macros;
	}
	
	/**
	 * static wrapper method for getEmbedHTML() for usage by macro()
	 * @param type $url
	 * @return type
	 */
	static function getMacroEmbedHTML($url) {
		$essenceobj = new zpoembed();
		return $essenceobj->getEmbedHTML($url);
	}
	
	/**
	 * Gets the omebed html for embedding by $url
	 * 
	 * @global type $essence
	 * @param string $url
	 * @return string
	 */
	function getEmbedHTML($url) {
		//global $essence;
		$maxwidth = getOption('zpoembed_maxwidth');
		$maxheight = getOption('zpoembed_maxheight');
		if (empty($maxwidth)) {
			$maxwidth = 640;
		}
		if (empty($maxheight)) {
			$maxheight = 480;
		}
		$media = $this->essence->embed($url, array(
				'maxwidth' => $maxwidth,
				'maxheight' => $maxheight
		));
		if ($media) {
			return '<div class="zpoembed">' . $media->html . '</div>';
		}
		return NULL;
	}

	/**
	 * 
	 * @global type $essence
	 * @param type $text
	 * @return type
	 */
	function getReplaceEmbeds($text) {
		$replace = $this->essence->replace($text, function($Media) {
			return '<div class="zpoembed">'.$Media->title.'</div>';
		});
		if($replace) {
			return $replace;
		}
		return NULL;
	}
	/**
	 * Extracts 
	 * @global type $essence
	 * @param type $url
	 * @return type
	 */
	function getExtractEmbeds($url) {
		$urls = $this->essence->extract($url);
		if(is_array($urls)) {
			$media = $this->essence->embedAll($urls);
			return $media; 
		}
		return NULL;
	}

} // class end
?>