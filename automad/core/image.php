<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2013-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Image object represents a resized (and cropped) copy of a given image.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Image {
	
	/**
	 *	The filename of the source image
	 */
	
	private $originalFile;
	
	
	/**
	 *	The width of the source image.
	 */
	
	public $originalWidth;
	
	
	/**
	 *	The height of the source image.
	 */
	
	public $originalHeight;
	
	
	/**
	 *	The disired width of the new image. (May not be the resulting width, depending on cropping or original image size)
	 */
	
	private $requestedWidth;
	
	
	/**
	 *	The desired height of the new image. (May not be the resulting width, depending on cropping or original image size)
	 */
	
	private $requestedHeight;
	
	
	/**
	 *	Cropping parameter.
	 */
	
	private $crop;
	
	
	/**
	 *	The pixels to crop the image on the X-axis on both sides.
	 */
	
	private $cropX;
	
	
	/**
	 *	The pixels to crop the image on the Y-axis on both sides.
	 */
	
	private $cropY;
	
	
	/**
	 *	The given image type.
	 */
	
	private $type;
	
	
	/**
	 *	The filename of generated image.
	 */
	
	public $file;
	
	
	/**
	 *	The full file system path to the generated image.
	 */
	
	private $fileFullPath;
	
		
	/**
	 *	The width of the generated image.
	 */
	
	public $width;
	
	
	/**
	 *	The height of the generated image.
	 */
	
	public $height;
	
	
	/**
	 *	The constructor defines the main object properties from the given parameters and initiates the main methods.
	 *
	 *	@param string $originalFile    
	 *	@param integer $requestedWidth  
	 *	@param integer $requestedHeight 
	 *	@param boolean $crop            
	 */
	
	public function __construct($originalFile = false, $requestedWidth = false, $requestedHeight = false, $crop = false) {
		
		if ($originalFile) {
			
			ini_set('memory_limit', -1);

			$getimagesize = @getimagesize($originalFile);
			
			if ($getimagesize) {
			
				$this->originalFile = $originalFile;
				$this->originalWidth = $getimagesize[0];
				$this->originalHeight = $getimagesize[1];	
				$this->type = $getimagesize['mime'];
				$this->crop = $crop;
		
				if ($requestedWidth) {
					$this->requestedWidth = $requestedWidth;
				} else {
					$this->requestedWidth = $this->originalWidth;
				}
		
				if ($requestedHeight) {
					$this->requestedHeight = $requestedHeight;
				} else {
					$this->requestedHeight = $this->originalHeight;
				}
	
				// Get the possible size for the generated image (based on crop and original size).
				$this->calculateSize();
			
				// Get the filename hash, based on the given settings, to check later, if the file exists.
				$this->file = $this->getImageCacheFilePath();
				$this->fileFullPath = AM_BASE_DIR . $this->file;
		
				// Check if an image with the generated hash exists already and create the file, when neccassary.
				$this->verifyCachedImage();
				
			}
			
		}
		
	}
	
	
	/**
	 *	Calculate the size and pixels to crop for the generated image.
	 */
	
	private function calculateSize() {
		
		$originalAspect = $this->originalWidth / $this->originalHeight;
		$requestedAspect = $this->requestedWidth / $this->requestedHeight;
		
		if ($this->crop) {
			
			// Crop image
					
			if ($originalAspect > $requestedAspect) {
			
				if ($this->requestedWidth < $this->originalWidth) {
					$w = $this->requestedWidth;
				} else {
					$w = $this->originalWidth;
				}
				
				$h = $w / $requestedAspect;
				
				if ($h > $this->originalHeight) {
					$h = $this->originalHeight;
					$requestedAspect = $w / $h;
				}
				
				// crop X
				$x = ($this->originalWidth - ($this->originalHeight * $requestedAspect)) / 2;
				$y = 0;
				
				
			} else {
				
				if ($this->requestedHeight < $this->originalHeight) {
					$h = $this->requestedHeight;
				} else {
					$h = $this->originalHeight;
				}
				
				$w = $h * $requestedAspect;
				
				if ($w > $this->originalWidth) {
					$w = $this->originalWidth;
					$requestedAspect = $w / $h;
				}
				
				// crop X
				$x = 0;
				$y = ($this->originalHeight - ($this->originalWidth / $requestedAspect)) / 2;
				
			}
			
		} else {
			
			// No cropping
			
			$x = 0;
			$y = 0;
			
			if ($originalAspect > $requestedAspect) {
				
				if ($this->requestedWidth < $this->originalWidth) {
					$w = $this->requestedWidth;
				} else {
					$w = $this->originalWidth;
				}
				
				$h = $w / $originalAspect;
				
			} else {
				
				if ($this->requestedHeight < $this->originalHeight) {
					$h = $this->requestedHeight;
				} else {
					$h = $this->originalHeight;
				}
				
				$w = $h * $originalAspect;
				
			}
			
		}
		
		$this->width = $w;
		$this->height = $h;
		$this->cropX = $x;
		$this->cropY = $y;
		
	}
	
	
	/**
	 *	Create a new (resized and cropped) image from the source image and save that image in AM_DIR_CACHE_IMAGES.
	 */
	
	private function createImage() {
		
		switch($this->type){
		
			case 'image/jpeg':
				$src = imagecreatefromjpeg($this->originalFile);
				break;
			case 'image/gif':
				$src = imagecreatefromgif($this->originalFile);
				break;
			case 'image/png':
				$src = imagecreatefrompng($this->originalFile);
				break;
			default: 
				$src = false;
		    	    	break;
		
		}
		
		$dest = imagecreatetruecolor($this->width, $this->height);
		
		imagealphablending($dest, false);
		imagesavealpha($dest, true);
		imagecopyresampled($dest, $src, 0, 0, $this->cropX, $this->cropY, $this->width, $this->height, $this->originalWidth - (2 * $this->cropX), $this->originalHeight - (2 * $this->cropY));
			
		Debug::log($this, 'Saving "' . $this->fileFullPath . '"');
		
		// Create cache directory, if not existing.
		FileSystem::makeDir(AM_BASE_DIR . AM_DIR_CACHE_IMAGES);
		
		switch($this->type){
		
			case 'image/jpeg':
				imagejpeg($dest, $this->fileFullPath, AM_IMG_JPG_QUALITY);
				break;		
			case 'image/gif':
				imagegif($dest, $this->fileFullPath);
				break;
			case 'image/png':
				imagepng($dest, $this->fileFullPath);
				break;
		
		}
		
		chmod($this->fileFullPath, AM_PERM_FILE);
		
		ImageDestroy ($src);
		ImageDestroy ($dest);
		
	}
	
		
	/**
	 *	Determine the corresponding image file to a source file based on a md5 hash.
	 *	That hash is based on the source image's path, mtime, the new width and height and the cropping parameter.
	 *	If one parameter changes, the hash will be different, which will result in recreating an image.
	 *	Since the mtime is part of the hash, also any modification to the source image will be reflected in a different name.
	 *	For each size and cropping setting, a unique filename will be returned, to clearly identify that setting.
	 *
	 *	@return string The matching filename for the requested source image, based on its parameters
	 */
	
	private function getImageCacheFilePath() {
		
		$extension = strtolower(pathinfo($this->originalFile, PATHINFO_EXTENSION));
		
		if ($extension == 'jpeg') {
			$extension = 'jpg';
		}
		
		// Create unique filename in the cache folder:
		// The hash makes it possible to clearly identify an unchanged file in the cache, 
		// since the given hashData will always result in the same hash.
		// So if a file gets requested, the hash is generated from the path, calculated width x height, the mtime from the original and the cropping setting. 
		$hashData = $this->originalFile . '-' . $this->width . 'x' . $this->height . '-' . filemtime($this->originalFile) . '-' . var_export($this->crop, true);
		$hash = hash('md5', $hashData);
		
		$file = AM_DIR_CACHE_IMAGES . '/' . AM_FILE_PREFIX_CACHE . '_' . $hash . '.' . $extension;
		
		Debug::log($hashData, 'Hash data for ' . $hash);
		
		return $file;
		
	}
	
	
	/**
	 *	To verify, if the requested image is up to date, only the existence has to be tested, 
	 *	since any changes in the source image will be reflected in the filename's hash.
	 */
	
	private function verifyCachedImage() {
		
		if (!file_exists($this->fileFullPath)) {
					
			$this->createImage();
		
		}	
		
	}
		
	
}
