<?php
/**
 * @package     Slideshow
 * @subpackage  com_slideshow
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Slideshow Component Video Helper
 *
 * @package     Slideshow
 * @subpackage  com_slideshow
 * @since       3.0
 */
abstract class VideoHelper
{
	/**
	 * Method to get video url.
	 *
	 * @param   string  $url  The youtube or vimeo url.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getVideoIframe($url)
	{
		if ($id = self::getYoutubeId($url))
		{
			return 'http://www.youtube.com/embed/' . $id;
		}

		if ($id = self::getVimeoId($url))
		{
			return 'http://player.vimeo.com/video/' . $id . '?title=0&amp;byline=0&amp;portrait=0';
		}

		return false;
	}

	/**
	 * Method to get YouTube Id.
	 *
	 * @param   string  $url  The youtube url.
	 *
	 * @return  string  The youtube id.
	 *
	 * @since   3.0
	 */
	public static function getYoutubeId($url)
	{
		if ($matches = self::_isYoutubeUrl($url))
		{
			return (self::_isValidId($matches[0])) ? $matches[0] : false;
		}

		return false;
	}

	/**
	 * Method to get Vimeo Id.
	 *
	 * @param   string  $url  The vimeo url.
	 *
	 * @return  integer  The vimeo id.
	 *
	 * @since   3.0
	 */
	public static function getVimeoId($url)
	{
		if ($vimeo_id = self::_isVimeoUrl($url))
		{
			return (self::_isValidId($vimeo_id, true)) ? $vimeo_id : false;
		}

		return false;
	}

	/**
	 * Method to get youtube video url.
	 *
	 * @param   string  $id  The youtube video id.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getYoutubeUrl($id)
	{
		if (!self::_isValidId($id))
		{
			return false;
		}

		return 'http://www.youtube.com/v/' . $id;
	}

	/**
	 * Method to get vimeo video url.
	 *
	 * @param   int  $id  The vimeo video id.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getVimeoUrl($id)
	{
		if (!self::_isValidId($id, true))
		{
			return false;
		}

		return 'http://vimeo.com/' . $id;
	}

	/**
	 * Method to get youtube thumbs.
	 *
	 * @param   string  $id     The youtube video url or id.
	 * @param   number  $thumb  The thumb size, select 0 to 3 for return a specific thumb.
	 *
	 * @return  array  Url's to thumbs or specific thumb.
	 *
	 * @since   3.0
	 */
	public static function getYoutubeThumbs($id, $thumb = null)
	{
		if (!self::_isValidId($id))
		{
			$id = self::getYoutubeId($id);
		}

		$result = array(
			'0' => 'http://img.youtube.com/vi/' . $id . '/0.jpg',
			'1' => 'http://img.youtube.com/vi/' . $id . '/1.jpg',
			'2' => 'http://img.youtube.com/vi/' . $id . '/2.jpg',
			'3' => 'http://img.youtube.com/vi/' . $id . '/3.jpg',
		);

		if (($thumb === null) || ($thumb > 3 || $thumb < 0))
		{
			return $result;
		}
		else
		{
			return $result[$thumb];
		}
	}

	/**
	 * Method to get vimeo thumbs.
	 *
	 * @param   string  $id     The vimeo video url or id.
	 * @param   number  $thumb  The thumb size, select 0 to 2 to return a specific thumb.
	 *
	 * @return  array  Url's to thumbs or specific thumb.
	 *
	 * @since   3.0
	 */
	public static function getVimeoThumbs($id, $thumb = null)
	{
		if (!self::_isValidId($id, true))
		{
			$id = self::getVimeoId($id);
		}

		$hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/' . $id . '.php'));

		$result = array(
			'0' => $hash[0]['thumbnail_small'],
			'1' => $hash[0]['thumbnail_medium'],
			'2' => $hash[0]['thumbnail_large']
		);

		if (($thumb === null) || ($thumb > 2 || $thumb < 0))
		{
			return $result;
		}
		else
		{
			return $result[$thumb];
		}

	}

	/**
	 * Method to check if is youtube URL.
	 *
	 * @param   string  $url  The youtube url.
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	private static function _isYoutubeUrl($url)
	{
		if (!self::_isValidUrl($url))
		{
			return false;
		}

		if (preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches))
		{
			return $matches;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to check if is vimeo URL.
	 *
	 * @param   string  $url  The vimeo url.
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	private static function _isVimeoUrl($url)
	{
		if (!self::_isValidUrl($url))
		{
			return false;
		}

		if (sscanf(parse_url($url, PHP_URL_PATH), '/%d', $vimeo_id))
		{
			return $vimeo_id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to validate the URL.
	 *
	 * This URL could have http or just www.
	 *
	 * @param   string  $url  The youtube or vimeo url.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.0
	 */
	private static function _isValidUrl($url)
	{
		if (preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i', $url))
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to validate the ID.
	 *
	 * Check if the id is valid or not.
	 *
	 * @param   string   $id     The youtube or vimeo id.
	 * @param   boolean  $vimeo  If vimeo set true.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.0
	 */
	private static function _isValidId($id, $vimeo = false)
	{
		if ($vimeo)
		{
			$headers = get_headers('http://vimeo.com/' . $id);
		}
		else
		{
			$headers = get_headers('http://gdata.youtube.com/feeds/api/videos/' . $id);
		}

		if (!strpos($headers[0], '200'))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}
