<?php

namespace EyeWitness\Controller;

//use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController
{
	public function __construct($policeData, $db)
	{
		$this->policeData = $policeData;
		$this->db = $db;
	}

	private function getBlockId(int $latitude, int $longitude)
	{
		return strval($latitude . ',' . $longitude);
	}

	public function appealPostAction(Request $request)
	{
		if (0 !== strpos($request->headers->get('Content-Type'), 'application/json'))
		{
			return $this->apiError(400, 'Your request was not intepreted as a JSON Request (Content Type Header)');
		}

		$postData = array(
			'blocks' => $request->request->get('blocks'),
			'time'  => $request->request->get('time'),
			'lastFetched' => $request->request->get('lastFetched'),
		);

		$blocks = array();

		foreach ($postData['blocks'] as $block)
		{
			if (!is_int($block['latitude']) || !is_int($block['longitude']))
			{
				return $this->apiError(400, 'Your block id determined by your generic latitude and longitude must be integers');
			}

			$blocks[] = strval($this->getBlockId($block['latitude'], $block['longitude']));
		}

		$whereStatement = implode("' OR block_id='", $blocks);

		if (!is_int($postData['time']))
		{
			return $this->apiError(400, 'Time must be an integer (Unix timestamp)');
		}

		$offset = ($time - $postData['time']);
		$lastFetched = (int) ($postData['time'] + $offset);

		$sql = "SELECT *
			FROM appeals
			WHERE (block_id ='" . $whereStatement . "') AND created >='" . $lastFetched . "'";

		$appealsRaw = $this->db->fetchAll($sql);

		$appeals = array();

		foreach ($appealsRaw as $i => $appeal)
		{
			$processedAppeal = $appeal;
			$processedAppeal['location'] = [
				'latitude' => $appeal['latitude'],
				'longitude'=> $appeal['longitude'],
			];
			$processedAppeal['description'] = [];
			$processedAppeal['description']['location'] = $appeal['location'];
			$processedAppeal['description']['crimeType'] = $appeal['crime_type'];
			$processedAppeal['description']['text'] = $appeal['description'];
			$processedAppeal['contact'] = $this->policeData->getContactInfo($appeal['police_force_id']);

			unset(
				$processedAppeal['location'],
				$processedAppeal['crime_type'],
				$processedAppeal['policeForceId']
			);

			$appeals[$i] = $processedAppeal;
		}

		return new JsonResponse($appeals, 200);
	}

	public function appealPutAction(Request $request)
	{
		if (0 !== strpos($request->headers->get('Content-Type'), 'application/json'))
		{
			return $this->apiError(400, 'Your request was not intepreted as a JSON Request (Content Type Header)');
		}

		$putData = $request->request->all();

		$data['created'] = time();

		$blockId = strval($this->getBlockId(ceil($putData['latitude']*2), floor($putData['longitude']*2)));

		$data['policeForceId'] = $this->policeData->getId($putData['policeForce']);

		$incidentTime = new DataTime();
		$incidentTime = $this->incidentTime
			->setDate($putData['year'], $putData['month'], $putData['day'])
			->setTime($putData['hour'], $putData['minute'])
			->getTimestamp();

		$pass = $this->db->insert('appeals', array(
			'case_id' => $putData['case_id'],
			'time' => $incidentTime,
			'created' => $data['created'],
			'latitude' => $putData['latitude'],
			'longitude' => $putData['longitude'],
			'radius' => $putData['radius'],
			'location' => $putData['location'],
			'crime_type' => $putData['crime_type'],
			'description' => $putData['description'],
			'police_force_id' => $putData['police_force_id'],
			'block_id' => $blockId,
		));

		if (!$pass)
		{
			return $this->apiError(500, "For some reason we couldn't write this to our DB but your request was 201 accepted");
		}
		else
		{
			return $this->apiError(200, "Success");
		}
	}

	private function apiError(int $statusCode, string $message)
	{
		$error['code'] = $statusCode;
		$error['message'] = $message;

		return new JsonResponse($error, $statusCode)
	}
}
