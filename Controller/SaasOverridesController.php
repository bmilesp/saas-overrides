<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Controller', 'Controller');
App::uses('Domains', 'MultiTenancy.Lib');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class SaasOverridesController extends Controller {

	public $layout = 'BootstrapExtend.default';
 
	public $helpers = array(
		'Form' => array('className' => 'BootstrapExtend.BootstrapExtForm'),
		'Paginator' => array('className' => 'BoostCake.BoostCakePaginator'),
	);

	public $uses = [
		'MultiTenancy.Tenant'
	];

	public $organization_id = null;

	public function beforeFilter(){

		$subdomain = Domains::getSubdomain();

		if ($this->Tenant->domainExists($subdomain)){
			Configure::write('organization_id', $subdomain);
			$this->organization_id = $subdomain;
		}
		$Tenant = null;

		parent::beforeFilter();
	}
	
}
