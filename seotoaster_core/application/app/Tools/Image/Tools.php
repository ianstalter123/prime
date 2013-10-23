<?php
/**
 * Tools
 *
 * @author Pavel Kovalyov <pavlo.kovalyov@gmail.com>
 */
class Tools_Image_Tools {
	public static $imgResizedFolders = array(
		'small',
		'medium',
		'large',
		'product',
		'crop',
        'thumbnails'
	);

	public function init() {
		if (!Zend_Registry::isRegistered('extConfig')) {
			$configTable   = new Application_Model_DbTable_Config();
			Zend_Registry::set('extConfig', $configTable->selectConfig());
		}
	}

	public static function resize($imageFile, $newWidth,  $saveProportion = true, $destination = null, $crop = false, $fixRotate = true) {
		if ( !$imageFile  || !$newWidth){
			return 'Missing parameters';
		}

		if (!is_file($imageFile) || !is_readable($imageFile)){
			return 'No file specified';
		}

		$iniConfig = Zend_Registry::get('misc');
		//setting quality
		$quality = isset($iniConfig['imgQuality']) ? $iniConfig['imgQuality'] : 90;
        $sessionHelper = Zend_Registry::get('session');
        if(isset($sessionHelper->imageQuality)){
            $quality = $sessionHelper->imageQuality;
        }
        $pngQuality = floor((100-$quality)/10);

		$fileInfo	= getimagesize($imageFile);
		$imgWidth	= $fileInfo[0];
		$imgHeight	= $fileInfo[1];
		$fileType	= $fileInfo[2];
		$mimeType	= $fileInfo['mime'];

		if ($imgWidth >= $newWidth){
			if ($saveProportion) {
				$newHeight = $imgHeight * $newWidth / $imgWidth;
			} else {
				$newHeight = $newWidth;
			}
		} else {
			// if the original size less then it needs to resized at
			// copying original file to destination and exiting
			if ($destination){
				if(isset($sessionHelper->imageQuality)){
                    $optimizedImageName = preg_replace('~\.[a-zA-Z]{3,4}~iu', '.jpg',Tools_Filesystem_Tools::basename($imageFile));
                    switch ($mimeType) {
                        case 'image/gif':
                            $image = imagecreatefromgif($imageFile);
                            imagejpeg($image, $destination.DIRECTORY_SEPARATOR.$optimizedImageName, $quality);
                            imagedestroy($image);
                            break;
                        case 'image/jpg':
                        case 'image/jpeg':
                            $image = imagecreatefromjpeg($imageFile);
                            imagejpeg($image, $destination.DIRECTORY_SEPARATOR.$optimizedImageName, $quality);
                            imagedestroy($image);
                            break;
                        case 'image/png':
                            $image = imagecreatefrompng($imageFile);
                            $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                            imagealphablending($bg, true);
                            imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                            imagedestroy($image);
                            imagejpeg($bg, $destination.DIRECTORY_SEPARATOR.$optimizedImageName, 90);
                            imageDestroy($bg);
                            break;
                    }
                }else{
                    copy($imageFile, $destination.DIRECTORY_SEPARATOR.Tools_Filesystem_Tools::basename($imageFile));
                }
                //
			}
			return true;
		}
		$saveAlphaChannel = false;
		switch ($mimeType) {
			case 'image/gif':
				$image = imagecreatefromgif($imageFile);
				$saveAlphaChannel = true;
				break;
			case 'image/jpg':
			case 'image/jpeg':
				$image = imagecreatefromjpeg($imageFile);
				break;
			case 'image/png':
				$image = imagecreatefrompng($imageFile);
				$saveAlphaChannel = true;
				break;
			default:
				return 'Unknow MIME type';
				break;
		}
        
        if($fixRotate && function_exists('exif_read_data')){
            $exif = @exif_read_data($imageFile, 0, true);
            if(isset($exif['IFD0']['Orientation'])){
                $ort = $exif['IFD0']['Orientation'];
                switch($ort){
                    default:
                    case 1: // nothing
                        break;
                    case 3: // 180 rotate left
                         $image = imagerotate($image, 180, 0);
                        break;
                    case 6: // 90 rotate right
                        $image = imagerotate($image, -90, 0);
                        
                        break;
                    case 8:    // 90 rotate left
                        $image = imagerotate($image, 90, 0);
                        
                        break;
                }
                if($ort == 6 || $ort == 8){
                    list($newWidth, $newHeight) = array($newHeight, $newWidth);
                    list($imgWidth, $imgHeight) = array($imgHeight, $imgWidth);
                } 
                
          }
        }
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
		// fix for transparency
		if ($saveAlphaChannel) {
			imagealphablending($newImage, false);
			imagesavealpha($newImage, true);
			$transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
			imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
		}

		if($crop) {
			$originalAspect = $imgWidth / $imgHeight;
			$thumbAspect    = $newWidth / $newHeight;
			if($originalAspect >= $thumbAspect) {
				$thumbHeight = $newHeight;
				$thumbWidth  = $imgWidth / ($imgHeight / $newHeight);
			}
			else {
				$thumbWidth  =  $newWidth;
				$thumbHeight = $imgHeight / ($imgWidth / $newWidth);
			}
			imagecopyresampled(
				$newImage,
				$image,
				0 - ($thumbWidth - $newWidth) / 2,
				0 - ($thumbHeight - $newHeight) / 2,
				0, 0,
				$thumbWidth, $thumbHeight,
				$imgWidth, $imgHeight
			);
		}
		else {
			imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $imgWidth, $imgHeight);
		}

