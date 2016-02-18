<?php

/**
 * @package Easy_Domain_Change
 * @version 0.1
 * 
 * Plugin Name: Easy Domain Change
 * Description: Helps change domain name to a new one in highly nested properties' and posts' content
 * Author: Anton Matiyenko
 * Version: 0.1.
 * Author URI: http://bugexorcism.com/
 */

namespace EasyDomainChange;

if(is_readable(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
    include_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
}

/**
 * Default parser interface
 */
interface Parser {
    public function test($data);
    public function unpack($data);
    public function pack($data);
    public function process($data, $oldDomain, $newDomain);
    public function replace(&$data, $oldDomain, $newDomain);
}

/**
 * Contains all plugin structure and logic
 */
class Main {
    
    /**
     * Indicates if input contained any errors
     * 
     * @var boolean/integer 
     */
    private static $errors = 0;
    
    /**
     * Array of parser objects
     * 
     * @var array 
     */
    private static $parsers = array();
    
    /**
     * The list of tables and fields to search through and change modify values of
     * 
     * @var array 
     */
    private static $processableEntities = array(
        array( 'table' => 'options', 'field' => 'option_value', 'key' => 'option_name'),
        array( 'table' => 'posts', 'field' => 'post_content', 'key' => 'ID'),
        array( 'table' => 'posts', 'field' => 'post_title', 'key' => 'ID'),
        array( 'table' => 'posts', 'field' => 'guid', 'key' => 'ID'),
        array( 'table' => 'postmeta', 'field' => 'meta_value', 'key' => 'meta_id'),
        array( 'table' => 'wp_layerslider', 'field' => 'data', 'key' => 'id'),
        array( 'table' => 'wp_revslider_slides', 'field' => 'params', 'key' => 'id'),
    );
    
    /**
     * Sets the necessary WordPress hooks up
     */
    public static function setup() {
        if(function_exists('add_action')) {
            add_action('admin_enqueue_scripts', function(){
                wp_enqueue_script('easy_domain_change_js', plugin_dir_url(__FILE__) . 'assets/js/manage.js', array(), false, true);
            });
            add_action('admin_menu', array(__NAMESPACE__ . '\Main', 'adminMenu'));
        }
    }
    
    /**
     * Creates a WP Admin page and link in the main menu
     */
    public static function adminMenu() {
        add_menu_page( 'Easy Domain Change', 'Domain Change', 'manage_options', 'easy_domain_change.php', array(__NAMESPACE__ . '\Main', 'manage'), 'dashicons-update', 99999  );
    }
    
    /**
     * Main controller function standing for processing data and showing the page contents
     */
    public static function manage() {
        if(!empty($_POST)) {
            if(self::validateInput()) {
                self::process($_POST['old_domain'], $_POST['new_domain']);
            }
        }
        self::renderManagePage();
    }
    
    /**
     * Renders the HTML for plugin management page
     */
    public static function renderManagePage() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'manage.php';
    }
    
    /**
     * Validates the data sent in POST
     * 
     * @return boolean
     */
    public static function validateInput() {
        if (empty($_POST['old_domain']) || !preg_match('/^([a-zA-Z\@]{1,}\.)?[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}(\.[a-zA-Z\/]{2,})?$/', $_POST['old_domain'])) {
            self::renderError('Old domain must not be empty and must have valid format');  
        }
        if (empty($_POST['new_domain']) || !preg_match('/^([a-zA-Z\@]{1,}\.)?[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}(\.[a-zA-Z\/]{2,})?$/', $_POST['new_domain'])) {
            self::renderError('New domain must not be empty and must have valid format');
        }
        if(self::$errors) {
            return false;
        }
        return true;
    }
    
    /**
     * Shows error notice using native WP styling
     * 
     * @param string $errorMessage
     */
    public static function renderError($errorMessage) {
        self::renderMessage($errorMessage, 'notice notice-error is-dismissible');
    }
    
    /**
     * Shows WP notice
     * 
     * @param string $message
     * @param string $class
     */
    public static function renderMessage($message, $class = 'notice notice-info is-dismissible') {
        self::$errors = 1;
        printf('<div class="%1$s"><p>%2$s</p></div>', $class, __($message, 'sample-text-domain'));
    }
    
    /**
     * Retrieves and processes posts and options
     * 
     * @param string $oldDomain
     * @param string $newDomain
     */
    public static function process($oldDomain, $newDomain) {
        self::setupParsers();
        global $wpdb;
        $entity = self::$processableEntities[0];
        foreach(self::$processableEntities as $entity) {
            $table = property_exists($wpdb, $entity['table'])?$wpdb->$entity['table']:$entity['table'];
            //Suppress errors in order to avoid them in case table does not exist
            if($items = @$wpdb->get_results( @$wpdb->prepare( "SELECT {$entity['field']}, {$entity['key']} FROM $table WHERE {$entity['field']} LIKE %s", '%' . $oldDomain . '%' ))) {
                foreach($items as $item) {
                    if(self::processItem($item, $entity, $oldDomain, $newDomain)) {
                        self::renderMessage('`' . $entity['table'] . '`.`' . $entity['field'] . '` for ' . $entity['key'] . '=="' . $item->$entity['key'] . '" has been processed', 'notice notice-success is-dismissible');
                    } else {
                        self::renderError('`' . $entity['table'] . '`.`' . $entity['field'] . '` for ' . $entity['key'] . '=="' . $item->$entity['key'] . '" processing failed');
                    }
                }
            }
        }
    }
    
    /**
     * Retrieves available parsers and initalizes them into class var
     */
    public static function setupParsers() {
        self::$parsers = self::getParsers();
    }
    
    /**
     * Retrieves all available parser objects
     * 
     * @return \EasyDomainChange\className
     */
    public static function getParsers() {
        
        $parsers = array();
        
        foreach(glob(__DIR__ . DIRECTORY_SEPARATOR . 'parsers' . DIRECTORY_SEPARATOR . '*.php') as $parserFile) {
            require_once $parserFile;
            $className = '\EasyDomainChange\Parsers\\' . explode('.',basename($parserFile))[0];
            if(class_exists($className)) {
                $parsers[] = new $className;
            }
        }
        
        usort($parsers, function($a, $b){
            return ($a->getPriority() > $b->getPriority());
        });
        
        return $parsers;
    }
    
    /**
     * Updates a single entry of table
     * 
     * @global \wpdb $wpdb
     * @param \stdObject $item
     * @param array $entity
     * @param string $oldDomain
     * @param string $newDomain
     * 
     * @return boolean
     */
    public static function processItem($item, $entity, $oldDomain, $newDomain) {
        global $wpdb;
        $table = property_exists($wpdb, $entity['table'])?$wpdb->$entity['table']:$entity['table'];
        foreach (self::$parsers as $parser) {
            if ($parser->test($item->$entity['field'])) {
                $update_args = array(
                    $entity['field'] => $parser->process($item->$entity['field'], $oldDomain, $newDomain),
                );
                return $wpdb->update($table, $update_args, array($entity['key'] => $item->$entity['key']));
            }
        }
        return false;
    }

}

Main::setup();
