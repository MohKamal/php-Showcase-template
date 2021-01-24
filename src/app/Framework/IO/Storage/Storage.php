<?php
namespace  Showcase\Framework\IO\Storage{
    
    use \Showcase\Framework\HTTP\Links\URL;
    use \Showcase\Framework\Utils\Utilities;
    use \Showcase\Framework\IO\Debug\Log;
    /**
     * Manage the files storage
     */
    class Storage{

        private static $_instance = null;
        private static $_currentFolder;
        private static $_rootFolder;
        private static $_onlyFolder;
        private static $_folder_type = 0; //0 for storage, 1 for resources and 2 for global

        /**
         * Select the folder where to do the maniplulations
         * @param string $name folder name
         * 
         * @return \Showcase\Framework\Storage\Storage object
         */
        public static function folder($name){
            if(empty($name))
                return null;

            if (self::$_instance === null) {
                self::$_instance = new self;
            }
            self::$_rootFolder = dirname(__FILE__) . '/../../../../storage/';

            //create folder if no exist
            $folder = self::$_rootFolder . $name . '/';
            if (!file_exists($folder)) {
                if(!mkdir($folder, 0777, true))
                    return null;
            }
            self::$_currentFolder = $folder;
            self::$_onlyFolder = $name;
            self::$_folder_type = 0;
            return self::$_instance;
        }

        /**
         * Select the folder where to do the maniplulations
         * @param string $name folder name
         * 
         * @return \Showcase\Framework\Storage\Storage object
         */
        public static function resources($name){
            if(empty($name))
                return null;

            if (self::$_instance === null) {
                self::$_instance = new self;
            }
            self::$_rootFolder = dirname(__FILE__) . '/../../../../resources/';

            //create folder if no exist
            $folder = self::$_rootFolder . $name . '/';
            if (!file_exists($folder)) {
                if(!mkdir($folder, 0777, true))
                    return null;
            }
            self::$_currentFolder = $folder;
            self::$_onlyFolder = $name;
            self::$_folder_type = 1;
            return self::$_instance;
        }

        /**
         * Set no folder 
         * @param string $name folder name
         * 
         * @return \Showcase\Framework\Storage\Storage object
         */
        public static function global(){
            if (self::$_instance === null) {
                self::$_instance = new self;
            }
            self::$_rootFolder = dirname(__FILE__) . '/../../../../storage/';
            self::$_currentFolder = "";
            self::$_folder_type = 2;

            return self::$_instance;
        }

        /**
         * Save content into a file
         * @param string $filename
         * @param mixed $content
         * 
         * @return boolean
         */
        public function put($filename, $content){
            if(empty($filename) || empty($content) || is_null(self::$_instance))
                return null;
            $file = self::$_currentFolder . $filename;
            if(!file_put_contents($file, $content))
                return false;
            return true;
        }

        /**
         * Get a file content
         * @param string $filename
         * 
         * @return mixed
         */
        public function get($filename){
            if(empty($filename) || is_null(self::$_instance))
                return null;

            if(!file_exists(self::$_currentFolder . $filename))
                return false;
            
            return file_get_contents(self::$_currentFolder . $filename);
        }

        /**
         * Check if a file exists
         * @param string $filename
         * 
         * @return mixed
         */
        public function exists($filename){
            if(empty($filename) || is_null(self::$_instance))
                return null;
            return file_exists(self::$_currentFolder . $filename);
        }

        /**
         * Download a file
         * @param string $filename
         * 
         * @return mixed
         */
        public function download($filename){
            if(empty($filename) || is_null(self::$_instance))
                return null;
            $file = self::$_currentFolder . $filename;
            if (file_exists($file)) {
                while (ob_get_level()) {
                    ob_end_clean();
                }
                ob_start();
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_flush();
                ob_clean();
                readfile($file);

                return true;
            }

            return false;
        }

        /**
         * Copy a file from a dir to the selected dir
         * @param string $filename to copy
         * @param string $newname for the new file
         * 
         * @return mixed
         */
        public function copy($filename, $newname){
            if(empty($filename) || empty($newname) || is_null(self::$_instance))
                return null;
            $file = self::$_rootFolder . $filename;
            if(!file_exists($file))
                return false;
            $new = self::$_currentFolder . $newname;
            return copy($file, $new);
        }

        /**
         * Move a file from a dir to the selected dir
         * @param string $filename to move
         * @param string $newname for the new file
         * 
         * @return mixed
         */
        public function move($filename, $newname){
            if(empty($filename) || empty($newname) || is_null(self::$_instance))
                return null;
            $file = self::$_rootFolder . $filename;
            if(!file_exists($file))
                return false;
            $new = self::$_currentFolder . $newname;
            return rename($file, $new);
        }

        /**
         * Get url for download
         * @param string $filename
         * 
         * @return mixed
         */
        public function url($filename){
            if(empty($filename) || is_null(self::$_instance))
                return null;

            $currentFile = self::$_currentFolder . $filename;
            $toFile = self::$_rootFolder . "downloads/" . basename($filename);

            if(!file_exists($currentFile))
                return false;

            if ($currentFile !== $toFile) {
                if (!copy($currentFile, $toFile)) {
                    return false;
                }
            }

            if(!file_exists($toFile))
                return false;
            $base = URL::base();
            if(Utilities::endsWith($base, '/'))
                $base = substr($base, 0, -1);
            return  $base . "/download?file=" . basename($filename); 
        }

        /**
         * Get file path
         * @param string $filename
         * 
         * @return mixed
         */
        public function path($filename, $verify=true){
            if(empty($filename) || is_null(self::$_instance))
                return null;
            $subfoler = "Storage";
            if(self::$_folder_type == 1)
                $subfoler = "resources";
            if(self::$_folder_type == 2)
                $subfoler = "";
            $file = __DIR__ . "/../../../../$subfoler/" . self::$_onlyFolder . "/" . $filename;
            if ($verify) {
                if (!file_exists($file)) {
                    return false;
                }
            }
            return $file;
        }
    }
}