<?php
namespace mfmbarber\PHPHelpers;
/**
 * A helper class to distinguish differences between XML objects
 * with the ability to ignore certain keys
 *
 * @author Matt Barber <mfmbarber@gmail.com>
 *
**/
class XMLArray {
    private $_xml_obj;

    /**
     * On instantiation load the xml file into an attribute
     * @param string $fileName The name of the XML file
     *
    **/
    public function __construct($fileName) {
        if (!is_readable($fileName)) {
            throw new \Exception("$fileName is not readable - check permissions");
        }
        if (!filesize($fileName)) {
            throw new \Exception("$fileName is empty - check file!");
        }
        $this->_xml_obj = json_decode(json_encode(simplexml_load_file($fileName)), true);
    }

    /**
     * Getter for the xml array itself
     * @return array
    **/
    public function get() {
        return $this->_xml_obj;
    }

    /**
     * Compares an XMLArray object with another object, with the ability to
     * ignore keys
     *
     * @param XMLArray $comp        The comparative object
     * @param array    $ignoreKeys  The keys in the array to ignore
     *
     * @return bool
    **/
    public function compare(XMLArray $comp, array $ignoreKeys = []) {
        $this_properties = $this->_xml_obj;
        $that_properties = $comp->get();

        // Manipulate the ignoreKeys a bit
        $ignoreKeys = $this->_configureIgnore($ignoreKeys);

        return count($this->_recursiveArrayDiff($this_properties, $that_properties, $ignoreKeys)) === 0;
    }

    /**
     * A helper method to turn an array of keys into something we can use.
     *
     * @param array $ignoreKeys     The ignore keys to configure
     *
     * @return array
    **/
    private function _confIgnore(array $ignoreKeys) {
        $ignore = [];

        foreach ($ignoreKeys as $k => $v) {
            // if the value is an array, recursive call, else if it's numeric, set the val to false
            if (is_array($v)) { $ignore[$k] = $this->_confIgnore($v); }
            if (is_numeric($k)) { $ignore[$v] = false; }
        }
        return $ignore;
    }

    /**
     * A helper method to recursively compare two arrays
     *
     * @param array $arr1           The left hand comparison
     * @param array $arr2           The right hand comparison
     * @param array $ignoreKeys     The keys to ignore
     *
     * @return bool
    **/
    private function _recursiveArrayDiff(array $arr1, array $arr2, array $ignoreKeys = []) {
        $arrOut = [];
        foreach($arr1 as $key => $value) {
            // if we're ignorining, the continue
            if (array_key_exists($key, $ignoreKeys) && !$ignoreKeys[$key]) { continue; }
            // if the key exists in the comparison...
            if (array_key_exists($key, $arr2)) {
                // ... and the value is an array on both sides - recursive hunt!
                if (is_array($value) && is_array($arr2[$key])) {
                    $res = $this->_recursiveArrayDiff(
                        $value,
                        $arr2[$key],
                        array_key_exists($key, $ignoreKeys) ? $ignoreKeys[$key] : []
                    );
                    // and if we have some differences - add them in
                    if (count($res)) { $arrOut[$key] = $res; }
                } else {
                    // if the value isn't an array, and is different - then add it in
                    if ($value !== $arr2[$key]) { $arrOut[$key] = $value; }
                }
            // if the array key doesn't exist - it's different! so add it!
            } else { $arrOut[$key] = $value; }
        }
        // return the difference array
        return $arrOut;
    }
}
