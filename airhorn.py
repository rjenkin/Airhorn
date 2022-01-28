#!/usr/bin/python
#
# This script creates a socket and listens in for Airhorn activation requests. When
# one is received, a GPIO pin is set HIGH for a period of time.
#
# @package Airhorn
# @author  Ryan Jenkin
# @version v1.0
# @date    2013-04-21
# @url     http://rjenkin.com/
# @email   contact@rjenkin.com

# Imports
import RPi.GPIO as GPIO
import time
import socket as S

# Variables
pin = 18
port = 1010
duration = 0.3

# Refer to pins using BCM mode
GPIO.setmode(GPIO.BCM)

# Set pin to output
GPIO.setup(pin, GPIO.OUT)

# Create socket
sk = make_server_socket()

# Continuously listen to socket
try:
	while True:
		print("Listening..")
		serve_client(sk)

except KeyboardInterrupt:
	print("\nClosing\n")

# Close the connection
sk.close()
GPIO.cleanup()


#
# Functions
#


# Function to create connections
def make_server_socket():
	sk = S.socket(S.AF_INET, S.SOCK_STREAM)
	sk.setsockopt(S.SOL_SOCKET, S.SO_REUSEADDR, 1)
	sk.bind(('', port))
	sk.listen(5)
	return sk


# Function for listening to connections
def serve_client(sk):

	(nsk, addr) = sk.accept()

	data = nsk.recv(1024)

	if ( data == "Activate AirHorn ph2fu5Et" ):
		print("\tActivating airhorn")
		nsk.send("Activating airhorn")

		# Activate the airhorn
		airhorn_activate()
	else:
		print("Invalid command")
		nsk.send("Invalid command")

	nsk.close()


# Activate the airhorn
def airhorn_activate():
	# Turn on, wait, turn off
	GPIO.output(pin, GPIO.HIGH)
	time.sleep(duration)
	GPIO.output(pin, GPIO.LOW)
