<?php
/**
* Wandering PHP Framework
*
* PHP 5
*
* @package Wandering
* @author Nowayforback<nowayforback@gmail.com>
* @copyright Copyright (c) 2012, Nowayforback, (http://nowayforback.com) 
* @license http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
* @link http://nowayforback.com
* @since Version 1.0
* @filesource
*/
class WaFileSystem {

	/**
	 * Constructor method
	 *
	 */
	private function __construct(){

	}


	/**
	 * Check access dir
	 *
	 * @param string $dir
	 * @param boolean $write
	 * @return boolean
	 */
    public static function checkAccess($dir, $write = false) {

    	if (!$write) {
            return (boolean) @is_readable($dir);
        } else {
            return (boolean) @is_writable($dir);
        }
    }

    /**
     * make dir
     *
     * @param string $dirs
     */
	public static function mkdirs($dirs) {

		$pattern = '/\\\|\//';
		$aryPart = preg_split($pattern, $dirs);
		$res = true;

		if (is_array($aryPart)) {

			$curUmask = umask(0000);
			$strPath = '';
			if (substr(php_uname(), 0, 7) != "Windows") {
				$strPath = '/';
			}
			foreach ($aryPart as $dir) {

				if ($dir == '') {
					continue;
				}

				$strPath .= $dir . DS;
				if (!is_dir($strPath)) {
					if (!mkdir($strPath, 0755)) {
						$res = false;
					}
				}
			}
			umask($curUmask);
		} else {
			$res = false;
		}

		return $res;
	}

	/**
	 * Copy from src to dest (file or directory)
	 *
	 * @param string $src
	 * @param string $dest
	 * @param boolean $overwrite
	 * @return boolean
	 */
	private function copyDir($src, $dest, $overwrite = false) {

		$res = true;

		try {
			if ($hd = opendir($src)) {

				$aryNotRequire = array('.', '..', '.svn');

				while(false !== ($entry = readdir($hd))){

					if(!in_array($entry, $aryNotRequire)){

						if(is_file($src . $entry)){

							// Check overwrite
							if (!$overwrite && is_file($dest . $entry)) {
								$res = false;
								break;
							}

							// copy file
							if (!@copy($src . $entry, $dest . $entry)) {
								$res = false;
								break;
							}

						} elseif(is_dir($src . $entry)){
							if(!is_dir($dest . $entry)) {
								$curUmask = umask(0000);
								if (!@mkdir($dest . $entry, 0755)) {
									$res = false;
									break;
								}
								umask($curUmask);
							}

							//recurse!
							$res = self::copyDir($src . $entry . '/', $dest . $entry . '/', $overwrite);
							if (!$res) {
								break;
							}
						}
					}
				}
				closedir($hd);
			}
		} catch (Exception $e) {
			show_error($e->getMessage());
		}

		return $res;
	}

	/**
	 * Copy file or directory
	 *
	 * @param string $src
	 * @param string $dest
	 * @param boolean $overwrite
	 * @param boolean $newFile
	 * @return boolean
	 */
	public static function xCopy($src, $dest, $overwrite = false, $newFile = false) {

		$res = true;
		try {
			if (self::checkAccess($src)) {

				// Check new file
				if ($newFile && !is_dir($src)) {
					if ($dest{strlen($dest) - 1} != '/') {
						$file = basename($dest);
						$dest = substr($dest, 0, strlen($dest) - strlen($file));
					}
				}

				if (is_dir($src)) {
					if ($src{strlen($src) - 1} != '/') {
						$src .= '/';
					}
					if ($dest{strlen($dest) - 1} != '/') {
						$dest .= '/';
					}

					// make destination dir
					if (!(self::mkdirs($dest))) {
						$res = false;
					}
					if (!(self::copyDir($src, $dest, $overwrite))) {
						$res = false;
					}

				} else {

					// Check file existed
					if (!$newFile) {
						if ($dest{strlen($dest) - 1} != '/') {
							$dest .= '/';
						}
						$file = basename($src);
					}

					// Check overwrite
					if (!$overwrite && is_file($dest . $file)) {
						$res = false;
					}

					// make destination dir
					if (!(self::mkdirs($dest))) {
						$res = false;
					}

					// copy file
					if (!@copy($src, $dest . $file)) {
						$res = false;
					}
				}
			} else {
				$res = false;
			}

			return $res;

		} catch (Exception $e) {
			show_error($e->getMessage());
		}

	}

