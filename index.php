<?php
/**
 * This script provides a form for sending Airhorn activation requests to a Raspberry Pi
 *
 * @package Airhorn
 * @author  Ryan Jenkin
 * @version v1.0
 * @date    2013-04-21
 * @url     http://rjenkin.com/
 * @email   contact@rjenkin.com
 */


// Socket settings
$socket_ip      = "192.168.0.83";
$socket_port    = "1010";
$socket_message = "Activate AirHorn ph2fu5Et";

$request_send = FALSE;
if (count($_POST) > 0) {

  $result = airhorn_activation_request($socket_ip, $socket_port, $socket_message);
	if (FALSE == $result) {
		// Something went wrong
		echo "<p>Error activating socket</p>";
		exit();
	}

	$request_send = TRUE;
}

?><html>
<head>
	<title>Airhorn</title>
</head>
<body>


<?php

if ($request_send) {
	echo "<p>Request sent</p>";
}

?>


<form method="post">
	<input type="submit" name="airhorn" value="Activate" />
</form>


</body>
</html><?php


/**
 * Send a message to a remote socket
 *
 * @param string $ip
 * @param int $port
 * @param string $message
 * @return bool
 */
function airhorn_activation_request( $ip, $port, $message ) {
	assert('is_string($ip)');
	assert('preg_match("/^\d+{1,3}\.\d+{1,3}\.\d+{1,3}\.\d+{1,3}$/", $ip)');
	assert('is_int($port)');
	assert('(int)$port > 0 AND (int)$port <= 65535');
	assert('is_string($message)');
	assert('strlen($message) > 0');

	// Create a TCP/IP socket
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if (FALSE == $socket) {
		echo "Error creating socket: " . socket_strerror(socket_last_error());
		return FALSE;
	}

	// Connect to remote socket
	$result = socket_connect($socket, $ip, $port);
	if (FALSE == $result) {
		echo "Error connecting to socket: " . socket_strerror(socket_last_error($socket));
		return FALSE;
	}

	// Send message
	$bytes_written = socket_write($socket, $message, strlen($message));
	if (FALSE === $bytes_written) {
		echo "Error writing to socket: " . socket_strerror(socket_last_error($socket));
		return FALSE;
	}

	// Close the socket
	socket_close($socket);

	return TRUE;
}

