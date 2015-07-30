<?php

namespace EyeWitness\Controller;

//use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController
{
	function __construct($policeData, $db)
	{
		$this->policeData = $policeData;
		$this->db = $db;
	}

	private function getBlockId(int $latitude, int $longitude)
	{
		$lat = strval(ceil($putData['latitude'] * 2));
		$long = strval(ceil($putData['longitude'] * 2));

		$lat = str_pad($lat, 3, "0", STR_PAD_LEFT);
		$long = str_pad($long, 3, "0", STR_PAD_LEFT);

		return strval($lat . $long);
	}

	public function appealPostAction(Request $request)
	{
		if (0 !== strpos($request->headers->get('Content-Type'), 'application/json'))
		{
			$app->abort(400, 'Your request was not intepreted as a JSON Request (Content Type Header)');
		}

		$postData = array(
			'blocks' => $request->request->get('blocks'),
			'time'  => $request->request->get('time'),
			'lastFetched' => $request->request->get('lastFetched'),
		);

		$blocks = array();

		foreach ($postData['blocks'] as $block)
		{
			$blocks[] = strval($this->getBlockId($block['latitude'], $block['longitude']));
		}

		$whereStatement = implode("' OR block_id='", $blocks);

		$offset = ($time - $postData['time']);
		$lastFetched = (int) ($postData['time'] + $offset);

		$sql = "SELECT *
			FROM appeals
			WHERE (block_id ='" . $whereStatement . "') AND created >='" . $lastFetched . "'";

		$query = $this->db->prepare($sql)->execute();
		$appealsRaw = $query->fetchAll();

		$appeals = array();

		foreach ($appealsRaw as $i => $appeal)
		{
			$processedAppeal = $appeal;
			$processedAppeal['location'] = [
				'latitude' => $appeal['latitude'],
				'longitude'=> $appeal['longitude'],
			];
			$processedAppeal['description']['location'] = $appeal['location'];
			$processedAppeal['description']['crimeType'] = $appeal['crime_type'];
			$processedAppeal['description']['text'] = $appeal['description'];
			$processedAppeal['contact'] = $this->policeData->getContactInfo($appeal['police_force_id']);

			unset(
				$processedAppeal['latitude'],
				$processedAppeal['longitude'],
				$processedAppeal['location'],
				$processedAppeal['crime_type'],
				$processedAppeal['description'],
				$processedAppeal['policeForceId']
			);

			$appeals[$i] = $processedAppeal;
		}

		return new JsonResponse($appeals, 200, 'API Version: 1.0.0');
	}

	public function appealPutAction(Request $request)
	{
		if (0 !== strpos($request->headers->get('Content-Type'), 'application/json'))
		{
			$app->abort(400, 'Your request was not intepreted as a JSON Request (Content Type Header)');
		}

		$putData = $request->request->all();

		$data['created'] = time();

		$blockId = strval($this->getBlockId($putData['latitude'], $putData['longitude']));

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
			$app->abort(500, "For some reason we couldn't write this to our DB but your request was 201 accepted");
		}
		else
		{
			$app->abort(200, "Success");
		}
	}
}
