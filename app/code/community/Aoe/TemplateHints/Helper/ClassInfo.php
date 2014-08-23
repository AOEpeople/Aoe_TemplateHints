<?php

/**
 * Class info helper
 *
 * @author Fabrizio Branca
 */
class Aoe_TemplateHints_Helper_ClassInfo extends Mage_Core_Helper_Abstract {


    /**
     * Find file and line of the class statement
     *
     * @param $className
     * @return bool|array
     */
    public function findFileAndLine($className) {
        $result = false;
        $fullPath = $this->searchFullPath($this->getFileFromClassName($className));
        if ($fullPath) {
            $result = array('file' => $fullPath, 'line' => 0);
            $lineNumber = $this->getLineNumber($fullPath, '/class\s+'.$className.'/');
            if ($lineNumber) {
                $result['line'] = $lineNumber;
            }
        }
        return $result;
    }



    /**
     * Get the line number of the first line in a file matching a given regex
     * Not the nicest solution, but probably the fastest
     *
     * @param $file
     * @param $regex
     * @return bool|int
     */
    public function getLineNumber($file, $regex) {
        $i = 0;
        $lineFound = false;
        $handle = @fopen($file, 'r');
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $i++;
                if (preg_match($regex, $buffer)) {
                    $lineFound = true;
                    break;
                }
            }
            fclose($handle);
        }
        return $lineFound ? $i : false;
    }



    /**
     * Find a filename in the include path fallback
     *
     * @param $filename
     * @return bool|string
     */
    public function searchFullPath($filename) {
        $paths = explode(PATH_SEPARATOR, get_include_path());
        foreach ($paths as $path) {
            $fullPath = $path . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($fullPath)) {
                return $fullPath;
            }
        }
        return false;
    }



    /**
     * Get php file from class name
     *
     * @param $className
     * @return string
     */
    public function getFileFromClassName($className) {
        return str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $className))) . '.php';
    }

}