	/**
	 * delete file or directories
	 *
	 * @param string $strPath
	 * @return boolean
	 */
	private static function delDir($strPath) {

		$res = true;
		if (is_file($strPath)) {
			$res = @unlink($strPath);

		} elseif (is_dir($strPath)) {

			// Open dir
			if ($hd = @opendir($strPath)) {

				while(false !== ($entry = readdir($hd))){
					if($entry != '.' && $entry != '..'){
						if(is_file($strPath . $entry)){

							if (!@unlink($strPath . $entry)) {
								$res = false;
							}

						} elseif(is_dir($strPath . $entry)){

							//recurse!
							$res = self::delDir($strPath . $entry . '/');
							if (!$res) {
								break;
							}
						}
					}
				}

				// Close dir
				closedir($hd);
				if (!@rmdir($strPath)) {
					$res = false;
				}
			}
		}

		return $res;
	}

	/**
	 * delete file or directory
	 *
	 * @param string $dir
	 * @return boolean
	 */
	public static function del($dir) {

		$res = false;
		if (is_file($dir)) {
			$res = @unlink($dir);
		}
		elseif (is_dir($dir)) {
			$dir = realpath($dir);
			$rootDir = realpath(WAPATH_BASE);
			$systemDir = realpath(WAPATH_SYSTEM);
	
			// Check dir
			if (!in_array($dir, array($rootDir, $systemDir))) {
				$res = self::delDir($dir . '/');
			}
		}
		
		return $res;
	}

	/**
	 * move file or directories from src to dest
	 *
	 * @param string $src
	 * @param string $dest
	 * @return boolean
	 */
	public static function move($src, $dest, $newFile = false) {

		if (self::xCopy($src, $dest, true, $newFile)) {
			if (!self::del($src)) {
				return FALSE;
			}
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
    
    /**
     * write_ini_file function to support arrays and eliminate redundant speechmarking
     * 
     * @param array $assoc_arr
     * @param string $path
     * @param boolean $has_sections [optional]
     * @return boolean
     */
    public static function write_ini_file($assoc_arr, $path, $has_sections=FALSE) {
        $content = '';
        if ($has_sections) {
            foreach ($assoc_arr as $key=>$elem) {
                $content .= '['.$key.']'.PHP_EOL;
                foreach ($elem as $key2=>$elem2){
                    if(is_array($elem2)){
                        for($i=0;$i<count($elem2);$i++)
                        {
                            $content .= $key2.'[] = "'.$elem2[$i].'"'.PHP_EOL;
                        }
                    }
                    else if($elem2=='') {
                        $content .= $key2.' = '. PHP_EOL;
                    }
                    else {
                        $v = (is_string($elem2)) ? '"'.$elem2.'"' : $elem2;
                        $content .= $key2.' = ' . $v . PHP_EOL;
                    }
                    
                }
            }
        }
        else {
            foreach ($assoc_arr as $key=>$elem) {
                if(is_array($elem))
                {
                    for($i=0;$i<count($elem);$i++)
                    {
                        $content .= $key2."[] = \"".$elem[$i]."\"" . PHP_EOL;
                    }
                }
                else if($elem=="") $content .= $key2." = " . PHP_EOL;
                else $content .= $key2." = \"".$elem."\"". PHP_EOL;
            }
        }

        if (!$handle = fopen($path, 'w')) {
            return false;
        }
        if (!fwrite($handle, $content)) {
            return false;
        }
        fclose($handle);
        return true;
    }
    
}
