<?php

class ControllerPaymentPaymentSenseCybersource extends Controller {
    /**
	 * Content types of the output of the module information
	 */
	const TYPE_APPLICATION_JSON = 'application/json';
	const TYPE_TEXT_PLAIN       = 'text/plain';

	/**
	 * OpenCart front routes
	 */
	const ROUTE_CHECKOUT_CART     = 'payment/paymentsense_cybersource/failure';
	const ROUTE_CHECKOUT_SUCCESS  = 'checkout/success';
	const ROUTE_CUSTOMER_REDIRECT = 'payment/paymentsense_cybersource/customerredirect';

	/**
	 * Supported content types of the output of the module information
	 *
	 * @var array
	 */
	protected $content_types = array(
		'json' => self::TYPE_APPLICATION_JSON,
		'text' => self::TYPE_TEXT_PLAIN
	);
        private $log;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('paymentsensecybersource.log');
    }

    protected function index() {
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['button_back'] = $this->language->get('button_back');
        $this->language->load('payment/paymentsense_cybersource');
        $this->load->model('payment/paymentsense_cybersource');
        list($error_message, $form_data, $order_id) = $this->model_payment_paymentsense_cybersource->getPaymentFormData();

        $this->data['back'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?route=checkout/checkoutetm');
        if ($error_message) {
            
             $this->data['error_message']= json_encode($error_message);
             $this->data['title']=  $this->getConfigValue('paymentsenserp_title');
             $this->data['message']=  '';
             $this->data['payment_details_amount']=  '';
             $this->data['payment_details_currency_code']=  '';
             $this->data['payment_details_payment_token']=  '';
             $this->data['return_url']=  '';
              $this->data['client_js_url']=  '';
             $this->data['button_confirm']=  $this->language->get('button_confirm');
          
        } else {
             $this->data['error_message']= '';
                $this->data['title']= $this->getConfigValue('paymentsenserp_title');
                $this->data['message']= $this->getConfigValue('paymentsenserp_description');
                $this->data['payment_details_amount']= $form_data['amount'];
                $this->data['payment_details_currency_code']= $form_data['currency_code'];
                $this->data['payment_details_payment_token']= $form_data['access_token'];
                $this->data['return_url']= $this->getCustomerRedirectUrl($order_id);
                $this->data['client_js_url']= $this->model_payment_paymentsense_cybersource->getClientJsUrl();
                $this->data['button_confirm']= $this->language->get('button_confirm');
      
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paymentsense_cybersource.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/paymentsense_cybersource.tpl';
        } elseif (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/payment/paymentsense_cybersource.tpl')) {
            $this->template = $this->config->get('config_template') . '/payment/paymentsense_cybersource.tpl';
        } else {
            $this->template = 'default/template/payment/paymentsense_cybersource.tpl';
        }

        $this->render();
    }

    /**
	 * Customer Redirect action handler handling redirection of the customer after completing the payment
	 */
	public function customerredirect() {
		$this->language->load('payment/paymentsense_cybersource');
		$this->load->model('payment/paymentsense_cybersource');

		$error_message = '';
		$session_data  = $this->session->data;
		$order_id      = isset($session_data['order_id'])
			? (string)$session_data['order_id']
			: $this->getHttpVar('order_id', ModelPaymentPaymentSenseCybersource::API_METHOD_GET);
		if ($order_id) {
			$access_token = $this->getHttpVar('accessToken');
			if (empty($access_token)) {
				$access_token = $this->getHttpVar('paymentToken');
			}
			try {
                              //  $this->log->write(" customer redirect : " . $order_id . " " . print_r($access_token,true) );
				$error_message = $this->model_payment_paymentsense_cybersource->processGatewayResponse($order_id, $access_token);
			} catch (Exception $exception) {
				$error_message = sprintf($this->language->get('error_exception'), $exception->getMessage());
			}
		}
                $this->log->write(" paymentsense_cybersource  : " . $order_id . " " . print_r($error_message,true) );
		$this->session->data['error_paymentsense_cybersource'] = $error_message ? $error_message : '';
		$route = $error_message ? self::ROUTE_CHECKOUT_CART : self::ROUTE_CHECKOUT_SUCCESS;
		$this->response->redirect($this->url->link($route, '', 'SSL'));
	}

	/**
	 * Module Information action handler
	 */
	public function info() {
		$this->load->model('payment/paymentsense_cybersource');
		$info = array(
			'Module Name'              => $this->model_payment_paymentsense_cybersource->getModuleName(),
			'Module Installed Version' => $this->model_payment_paymentsense_cybersource->getModuleInstalledVersion()
		);
		if ($this->getRequestParameter('extended_info', '') === 'true') {
			$extended_info = array(
				'OpenCart Version' => $this->model_payment_paymentsense_cybersource->getOpenCartVersion(),
				'PHP Version'      => $this->model_payment_paymentsense_cybersource->getPhpVersion()
			);
			$info = array_merge($info, $extended_info);
		}
		$this->outputInfo($info);
	}

	/**
	 * Checksums action handler
	 */
	public function checksums() {
		$info = array(
			'Checksums' => $this->getFileChecksums()
		);
		$this->outputInfo($info);
	}

	/**
	 * Gets the URL of the page where the customer will be redirected after completing the payment
	 *
	 * @param string $order_id Order ID
	 *
	 * @return string A string containing URL
	 */
	protected function getCustomerRedirectUrl($order_id) {
		return str_replace(
			'&amp;',
			'&',
			$this->url->link(self::ROUTE_CUSTOMER_REDIRECT, 'order_id=' . $order_id, 'SSL'
			)
		);
	}

	/**
	 * Gets the value of a parameter from the extension configuration settings
	 *
	 * @param string      $key     Configuration key
	 * @param string|null $default Default value
	 *
	 * @return string|null
	 */
	protected function getConfigValue($key, $default = null) {
		$key = "payment_{$key}";
		$value = $this->config->get($key);
		if (is_null($value) && !is_null($default)) {
			$value = $default;
		}
		return $value;
	}

	/**
	 * Gets the value of an HTTP GET/POST parameter
	 *
	 * @param string      $parameter HTTP GET/POST parameter
	 * @param string|null $method    HTTP method
	 *
	 * @return mixed
	 */
	protected function getHttpVar($parameter, $method = null) {
		if (empty($method)) {
			$method = $this->request->server['REQUEST_METHOD'];
		}
		switch ($method) {
			case 'GET':
				return array_key_exists($parameter, $this->request->get)
					? $this->request->get[$parameter]
					: '';
			case 'POST':
				return array_key_exists($parameter, $this->request->post)
					? $this->request->post[$parameter]
					: '';
			default:
				return '';
		}
	}

	/**
	 * Gets the value of a parameter from the route
	 *
	 * @param string $parameter Parameter
	 * @param string $default   Default value
	 *
	 * @return string
	 */
	protected function getRequestParameter($parameter, $default = '') {
		$result = $default;
		if (isset($this->request->get['route'])) {
			$route = $this->request->get['route'];
			if (preg_match('#/' . $parameter .'/([a-z0-9]+)#i', $route, $matches)) {
				$result = $matches[1];
			}
		}
		return $result;
	}

	/**
	 * Converts an array to string
	 *
	 * @param array  $arr   An associative array
	 * @param string $ident Indentation
	 *
	 * @return string
	 */
	protected function convertArrayToString($arr, $ident = '') {
		$result         = '';
		$indent_pattern = '  ';
		foreach ($arr as $key => $value) {
			if ('' !== $result) {
				$result .= PHP_EOL;
			}
			if (is_array($value)) {
				$value = PHP_EOL . $this->convertArrayToString($value, $ident . $indent_pattern);
			}
			$result .= $ident . $key . ': ' . $value;
		}
		return $result;
	}

	/**
	 * Outputs plugin information
	 *
	 * @param array $info Module information
	 */
	protected function outputInfo($info) {
		$output       = $this->getRequestParameter('output', 'text');
		$content_type = array_key_exists($output, $this->content_types)
			? $this->content_types[ $output ]
			: self::TYPE_TEXT_PLAIN;

		switch ($content_type) {
			case self::TYPE_APPLICATION_JSON:
				$body = json_encode($info);
				break;
			case self::TYPE_TEXT_PLAIN:
			default:
				$body = $this->convertArrayToString($info);
				break;
		}

		@header('Cache-Control: max-age=0, must-revalidate, no-cache, no-store', true);
		@header('Pragma: no-cache', true);
		@header('Content-Type: ' . $content_type, true);
		echo $body;
		exit;
	}

	/**
	 * Gets file checksums
	 *
	 * @return array
	 */
	protected function getFileChecksums() {
		$result = array();
		$root_path = realpath(__DIR__ . '/../../../..');
		$file_list = $this->getHttpVar('data');
		if (is_array($file_list)) {
			foreach ($file_list as $key => $file) {
				$filename = $root_path . '/' . $file;
				$result[$key] = is_file($filename)
					? sha1_file($filename)
					: null;
			}
		}
		return $result;
	}
        public function failure() {
        $this->language->load('payment/paymentsense_cybersource');
        //Setup page headers
        $isSecure = isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] == 'on';
        $this->data['base'] 			 = ($isSecure ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['language']  		 = $this->language->get('code');
        $this->data['direction'] 		 = $this->language->get('direction');
        //Page titles
        $this->data['title'] 			 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['heading_title'] 	 = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['text_response'] 	 = $this->language->get('text_response');
        $this->data['error_response'] 	 = isset($this->session->data['error_paymentsense_cybersource'])?$this->session->data['error_paymentsense_cybersource']:'';
        //Error text
        $this->data['text_failure']      = $this->language->get('text_failure');
        $this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkoutetm'));
        //Mismatch
        $this->data['continue'] = $this->url->link('checkout/checkoutetm');
        $this->template = $this->config->get('config_template') . '/template/payment/paymentsense_cybersource_failure.tpl';
        $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
        );
        unset($this->session->data['error_paymentsense_cybersource']);
        $this->response->setOutput($this->render());
    }
}

?>