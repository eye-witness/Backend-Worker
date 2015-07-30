<?php

namespace Eyewitness\Utils;

interface PoliceDataInterface
{
	/**
	 * Get the contact information for the police force
	 * @param  int 		$id 		Police force ID
	 * @return array 				[phoneNumber, policeForce] elements's values both formatted as strings
	 */
	public function getContactInfo(int $id) : array;

	/**
	 * Get the internal id for a police force
	 * @param  string 	$policeForce 	Name of the police force
	 * @return int               		The integer id of the police force
	 */
	public function getId(string $policeForce) : int;
}
