<?php

$app->post('/coordinatorReg', function($req, $resp) 
{
	$arg = $req->getParsedBody();
	if($arg['secretCode'] !== 'fyp20162017')
		return $resp->withJson(['error' => 'w.s.code'], 400);
	$selectCoor = $this->db->prepare('SELECT COUNT(id) AS id FROM coordinator WHERE id = ?');
	$selectCoor->execute([$arg['id']]);
	$exist = $selectCoor->fetch();
	if($exist['id'] != 0)
	{
		$this->db->prepare('UPDATE coordinator SET ipAddress = ?, version = ? WHERE id = ?')->execute([
		$arg['ipAddress'],
		$arg['version'],
		$arg['id'],
		]);
		$this->db->prepare('UPDATE coordinator SET lastSeen = NULL WHERE id = ?')->execute([
			$arg['id'],
		]);
		return $resp->withJson(['massage' => 'updated'], 200);
	}
	$this->db->prepare('INSERT INTO coordinator (id, ipAddress, location, version) VALUES (?,?,?,?)')->execute([
    $arg['id'],
    $arg['ipAddress'],
    $arg['location'],
	$arg['version'],
	]);
	$this->db->prepare('UPDATE coordinator SET lastSeen = NULL WHERE id = ?')->execute([
			$arg['id'],
		]);
	return $resp->withJson(['massage' => 'ok'], 200);
});

$app->post('/deviceReg', function($req, $resp) 
{
	$arg = $req->getParsedBody();
	if($arg['secretCode'] !== 'fyp20162017')
		return $resp->withJson(['error' => 'w.s.code'], 400);
	$selectDevice = $this->db->prepare('SELECT COUNT(id) AS id FROM device WHERE id = ?');
	$selectDevice->execute([$arg['id']]);
	$exist = $selectDevice->fetch();
	if($exist['id'] != 0)
	{
		$this->db->prepare('UPDATE device SET coordinatorId = ?, version = ? WHERE id = ?')->execute([
		$arg['coordinatorId'],
		$arg['version'],
		$arg['id'],
		]);
		$this->db->prepare('UPDATE device SET lastSeen = NULL WHERE id = ?')->execute([
			$arg['id'],
		]);
		return $resp->withJson(['massage' => 'updated'], 200);
	}
	$this->db->prepare('INSERT INTO device (id, name, coordinatorId, status, version) VALUES (?,?,?,?,?)')->execute([
    $arg['id'],
    $arg['name'],
    $arg['coordinatorId'],
	"out-of-service",
	$arg['version'],
	]);
	$this->db->prepare('UPDATE device SET lastSeen = NULL WHERE id = ?')->execute([
			$arg['id'],
	]);
	return $resp->withJson(['massage' => 'ok'], 200);
});

$app->post('/updateStat', function($req, $resp) 
{
	$arg = $req->getParsedBody();
	if($arg['secretCode'] !== 'fyp20162017')
		return $resp->withJson(['error' => 'w.s.code'], 400);
	$selectDevice = $this->db->prepare('SELECT COUNT(id) AS id FROM device WHERE id = ?');
	$selectDevice->execute([$arg['id']]);
	$exist = $selectDevice->fetch();
	if($exist['id'] == 0)
		return $resp->withJson(['error' => 'unkown id'], 200);
	$selectDevice = $this->db->prepare('UPDATE device SET coordinatorId = ?, washingTime = ?, status = ?, currentProgress = ?, version = ? WHERE id = ?');
	$selectDevice->execute([
	$arg['coordinatorId'],
	$arg['washingTime'],
	$arg['status'],
	$arg['currentProgress'],
	$arg['version'],
	$arg['id'],
	]);
	
	$this->db->prepare('UPDATE coordinator SET lastSeen = NULL WHERE id = ?')->execute([
		$arg['id'],
	]);
	$this->db->prepare('UPDATE device SET lastSeen = NULL WHERE id = ?')->execute([
		$arg['id'],
	]);
	return $resp->withJson(['massage' => 'updated'], 200);
});