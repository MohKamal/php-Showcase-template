<?php
/**
 * More at : https://medium.com/@hfally/how-to-create-an-environment-variable-file-like-laravel-symphonys-env-37c20fc23e72
 */
namespace Showcase\Framework\Initializer{
  use \Showcase\Framework\IO\Debug\Log;
  
  class AppSetting{

    public static function Init(){
        $variables = [
        'RES_FOLDER' => dirname(__FILE__) . '\..\..\ressources',
        'RESOURCES' => 'ressources',
      ];
    
        foreach ($variables as $key => $value) {
            putenv("$key=$value");
        }

        $_variables = file_get_contents(dirname(__FILE__) . "\..\..\appsettings.json");
        if ($_variables === false) {
          Log::print("appsetting.json file was not found, create one from the example file.");
        }else{
          $json_variables = json_decode($_variables, true);
          if ($json_variables === null) {
            Log::print("appsetting.json file has error(s), please check the file or delete it and create another one from the example file.");
          }else{
              foreach ($json_variables as $key => $value) {
                  putenv("$key=$value");
            }
          }
        }
    }
  }
}