		if ($destination) {
			if (!is_dir($destination)){
				Tools_Filesystem_Tools::mkDir($destination);
			}
			$imageFile = $destination . DIRECTORY_SEPARATOR . Tools_Filesystem_Tools::basename($imageFile);
		}

        if(!isset($sessionHelper->imageQuality)){
            switch ($mimeType) {
                case 'image/gif':
                    imagegif($newImage, $imageFile);
                    break;
                case 'image/jpeg':
                    imagejpeg($newImage, $imageFile, $quality);
                    break;
                case 'image/png':
                    imagepng($newImage, $imageFile, $pngQuality);
                    break;
                default:
                    return 'Unknow MIME type';
                    break;
            }
        }else{
            $optimizedImageName = preg_replace('~\.[a-zA-Z]{3,4}~iu', '.jpg',Tools_Filesystem_Tools::basename($imageFile));      
			imagejpeg($newImage,$destination.DIRECTORY_SEPARATOR.$optimizedImageName, $quality);
        }
		imagedestroy($newImage);
		imagedestroy($image);

		return true;
	}

    public static function optimizeImage($imageFile, $quality){
        $fileInfo	= getimagesize($imageFile);
        $mimeType	= $fileInfo['mime'];
        switch ($mimeType) {
            case 'image/jpg':
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($imageFile);
                    imagejpeg($image, $imageFile, $quality);
                    imagedestroy($image);
                break;
                default:
                    return 'Unknow MIME type';
                    break;
        }
    }
    /*
     * optimize original image
     * @param string original file
	 * @param string desination of resized files
     * @param string desination of resized files
     */
    public static function optimizeOriginalImage($imageFile, $savePath, $quality){
        $fileInfo	= getimagesize($imageFile);
        $mimeType	= $fileInfo['mime'];
        $destination = $savePath.DIRECTORY_SEPARATOR.'original'.DIRECTORY_SEPARATOR;
        $optimizedImageName = preg_replace('~\.[a-zA-Z]{3,4}~iu', '.jpg',Tools_Filesystem_Tools::basename($imageFile));    
        switch ($mimeType) {
                case 'image/gif':
                    $image = imagecreatefromgif($imageFile);
                    imagejpeg($image, $destination.$optimizedImageName, $quality);
                    imagedestroy($image);
                    Tools_Filesystem_Tools::deleteFile($imageFile);
                    break;
                case 'image/jpg':
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($imageFile);
                    Tools_Filesystem_Tools::deleteFile($imageFile);
                    imagejpeg($image, $destination.$optimizedImageName, $quality);
                    imagedestroy($image);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($imageFile);
                    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                    imagealphablending($bg, true);
                    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                    imagedestroy($image);
                    imagejpeg($bg, $destination.$optimizedImageName, 90);
                    imageDestroy($bg);
                    Tools_Filesystem_Tools::deleteFile($imageFile);
                    break;
                default:
                    return 'Unknow MIME type';
                    break;
        }
            
    }

	/**
	 * Batch resize for image upload proccess
	 * @param string original file
	 * @param string desination of resized files
	 * @return boolean|array true-on success, array when errors occur
	 */
	public static function batchResize($imageFile, $destination) {
		$imageFile = trim($imageFile);
		$destination = trim($destination);

		if (empty($imageFile) || empty ($destination)){
			return false;
		}

		$dbConfig = Zend_Registry::get('extConfig');
		$iniConfig = Zend_Registry::get('misc');

		$sizeConfig = array(
			'small'	 => intval($dbConfig['imgSmall']),
			'medium' => intval($dbConfig['imgMedium']),
			'large'	 => intval($dbConfig['imgLarge']),
			'product' => intval($iniConfig['imgProduct'])
		);

		$errors = array();

		foreach ($sizeConfig as $type => $size){
			if (!is_dir($destination.DIRECTORY_SEPARATOR.$type)){
				Tools_Filesystem_Tools::mkDir($destination.DIRECTORY_SEPARATOR.$type);
			}
			$result = self::resize($imageFile, $size, true, $destination.DIRECTORY_SEPARATOR.$type );
			if ($result !== true){
				array_push($errors, $result);
			}
		}

		return empty($result) ? true : $result;
	}

	/**
	 * Method removes images with given name in the given directory via recursive scan of subfolders
	 * such as, small, medium, etc.
	 * @param string $imageName Name of image to be deleted
	 * @param string $folderName Name of folder where image is
	 * @return mixed  Boolean true on success of all operations array with errors
	 * @return mixed  Boolean false on empty parameters given
	 * @return mixed  Array with errors if something went wrong
	 */
	public static function removeImageFromFilesystem($imageName, $folderName) {
		$imageName = trim($imageName);
		$folderName = trim($folderName);
		if (empty ($imageName) || empty ($folderName)){
			return false;
		}

		$websiteConfig = Zend_Registry::get('website');

		$folderPath = $websiteConfig['path'].$websiteConfig['media'].$folderName;
		if (!is_dir($folderPath)) {
			throw new Exceptions_SeotoasterException('Wrong folder name specified');
		}

		$errorCount = 0;

		$subFoldersList = array_merge(self::$imgResizedFolders, array('original'));

		//list of file that can be removed
		$removable = array();

		foreach ($subFoldersList as $key => $subfolder) {
			if (!is_dir($folderPath.DIRECTORY_SEPARATOR.$subfolder)){
				error_log('Not a folder:'.$folderPath.DIRECTORY_SEPARATOR.$subfolder);
				unset($subFoldersList[$key]);
				continue;
			}
			$filename = $folderPath.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.$imageName;
			//checking if enough permission to remove file
			if (is_file($filename)) {
				array_push($removable, $filename);
            }
		}

		/**
		 * checking if we can remove all files at once
		 * if not - returning with error
		 */
        
        foreach ($removable as $file) {
            if(!is_writable($file)){
                return 'Permission denied';
            }
        }
		
		foreach ($removable as $file) {
            try {
                Tools_Filesystem_Tools::deleteFile($file);
			} catch (Exceptions_SeotoasterException $e) {
				$errorCount++;
				error_log($file.': '. $e->getMessage() );
			}
		}
		if ($errorCount){
			return false;
		}
		return true;
	}
}
