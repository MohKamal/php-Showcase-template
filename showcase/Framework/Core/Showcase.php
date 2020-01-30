<?php
/**
 * The Main App Class to Start the Showcase
 */
namespace Showcase\Framework\Core{
    require_once dirname(__FILE__) . '\..\..\autoload.php';
    require_once dirname(__FILE__) . '\..\Initializer\AppSetting.php';
    
    use \Showcase\AutoLoad;

    /**
     * Register the autoloader
     */
    AutoLoad::register();
    
    use \Showcase\Framework\Initializer\AppSetting;
    use \Showcase\Framework\Database\Wrapper;
    use \Showcase\Web;

    class Showcase{

        /**
         * Load the Env Variables
         * Include the routes
         * Init the database
         */
        public static function HakunaMatata(){
            //init the global settings
            AppSetting::Init();
            //Database init
            $db = new Wrapper();
            $db->Initialize();
            //including the routes
            include_once dirname(__FILE__) .'\..\..\route\Web.php';
        }

    }
}