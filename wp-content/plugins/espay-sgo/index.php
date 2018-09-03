<?php
/*
  Plugin Name: ESPay Payment Gateways
  Plugin URI: http://sgo.co.id
  Description: Accept payments for your products via ESPay Payment Gateways
  Version: 1.1
  Author: PT. Square Gate One
  Author URI: http://sgo.co.id
 */

if (!defined('ABSPATH'))
    exit;

add_action('plugins_loaded', 'woocommerce_espay_init', 0);

function woocommerce_espay_init() {

    if (!class_exists('WC_Payment_Gateway'))
        return;

    class WC_Gateway_eSpay extends WC_Payment_Gateway {

        public function __construct() {

            //plugin id
            $this->id = 'espay';
            //Payment Gateway title
            $this->method_title = 'ESPay Payment Gateways';
            //true only in case of direct payment method, false in our case
            $this->has_fields = false;
            //payment gateway logo
            $this->icon = plugins_url('/sgo1.png', __FILE__);

            //redirect URL
            $this->redirect_url = str_replace('https:', 'http:', add_query_arg('wc-api', 'WC_Gateway_eSpay', home_url('/')));

            //Load settings
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables

            $this->enabled = $this->settings['enabled'];
//
//            $espayproductOri = WC()->session->get( 'espayproduct' );
//			$espayproduct = stripslashes($espayproductOri);
//			$valJsonPost1 = json_decode($espayproduct);
//
//			$this->productNameP = $productName1 = ' - '.$valJsonPost1->productName;
//			$this->title        = "ESPay Payment Gateways". $this->productNameP;
            $this->title = "ESPay Payment Gateways";

            $this->description = $this->settings['description'];
            $this->apikey = $this->settings['apikey'];
            $this->password = $this->settings['password'];
            $this->processor_id = $this->settings['processor_id'];
            $this->salemethod = $this->settings['salemethod'];
            $this->gatewayurl = $this->settings['gatewayurl'];
            $this->order_prefix = $this->settings['order_prefix'];
            $this->debugon = $this->settings['debugon'];
            $this->debugrecip = $this->settings['debugrecip'];
            $this->cvv = $this->settings['cvv'];
//            $this->ipaddress          = $this->settings['ipaddress'];
            $this->paymentpassword = $this->settings['paymentpassword'];
            $this->signatureKey = $this->settings['signatureKey'];
            $this->environment = $this->settings['environment'];
            $this->min_order_total = $this->settings['min_order_total'];
            $this->max_order_total = $this->settings['max_order_total'];
            $this->creditcardfee = $this->settings['creditcardfee'];
//            $this->fee = $this->settings['fee'];
            $this->fee_bca_klikpay = $this->settings['fee_bca_klikpay'];
            $this->fee_bri = $this->settings['fee_bri'];
            $this->fee_bri_atm = $this->settings['fee_bri_atm'];
            $this->fee_mandiri_ib = $this->settings['fee_mandiri_ib'];
            $this->fee_mandiri_ecash = $this->settings['fee_mandiri_ecash'];
            $this->fee_mandiri_atm = $this->settings['fee_mandiri_atm'];
            $this->fee_credit_card = $this->settings['fee_credit_card'];
            $this->fee_permata_atm = $this->settings['fee_permata_atm'];
            $this->fee_danamon_atm = $this->settings['fee_danamon_atm'];
            $this->fee_danamon_ob = $this->settings['fee_danamon_ob'];
            $this->fee_cimb_atm = $this->settings['fee_cimb_atm'];
            $this->fee_dki_ib = $this->settings['fee_dki_ib'];
            $this->fee_xl_tunai = $this->settings['fee_xl_tunai'];
            $this->fee_bii_atm = $this->settings['fee_bii_atm'];
            $this->fee_bnidbo = $this->settings['fee_bnidbo'];
            $this->fee_bni_atm = $this->settings['fee_bni_atm'];
            $this->fee_permata_net_pay = $this->settings['fee_permata_net_pay'];
            $this->fee_nobupay = $this->settings['fee_nobupay'];
            $this->fee_finpay = $this->settings['fee_finpay'];
            $this->fee_mandiri_sms = $this->settings['fee_mandiri_sms'];
            $this->fee_mayapada_ib = $this->settings['fee_mayapada_ib'];
            $this->fee_mualamatatm = $this->settings['fee_mualamatatm'];
            $this->fee_bitcoin = $this->settings['fee_bitcoin'];



//            $this->bankmayapada       = $this->get_option( 'bankmayapada' );
//		    $this->bankdki       = $this->get_option( 'bankdki' );
//		    define("BANK_MAYAPADA", ($this->bankmayapada=='yes'? false : true));
//		    define("BANK_DKI", ($this->bankdki=='yes'? false : true));
            // Actions
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
            add_action('woocommerce_receipt_espay', array(&$this, 'receipt_page'));

            // Payment listener/API hook
            add_action('woocommerce_api_wc_gateway_espay', array($this, 'check_espay_response'));
        }

//		function my_custom_checkout_field( $checkout ) {
//
//			echo '<div id="my_custom_checkout_field"><h3>'.__('Test Agung').'</h3>';
//
//			woocommerce_form_field( 'my_field_name', array(
//			'type' => 'text',
//			'class' => array('my-field-class form-row-wide'),
//			'placeholder' => __('Enter your Trade Account Number'),
//			), $checkout->get_value( 'my_field_name' ));
//
////			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
//			echo '</div>';
//
//		}

        function init_form_fields() {

            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woothemes'),
                    'label' => __('Enable ESPay', 'woothemes'),
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => __('Title', 'woothemes'),
                    'type' => 'text',
                    'description' => __('', 'woothemes'),
                    'default' => __('Pembayaran ESPay', 'woothemes')
                ),
                'description' => array(
                    'title' => __('Description', 'woothemes'),
                    'type' => 'textarea',
                    'description' => __('', 'woothemes'),
                    'default' => 'Sistem pembayaran menggunakan ESPay.'
                ),
                'apikey' => array(
                    'title' => __('Payment Key', 'woothemes'),
                    'type' => 'text',
                    'description' => __(' ', 'woothemes'),
                    'default' => ''
                ),
//                'ipaddress' => array(
//                                'title' => __( 'Payment Ip Address', 'woothemes' ),
//                                'type' => 'text',
//                                  'description' => __( ' ', 'woothemes' ),
//                                'default' => ''
//                            ),
                'paymentpassword' => array(
                    'title' => __('Service Password', 'woothemes'),
                    'type' => 'password',
                    'description' => __(' ', 'woothemes'),
                    'default' => ''
                ),
                'signatureKey' => array(
                    'title' => __('Signature Key', 'woothemes'),
                    'type' => 'password',
                    'description' => __(' ', 'woothemes'),
                    'default' => ''
                ),
                'environment' => array(
                    'title' => __('Environment', 'woocommerce'),
                    'type' => 'select',
                    'description' => '',
                    'default' => 'development',
                    'options' => array(
                        'development' => 'Sandbox',
                        'production' => 'Production',
                    ),
                ),
//                 'bankmayapada' => array(
//								  'title'       => __( 'Mayapada Internet Banking', 'woocommerce' ),
//								  'type'        => 'checkbox',
//								  'label'       => __( 'Internet Banking', 'woocommerce' ),
//								  'default'     => 'no',
//								  'description' => __( '', 'woocommerce' )
//			  	),
//                 'bankdki' => array(
//								  'title'       => __( 'DKI Internet Banking', 'woocommerce' ),
//								  'type'        => 'checkbox',
//								  'label'       => __( 'Internet Banking', 'woocommerce' ),
//								  'default'     => 'no',
//								  'description' => __( '', 'woocommerce' )
//			  	),
                'min_order_total' => array(
                    'title' => __('Minimum Order Total', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'max_order_total' => array(
                    'title' => __('Maximum Order Total', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'creditcardfee' => array(
                    'title' => __('Credit Card Fee %', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
//				'fee' => array(
//				        'title'     => __( 'Fee', 'woocommerce' ),
//				        'type'       => 'price',
//				        'placeholder'  => wc_format_localized_price( 0 ),
//				        'description'   => __( '', 'woocommerce' ),
//				        'default'     => '0',
//				        'desc_tip'    => true
//				      ),
                'fee_bca_klikpay' => array(
                    'title' => __('Transaction Fee BCA KlikPay', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_bri' => array(
                    'title' => __('Transaction Fee Epay Bri', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_bri_atm' => array(
                    'title' => __('Transaction Fee BRI ATM', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_mandiri_ib' => array(
                    'title' => __('Transaction Fee Mandiri Internet Banking', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_mandiri_ecash' => array(
                    'title' => __('Transaction Fee Mandiri Ecash', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_mandiri_atm' => array(
                    'title' => __('Transaction Fee Mandiri ATM', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_credit_card' => array(
                    'title' => __('Transaction Fee Credit Card', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_permata_atm' => array(
                    'title' => __('Transaction Fee Permata ATM', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_danamon_ob' => array(
                    'title' => __('Transaction Fee Danamon OB', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_danamon_atm' => array(
                    'title' => __('Transaction Fee Danamon ATM', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_cimb_atm' => array(
                    'title' => __('Transaction Fee CIMB ATM', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_dki_ib' => array(
                    'title' => __('Transaction Fee DKI Internet Banking', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_xl_tunai' => array(
                    'title' => __('Transaction Fee XL Tunai', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_bii_atm' => array(
                    'title' => __('Transaction Fee BII ATM', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_bnidbo' => array(
                    'title' => __('Transaction Fee BNI Debit Online', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_bni_atm' => array(
                    'title' => __('Transaction Fee BNI ATM', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_permata_net_pay' => array(
                    'title' => __('Transaction Fee Permata Net Pay', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_nobupay' => array(
                    'title' => __('Transaction Fee Nobupay', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_finpay' => array(
                    'title' => __('Transaction Fee Finpay', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_mandiri_sms' => array(
                    'title' => __('Transaction Fee Mandiri SM', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_mayapada_ib' => array(
                    'title' => __('Transaction Fee Mayapada Internet Banking', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_mualamatatm' => array(
                    'title' => __('Transaction Fee MUAMALAT ATM', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
                'fee_bitcoin' => array(
                    'title' => __('Transaction Fee Bitcoin', 'woocommerce'),
                    'type' => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('', 'woocommerce'),
                    'default' => '0',
                    'desc_tip' => true
                ),
            );
        }

        public function admin_options() {
            echo '<table class="form-table">';
            $this->generate_settings_html();
            echo '</table>';
        }

        private function callApiProduct() {
            $url = $this->environment == 'production' ? 'https://api.espay.id/rest/merchant/merchantinfo' : 'https://sandbox-api.espay.id/rest/merchant/merchantinfo';
//	        $url = 'http://116.90.162.170:10809/rest/merchant/merchantinfo';
//	        $key =   Mage::getStoreConfig('payment/espay/paymentid');
            $key = $this->apikey; //'7ea1d02c9fab152d9c82c9415870b876';
            $request = 'key=' . $key;

            $url = $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

            curl_setopt($curl, CURLOPT_HEADER, false);
            // curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // use http 1.1
            curl_setopt($curl, CURLOPT_TIMEOUT, 60);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
            // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            // NOTE: skip SSL certificate verification (this allows sending request to hosts with self signed certificates, but reduces security)
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            // enable ssl version 3
            // this is added because mandiri ecash case that ssl version that have been not supported before
            curl_setopt($curl, CURLOPT_SSLVERSION, 1);

            curl_setopt($curl, CURLOPT_VERBOSE, true);
            // save to temporary file (php built in stream), cannot save to php://memory
            $verbose = fopen('php://temp', 'rw+');
            curl_setopt($curl, CURLOPT_STDERR, $verbose);

            $response = curl_exec($curl);

            $response = json_decode($response);

            return $response->data;
        }

//		function endo_handling_fee() {
//		     global $woocommerce;
//
//		     if ( is_admin() && ! defined( 'DOING_AJAX' ) )
//		          return;
//
////		          $this -> current_gateway_extra_charges = 5;
//		     $fee = 5.00;
//		     $test = $woocommerce->cart->add_fee( 'Handling', $fee, true, 'standard' );
//		     return $test;
//		}

        function payment_fields() {
            global $woocommerce;
            $callApiProduct = $this->callApiProduct();
//        	echo woocommerce_price($this -> current_gateway_extra_charges);

            echo'<h4><b>Online Payment</b></h4>';

            if (empty($callApiProduct)) {
                echo 'There\'s something wrong here, please check your api key';
            } else {

                foreach ($callApiProduct as $value) {
                    $valJson = json_encode($value);
                    $valJsonPost = json_decode($valJson);

                    $valJsonPost->bankCode;
                    $valJsonPost->productCode;
                    $valJsonPost->productName;
                    ?>

                                                                                                                                                                                                                                                                                <!--	        	<input type="radio" class="input-radio" name="espayproduct" id="espayproduct-<?= $valJsonPost->productCode; ?>" value='{"productName":"<?= $valJsonPost->productName ?>","bankCode":"<?= $valJsonPost->bankCode ?>","productCode":"<?= $valJsonPost->productCode ?>"}'>-->
                    <label for="espayproduct-<?= $valJsonPost->productCode; ?>" style="display: inline;">
                        <?php
//					$src = "https://secure.sgo.co.id/images/products/".$valJsonPost->productCode.".png";
//					echo '<img src="' .$src.'" alt="' . esc_attr__('', 'woocommerce' ) . '" height="' . esc_attr( $image_size[1] ) . '" width="' . esc_attr( $image_size[0] ) . '" style="vertical-align:middle; margin-right: 10px;" />';
//					echo 'Payment using '.$valJsonPost->productName ;
                        ?>
                        <input type="radio" class="input-radio" name="espayproduct" id="espayproduct-<?= $valJsonPost->productCode; ?>" value='{"productName":"<?= $valJsonPost->productName ?>","bankCode":"<?= $valJsonPost->bankCode ?>","productCode":"<?= $valJsonPost->productCode ?>"}'>
                        <?php
                        echo'
				   		<img align="middle" src="https://kit.espay.id/images/products/' . $valJsonPost->productCode . '.png" width="100" height="90" style="border-radius:30px;background:#7dd4e1;padding:15px;border:4px solid #fff;"/>
						Payment Using ' . $valJsonPost->productName . '';
                        ?>

                    </label></p>
                    <?php
                }
            }
            ?>
            <div align=center>
                Powered by <a href="http://www.espay.id/"> <b><font color="#7dd4e1">espay.id</font></b></a>
            </div>

            <?php
        }

        function receipt_page($order) {
            global $woocommerce;
            $order = new WC_Order($order_id);
            $cart_url = $woocommerce->cart->get_cart_url();
            $checkout = $woocommerce->cart->get_checkout_url();

            echo $this->generate_espay_form($order);
        }

        private function get_post($name) {
            if (isset($_POST[$name]))
                return $_POST[$name];
            else
                return NULL;
        }

        function process_payment($order_id) {
//            global $woocommerce;
            global $woo_cp, $woocommerce;

            $order = new WC_Order($order_id);

            WC()->session->set('espayproduct', $this->get_post('espayproduct'));
            WC()->session->set('order_id_get', $order_id);

            if ($this->apikey != '') { //sukses
//					$order->reduce_order_stock();
//					WC()->cart->empty_cart();
                $order->add_order_note(__("Sedang menunggu konfirmasi dan pembayaran", 'woothemes'));
                return array(
                    'result' => 'success',
                    'redirect' => $order->get_checkout_payment_url(true)
                        //				'redirect' => $this->get_return_url( $order ),
                );
            } else { //error submit
                $order->add_order_note(__("ESPay Payment failed. Some trouble happened.", 'woothemes') . $response['err_code'] . ':' . $this->apikey);
                wc_add_notice(__('No response from payment gateway server. Try again later or contact the site administrator.', 'woothemes') . '- ' . $this->apikey, $notice_type = 'error');
            }
        }

        public function password() {
            return $this->paymentpassword;
        }

        public function sigKey() {
            return $this->signatureKey;
        }

        public function creditcardfee() {
            return $this->creditcardfee;
        }

        public function validate_fields() {
            // SET GLOBAL VARS
//			global $woocommerce;
            global $woo_cp, $woocommerce;

            // CHECK FOR MISSING FIELDS
            $espayproduct = $this->get_post('espayproduct');
            if (empty($espayproduct)) {
                wc_add_notice('Please Select Payment Method.', 'error');
                return false;
            }

            // CHECK FOR NO ERRORS
            //if(!$woocommerce->error_count()){
            if (!wc_get_notices('error')) {
                return true;
            } else {
                // NO VALID
            }
        }

        public function generate_espay_form($order_id) {
            global $woo_cp, $woocommerce, $wpdb;

            $_prefix = $wpdb->prefix;

            $order = new WC_Order($order_id);

            $espayproductOri = WC()->session->get('espayproduct');
            $order_id_get = WC()->session->get('order_id_get');
            $espayproduct = stripslashes($espayproductOri);

            $valJsonPost = json_decode($espayproduct);
            $productName = $valJsonPost->productName;
            $bankCode = $valJsonPost->bankCode;
            $productCode = $valJsonPost->productCode;

            $urlsite = get_site_url();

            if ($productCode == 'PERMATAATM' || $productCode == 'MUAMALATATM' || $productCode == 'BIIATM'  || $productCode == 'BCAATM' || $productCode == 'BNIATM' || $productCode == 'MANDIRIATM' || $productCode == 'BRIATM' || $productCode == 'CIMBATM' || $productCode == 'FINPAY195') {
                $urlplugin = '/wp-content/plugins/espay-sgo/notif/notif-atm.php?order=' . $order_id_get . '';
            } else {
                $urlplugin = '/wp-content/plugins/espay-sgo/notif/notif-ib.php?order=' . $order_id_get . '';
            }

            $redirect = $urlsite . $urlplugin;

            $_order_id_get = $order_id_get;

            $sql = "SELECT *
			FROM {$_prefix}postmeta
			where
			{$_prefix}postmeta.post_id = '" . $_order_id_get . "'
			and
			{$_prefix}postmeta.meta_key in('_order_total','_order_total_ori')
			";

            $results = $wpdb->get_results($sql);
            ?>
            <?php
            if ($productCode == 'CREDITCARD') {

                $fee = ($this->fee_credit_card == '') ? 0 : $this->fee_credit_card;
                $creditcardfee = ($this->creditcardfee == '') ? 0 : $this->creditcardfee;

                if ($results[1]->meta_value) {
                    $amount = $results[1]->meta_value;
                } else {
                    $amount = $results[0]->meta_value;
                }

                $amountcredit = $amount + $fee;
                $amountFinish = (($amountcredit * $creditcardfee) / 100) + $fee;
                $totalamount = $amount + $amountFinish; //disc rate
            } else {
                if ($productCode == 'BCAATM') {
                    $fee = ($this->fee_bca_klikpay == '') ? 0 : $this->fee_bca_klikpay;
                } elseif ($productCode == 'XLTUNAI') {
                    $fee = ($this->fee_xl_tunai == '') ? 0 : $this->fee_xl_tunai;
                } elseif ($productCode == 'BIIATM') {
                    $fee = ($this->fee_bii_atm == '') ? 0 : $this->fee_bii_atm;
                } elseif ($productCode == 'BNIDBO') {
                    $fee = ($this->fee_bnidbo == '') ? 0 : $this->fee_bnidbo;
                } elseif ($productCode == 'BNIATM') {
                    $fee = ($this->fee_bni_atm == '') ? 0 : $this->fee_bni_atm;
                } elseif ($productCode == 'DANAMONOB') {
                    $fee = ($this->fee_danamon_ob == '') ? 0 : $this->fee_danamon_ob;
                } elseif ($productCode == 'DKIIB') {
                    $fee = ($this->fee_dki_ib == '') ? 0 : $this->fee_dki_ib;
                } elseif ($productCode == 'MANDIRIIB') {
                    $fee = ($this->fee_mandiri_ib == '') ? 0 : $this->fee_mandiri_ib;
                } elseif ($productCode == 'MANDIRIATM') {
                    $fee = ($this->fee_mandiri_atm == '') ? 0 : $this->fee_mandiri_atm;
                } elseif ($productCode == 'MANDIRIECASH') {
                    $fee = ($this->fee_mandiri_ecash == '') ? 0 : $this->fee_mandiri_ecash;
                } elseif ($productCode == 'FINPAY195') {
                    $fee = ($this->fee_finpay == '') ? 0 : $this->fee_finpay;
                } elseif ($productCode == 'MANDIRISMS') {
                    $fee = ($this->fee_mandiri_sms == '') ? 0 : $this->fee_mandiri_sms;
                } elseif ($productCode == 'MAYAPADAIB') {
                    $fee = ($this->fee_mayapada_ib == '') ? 0 : $this->fee_mayapada_ib;
                } elseif ($productCode == 'MUAMALATATM') {
                    $fee = ($this->fee_mualamatatm == '') ? 0 : $this->fee_mualamatatm;
                } elseif ($productCode == 'NOBUPAY') {
                    $fee = ($this->fee_nobupay == '') ? 0 : $this->fee_nobupay;
                } elseif ($productCode == 'PERMATAATM') {
                    $fee = ($this->fee_permata_atm == '') ? 0 : $this->fee_permata_atm;
                } elseif ($productCode == 'EPAYBRI') {
                    $fee = ($this->fee_bri == '') ? 0 : $this->fee_bri;
                } elseif ($productCode == 'BRIATM') {
                    $fee = ($this->fee_bri_atm == '') ? 0 : $this->fee_bri_atm;
                } elseif ($productCode == 'PERMATANETPAY') {
                    $fee = ($this->fee_permata_net_pay == '') ? 0 : $this->fee_permata_net_pay;
                } elseif ($productCode == 'DANAMONATM') {
                    $fee = ($this->fee_danamon_atm == '') ? 0 : $this->fee_danamon_atm;
                } elseif ($productCode == 'CIMBATM') {
                    $fee = ($this->fee_cimb_atm == '') ? 0 : $this->fee_cimb_atm;
                } elseif ($productCode == 'BITCOIN') {
                    $fee = ($this->fee_bitcoin == '') ? 0 : $this->fee_bitcoin;
                } else {
                    $fee = 0;
                }

                $creditcardfee = 0;

                if ($results[1]->meta_value) {
                    $amount = $results[1]->meta_value;
                } else {
                    $amount = $results[0]->meta_value;
                }

                $totalamount = $amount + $fee;
            }

            $currency = get_woocommerce_currency_symbol();
            ?>
            <table class="shop_table order_details">
                <tbody>
                <h2>Additional Information</h2>
                <hr>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row">Online Payment</th>
                        <td><?php echo $productName; ?></td>
                    </tr>

                    <tr>
                        <th scope="row">Transaction Fee</th>
                        <td><?php echo $currency . number_format($fee, 2); ?></td>
                    </tr>

                    <?php
                    if ($productCode == 'CREDITCARD') {
                        ?>
                        <tr>
                            <th scope="row">Merchant Discount Rate</th>
                            <td><?php echo $creditcardfee . '%'; ?></td>
                        </tr>
                    <?php } ?>

                    <tr>
                        <th scope="row">Total Amount</th>
                        <td><?php echo $currency . number_format($totalamount, 2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <?php
            if ($totalamount < $this->min_order_total) {
//			echo 'Total amount is exceeding your limit amount per transaction for';
                echo '<i><b><font color="red">Error: Amount cannot be lower than ' . $currency . number_format($this->min_order_total, 2) . '</font></b></i>';
                ?>
                <br>
                <input type="submit" class="submit buy button" value="<?php _e('Confirm and Pay', 'woocommerce'); ?>" disabled />
                <?php
            } elseif ($totalamount > $this->max_order_total) {
                echo '<i><b><font color="red">Error: Total amount is exceeding your maximum amount ' . $currency . number_format($this->max_order_total, 2) . '</font></b></i>';
                ?>
                <br>
                <input type="submit" class="submit buy button" value="<?php _e('Confirm and Pay', 'woocommerce'); ?>" disabled />
                <?php
            } else {
                ?>
                <form method="POST" action="<?php //echo $this->payurl;           ?>">
                    <input type="hidden" name="method" value="<?php echo $method; ?>" />
                    <input type="hidden" name="merchantUUID" value="<?php echo $this->settings['merchant_id']; ?>" />
                    <input type="submit" name="post" class="submit buy button" value="<?php _e('Confirm and Pay', 'woocommerce'); ?>" />
                </form>
            <?php } ?>

            <?php
            if (isset($_POST['post'])) {
                ini_set('display_errors', false);
                error_reporting(0);
                $payment_method_title = 'ESPay Payment Gateways - ' . $productName . ' - (Waiting for Payment)';
                $order_productcode_espay = '_order_productcode_espay';
                $order_fee_espay = '_order_fee_espay';
                $order_creditcardfee_espay = '_order_creditcardfee_espay';
                $order_total_ori = '_order_total_ori';

                $wpdb->query($wpdb->prepare("UPDATE {$_prefix}postmeta SET meta_value = %s WHERE post_id = %d and meta_key = %s",array($payment_method_title,$order_id_get,'_payment_method_title')));
                $wpdb->query($wpdb->prepare("UPDATE {$_prefix}postmeta SET meta_value = %d WHERE post_id = %d and meta_key = %s",array($totalamount.'.00',$order_id_get,'_order_total')));

                if ($productCode == 'CREDITCARD') {
                    $wpdb->query("DELETE FROM {$_prefix}postmeta where post_id='" . $order_id_get . "' and meta_key= '" . $order_productcode_espay . "'");
                    $wpdb->query("INSERT INTO {$_prefix}postmeta (post_id, meta_key, meta_value) VALUES ('" . $order_id_get . "', '" . $order_productcode_espay . "', '" . $productCode . "')");

                    $wpdb->query("DELETE FROM {$_prefix}postmeta where post_id='" . $order_id_get . "' and meta_key= '" . $order_fee_espay . "'");
                    $wpdb->query("INSERT INTO {$_prefix}postmeta (post_id, meta_key, meta_value) VALUES ('" . $order_id_get . "', '" . $order_fee_espay . "', '" . $fee . '.00' . "')");

                    $wpdb->query("DELETE FROM {$_prefix}postmeta where post_id='" . $order_id_get . "' and meta_key= '" . $order_creditcardfee_espay . "'");
                    $wpdb->query("INSERT INTO {$_prefix}postmeta (post_id, meta_key, meta_value) VALUES ('" . $order_id_get . "', '" . $order_creditcardfee_espay . "', '" . $creditcardfee . '.00' . "')");

                    $wpdb->query("DELETE FROM {$_prefix}postmeta where post_id='" . $order_id_get . "' and meta_key= '" . $order_total_ori . "'");
                    $wpdb->query("INSERT INTO {$_prefix}postmeta (post_id, meta_key, meta_value) VALUES ('" . $order_id_get . "', '" . $order_total_ori . "', '" . $amount . "')");
                } else {
                    $wpdb->query("DELETE FROM {$_prefix}postmeta where post_id='" . $order_id_get . "' and meta_key= '" . $order_productcode_espay . "'");
                    $wpdb->query("INSERT INTO {$_prefix}postmeta (post_id, meta_key, meta_value) VALUES ('" . $order_id_get . "', '" . $order_productcode_espay . "', '" . $productCode . "')");

                    $wpdb->query("DELETE FROM {$_prefix}postmeta where post_id='" . $order_id_get . "' and meta_key= '" . $order_fee_espay . "'");
                    $wpdb->query("INSERT INTO {$_prefix}postmeta (post_id, meta_key, meta_value) VALUES ('" . $order_id_get . "', '" . $order_fee_espay . "', '" . $fee . '.00' . "')");

                    $wpdb->query("DELETE FROM {$_prefix}postmeta where post_id='" . $order_id_get . "' and meta_key= '" . $order_creditcardfee_espay . "'");
                    $wpdb->query("INSERT INTO {$_prefix}postmeta (post_id, meta_key, meta_value) VALUES ('" . $order_id_get . "', '" . $order_creditcardfee_espay . "', '" . $creditcardfee . '.00' . "')");

                    $wpdb->query("DELETE FROM {$_prefix}postmeta where post_id='" . $order_id_get . "' and meta_key= '" . $order_total_ori . "'");
                    $wpdb->query("INSERT INTO {$_prefix}postmeta (post_id, meta_key, meta_value) VALUES ('" . $order_id_get . "', '" . $order_total_ori . "', '" . $amount . "')");
                }

                $wpdb->flush();

                $order->reduce_order_stock();
                WC()->cart->empty_cart();
//					$order->add_order_note( __( 'Menunggu pembayaran melalui espay via '.$productName.' dengan id transaksi '.$_REQUEST['trx_id'], 'woocommerce' ) );
//            	https://secure.sgo.co.id/public/signature/js //production
//	            http://secure-dev.sgo.co.id/public/signature/js //development
                $urlserver = $this->environment == 'production' ? 'https://kit.espay.id/public/signature/js' : 'https://sandbox-kit.espay.id/public/signature/js';
                ?>
                <script type="text/javascript" src="<?= $urlserver ?>"></script>
                <script type="text/javascript">
                    window.onload = function() {
                        var data = {
                            paymentId: '<?= $order_id_get ?>', //ORDERID
                            key: '<?= $this->apikey ?>',
                            backUrl: encodeURIComponent('<?= $redirect ?>'),
                            bankCode: '<?= $bankCode ?>',
                            bankProduct: '<?= $productCode ?>'//'MAYAPADAIB'
                        },
                        sgoPlusIframe = document.getElementById("sgoplus-iframe");
                        if (sgoPlusIframe !== null) {
                            sgoPlusIframe.src = SGOSignature.getIframeURL(data);
                        }
                        SGOSignature.receiveForm();
                    };
                </script>
                <iframe id="sgoplus-iframe" sandbox="allow-scripts allow-top-navigation" src="" scrolling="no" allowtransparency="true" frameborder="0" height="300"></iframe>
                <?php
            }
        }

    }

    function add_espay_gateway($methods) {
        $methods[] = 'WC_Gateway_eSpay';
        return $methods;
    }

    function myaccount_view_order_admin($order_id) {
        global $woocommerce, $wpdb;
        $_prefix = $wpdb->prefix;
        $order = new WC_Order($order_id);
        $order_id = trim(str_replace('#', '', $order->get_order_number()));
        $currency = get_woocommerce_currency_symbol();

        $sql = "SELECT *
			FROM {$_prefix}postmeta
			where
			{$_prefix}postmeta.post_id = '" . $order_id . "'
			and
			{$_prefix}postmeta.meta_key in('_order_total','_order_productcode_espay','_order_fee_espay','_order_creditcardfee_espay','_order_total_ori')
			";
        //{$_prefix}postmeta.meta_key = '".$meta_key."'
        $results = $wpdb->get_results($sql);
//			echo'<pre>';
//			var_dump($results);
//			echo'</pre>';
        $amountOri = $results[0]->meta_value;
        $productCode = $results[1]->meta_value;
        $feeOri = $results[2]->meta_value;
        $creditcardfeeOri = $results[3]->meta_value;
        $amountOriginal = $results[4]->meta_value;

        if ($productCode == 'CREDITCARD') {
            $fee = ($feeOri == '') ? 0 : $feeOri;
            $creditcardfee = ($creditcardfeeOri == '') ? 0 : $creditcardfeeOri;
//	        	$amount = WC()->cart->cart_contents_total; //ori angka
            $totalamount = $amountOri;
        } else {
            $fee = ($feeOri == '') ? 0 : $feeOri;
            $creditcardfee = 0;
//	        	$amount = WC()->cart->cart_contents_total; //ori angka
            $amount = $amountOri;
            $totalamount = ($amount + $fee) - $fee;
        }
        ?>
        <tr>
            <td class="label"></td>
            <td class="total">
                &nbsp;
            </td>
            <td width="1%"></td>
        </tr>

        <tr>
            <td class="label"><?php _e('Subtotal', 'woocommerce'); ?>:</td>
            <td class="total">
                <?php echo $currency . number_format(floatval($amountOriginal), 2); ?>
            </td>
            <td width="1%"></td>
        </tr>
        <tr>
            <td class="label"><?php echo wc_help_tip(__('This is the transaction fee. transaction fee are defined per line item.', 'woocommerce')); ?> <?php _e('Transaction Fee', 'woocommerce'); ?>:</td>
            <td class="total">
                <?php echo $currency . number_format(floatval($fee), 2); ?>
            </td>
            <td width="1%"></td>
        </tr>
        <?php if ($productCode == 'CREDITCARD') { ?>
            <tr>
                <td class="label"><?php echo wc_help_tip(__('This is the merchant disc rate. merchant disc rate are defined per line item.', 'woocommerce')); ?> <?php _e('Merchant Discount Rate', 'woocommerce'); ?>:</td>
                <td class="total">
                    <?php echo $creditcardfee . '%'; ?>
                </td>
                <td width="1%"></td>
            </tr>
        <?php } ?>
        <tr>
            <td class="label"><?php echo wc_help_tip(__('This is the total amount. total amount are defined per line item.', 'woocommerce')); ?> <?php _e('Total Amount', 'woocommerce'); ?>:</td>
            <td class="total">
                <?php echo $currency . number_format($totalamount, 2); ?>
            </td>
            <td width="1%"></td>
        </tr>
        <?php
    }

    function myaccount_view_order($order_id) {
        global $woocommerce, $wpdb;
        $_prefix = $wpdb->prefix;
        $order = new WC_Order($order_id);
        $order_id = trim(str_replace('#', '', $order->get_order_number()));
        $currency = get_woocommerce_currency_symbol();

        $sql = "SELECT *
			FROM {$_prefix}postmeta
			where
			{$_prefix}postmeta.post_id = '" . $order_id . "'
			and
			{$_prefix}postmeta.meta_key in('_order_total')
			";
//			{$_prefix}postmeta.meta_key in('_order_total','_order_productcode_espay','_order_fee_espay','_order_creditcardfee_espay')
        //{$_prefix}postmeta.meta_key = '".$meta_key."'
        $results = $wpdb->get_results($sql);

        $sql1 = "SELECT *
			FROM {$_prefix}postmeta
			where
			{$_prefix}postmeta.post_id = '" . $order_id . "'
			and
			{$_prefix}postmeta.meta_key in('_order_fee_espay')
			";
        $results1 = $wpdb->get_results($sql1);

        $sql2 = "SELECT *
			FROM {$_prefix}postmeta
			where
			{$_prefix}postmeta.post_id = '" . $order_id . "'
			and
			{$_prefix}postmeta.meta_key in('_order_creditcardfee_espay')
			";
        $results2 = $wpdb->get_results($sql2);

        $sql3 = "SELECT *
			FROM {$_prefix}postmeta
			where
			{$_prefix}postmeta.post_id = '" . $order_id . "'
			and
			{$_prefix}postmeta.meta_key in('_order_productcode_espay')
			";
        $results3 = $wpdb->get_results($sql3);

//			echo'<pre>';
//			var_dump($results3);
//			echo'</pre>';
        $amountOri = $results[0]->meta_value;

        $productCode = $results3[0]->meta_value;
        $feeOri = $results1[0]->meta_value;
        $creditcardfeeOri = $results2[0]->meta_value;

        if ($productCode == 'CREDITCARD') {
            $fee = ($feeOri == '') ? 0 : $feeOri;
            $creditcardfee = ($creditcardfeeOri == '') ? 0 : $creditcardfeeOri;
//	        	$amount = WC()->cart->cart_contents_total; //ori angka
//        		rumus
            $amount = $amountOri;
            $totalamount = $amount; //disc rate
        } else {
            $fee = ($feeOri == '') ? 0 : $feeOri;
            $creditcardfee = 0;
//	        	$amount = WC()->cart->cart_contents_total; //ori angka
            $amount = $amountOri;
            $totalamount = ($amount + $fee) - $fee;
        }
        ?>
        <h2>Additional Information</h2>

        <table class="shop_table order_details">
            <tbody>

            </tbody>
            <tfoot>
                <tr>
                    <th scope="row">Transaction Fee</th>
                    <td><?php echo $currency . number_format($fee, 2); ?></td>
                </tr>
                <?php if ($productCode == 'CREDITCARD') { ?>
                    <tr>
                        <th scope="row">Merchant Discount Rate</th>
                        <td><?php echo $creditcardfee . '%'; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <th scope="row">Total Amount</th>
                    <td><?php echo $currency . number_format($totalamount, 2); ?></td>
                </tr>
            </tfoot>
        </table>
        <?php
    }

    function myaccount_view_order_text($order_id) {
        global $woocommerce, $wpdb;
        $_prefix = $wpdb->prefix;
        $order = new WC_Order($order_id);
        $order_id = trim(str_replace('#', '', $order->get_order_number()));
        $currency = get_woocommerce_currency_symbol();

        $sql = "SELECT *
			FROM {$_prefix}postmeta
			where
			{$_prefix}postmeta.post_id = '" . $order_id . "'
			and
			{$_prefix}postmeta.meta_key in('_order_total','_order_productcode_espay','_order_fee_espay','_order_creditcardfee_espay','_payment_method_title')
			";
        //{$_prefix}postmeta.meta_key = '".$meta_key."'
        $results = $wpdb->get_results($sql);
//			echo'<pre>';
//			var_dump($results);
//			echo'</pre>';

        $paymentmethod = $results[0]->meta_value;
        $amountOri = $results[1]->meta_value;
        $productCode = $results[2]->meta_value;
        $feeOri = $results[3]->meta_value;
        $creditcardfeeOri = $results[4]->meta_value;

        if ($productCode == 'CREDITCARD') {
            $fee = ($feeOri == '') ? 0 : $feeOri;
            $creditcardfee = ($creditcardfeeOri == '') ? 0 : $creditcardfeeOri;
//	        	$amount = WC()->cart->cart_contents_total; //ori angka
//        		rumus
//				(total * settingbackend%) + setting backend fee
            $amount = $amountOri;

            $amountcredit = $amount + $fee;
            $amountFinish = (($amountcredit * $creditcardfee) / 100) + $fee;
            $totalamount = $amount + $amountFinish; //disc rate
        } else {
            $fee = ($feeOri == '') ? 0 : $feeOri;
            $creditcardfee = 0;
//	        	$amount = WC()->cart->cart_contents_total; //ori angka
            $amount = $amountOri;
            $totalamount = $amount + $fee;
        }
        $statusOrder = wc_get_order_status_name($order->get_status());
        ?>
        <?php if ($statusOrder == 'Processing') { ?>
            <p class="order-info">
                Terima Kasih telah belanja di toko kami,
                kami akan segera memproses pesanan Anda dan mengatur pengiriman pesanan.
                <br><br>
            </p>
            <?php
        } elseif ($statusOrder == 'Completed') {
            ?>
            <p class="order-info">
                Great news! Order kamu #<font color='blue'><?= $order_id ?></font> sudah dikirim, dan akan tiba sesuai dengan estimasi pengiriman.
                <br><br>
            </p>

            <?php
        }
    }

    add_action('woocommerce_order_items_table', 'myaccount_view_order_text');

    add_action('woocommerce_order_details_after_order_table', 'myaccount_view_order');
    add_action('woocommerce_admin_order_totals_after_total', 'myaccount_view_order_admin');


    add_filter('woocommerce_payment_gateways', 'add_espay_gateway');
}
