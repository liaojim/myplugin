<?php
/**
 * Points and Rewards class to use WooCommerce Points and Rewards plugin.
 *
 * @class 	A2Z_Points_Rewards
 * @version 1.0
 * @author 	Jim
 *
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class A2Z_Points_Rewards {
    public function __construct() {
        // Below add_filter doesn't seem to work. Points and Rewards don't seem to recognize the fitler hook, and never calls it. 
        add_filter( 'wc_points_rewards_event_description', array( $this, 'event_description' ) , 10, 3 );
        a2z_log("INFO: __construct- add_filter called ");
    }

    public function event_description( $event_description, $event_type, $event ) {
        $event_description = 'Referral reward';
        a2z_log("INFO: event_description called ");
        return $event_description;
    }

    public function increase_rewards( $order_id, $owner, $points ) {
        //add_filter( 'wc_points_rewards_event_description', array( $this, 'event_description' ) , 10, 3 );
        //a2z_log("INFO: increase_rewards- add_filter called ");
        // Settings Information
        $reward_amount = $points;
        // Order Information
        $order = wc_get_order( $order_id );
        // Add Points
        WC_Points_Rewards_Manager::increase_points( $owner, $reward_amount, 'customer-referral' );
        do_action( 'customer_reward_coupons_points_added', $order, $reward_amount, $owner );
        
        // Add Order Note visible on dashboard. 
        global $wc_points_rewards;
        $points_label = $wc_points_rewards->get_points_label( $reward_amount );
        
        $user_info = get_userdata($owner);
        $user_login = $user_info->user_login;
        
        $message = sprintf( __( 'A reward coupon was used. Added %1$s %2$s to "%3$s"', 'a2z-control' ), $reward_amount, $points_label, $user_login );
        $order->add_order_note( $message );
    }
}

// Ways to call this class to increase_rewards()
// $points=new A2Z_Points_Rewards();
// $points->increase_rewards($order_id, $web_user_id, $reward_points);

// The above code works in crediting points to the user. But the above hook event_description() was 
// never called even though the add_filter() call in constructor was called.
