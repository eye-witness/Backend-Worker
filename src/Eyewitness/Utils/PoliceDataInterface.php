<?php

namespace EyeWitness\Utils;

interface PoliceDataInterface
{
	/**
	 * Get the contact information for the police force
	 * @param  int 		$id 		Police force ID
	 * @return array 				[phoneNumber, policeForce] elements's values both formatted as strings
	 */
	public function getContactInfo(int $id) : array;
}
