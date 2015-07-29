<?php

namespace EyeWitness\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController
{
	function __construct($policeData, $db)
	{
		$this->policeData = $policeData;
		$this->db = $db;
	}

	public function appealAction(Request $request, Application $app)
	{
		if (0 !== strpos($request->headers->get('Content-Type'), 'application/json'))
		{
			$app->abort(400, 'Your request was not intepreted as a JSON Request (Content Type Header)');
		}

		$postData = array(
			'blocks' => $request->request->get('blocks'),
			'time'  => $request->request->get('time'),
		);

		$blocks = array();

		foreach ($postData['blocks'] as $block)
		{
			$blocks[] = strval(int($block['lat'])) . strval(int($block['long']));
		}

		$whereStatement = implode("' OR block_id='", $blocks);

		$sql = "SELECT *
			FROM appeals
			WHERE block_id ='". $whereStatement;

		$query = $this->db->prepare($sql)->execute();
		$appealsRaw = $query->fetchAll();

		$appeals = array();

		foreach ($appealsRaw as $i => $appeal)
		{
			$processedAppeal = $appeal;
			$processedAppeal['location'] = [
				'lat' => $appeal['lat'],
				'long'=> $appeal['long'],
			];
			$processedAppeal['description']['location'] = $appeal['location'];
			$processedAppeal['description']['crimeType'] = $appeal['crime_type'];
			$processedAppeal['description']['text'] = $appeal['description'];
			$processedAppeal['contact'] = $this->policeData->getContactInfo($appeal['policeForceId']);

			unset(
				$processedAppeal['lat'],
				$processedAppeal['long'],
				$processedAppeal['location'],
				$processedAppeal['crime_type'],
				$processedAppeal['description'],
				$processedAppeal['policeForceId']
			);

			$appeals[$i] = $processedAppeal;
		}

		return new JsonResponse($appeals, 200, 'API Version: 1.0.0');
	}
}
