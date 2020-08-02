<?php

namespace Beluga {


    class IO
    {

        public static function createId(string $documentName) {
            return $documentName."-" . date('ymdHis') . "-" . uniqid();
        }

        public static function isWritable(string $target) : bool {
            return is_writable($target);
        }
        
        public static function delete_directory($dirname)
        {
            $dir_handle = null;
            if (is_dir($dirname)) {
                $dir_handle = opendir($dirname);
                if ($dir_handle === FALSE) {
                    return false;
                }
                while ($file = readdir($dir_handle)) {
                    if ($file != "." && $file != "..") {
                        if (!is_dir($dirname . "/" . $file))
                            if (!unlink($dirname . "/" . $file)) {
                                return false;
                            }
                        elseif (!self::delete_directory($dirname . '/' . $file)) {
                            return false;
                        }
                    }
                }
                closedir($dir_handle);
                return rmdir($dirname);
            } else {
                return false;
            }          
        }

        public static function read($file) {
            $data = json_decode(file_get_contents($file),TRUE);
            if ( \json_last_error() !== JSON_ERROR_NONE ) {
                throw new \Beluga\Exception("File read error / $file");
            }
            return $data;
        }

        public static function write($file,$data) {
            $strdata = json_encode($data);
            if (file_put_contents($file, $strdata, LOCK_EX) === FALSE) {
                throw new \Beluga\Exception("Data write error / $file");
            }
        }

        public static function remove($file) {
            if (!unlink($file)) {
                throw new \Beluga\Exception("The record could not delete / $file");
            }
        }
    }
}
