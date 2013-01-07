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
final class XO
{
	/**
	 *
	 */
	function __construct($url)
	{
		$this->_handle = @stream_socket_client(
			$url,
			/* out */ $errno,
			/* out */ $errstr
		);

		if (!$this->_handle)
		{
			throw new XO_Exception(
				'failed to connect: {$errno} − {$errstr}',
				array(
					'errno'  => $errno,
					'errstr' => $errstr,
				)
			);
		}
	}

	/**
	 *
	 */
	function __call($method, array $params)
	{
		if($this->_ns)
		{
			$method = $this->_ns.'.'.$method;
			$this->_ns = null;
		}
		return $this->_call($method, $params);
	}

	function __get($name)
	{
		if ($this->_ns)
		{
			$this->_ns .= '.'.$name;
		}
		else
		{
			$this->_ns = $name;
		}

		return $this;
	}

	/**
	 * @var resource
	 */
	private $_handle;

	/**
	 *
	 */
	private $_ns;

	/**
	 *
	 */
	private function _call($method, array $params)
	{
		$id = uniqid();

		$request = array(
			'jsonrpc' => '2.0',
			'method'  => $method,
			'params'  => $params,
			'id'      => $id,
		);

		$_ = json_encode($request);
		$len = @fwrite($this->_handle, strlen($_)."\n".$_);
		if ($len === false)
		{
			$error = error_get_last();

			throw new XO_Exception(
				'failed to send a request: {$error}',
				array(
					'error'   => $error['message'],
					'request' => $request
				)
			);
		}

		$len = @stream_get_line($this->_handle, 1024, "\n");
		if ($len === false)
		{
			$error = error_get_last();

			throw new XO_Exception(
				'failed to read the response length: {$error}',
				array(
					'error'   => $error['message'],
					'request' => $request
				)
			);
		}

		$len = (int) $len;
		$response = @fread($this->_handle, $len);
		if (($response === false)
		    || (strlen($response) !== $len))
		{
			$error = error_get_last();

			throw new XO_Exception(
				'failed to read the response: {$error}',
				array(
					'error'   => $error['message'],
					'request' => $request
				)
			);
		}

		$response = json_decode($response, true);

		if (!isset($response['jsonrpc'])
		    || !(isset($response['result'])
		         || isset($response['error']['code'], $response['error']['message']))
		    || ($response['jsonrpc'] !== '2.0'))
		{
			throw new XO_Exception(
				'invalid JSON RPC response',
				array(
					'response' => $response
				)
			);
		}

		if (isset($response['error']))
		{
			throw new XO_Exception(
				'error response: {$code} − {$message}',
				array(
					'code'    => $response['error']['code'],
					'message' => $response['error']['message'],
				)
			);
		}

		if (!isset($response['id'])
		    || ($response['id'] !== $id))
		{
			throw new XO_Exception(
				'invalid response id {$actual}, {$expected} expected',
				array(
					'actual'   => @$response['id'],
					'expected' => $id,
				)
			);
		}

		return $response['result'];
	}
}
