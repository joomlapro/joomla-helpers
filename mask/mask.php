<?php
/**
 * @package     Component
 * @subpackage  com_component
 * @copyright   Copyright (C) 2013 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Component Mask Helper
 *
 * @package     Component
 * @subpackage  com_component
 * @since       3.0
 */
abstract class MaskHelper
{
	/**
	 * Method to get the mask string.
	 *
	 * @param   string  $i  The string mask.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	private static function getMask($i)
	{
		// Define the regex chars.
		$regex = array(
			'9' => '/[0-9]/',
			'a' => '/[A-Za-z]/',
			'*' => '/[A-Za-z0-9]/'
		);

		if (isset($regex[$i]))
		{
			return array(
				'type' => 'regexp',
				'value' => $regex[$i]
			);
		}
		else
		{
			return array(
				'type'  => 'char',
				'value' => $i
			);
		}
	}

	/**
	 * Method to set the mask.
	 *
	 * @param   string  $mask  The mask format.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function setMask($mask)
	{
		// Initialiase variables.
		$array = str_split($mask);
		$size  = count($mask);
		$masks = array();

		foreach ($array as $k => $v)
		{
			if ($v == "?")
			{
				$size--;
			}
			else
			{
				$masks[] = self::getMask($v);
			}
		}

		return $masks;
	}

	/**
	 * Method to mask the string.
	 *
	 * @param   string  $string  The string to format.
	 * @param   string  $mask    The mask format.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function mask($string, $mask)
	{
		if (empty($string))
		{
			return;
		}

		// Get a list of saved masks.
		$list = array(
			'phone' => '(99) 9999-9999',
			'phone-us' => '(999) 999-9999',
			'cpf'  => '999.999.999-99',
			'cnpj' => '99.999.999/9999-99',
			'date' => '99/99/9999',
			'zip'  => '99.999-999',
			'time' => '99:99',
			'issn' => '****-****'
		);

		// Verify if have the mask in the list.
		if (array_key_exists($mask, $list))
		{
			$mask = $list[$mask];
		}

		// Initialiase variables.
		$mask   = self::setMask($mask);
		$index  = 0;
		$result = array();

		foreach ($mask as $k => $v)
		{
			$type  = $v['type'];
			$value = $v['value'];

			if (!isset($string[$index]) && $type != 'char')
			{
				break;
			}

			if ($type == 'char')
			{
				$result[] = $value;
				$index--;
			}
			else
			{
				preg_match($value, $string[$index], $matches);

				if (isset($matches[0]))
				{
					$result[] = $matches[0];
				}
			}

			$index++;
		}

		$result = implode('', $result);

		return $result;
	}
}
