<?php
/**
 * AnsPress Email
 *
 * Email notification extension for AnsPress
 *
 * @package   AnsPress_Email
 * @author    Rahul Aryan <support@anspress.io>
 * @license   GPL-2.0+
 * @link      http://anspress.io
 * @copyright 2014 Rahul Aryan
 *
 * @wordpress-plugin
 * Plugin Name:       AnsPress Email
 * Plugin URI:        http://anspress.io
 * Description:       Email notification extension for AnsPress
 * Version:           1.0
 * Author:            Rahul Aryan
 * Author URI:        http://anspress.io
 * Text Domain:       anspress_email
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: 
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


class AnsPress_Ext_AnsPress_Email
{

    /**
     * Class instance
     * @var object
     * @since 1.0
     */
    private static $instance;


    /**
     * Get active object instance
     *
     * @since 1.0
     *
     * @access public
     * @static
     * @return object
     */
    public static function get_instance() {

        if ( ! self::$instance )
            self::$instance = new AnsPress_Ext_AnsPress_Email();

        return self::$instance;
    }
    /**
     * Initialize the class
     * @since 1.0
     */
    public function __construct()
    {
        if( ! class_exists( 'AnsPress' ) )
            return; // AnsPress not installed

        if (!defined('ANSPRESS_EMAIL_DIR'))    
            define('ANSPRESS_EMAIL_DIR', plugin_dir_path( __FILE__ ));

        if (!defined('ANSPRESS_EMAIL_URL'))   
                define('ANSPRESS_EMAIL_URL', plugin_dir_url( __FILE__ ));

        // internationalization
        add_action( 'init', array( $this, 'textdomain' ) );
        add_filter( 'ap_default_options', array($this, 'ap_default_options') );
        add_action( 'init', array( $this, 'register_option' ), 100 );
        
        add_action( 'ap_after_new_question', array( $this, 'ap_after_new_question' ));
    }
    /**
     * Load plugin text domain
     *
     * @since 1.0
     *
     * @access public
     * @return void
     */
    public static function textdomain() {

        // Set filter for plugin's languages directory
        $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

        // Load the translations
        load_plugin_textdomain( 'AnsPress_Email', false, $lang_dir );

    }
    

    /**
     * Apppend default options
     * @param   array $defaults
     * @return  array           
     * @since   1.0
     */             
    public function ap_default_options($defaults)
    {
        $defaults['notify_admin_email']         = get_option( 'admin_email' ) ;
        $defaults['plain_email']                = false;
        $defaults['notify_admin_new_question']  = true;
        $defaults['notify_admin_new_answer']    = true;
        $defaults['notify_admin_new_comment']   = true;
        $defaults['notify_admin_edit_question'] = true;
        $defaults['notify_admin_edit_answer']   = true;
        $defaults['notify_admin_trash_question']= true;
        $defaults['notify_admin_trash_answer']  = true;

        $defaults['new_question_email_subject'] = __("New question posted by {asker}", 'AnsPress_Email');
        $defaults['new_question_email_body']    = __("Hello!\r\n A new question is posted by {asker}\r\n\r\nTitle: {question_title}\r\n Description:\r\n{question_excerpt}\r\nLink: {question_link}", 'AnsPress_Email');

        return $defaults;
    }


    /**
     * Register options
     */
    public function register_option(){
        if(!is_admin())
            return;

        $settings = ap_opt();

        // Register general settings
        ap_register_option_group('email', __('Email', 'AnsPress_Email') , array(
            array(
                'name' => 'anspress_opt[notify_admin_email]',
                'label' => __('Admin email', 'AnsPress_Email') ,
                'desc' => __('Enter emial where admin notification were send', 'AnsPress_Email') ,
                'type' => 'text',
                'value' => @$settings['notify_admin_email'],
                'show_desc_tip' => false,
            ),
            array(
                'name' => 'anspress_opt[plain_email]',
                'label' => __('Send plain email', 'AnsPress_Email') ,
                'desc' => __('No HTML in email simple text', 'AnsPress_Email') ,
                'type' => 'checkbox',
                'value' => @$settings['plain_email'],
                'show_desc_tip' => false,
            ),
            array(
                'name' => '__sep',
                'type' => 'custom',
                'html' => '<span class="ap-form-separator">' . __('Notify admin', 'AnsPress_Email') . '</span>',
            ) ,
            array(
                'name' => 'anspress_opt[notify_admin_new_question]',
                'label' => __('New question', 'AnsPress_Email') ,
                'desc' => __('Send email to admin for every new question.', 'AnsPress_Email') ,
                'type' => 'checkbox',
                'value' => @$settings['notify_admin_new_question'],
                'show_desc_tip' => false,
            ),
            array(
                'name' => 'anspress_opt[notify_admin_new_answer]',
                'label' => __('New answer', 'AnsPress_Email') ,
                'desc' => __('Send email to admin for every new answer.', 'AnsPress_Email') ,
                'type' => 'checkbox',
                'value' => @$settings['notify_admin_new_answer'],
                'show_desc_tip' => false,
            ),
            array(
                'name' => 'anspress_opt[notify_admin_new_comment]',
                'label' => __('New comment', 'AnsPress_Email') ,
                'desc' => __('Send email to admin for every new comment.', 'AnsPress_Email') ,
                'type' => 'checkbox',
                'value' => @$settings['notify_admin_new_comment'],
                'show_desc_tip' => false,
            ),
            array(
                'name' => 'anspress_opt[notify_admin_edit_question]',
                'label' => __('Edit question', 'AnsPress_Email') ,
                'desc' => __('Send email to admin when question is edited', 'AnsPress_Email') ,
                'type' => 'checkbox',
                'value' => @$settings['notify_admin_edit_question'],
                'show_desc_tip' => false,
            ),
            array(
                'name' => 'anspress_opt[notify_admin_edit_answer]',
                'label' => __('Edit answer', 'AnsPress_Email') ,
                'desc' => __('Send email to admin when answer is edited', 'AnsPress_Email') ,
                'type' => 'checkbox',
                'value' => @$settings['notify_admin_edit_answer'],
                'show_desc_tip' => false,
            ),
            array(
                'name' => 'anspress_opt[notify_admin_trash_question]',
                'label' => __('Delete question', 'AnsPress_Email') ,
                'desc' => __('Send email to admin when question is trashed', 'AnsPress_Email') ,
                'type' => 'checkbox',
                'value' => @$settings['notify_admin_trash_question'],
                'show_desc_tip' => false,
            ),
            array(
                'name' => 'anspress_opt[notify_admin_trash_answer]',
                'label' => __('Delete answer', 'AnsPress_Email') ,
                'desc' => __('Send email to admin when asnwer is trashed', 'AnsPress_Email') ,
                'type' => 'checkbox',
                'value' => @$settings['notify_admin_trash_answer'],
                'show_desc_tip' => false,
            )
        ));
    }

    public function header(){
        $header = '';
        if (!$charset = get_bloginfo('charset')) {
            $charset = 'utf-8';
        }
        $header .= 'Content-type: text/plain; charset=' . $charset . "\r\n";

        return $header;
    }

    public function replace_tags($content, $args){
        return strtr($content, $args);
    }

    public function send_mail($email, $subject, $message){
        wp_mail( $email, $subject, $message, $this->header() );
    }


    public function ap_after_new_question($question_id){
        if ( !wp_is_post_revision( $question_id ) && ap_opt('notify_admin_new_question')) {
            
            $current_user = wp_get_current_user();

            $question = get_post($question_id);

            // don't bother if current user is admin
            if(ap_opt( 'notify_admin_email' ) == $current_user->user_email)
                return;

            $args = array(
                '{asker}'             => ap_user_display_name($question->post_author),
                '{question_title}'    => $question->post_title,
                '{question_link}'     => get_permalink($question->ID),
                '{question_content}'  => $question->post_content,
                '{question_excerpt}'  => ap_truncate_chars( strip_tags($question->post_content), 100),
            );

            $args = apply_filters( 'ap_new_question_email_tags', $args );

            $subject = $this->replace_tags(ap_opt('new_question_email_subject'), $args);

            $message = $this->replace_tags(ap_opt('new_question_email_body'), $args);

            //sends email
            $this->send_mail(ap_opt( 'notify_admin_email' ), $subject, $message);
        }
    }
}

/**
 * Get everything running
 *
 * @since 1.0
 *
 * @access private
 * @return void
 */

function anspress_ext_AnsPress_Email() {
    $anspress_ext_AnsPress_Email = new AnsPress_Ext_AnsPress_Email();
}
add_action( 'plugins_loaded', 'anspress_ext_AnsPress_Email' );

