<?php
require_once( DIR_SYSTEM . 'vendor/stuart/vendor/autoload.php');

class ModelModuleStuart extends Model {
    private $log;
    private $client;
    public function __construct($registry) {
        parent::__construct($registry);
        $this->log = new Log('stuart.log');
    }
    private function initialize() {
        if($this->config->get('config_stuart_mode')) {
            $environment = \Stuart\Infrastructure\Environment::SANDBOX;
        }
        else {
            $environment = \Stuart\Infrastructure\Environment::PRODUCTION;
        }
        
       // $this->log->write('environment :' . print_r($environment, true));
        $authenticator = new \Stuart\Infrastructure\Authenticator($environment, $this->config->get('config_stuart_client_id'), $this->config->get('config_stuart_client_secret'));

        $httpClient = new \Stuart\Infrastructure\HttpClient($authenticator);

        $this->client = new \Stuart\Client($httpClient);
    }
    public function createJob($order,$package_size='',$pickuptime='',$package_description='') {
        $this->log->write('$pickuptime a :'.print_r($pickuptime, true)); 
        
        // check order job created
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stuart_delivery` WHERE order_id = '" . (int)$order['order_id'] . "'");
        if($query->num_rows) {
             return ['error'=>'order already assigned','message'=>'Already assigned'];
        }
        $this->initialize();
       
        $job = new \Stuart\Job();

        if($pickuptime=='') {
            $job->addPickup(str_replace(PHP_EOL, ' ',$this->config->get('config_address')).'. '. $this->config->get('config_postalcode'))  // . ' ' . $order['shipping_country']
            //   ->setComment('Wait outside for an employee to come.')
               ->setContactCompany($this->config->get('config_name'))
            //   ->setContactFirstName('Martin')
            //   ->setContactLastName('Pont')
                ->setContactPhone($this->config->get('config_telephone'));
        }else {
            $pickupAt = new \DateTime('now', new DateTimeZone('Europe/London'));
            $pickupAt->setTimestamp(strtotime($pickuptime));
          //  $pickupAt->setTimestamp(strtotime($pickuptime)); // ->add(new \DateInterval('PT2H'))

            $job->addPickup(str_replace(PHP_EOL, ' ',$this->config->get('config_address')).'. '. $this->config->get('config_postalcode'))  // . ' ' . $order['shipping_country']
               ->setPickupAt($pickupAt)
               ->setContactCompany($this->config->get('config_name'))
               ->setContactPhone($this->config->get('config_telephone'));
        }
        if($package_size=='') {
            $package_size = $this->config->get('config_stuart_package_size');
        }
        if($package_description=='') {
            $package_description = $this->config->get('config_stuart_package_description');
        }
        
        if($package_description=='') {
            $package_description= 'Pizza box.';
        }
        $job->addDropOff($order['shipping_address_1']. ($order['shipping_address_2']? '. ' .$order['shipping_address_2']:'').
                '. ' . $order['shipping_city'] . '. '. $order['shipping_postcode'] ) // . ' ' . $order['shipping_country']
            ->setPackageType($package_size)
            ->setComment($order['comment'])
            ->setContactFirstName($order['shipping_firstname'])
            ->setContactLastName($order['shipping_lastname'])
            ->setContactPhone($order['telephone'])
            ->setPackageDescription($package_description)
            ->setClientReference($order['order_id']); // Must be unique

         $this->log->write('createJob :'.print_r($job, true)); 

        $createdJob = $this->client->createJob($job);
      $this->log->write('$createdJob :'.print_r($createdJob, true));
        if ($createdJob instanceof \Stuart\Job) {
            $jobid = $createdJob->getId();
           // $this->log->write('$createdJob->getId :'.print_r($jobid, true));
                
           // $this->log->write('$createdJob->getStatus :'.print_r($createdJob->getStatus(), true));
            $distance = $createdJob->getDistance();
          //  $this->log->write('$createdJob->getDistance :'.print_r($distance, true));
            $PriceTaxIncluded = $createdJob->getPricing()->getPriceTaxIncluded();
          //  $this->log->write('$createdJob->getPricing->getPriceTaxIncluded :'.print_r($PriceTaxIncluded, true));
            
            $PriceTaxExcluded  = $createdJob->getPricing()->getPriceTaxExcluded();
         //   $this->log->write('$createdJob->getPricing->getPriceTaxExcluded :'.print_r($PriceTaxExcluded, true));
            $deliveries = $createdJob->getDeliveries();
            foreach($deliveries as $deliverie) {
                $deliverie_id = $deliverie->getId();
            //    $this->log->write('$deliverie->getId :'.print_r($deliverie_id, true));
                $deliverie_url = $deliverie->getTrackingUrl();
            //    $this->log->write('$deliverie->getTrackingUrl :'.print_r($deliverie_url, true));
                $deliverie_status = $deliverie->getStatus();
            //    $this->log->write('$deliverie->getStatus :'.print_r($deliverie_status, true));
                
                $this->db->query("INSERT INTO `" . DB_PREFIX . "stuart_delivery` SET order_id = '" . $order['order_id'] . "',delivery_id= '" . $this->db->escape($deliverie_id) . "',jobid= '" . $this->db->escape($jobid) . "',status= '" . $this->db->escape($deliverie_status) . "',tracking_url= '" . $this->db->escape($deliverie_url) . "',distance= '" . $this->db->escape($distance) . "',price_tax_included= '" . $this->db->escape($PriceTaxIncluded) . "',price_tax_excluded= '" . $this->db->escape($PriceTaxExcluded) . "',date_added = NOW(), date_modified = NOW()");
            }
            return ['success'=>['id'=>$jobid]];
//            }else {
//                return false;
//            }
        }else {
             
            $this->log->write('There was an error creating the job  :'.print_r($createdJob, true));
            $resonse = (array) $createdJob;
            $this->log->write('There was an error $resonse :'.print_r($resonse, true));
            return $resonse;
        }
        
    }
    public function cancelDelivery($order_id) {
        // check order job created
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stuart_delivery` WHERE order_id = '" . (int)$order_id . "'");
        if(!$query->num_rows) {
             return ['error'=>'order not found','message'=>'order not found in stuart db'];
        }
        $this->initialize();
        $resonse = $this->client->cancelDelivery($query->row['delivery_id']);
        $this->log->write(' $cancelDelivery :'.print_r($resonse, true));
        return $resonse;
    }
    public function getDelivery($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stuart_delivery` WHERE order_id = '" . (int)$order_id . "'");
        return $query->row;
    }
    public function setDeliveryStatus($delivery_id,$status,$driver) {
        if($driver) {
            $this->db->query("UPDATE `" . DB_PREFIX . "stuart_delivery` SET status= '" . $this->db->escape($status) ."',driver =  '" . $this->db->escape($driver) ."', date_modified = NOW() WHERE delivery_id = '" . $delivery_id. "'");
        }else {
            $this->db->query("UPDATE `" . DB_PREFIX . "stuart_delivery` SET status= '" . $this->db->escape($status) ."', date_modified = NOW() WHERE delivery_id = '" . $delivery_id. "'");
        }
        return true;
    }
    
}
?>