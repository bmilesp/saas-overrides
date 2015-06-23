<?php
App::uses('AuthComponent', 'Controller/Component');
App::uses('HttpSocket', 'Network/Http');
class RemoteAuthComponent extends AuthComponent {

	public function login($user = null) {
		$this->_setDefaults();
		
		$HttpSocket = new HttpSocket();

		$url = Configure::read('SaasOverrides.auth_domain') . '/api/accounts/login.json';
		
		$data = array(
			'username' => !empty($this->request->data['Login']['username']) ? $this->request->data['Login']['username'] : null,
			'password' => !empty($this->request->data['Login']['password']) ? $this->request->data['Login']['password'] : null
		);
		$results = json_decode($HttpSocket->post($url, $data), true);
		$user = $results['results']['account'];
		
		if ($user) {
			$this->Session->renew();
			$this->Session->write(self::$sessionKey, $user);
		}
		return $this->loggedIn();
	}

	public function isAuthorized($user = null, CakeRequest $request = null) {

		if (empty($user) && !$this->user()) {
			return false;
		}
		if (empty($user)) {
			$user = $this->user();
		}
		
		if (empty($request)) {
			$request = $this->request;
		}

		if (empty($this->_authorizeObjects)) {
			$this->constructAuthorize();
		}

		//Vars
		$account_id = $user['id'];
		$params = $request->params;
		$aco = $params['controller'];
		$action = $params['action'];

		$data = array(
			'account_id' => $account_id,
			'application_slug' => Configure::read('SaasOverrides.application_slug'),
			'aco' => $aco,
			'action' => $action
		);
		
		$HttpSocket = new HttpSocket();
		$url = Configure::read('SaasOverrides.auth_domain') . '/api/accounts/acl_check.json';
		
		$results = json_decode($HttpSocket->post($url, $data), true);
		
		if(!$results['results']['authorized']){
			$this->Session->setFlash(__($this->authError), 'alert', array(
				'plugin' => 'BoostCake',
				'class' => 'alert-danger'
			));
		}
		
		return $results['results']['authorized'];
	}

}
