<?php defined('SYSPATH') or die('No Direct Script Access.');

interface PaymentGatewayHandler
{
    public function name();
    public function title();
    public function is_ready();
    public function allow_for_limited_permissions();
    public function process($payment, $post);
    public function refund();
    public function get_inputs();

}
