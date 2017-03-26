<?php

$app->post('/site', function($req, $resp) 
{
	$arg = $req->getParsedBody();
	$selectApikey = $this->db->prepare('SELECT userId, void FROM access WHERE apiKey = ?');
	$selectApikey->execute([$arg['apiKey']]);
	$user = $selectApikey->fetch();
	if(!$user) {
		return $resp->withJson(['error' => 'Invalid key'], 401);
	}
	if($user['void']){
		return $resp->withJson(['error' => 'Key has been void'], 401);
	}
	$this->db->prepare('UPDATE access SET lastSeen = NULL WHERE apiKey = ?')->execute([
		$arg['apiKey'],
	]);
	$selectlocation = $this->db->query('SELECT location FROM coordinator');
	while($location = $selectlocation->fetch())
		$results[] = array($location['location']);
	$result_mag = array('location' => $results); 
	return $resp->withJson($result_mag, 200);
});

$app->post('/state', function($req, $resp) 
{
	$arg = $req->getParsedBody();
	$selectApikey = $this->db->prepare('SELECT userId, void FROM access WHERE apiKey = ?');
	$selectApikey->execute([$arg['apiKey']]);
	$user = $selectApikey->fetch();
	if(!$user) {
		return $resp->withJson(['error' => 'Invalid key'], 401);
	}
	if($user['void']){
		return $resp->withJson(['error' => 'Key has been void'], 401);
	}
	$this->db->prepare('UPDATE access SET lastSeen = NULL WHERE apiKey = ?')->execute([
		$arg['apiKey'],
	]);
	$selectCoordId = $this->db->prepare('SELECT id FROM coordinator WHERE location = ?');
	$selectCoordId->execute([$arg['location']]);
	$coorId = $selectCoordId->fetch();
	if(!$coorId) {
		return $resp->withJson(['error' => 'Invalid location'], 401);
	}
	$selectDevice = $this->db->prepare('SELECT COUNT(job.id) AS count,device.name,device.id,device.status,device.washingTime FROM device LEFT JOIN job ON device.id = job.deviceId WHERE coordinatorId = ? GROUP BY id');
	$selectDevice->execute([$coorId['id']]);
	while($data = $selectDevice->fetch())
		$results[] = array($data['name'] => array("id" => $data['id'], "state" => $data['status'], "r_time" => $data['washingTime'], "num_queue" => $data['count']));
	
	return $resp->withJson($results, 200);
});

