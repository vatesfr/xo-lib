<?php
/**
 * This file is a part of Xen Orchestra Library.
 *
 * Xen Orchestra Library is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * Xen Orchestra Library is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Xen Orchestra Library. If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @author Julien Fontanet <julien.fontanet@vates.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0-standalone.html GPLv3
 *
 * @package Xen Orchestra Library
 */

/**
 *
 */
final class XO_Exception extends Exception
{
	function __construct($message, array $context)
	{
		$message = preg_replace_callback(
			'/\{\$([a-z0-9-_]+)\}/',
			function ($matches) use ($context)
			{
				return (
					isset($context[$matches[1]])
					? $context[$matches[1]]
					: $matches[0]
				);
			},
			$message
		);

		parent::__construct($message);
	}
}
