<?php

namespace EyeWitness\Utils;

class PoliceDataUtils implements PoliceDataInterface
{
	public function getContactInfo(int $id) : array
	{
		return ['phoneNumber' => '101', 'policeForce' => 'Surrey Police'];
	}
}