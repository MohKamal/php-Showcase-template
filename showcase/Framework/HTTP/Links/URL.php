<?php
namespace Showcase\Framework\HTTP\Links{

    require_once dirname(__FILE__) . '\..\..\IO\Debug\Log.php';

    use \Showcase\AutoLoad;
    use \Showcase\Framework\IO\Debug\Log;

    class URL
    {
        /**
         * Make a redireciton with a message
         */
        static function RedirectWithMessage($url=NULL, $message=NULL, $message_type=NULL){
            $_SESSION['sess_flash_message']= array();
            if($message){
                switch($message_type){ 
                case 'success': $_SESSION['sess_flash_message'][] = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button>'.$message.'</div>';break;
                case 'error': $_SESSION['sess_flash_message'][] = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>'.$message.'</div>';break;
                case 'notice': $_SESSION['sess_flash_message'][] = '<div class="alert alert-info"><button data-dismiss="alert" class="close" type="button">×</button>'.$message.'</div>';break;
                case 'warning': $_SESSION['sess_flash_message'][] = '<div class="alert alert-block"><button data-dismiss="alert" class="close" type="button">×</button>'.$message.'</div>';break;
                default: $_SESSION['sess_flash_message'][] = $message;
                }
            }
            if($url) {
                header("Location: " . URL::BASE() . $url);
            } else {
                header("Location: ".$_SERVER['HTTP_REFERER']);
            }
            exit();
            ob_flush();    
        }

        /**
         * Redirection
         */
        function Redirect($url, $permanent = false)
        {
            if (headers_sent() === false)
            {
                Log::print($url);
                header('Location: ' . URL::BASE() . $url, true, ($permanent === true) ? 301 : 302);
            }

            exit();
        }

        /**
         * Generate url from page name
         * @param $page Page name as index.php
         */
        static function view($page){
            return AutoLoad::env('VIEW') . $page;
        }

        /**
         * Get base Url
         */
        static function BASE(){
            return AutoLoad::env('APP_URL');
        }

        static function assets(){
            $assets = '';
            if(AutoLoad::env('APP_SUBFOLDER') != null)
                $assets = AutoLoad::env('APP_SUBFOLDER') . '/';
            
            Log::print("SubFolder : " . AutoLoad::env('APP_SUBFOLDER'));
            Log::print("Ressources folder : " . AutoLoad::env('RESOURCES'));
            return $assets . AutoLoad::env('RESOURCES');
        }

    }
}