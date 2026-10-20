<?php

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;
use PayPal\Exception\PayPalConnectionException;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Order;

use Stripe\Error\InvalidRequest;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class ControllerPaymentOrderStatusCheck extends Controller {
    public function ipg_payment($data)
    {
        
        $this->load->model('checkout/order');
        $order_check_status = array();
        if($data['order_id'])
        {
            try {
                $this->load->library('ipg-payment');
                if ($this->config->get('ipg_payment_mode') == 'test') {
                    $payment = (new IpgPayment($this->registry))->testEnvironment(array(
                        "merchantId" => $this->config->get('ipg_payment_marchant'),
                        "password" => $this->config->get('ipg_payment_password'),
                    ));
                } else {
                    $payment = (new IpgPayment($this->registry))->productionEnvironment(array(
                        "merchantId" => $this->config->get('ipg_payment_marchant'),
                        "password" => $this->config->get('ipg_payment_password'),
                    ));
                }
                $order_status = $payment->status_check();
                
                if($data['order_id']) 
                {   
                    $order_status->allowOriginUrl($this->config->get('config_url'))
                        ->merchantId($this->config->get('ipg_payment_marchant'))
                        ->merchantTxId($data['order_id'])  // ORDER_ID
                        ->timestamp(time());
                    $status_check =  $order_status->execute();
                                        
                    if($status_check->status == 'ERROR')
                    {
                        $order_check_status[$data['order_id']] = $this->model_checkout_order->confirm($data['order_id'],'10', json_encode((array)$status_check),false);
                        $this->log->write("CRONE :- ".$status_check->status." Status updated Order id : ".$data['order_id']);
                    }
                    
                    if($status_check->status =='CAPTURED' || $status_check->status =='SUCCESS')
                    {
                        
                        $new_status = $this->config->get('ipg_payment_order_status_id');
                        $order_check_status[$data['order_id']] = $this->model_checkout_order->confirm($data['order_id'], $new_status, json_encode((array)$status_check));
                        $this->log->write("CRONE :- ".$status_check->status." Status updated Order id : ".$data['order_id']);
                    }
                    if(!$status_check->status)
                    {
                        $new_status = $this->config->get('config_live_delivery_timeout_orderstatus');
                        $order_check_status[$data['order_id']] = $this->model_checkout_order->confirm($data['order_id'],$new_status,json_encode((array)$status_check['errors']),false);
                        $this->log->write("CRONE :- ".$status_check->status." Status updated Order id : ".$data['order_id']."Page reloaded or Issue in card data");
                    }
                    // var_dump($status_check); die;
                }
            } catch (Exception $e) {
                $this->log->write("Exception -IPG");
                $this->log->write($e->getMessage());
                $this->log->write($e->getCode());
                $this->log->write($e->getFile());
                $this->log->write($e->getLine());
            }
        }
        else{
            echo "No data found.";
        }
    }

    public function stripe_payments($data)
    {
        $this->load->model('checkout/order');
        $this->stripelog->write("##############################################################################################");
        $orders = $orders = new \ArrayObject();
        $list = null;
        
        
        // $fileSystem = new Filesystem();
        
        // try {
        //     if(!$fileSystem->exists(DIR_LOGS.'stripe/'.date('m-Y').'.log')){
        //         $fileSystem->dumpFile(DIR_LOGS.'stripe/'.date('m-Y').'.log', '');
        //         $fileSystem->touch(DIR_LOGS.'stripe/'.date('m-Y').'.log');
        //         $fileSystem->chmod(DIR_LOGS.'stripe/'.date('m-Y').'.log', 0777);
        //         $this->stripelog->setFilename('stripe/'.date('m-Y').'.log');
        //     }else{
        //         $this->stripelog->setFilename('stripe/'.date('m-Y').'.log');
        //     }
        // } catch (IOExceptionInterface $exception) {
        //     throw $exception;
        // }
        try {
            Stripe::setApiKey($this->config->get('stripe_payments_private_key'));        
            $charges    = Charge::all(array("limit" => 50));
            $order_ids = [];
            $list = new \ArrayObject(json_decode($charges->getLastResponse()->body)->data);
            if($list->count() > 0){
                $listItr = $list->getIterator();
                while ($listItr->valid()){
                    if(!empty($listItr->current()->id) && !empty($listItr->current()->metadata->order_id)){
                        $orders->append((object) array(
                            'chargeId' => $listItr->current()->id,
                            'orderId' => $listItr->current()->metadata->order_id,
                            'paid' => $listItr->current()->paid,
                            'status' => $listItr->current()->status
                        ));
                        $order_ids[] = $listItr->current()->metadata->order_id;
                    }
                    $listItr->next();
                }
            }
            if($orders->count() > 0 && isset($data['order_id'])){
                $orderItr = $orders->getIterator();
                if(in_array($data['order_id'],$order_ids))
                {
                    while ($orderItr->valid()){
                        if($orderItr->current()->orderId == $data['order_id'])
                        {
                            if(!empty($orderItr->current()->chargeId) && !empty($orderItr->current()->orderId)){
                                if($orderItr->current()->status == 'succeeded'){
                                    $new_status = 1;
                                    $this->model_checkout_order->confirm($data['order_id'], $new_status, json_encode((array)$orderItr->current()));
                                    $this->log->write($data['order_id']." record status updated to pending");
                                }elseif ($orderItr->current()->status == 'failed') {
                                    $this->model_checkout_order->confirm($data['order_id'], '10', json_encode((array)$orderItr->current()),false);
                                    $this->log->write($data['order_id']." record status updated to failed");
                                }
                            }
                        }
                        $orderItr->next();
                    }
                }
                else{
                    $this->model_checkout_order->confirm($data['order_id'],'10','Page reloaded or Issue in card data.',false);
                    $this->paypalLog->write("CRONE :- Page reloaded or Issue in card data. Status updated Order id : ".$data['order_id']."Order id id not in payment server");
                }
            }

        } catch (InvalidRequest $ex) {
            $this->log->write(($ex->getMessage()));
            $this->log->write(($ex->getJsonBody()));
        }
        $this->log->write("##############################################################################################");
    }

    public function checkPaypal()
    {
        // $this->load->model('payment/pp_rest');
        $this->load->model('checkout/order');
        // $this->load->model('checkout/transaction');
        // $startTime = date('Y-m-d\TH:i:s\Z', strtotime("-40 minutes", strtotime($lastExcecutedTime)));
        
        $fileSystem = new Filesystem();
        
        try {
            if(!$fileSystem->exists(DIR_LOGS.'paypal/'.date('m-Y').'.log')){
                $fileSystem->touch(DIR_LOGS.'paypal/'.date('m-Y').'.log');
                $fileSystem->chmod(DIR_LOGS.'paypal/'.date('m-Y').'.log', 0777);
                $this->paypalLog->setFilename('paypal/'.date('m-Y').'.log');
            }else{
                $this->paypalLog->setFilename('paypal/'.date('m-Y').'.log');
            }
        } catch (IOExceptionInterface $exception) {
            throw $exception;
        }

        $this->paypalLog->write("##############################################################################################");
        $orders = new \ArrayObject();
        $list = null;

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->config->get('pp_rest_client_id'), $this->config->get('pp_rest_client_secret')
            )
        );



        try {
            $params = array('start_time' => $startTime, 'end_time' => $lastExcecutedTime);
            $payments = Payment::all($params, $apiContext)->getPayments();
            foreach($payments as $payment){
                $this->paypalLog->write($payment);
                if($payment->getState() == 'approved'){
                    foreach ($payment->getTransactions() as $transaction){
                        $orderInfo = $this->model_payment_pp_rest->getOrder($transaction->getInvoiceNumber());
                        if($orderInfo){
                            $this->model_payment_pp_rest->confirm($orderInfo['order_id'], $this->config->get('pp_rest_order_status_id'));
                            $this->model_checkout_transaction->updateTransaction(array('verified' => 1), $this->session->data['order_id']);
                            $this->paypalLog->write($orderInfo['order_id']." record status updated to pending");
                        }
                    }
                }elseif ($payment->getState() == 'failed') {
                    foreach ($payment->getTransactions() as $transaction){
                        $orderInfo = $this->model_payment_pp_rest->getOrder($transaction->getInvoiceNumber());
                        if($orderInfo){
                            $this->model_payment_pp_rest->confirm($orderInfo['order_id'], 10);
                            $this->model_checkout_transaction->updateTransaction(array('verified' => 1), $this->session->data['order_id']);
                            $this->paypalLog->write($orderInfo['order_id']." record status updated to failed");
                        }
                    }
                }
            }
        } catch (PayPalConnectionException $ex) {
            $this->paypalLog->write(($ex->getMessage()));
            $this->paypalLog->write(($ex->getData()));
        }
        
        $this->paypalLog->write("##############################################################################################");
        $this->paypalLog->write("                                                                                              ");
    }

    public function test()
    {
        $fileSystem = new Filesystem();
        var_dump($fileSystem); die;
    }

    public function stripetest()
    {
        $this->load->model('checkout/order');
        $this->stripelog->write("##############################################################################################");
        $orders = $orders = new \ArrayObject();
        $list = null;
        $data['order_id'] = 43410;
        $fileSystem = new Filesystem();
        
        try {
            if(!$fileSystem->exists(DIR_LOGS.'stripe/'.date('m-Y').'.log')){
                $fileSystem->dumpFile(DIR_LOGS.'stripe/'.date('m-Y').'.log', '');
                $fileSystem->touch(DIR_LOGS.'stripe/'.date('m-Y').'.log');
                $fileSystem->chmod(DIR_LOGS.'stripe/'.date('m-Y').'.log', 0777);
                $this->stripelog->setFilename('stripe/'.date('m-Y').'.log');
            }else{
                $this->stripelog->setFilename('stripe/'.date('m-Y').'.log');
            }
        } catch (IOExceptionInterface $exception) {
            throw $exception;
        }
        try {
            Stripe::setApiKey($this->config->get('stripe_payments_private_key'));        
            $charges    = Charge::all(array("limit" => 150));
            $order_ids = [];
            $list = new \ArrayObject(json_decode($charges->getLastResponse()->body)->data);
            if($list->count() > 0){
                $listItr = $list->getIterator();
                while ($listItr->valid()){
                    if(!empty($listItr->current()->id) && !empty($listItr->current()->metadata->order_id)){
                        $orders->append((object) array(
                            'chargeId' => $listItr->current()->id,
                            'orderId' => $listItr->current()->metadata->order_id,
                            'paid' => $listItr->current()->paid,
                            'status' => $listItr->current()->status
                        ));
                        $order_ids[] = $listItr->current()->metadata->order_id;
                    }
                    $listItr->next();
                }
            }
            if($orders->count() > 0 && isset($data['order_id'])){
                $orderItr = $orders->getIterator();
                if(in_array($data['order_id'],$order_ids))
                {
                    while ($orderItr->valid()){
                        $this->stripelog->write(json_encode($orderItr->current()));
                        if($orderItr->current()->orderId == $data['order_id'])
                        {
                            if(!empty($orderItr->current()->chargeId) && !empty($orderItr->current()->orderId)){
                                if($orderItr->current()->status == 'succeeded'){
                                    var_dump($orderItr->current()); die;
                                }elseif ($orderItr->current()->status == 'failed') {
                                    var_dump($orderItr->current()); die;
                                }
                            }
                        }
                        $orderItr->next();
                    }
                }
                else{
                    var_dump('$orderItr->current()'); die;
                }
            }

        } catch (InvalidRequest $ex) {
            $this->stripelog->write(($ex->getMessage()));
            $this->stripelog->write(($ex->getJsonBody()));
        }
        $this->stripelog->write("##############################################################################################");
        $this->paypalLog->write("                                                                                              ");
    }

    /*    
        public function index2()
        {
            // var_dump('kjfc'); die;
            // $this->load->model('payment/pp_rest');
            $this->load->model('checkout/order');
            // $this->load->model('checkout/transaction');
            // $startTime = date('Y-m-d\TH:i:s\Z', strtotime("-40 minutes", strtotime($lastExcecutedTime)));
            
            $fileSystem = new Filesystem();
            
            try {
                if(!$fileSystem->exists(DIR_LOGS.'paypal/'.date('m-Y').'.log')){
                    $fileSystem->touch(DIR_LOGS.'paypal/'.date('m-Y').'.log');
                    $fileSystem->chmod(DIR_LOGS.'paypal/'.date('m-Y').'.log', 0777);
                    $this->paypalLog->setFilename('paypal/'.date('m-Y').'.log');
                }else{
                    $this->paypalLog->setFilename('paypal/'.date('m-Y').'.log');
                }
            } catch (IOExceptionInterface $exception) {
                throw $exception;
            }
            
            $this->paypalLog->write("##############################################################################################");
            $orders = new \ArrayObject();
            $list = null;
            $apiContext = new ApiContext(
                new OAuthTokenCredential(
                    $this->config->get('pp_express_username'), $this->config->get('pp_express_password')
                )
            );
            
            // var_dump($this->config->get('pp_express_username')); echo '<br/>'; var_dump($this->config->get('pp_express_password')); die;


            try {
                // $params = array('start_time' => $startTime, 'end_time' => $lastExcecutedTime);
                $params = array('count' => 10);
                echo 'hai';
                $payments = Payment::all($params,$apiContext)->getPayments();
                echo 'hai'; die;
                foreach($payments as $payment){
                    $this->paypalLog->write($payment);
                    if($payment->getState() == 'approved'){
                        foreach ($payment->getTransactions() as $transaction){
                            $orderInfo = $this->model_payment_pp_rest->getOrder($transaction->getInvoiceNumber());
                            if($orderInfo){
                                $this->model_payment_pp_rest->confirm($orderInfo['order_id'], $this->config->get('pp_rest_order_status_id'));
                                $this->model_checkout_transaction->updateTransaction(array('verified' => 1), $this->session->data['order_id']);
                                $this->paypalLog->write($orderInfo['order_id']." record status updated to pending");
                            }
                        }
                    }elseif ($payment->getState() == 'failed') {
                        foreach ($payment->getTransactions() as $transaction){
                            $orderInfo = $this->model_payment_pp_rest->getOrder($transaction->getInvoiceNumber());
                            if($orderInfo){
                                $this->model_payment_pp_rest->confirm($orderInfo['order_id'], 10);
                                $this->model_checkout_transaction->updateTransaction(array('verified' => 1), $this->session->data['order_id']);
                                $this->paypalLog->write($orderInfo['order_id']." record status updated to failed");
                            }
                        }
                    }
                }
            } catch (PayPalConnectionException $ex) {
                $this->paypalLog->write(($ex->getMessage()));
                $this->paypalLog->write(($ex->getData()));
            }
            
            $this->paypalLog->write("##############################################################################################");
            $this->paypalLog->write("                                                                                              ");
        }
        public function index($data)
        {   
            die('fjbgveujh'); 
            $this->load->model('checkout/order');
            $this->stripelog->write("##############################################################################################");
            $orders = $orders = new \ArrayObject();
            $list = null;
            $fileSystem = new Filesystem();
            try {
                if(!$fileSystem->exists(DIR_LOGS.'stripe/'.date('m-Y').'.log')){
                    $fileSystem->touch(DIR_LOGS.'stripe/'.date('m-Y').'.log');
                    $fileSystem->chmod(DIR_LOGS.'stripe/'.date('m-Y').'.log', 0777);
                    $this->stripelog->setFilename('stripe/'.date('m-Y').'.log');
                }else{
                    $this->stripelog->setFilename('stripe/'.date('m-Y').'.log');
                }
            } catch (IOExceptionInterface $exception) {
                throw $exception;
            }

            try {
                Stripe::setApiKey($this->config->get('stripe_payments_private_key'));
                $charges    = Charge::all();
                $order_ids = [];
                $list = new \ArrayObject(json_decode($charges->getLastResponse()->body)->data);
                if($list->count() > 0){
                    $listItr = $list->getIterator();
                    while ($listItr->valid()){
                        if(!empty($listItr->current()->id) && !empty($listItr->current()->metadata->order_id)){
                            $orders->append((object) array(
                                'chargeId' => $listItr->current()->id,
                                'orderId' => $listItr->current()->metadata->order_id,
                                'paid' => $listItr->current()->paid,
                                'status' => $listItr->current()->status
                            ));
                            $order_ids[] = $listItr->current()->metadata->order_id;
                        }
                        $listItr->next();
                    }
                }
                if($orders->count() > 0 && isset($data['order_id'])){
                    $orderItr = $orders->getIterator();
                    if(in_array($data['order_id'],$order_ids))
                    {
                        while ($orderItr->valid()){
                            $this->stripelog->write(json_encode($orderItr->current()));
                            if($orderItr->current()->orderId == $data['order_id'])
                            {
                                if(!empty($orderItr->current()->chargeId) && !empty($orderItr->current()->orderId)){
                                    if($orderItr->current()->status == 'succeeded'){
                                        echo '<pre>'; print_r($orderItr->current()); die;
                                        $new_status = $this->config->get('ipg_payment_order_status_id');
                                        $this->model_checkout_order->updateOrderStatusCron($data['order_id'], $new_status, json_encode((array)$orderItr->current()));
                                        $this->stripelog->write($data['order_id']." record status updated to pending");
                                    }elseif ($orderItr->current()->status == 'failed') {
                                        $this->model_checkout_order->updateOrderStatusCron($data['order_id'], '10', json_encode((array)$orderItr->current()));
                                        $this->stripelog->write($data['order_id']." record status updated to failed");
                                    }
                                }
                            }
                            $orderItr->next();
                        }
                    }
                    else{
                        $this->model_checkout_order->updateOrderStatusCron($data['order_id'],'10','Order Id is not fount in the payment server.');
                        $this->paypalLog->write("CRONE :- Order Id is not fount in the payment server. Status updated Order id : ".$data['order_id']."Order id id not in payment server");
                    }
                }

            } catch (InvalidRequest $ex) {
                $this->stripelog->write(($ex->getMessage()));
                $this->stripelog->write(($ex->getJsonBody()));
            }
            $this->stripelog->write("##############################################################################################");
            $this->paypalLog->write("                                                                                              ");
        }
    */
}

?>