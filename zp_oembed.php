<?php
/**
 * A plugin to embed content from various services via oEmbed using a content macro.
 *
 * An adaption of https://github.com/oscarotero/Embed (MIT license)
 *
 * Usage:
 * Enter the macro within any supported content field like this and add the url of the service to use (here: Youtube):
 * <var>[EMBED http://www.youtube.com/watch?v=F3TP7LZ8a2w]</var>
 * On the theme the video will be directly be embeded.
 * 
 * You can also use the zpoembed class methods directly. Note that this is different than to the 1.x plugin version and not compatible anymore.
 * 
 * <code>
 * $embed = zpoembed::getEmbedCode($url);
 * echo $embed; 
 * </code>
 * 
 * You can also use the library adapted directly. 
 * 
 * `$embedinfo = Embed\Embed::create($url);`
 * 
 * See the documentation on the GitHub repository linked above.
 * 
 * The former class constructor is obsolete and has been removed as are these former methods:
 * 
 * - `getEmbedHTML()`
 * - `getReplaceEmbeds()`
 * - `getExtractEmbeds()`
 *
 * The plugin has options to set the maxwidth/height of an embeded element. Those are for the width and height attributes of the mostly iframe. 
 * Percentage values are not possibe so this bites with responsive behaviour. Therefore your theme's css needs to be setup by adding this:
 * <code>.zpoembed > * { width: 100%; }</code>
 *
 * PHP 5.5+ and cURL library required.
 *
 * @author Malte Müller (acrylian) <info@maltem.de>
 * @copyright 2019 Malte Müller
 * @license GPL v3 or later
 * @package plugins
 * @subpackage misc
 */
$plugin_is_filter = 9|THEME_PLUGIN|ADMIN_PLUGIN;
$plugin_description = gettext('A plugin to embed content from various services by URL using OEmbed. PHP 5.5+ required.');
$plugin_author = 'Malte Müller (acrylian)';
$plugin_version = '2.0b';
$plugin_disable = (version_compare(PHP_VERSION, '5.5') >= 0) ? false : gettext('zp_oembed requires PHP 5.5 or greater.');
$option_interface = 'zpoembedOptions';

zp_register_filter('content_macro','zpoembed::macro');

require_once(SERVERPATH.'/'.USER_PLUGIN_FOLDER.'/zp_oembed/autoloader.php');

/**
 * zpoembed options class
 */
class zpoembedOptions {
	
	
	/**
	 * class instantiation function
	 */
	function __construct() {
		purgeOption('zpoembed_maxwidth');
			purgeOption('zpoembed_maxwidth');
		
	}
	
	function getOptionsSupported() {
				
	}
	
	
}

use Embed\Embed;

/**
 * zpoembed class
 */
class zpoembed {

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
				'value'=>'zpoembed::getEmbedCode',
				'owner'=>'oembed',
				'desc'=>gettext('Pass the url of the service to embed the content.')
				);
		return $macros;
	}
	
	/**
	 * Gets the embed HTML either as provided by an oembed provider or the basic info via e.g. OpenGraph
	 * @param type $url
	 * @return type
	 */
	static function getEmbedCode($url) {
		/*
		 * If there is no oembed, OpenGraph etc we don't fetch images from the source as the site owner might not have intended that.
		 * This would literally be hotlinking after all. So without any embed code returned we do nothing
		 */
		$config = array( 
			'html' => array(
        'max_images' => 0,
        'external_images' => false
			)	
		);
		$embed = Embed::create($url);
		$html = '';
		if($embed->code) {
			return '<div class="zpoembed">' . $embed->code . '</div>';
		} 
	}
	
	/**
	 * Creates the default HTML to embed if there is nothing provided and fetched by the site
	 * 
	 * It only creates a title and description linked to the source
	 * 
	 * @param array $array Array with info as fetched by Embed library:
	 *				$array = array(
	 *						'title' => $embed->title,
	 *						'url' => $embed->url,
	 *						'image' => $embed->image,
	 *						'description' => $embed->description
	 *				);
	 * @return string
	 */
	static function buildDefaultHTML($array) {
		$html = '';
		if (!empty($array['image'])) {
			$html .= '<p><img src="' . html_encode($array['image']) . '" alt="" style="max-width: 100%; height: auto;"></p>';
		}
		if (!empty($array['title'])) {
			$html .= '<h3>' . html_encode($array['title']) . '</h3>';
		}
		if (!empty($array['description'])) {
			$html .= '<p>' . html_encode($array['description']) . '</p>';
		}
		if (!empty($html)) {
			return '<div class="zpoembed" style="border: 1px solid lightgray; padding: 20px; margin-bottom: 20px;"><a href="' . html_encode($array['url']) . '" title="' . html_encode($array['title']) . '">' . $html . '</a></div>';
		}
	}

}