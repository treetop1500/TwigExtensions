<?php
namespace Treetop1500\Twig\Extension;

/**
 * Class Treetop1500Extension
 * @package Treetop1500\Twig\Extension
 */
/**
 * Class Treetop1500Extension
 * @package Treetop1500\Twig\Extension
 */
class Treetop1500Extension extends \Twig_Extension
{

	/**
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
		  'fractions' => new \Twig_SimpleFunction('fractions',
		    array($this, 'decimalToFraction')
		  ),
          'lipsum' => new \Twig_SimpleFunction('lipsum',
            array($this, 'getLipsum')
          ),
		);
	}

	/**
	 * @return array
	 */
	public function getFilters()
	{
		return array(
		  'truecheck' => new \Twig_SimpleFilter('truecheck',
		    array($this, 'isTrueCheck'),
		    array('is_safe' => array('html'))
		  ),

		  'tel' => new \Twig_SimpleFilter('tel',
		    array($this, 'formatTelephone'),
		    array('is_safe' => array('html'))
		  ),

		  'url' => new \Twig_SimpleFilter('url',
		    array($this, 'addhttp')
		  ),

		  'bullets' => new \Twig_SimpleFilter('bullets',
		    array($this, 'convertToBullets'),
		    array('is_safe' => array('html'))
		  ),

		  'snippet' => new \Twig_SimpleFilter('snippet',
		    array($this,'snippet')
		  ),

		  'cleanString' => new \Twig_SimpleFilter('cleanString',
		    array($this,'cleanString')
		  ),

		  'truncateFileName' => new \Twig_SimpleFilter('truncateFileName',
		    array($this,'truncateFileName')
		  ),

		 'getFileExtension' => new \Twig_simpleFilter('getFileExtension',
		   array($this,'getFileExtension')
		 )

		);
	}

	/**
	 * @param $trunc : number of characters to return
	 * @return string
	 */
	public function getLipsum ($trunc) {
		$str = substr('Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Vestibulum id ligula porta felis euismod semper. Donec sed odio dui. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Curabitur blandit tempus porttitor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam id dolor id nibh ultricies vehicula ut id elit. Donec sed odio dui. Donec id elit non mi porta gravida at eget metus. Curabitur blandit tempus porttitor. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Donec ullamcorper nulla non metus auctor fringilla. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Aenean lacinia bibendum nulla sed consectetur. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Nullam id dolor id nibh ultricies vehicula ut id elit. Maecenas faucibus mollis interdum. Nullam quis risus eget urna mollis ornare vel eu leo. Sed posuere consectetur est at lobortis. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Maecenas faucibus mollis interdum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean lacinia bibendum nulla sed consectetur. Donec ullamcorper nulla non metus auctor fringilla. Donec ullamcorper nulla non metus auctor fringilla. Donec sed odio dui. Curabitur blandit tempus porttitor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',0,$trunc);

		return $str;
	}


	/**
	 * converts an array or string to an unordered list and returns html.
	 *
	 * @param $given_value : a string with line break delimeter or an array
	 * @return string
	 */
	function convertToBullets($given_value)
	{
		if (!is_array($given_value)) {
			$bullets = explode("\n", $given_value);
		} else {
			$bullets = $given_value;
		}
		$str = "<ul>";
		foreach($bullets as $bullet)
		{
			$str .= "<li>" . $bullet . "</li>";
		}
		$str .= "</ul>";

		return $str;
	}

	/**
	 * Returns a trimmed snippet of a string
	 *
	 * @param $text : the text you want to trim
	 * @param $limit : how many words you want in your final
	 * @return string
	 */
	public function snippet($text,$limit) {
		$text = $this->cleanString($text);
		if (str_word_count($text, 0) > $limit) {
			$words = str_word_count($text, 2);
			$pos = array_keys($words);
			$text = substr($text, 0, $pos[$limit]);
			$text = trim($text," .") . '...';
		}
		return $text;
	}


	/**
	 * Strips out non-numeric characters and adds the +1 country code and wraps in a proper anchor.
	 *
	 * @param $given_value : any US phone number
	 * @return string
	 */
	function formatTelephone($given_value)
	{
		$tel = '+1'.preg_replace('/\D+/', '', $given_value);
		$phone_link = "<a href='tel:".$tel."' title='Call ".$given_value."''>".$given_value."</a>";

		return $phone_link;
	}

	/**
	 * displays a fontawesome checkmark in place of a boolean:true
     *
	 * @param $val : boolean
	 * @return string
	 */
	public function isTrueCheck($val) {
		if ($val === 1 || $val === true) {

			return "<i class='fa fa-check'></i>";
		}
	}


	/**
	 * Ensures that urls include the http:// spec

	 * @param $url : any url
	 * @return string
	 */
	public function addhttp($url) {
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
			$url = "http://" . $url;
		}
		return $url;
	}

	/**
	 * cleans up strings from the tumblr api and other sources
	 * that come in all funky with UTF-8 no-break-spaces and html entities and html tags
	 *
	 * @param $text : any string
	 * @return string
	 */
	public function cleanString($text) {
		$badCharacters = array(chr(0xA0),chr(0xC2));
		$text = html_entity_decode(strip_tags($text));

		return str_replace($badCharacters,"",$text);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'treetop1500_extension';
	}


	/**
	 * @param $val
	 * @return string
	 */
	public function decimalToFraction($val) {
		$ar = explode(".",$val);
		$dec = rtrim($ar[1],'0');
		if ($dec == "062") {
			return  $ar[0]."<span class='frac'><sup>1</sup>&frasl;<sub>16</sub></span>";
		} else if ($dec == "125") {
			return  $ar[0]."<span class='frac'>&frac18;</span>";
		} else if ($dec == "25") {
			return  $ar[0]."<span class='frac'>&frac14;</span>";
		} else if ($dec == "375") {
			return  $ar[0]."<span class='frac'>&frac38;</span>";
		} else if ($dec == "5") {
			return  $ar[0]."<span class='frac'>&frac12;</span>";
		} else if ($dec == "625") {
			return $ar[0]."<span class='frac'>&frac58;</span>";
		} else if ($dec == "75") {
			return $ar[0]."<span class='frac'>&frac34;</span>";
		} else if ($dec == "875") {
			return $ar[0]."<span class='frac'>&frac78;</span>";
		} else if ($dec != null && $dec != 0 && $dec != '0') {
			return $ar[0].'.'.$dec;
		}
		return $ar[0];
	}


	/**
	 * @param $filename
	 * @return string
	 * truncates really long file names but keeps the file ending.
	 * Turns file names like alsdk02340983w0jskdsfl0230408weruwerwsfsdflalsdkfjasksji948snoiw.png
	 * to something like alsdk02340983w0...ksji948snoiw.png
	 */
	public function truncateFileName($filename){
		if (strlen($filename) > 36) {
			return substr($filename, 0, 16) . "..." . substr($filename, -16);
		}

		return $filename;
	}


	/**
	 * @param $path
	 * @return mixed
	 * returns the file extension from a path.
	 * For instance $path = 'http://grayloon.com/images/foo.png' would return 'png'
	 */
	public function getFileExtension($path) {

		$path_parts = pathinfo($path);

		return  $path_parts['extension'];

	}






}