<?php
class API extends RESTful
{
	function main()
	{
		$this->checkMethod('GET');

		if(!isset($_GET['only']) || $_GET['only']=='db')
		if ($this->core->config->get("dbName")) {
			$this->core->__p->init('test', 'CloudSQL connect');
			$db = $this->core->loadClass("CloudSQL");
			$db->connect();
			if (!$db->error()) {
				$db->close();
				$notes[] = ['dbServer'=>(strlen($this->core->config->get("dbServer")))?substr($this->core->config->get("dbServer"),0,4).'***':'None'];
				$notes[] = ['dbSocket'=>(strlen($this->core->config->get("dbSocket")))?'***':'None'];
			} else {
				$notes = array($db->getError());
			}

			/*
			$notes[] = ['dbServer'=>(strlen($this->core->config->get("dbServer")))?substr($this->core->config->get("dbServer"),0,4).'***':'None'];
			$notes[] = ['dbSocket'=>(strlen($this->core->config->get("dbSocket")))?'***':'None'];
			$notes[] = ['dbUser'=>(strlen($this->core->config->get("dbUser")))?'***':'None'];
			$notes[] = ['dbPassword'=>(strlen($this->core->config->get("dbPassword")))?'***':'None'];
			$notes[] = ['dbName'=>(strlen($this->core->config->get("dbName")))?'***':'None'];
			$notes[] = ['dbPort'=>(strlen($this->core->config->get("dbPort")))?'***':'None'];
			*/
			$this->core->__p->end('test', 'CloudSQL connect', !$db->error(), $notes);
		} else {
			$this->addReturnData(array('CloudSQL connect' => 'no DB configured'));
		}

		if(!isset($_GET['only']) || $_GET['only']=='localize')
		if (strlen($this->core->config->get("LocalizePath"))) {
			$this->core->__p->init('test', 'LocalizePath scandir');
			$errMsg = '';
			try {
				$ret = scandir($this->core->config->get("LocalizePath"));
			} catch (Exception $e) {
				$errMsg = 'Error reading ' . $this->core->config->get("LocalizePath") . ': ' . $e->getMessage() . ' ' . error_get_last();
			}
			$this->core->__p->end('test', 'LocalizePath scandir', is_array($ret), $this->core->config->get("LocalizePath") . ': ' . $errMsg);
		}

		// Cloud Service Connections
		if(!isset($_GET['only']) || $_GET['only']=='cloud')
		if ($this->core->request->getServiceUrl()) {
			$url = '/_version';
			$retErr = '';
			$this->core->__p->init('test', 'Cloud Service Url request->get');
			$ret = $this->core->request->get($url);
			if (!$this->core->request->error) {
				$ret = json_decode($ret);
				$retOk = $ret->success;
				if (!$retOk) $retErr = json_encode($ret);
				
			} else {
				$retOk = false;
			}
			$this->core->__p->end('test', 'Cloud Service Url request->get', $retOk, $this->core->request->getServiceUrl('/_version') . ' ' . $retErr);

			$this->core->__p->init('test', 'Cloud Service Url request->getCurl');
			$ret = $this->core->request->getCurl($url);
			if (!$this->core->request->error) {
				$ret = json_decode($ret);
				$retOk = $ret->success;
				if (!$retOk) $retErr = json_encode($ret);
			} else {
				$retOk = false;
			}
			$this->core->__p->end('test', 'Cloud Service Url request->getCurl', $retOk, $this->core->request->getServiceUrl('/_version') . ' ' . $retErr);
		}
		else {
			$this->addReturnData(array('Cloud Service Url' => 'no CloudServiceUrl configured'));
		}

		if (is_file($this->core->system->app_path . '/logic/_test.php')) include($this->core->system->app_path  . '/logic/_test.php');

		if (isset($this->core->__p->data['init']))
			$this->addReturnData($this->core->__p->data['init']['test']);
	}
}

