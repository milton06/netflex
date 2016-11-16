<?php
namespace NetFlex\MediaBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class MediaUploadService
{
	private $request;
	private $options;
	private $errorMessages;
	private $imageObjects;
	private $response;
	
	public function __construct(RequestStack $requestStack, $mediaUploadDefaultOptions, $mediaUploadErrorMessages)
	{
		$this->request = $requestStack->getCurrentRequest();
		$this->options = $mediaUploadDefaultOptions;
		$this->errorMessages = $mediaUploadErrorMessages;
		$this->imageObjects = $this->response = [];
	}
	
	public function setUserOptions($userMediaUploadOptions)
	{
		$this->options = array_merge($this->options, $userMediaUploadOptions);
	}
	
	/**
	 * @param  string      $uploadedFile
	 * @param  string      $name
	 * @param  int         $size
	 * @param  string      $type
	 * @param  int         $error
	 * @param  int|null    $index
	 * @param  string|null $contentRange
	 * @return \stdClass
	 */
	public function uploadFile($uploadedFile, $name, $size, $type, $error, $index = null, $contentRange = null)
	{
		$file = new \stdClass();
		
		$file->name = $this->getFileName($name, $type, $contentRange);
		$file->size = $this->fixIntegerOverflow((int) $size);
		$file->type = $type;
		
		if ($this->validate($uploadedFile, $file, $error)) {
			$uploadDirectory = $this->getUploadPath();
			
			if (false === @is_dir($uploadDirectory)) {
				@mkdir($uploadDirectory, $this->options['mkdir_mode'], true);
			}
			
			$filePath = $this->getUploadPath($file->name);
			
			$appendFile = ($contentRange && @is_file($filePath) && ($this->getFileSize($filePath) < $file->size));
			
			if (($uploadedFile) && (true === @is_uploaded_file($uploadedFile))) {
				if (true === $appendFile) {
					@file_put_contents($filePath, @fopen($uploadedFile, 'r'), FILE_APPEND);
				} else {
					@move_uploaded_file($uploadedFile, $filePath);
				}
			}
			
			$fileSize = $this->getFileSize($filePath, $appendFile);
			
			if ($fileSize === $file->size) {
				$file->url = $this->getDownloadUrl($file->name);
				
				if ($this->isValidImageType($filePath)) {
					$this->handleImageFile($filePath, $file);
				}
			} else {
				$file->size = $fileSize;
				if ((! $contentRange) && (true === $this->options['discard_aborted_uploads'])) {
					@unlink($filePath);
					
					$file->error = $this->getErrorMessage('abort');
				}
			}
			
			$this->setAdditionalFileProperties($file);
		}
		
		return $file;
	}
	
	/**
	 * @param  string $fileName
	 * @return bool
	 */
	public function deleteFile($fileName)
	{
		$filePath = $this->getUploadPath($fileName);
		$success = (is_file($filePath) && ('.' !== $fileName[0]) && @unlink($filePath));
		
		if (true === $success) {
			foreach ($this->options['image_versions'] as $version => $options) {
				if (!empty($version)) {
					$file = $this->getUploadPath($fileName, $version);
					if (true === is_file($file)) {
						@unlink($file);
					}
				}
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * @param  string $name
	 * @param  string $type
	 * @param  string $contentRange
	 * @return mixed
	 */
	protected function getFileName($name, $type, $contentRange)
	{
		$name = $this->trimFileName($name);
		
		if (false === strpos($name, '.')) {
			$name = $this->fixFileExtension($name, $type);
		} else {
			list($fileBaseName, $fileExtension) = $this->getFileNameAndExtension($name);
			$fileBaseName = $this->prettifyFileBaseName($fileBaseName);
			$name = $fileBaseName . '.' . $fileExtension;
		}
		
		return $this->getUniqueFileName($name, $contentRange);
	}
	
	/**
	 * @param  string       $name
	 * @return mixed|string
	 */
	protected function trimFileName($name)
	{
		$name = trim($this->basename(stripslashes($name)), ".\x00..\x20");
		
		if (! $name) {
			$name = 'media-' . str_replace('.', '-', microtime(true));
		} elseif (preg_match(str_replace('\.', '^', $this->options['accept_media_types']), $name, $matches)) {
			$name = 'media-' . str_replace('.', '-', microtime(true)) . '.' . $name;
		} else {
			//
		}
		
		return $name;
	}
	
	/**
	 * @param  string     $name
	 * @param  null       $suffix
	 * @return string
	 */
	protected function baseName($name, $suffix = null)
	{
		$splittedName = preg_split('/\//', rtrim($name, '/ '));
		
		return substr(basename('X' . $splittedName[count($splittedName)-1], $suffix), 1);
	}
	
	/**
	 * @param  string $name
	 * @param  string $contentRange
	 * @return mixed
	 */
	protected function getUniqueFileName($name, $contentRange)
	{
		while(@is_dir($this->getUploadPath($name))) {
			$name = $this->upcountName($name);
		}
		
		$uploadedBytes = $this->fixIntegerOverflow((int) $contentRange[1]);
		
		while (@is_file($this->getUploadPath($name))) {
			if ($uploadedBytes === $this->getFileSize($this->getUploadPath($name))) {
				break;
			}
			
			$name = $this->upcountName($name);
		}
		
		return $name;
	}
	
	/**
	 * @param  string|null $name
	 * @param  string|null $version
	 * @return string
	 */
	protected function getUploadPath($name = null, $version = null) {
		$name = ($name) ? $name : '';
		$versionPath = (! $version) ? '' : $version . '/';
		
		return $this->options['media_upload_directory'] . '/' . $versionPath . $name;
	}
	
	/**
	 * @param  string $name
	 * @return mixed
	 */
	protected function upcountName($name)
	{
		return preg_replace_callback('/(?:(?:\(([\d]+)\))?(\.[^.]+))?$/', [$this, 'upcountNameCallback'], $name, 1);
	}
	
	/**
	 * @param  array $matches
	 * @return string
	 */
	protected function upcountNameCallback($matches)
	{
		$index = (isset($matches[1])) ? ((int) $matches[1] + 1) : 1;
		$extension = (isset($matches[2])) ? $matches[2] : '';
		
		return "($index)$extension";
	}
	
	/**
	 * Fix for overflowing signed 32 bit integers; works for sizes up to 2^32-1 bytes (4 GiB - 1).
	 *
	 * @param int $size
	 * @return float
	 */
	protected function fixIntegerOverflow($size) {
		if ($size < 0) {
			$size += (2.0 * (PHP_INT_MAX + 1));
		}
		
		return $size;
	}
	
	/**
	 * @param  string $name
	 * @param  bool   $clearStateCache
	 * @return float
	 */
	protected function getFileSize($name, $clearStateCache = false) {
		if ($clearStateCache) {
			if (0 <= version_compare(PHP_VERSION, '5.3.0')) {
				clearstatcache(true, $name);
			} else {
				clearstatcache();
			}
		}
		
		return $this->fixIntegerOverflow(filesize($name));
	}
	
	/**
	 * @param  string    $uploadedFile
	 * @param  \stdClass $file
	 * @param  array     $error
	 * @return bool
	 */
	protected function validate($uploadedFile, \stdClass $file, $error)
	{
		if ($error) {
			$file->error = $this->getErrorMessage($error);
			
			return false;
		}
		
		$contentLength = $this->fixIntegerOverflow((int) $this->request->server->get('CONTENT_LENGTH'));
		$postMaxSize = $this->getConfigBytes(ini_get('post_max_size'));
		
		if (($postMaxSize) && ($postMaxSize < $contentLength)) {
			$file->error = $this->getErrorMessage('post_max_size');
			
			return false;
		}
		
		if (! preg_match($this->options['accept_media_types'], $file->name)) {
			$file->error = $this->getErrorMessage('accept_media_types');
			
			return false;
		}
		
		if (($uploadedFile) && (true === @is_uploaded_file($uploadedFile))) {
			$fileSize = $this->getFileSize($uploadedFile);
		} else {
			$fileSize = $contentLength;
		}
		
		if (($this->options['max_media_size']) && ((($this->options['max_media_size'] < $fileSize) || ($this->options['max_media_size'] < $file->size)))
		) {
			$file->error = $this->getErrorMessage('max_media_size');
			
			return false;
		}
		
		if (($this->options['min_media_size']) && ((($this->options['min_media_size'] > $fileSize) || ($this->options['min_media_size'] > $file->size)))
		) {
			$file->error = $this->getErrorMessage('min_media_size');
			
			return false;
		}
		
		if ((is_int($this->options['max_number_of_medias'])) &&
			($this->options['max_number_of_medias'] <= $this->countFileObjects()) && (false === @is_file($this->getUploadPath($file->name)))) {
			$file->error = $this->getErrorMessage('max_number_of_medias');
			
			return false;
		}
		
		$maxWidth = $this->options['max_width'];
		$minWidth = $this->options['min_width'];
		$maxHeight = $this->options['max_height'];
		$minHeight = $this->options['min_height'];
		
		if (($maxWidth || $minWidth || $maxHeight || $minHeight) && (preg_match($this->options['image_media_types'], $file->name))) {
			list($imageWidth, $imageHeight) = @getimagesize($uploadedFile);
		}
		
		if (!empty($imageWidth)) {
			if (($maxWidth) && ($maxWidth < $imageWidth)) {
				$file->error = $this->getErrorMessage('max_width');
				
				return false;
			}
			
			if (($minWidth) && ($imageWidth < $minWidth)) {
				$file->error = $this->getErrorMessage('min_width');
				
				return false;
			}
			
			if (($maxHeight) && ($maxHeight < $imageHeight)) {
				$file->error = $this->getErrorMessage('max_height');
				
				return false;
			}
			
			if (($minHeight) && ($imageHeight < $minHeight)) {
				$file->error = $this->getErrorMessage('min_height');
				
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * @param  array $error
	 * @return mixed
	 */
	protected function getErrorMessage($error) {
		return (isset($this->errorMessages[$error])) ? $this->errorMessages[$error] : $error;
	}
	
	/**
	 * @param  string $val
	 * @return float
	 */
	protected function getConfigBytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		$val = (int) $val;
		
		switch ($last) {
			case 'g':
				$val *= 1024;
			
			case 'm':
				$val *= 1024;
			
			case 'k':
				$val *= 1024;
		}
		
		return $this->fixIntegerOverflow($val);
	}
	
	/**
	 * @return int
	 */
	protected function countFileObjects() {
		return count($this->getFileObjects('isValidFileObject'));
	}
	
	/**
	 * @param string $iterator
	 * @return array
	 */
	protected function getFileObjects($iterator = 'getFileObject') {
		$uploadDirectory = $this->getUploadPath();
		
		if (false === is_dir($uploadDirectory)) {
			return [];
		}
		
		return array_values(array_filter(array_map([$this, $iterator], scandir($uploadDirectory))));
	}
	
	/**
	 * @param  string $name
	 *
	 * @return null|\stdClass
	 */
	protected function getFileObject($name) {
		if (true === $this->isValidFileObject($name)) {
			$file = new \stdClass();
			
			$file->name = $name;
			$file->size = $this->getFileSize($this->getUploadPath($name));
			$file->url = $this->getDownloadUrl($file->name);
			
			foreach ($this->options['image_versions'] as $version => $options) {
				if (!empty($version)) {
					if ((true === @is_file($this->getUploadPath($file->name, $version))) && ('thumbnail' === $version)) {
						$file->{$version.'Url'} = $this->getDownloadUrl($file->name, $version);
					}
				}
			}
			
			$this->setAdditionalFileProperties($file);
			
			return $file;
		}
		
		return null;
	}
	
	/**
	 * @param \stdClass $file
	 */
	protected function setAdditionalFileProperties(\stdClass $file) {
		$file->deleteUrl = $this->options['script_url'] . '?' . $this->getSingularParamName() . '=' . rawurlencode($file->name);
		$file->deleteType = $this->options['delete_type'];
		
		if ('DELETE' !== $file->deleteType) {
			$file->deleteUrl .= '&_method=DELETE';
		}
	}
	
	/**
	 * @return string
	 */
	protected function getSingularParamName() {
		return substr($this->options['param_name'], 0, -1);
	}
	
	/**
	 * @param  string $name
	 * @return bool
	 */
	protected function isValidFileObject($name) {
		$filePath = $this->getUploadPath($name);
		
		if ((true === is_file($filePath)) && ('.' !== $name[0])) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * @param  string $filePath
	 * @return bool|int
	 */
	protected function isValidImageType($filePath) {
		if (! preg_match($this->options['image_media_types'], $filePath)) {
			return false;
		}
		
		if (function_exists('exif_imagetype')) {
			return @exif_imagetype($filePath);
		}
		
		$imageInfo = @getimagesize($filePath);
		
		return ($imageInfo && $imageInfo[0] && $imageInfo[1]);
	}
	
	/**
	 * @param string $filePath
	 * @param string $file
	 */
	protected function handleImageFile($filePath, $file) {
		$failedVersions = [];
		
		foreach ($this->options['image_versions'] as $version => $options) {
			if ($this->gdCreateScaledImage($file->name, $version, $options)) {
				if ((! empty($version)) && ('thumbnail' === $version)) {
					$file->{$version.'Url'} = $this->getDownloadUrl($file->name, $version);
				} else {
					$file->size = $this->getFileSize($filePath, true);
				}
			} else {
				$failedVersions[] = $version ? $version : 'original';
			}
		}
		
		if (0 < count($failedVersions)) {
			$file->error = $this->getErrorMessage('image_resize') . ' (' . implode($failedVersions, ', ') . ')';
		}
	}
	
	/**
	 * @param string $fileName
	 * @param string $version
	 * @param array $options
	 * @return bool
	 */
	protected function gdCreateScaledImage($fileName, $version, $options) {
		if (false === function_exists('imagecreatetruecolor')) {
			return false;
		}
		
		list($filePath, $versionFilePath) = $this->getScaledImageFilePaths($fileName, $version);
		
		$type = strtolower(substr(strrchr($fileName, '.'), 1));
		
		switch ($type) {
			case 'jpg':
			case 'jpeg':
				$sourceFunction = 'imagecreatefromjpeg';
				$writeFunction = 'imagejpeg';
				$imageQuality = isset($options['jpeg_quality']) ? $options['jpeg_quality'] : 75;
				
				break;
			
			case 'gif':
				$sourceFunction = 'imagecreatefromgif';
				$writeFunction = 'imagegif';
				$imageQuality = null;
				
				break;
			
			case 'png':
				$sourceFunction = 'imagecreatefrompng';
				$writeFunction = 'imagepng';
				$imageQuality = isset($options['png_quality']) ? $options['png_quality'] : 9;
				
				break;
			
			default:
				
				return false;
		}
		
		$sourceImage = $this->gdGetImageObject($filePath, $sourceFunction, ! empty($options['no_cache']));
		
		$maxWidth = $imageWidth = @imagesx($sourceImage);
		$maxHeight = $imageHeight = @imagesy($sourceImage);
		
		if (! empty($options['max_width'])) {
			$maxWidth = $options['max_width'];
		}
		
		if (! empty($options['max_height'])) {
			$maxHeight = $options['max_height'];
		}
		
		$scale = min(($maxWidth / $imageWidth), ($maxHeight / $imageHeight));
		
		if (1 <= $scale) {
			if ($filePath !== $versionFilePath) {
				return @copy($filePath, $versionFilePath);
			}
			
			return true;
		}
		
		$dstX = $dstY = 0;
		
		if (false === $options['crop']) {
			$newWidth = ($imageWidth * $scale);
			$newHeight = ($imageHeight * $scale);
			
			$newImage = @imagecreatetruecolor($newWidth, $newHeight);
		} else {
			if (($maxWidth / $maxHeight) <= ($imageWidth / $imageHeight)) {
				$newWidth = ($imageWidth / ($imageHeight / $maxHeight));
				$newHeight = $maxHeight;
			} else {
				$newWidth = $maxWidth;
				$newHeight = ($imageHeight / ($imageWidth / $maxWidth));
			}
			
			$dstX = 0 - ($newWidth - $maxWidth) / 2;
			$dstY = 0 - ($newHeight - $maxHeight) / 2;
			
			$newImage = @imagecreatetruecolor($maxWidth, $maxHeight);
		}
		
		switch ($type) {
			case 'gif':
			case 'png':
				@imagecolortransparent($newImage, @imagecolorallocate($newImage, 0, 0, 0));
			
			case 'png':
				@imagealphablending($newImage, false);
				@imagesavealpha($newImage, true);
				
				break;
		}
		
		$success = @imagecopyresampled($newImage, $sourceImage, $dstX, $dstY, 0, 0, $newWidth, $newHeight, $imageWidth, $imageHeight) && $writeFunction($newImage, $versionFilePath, $imageQuality);
		
		$this->gdSetImageObject($filePath, $newImage);
		
		return $success;
	}
	
	/**
	 * @param  string $fileName
	 * @param  string $version
	 * @return array
	 */
	protected function getScaledImageFilePaths($fileName, $version) {
		$filePath = $this->getUploadPath($fileName);
		
		if (! empty($version)) {
			$versionDirectory = $this->getUploadPath(null, $version);
			
			if (false === is_dir($versionDirectory)) {
				@mkdir($versionDirectory, $this->options['mkdir_mode'], true);
			}
			
			$versionFilePath = $versionDirectory . '/' . $fileName;
		} else {
			$versionFilePath = $filePath;
		}
		
		return [$filePath, $versionFilePath];
	}
	
	/**
	 * @param  string     $filePath
	 * @param  string     $function
	 * @param  bool       $noCache
	 * @return mixed
	 */
	protected function gdGetImageObject($filePath, $function, $noCache = false) {
		if (empty($this->imageObjects[$filePath]) || $noCache) {
			$this->gdDestroyImageObject($filePath);
			
			$this->imageObjects[$filePath] = $function($filePath);
		}
		
		return $this->imageObjects[$filePath];
	}
	
	/**
	 * @param string $filePath
	 * @param string $image
	 */
	protected function gdSetImageObject($filePath, $image) {
		$this->gdDestroyImageObject($filePath);
		
		$this->imageObjects[$filePath] = $image;
	}
	
	/**
	 * @param  string $filePath
	 * @return bool
	 */
	protected function gdDestroyImageObject($filePath) {
		$image = (isset($this->imageObjects[$filePath])) ? $this->imageObjects[$filePath] : null;
		
		return ($image && @imagedestroy($image));
	}
	
	/**
	 * @param  string     $fileName
	 * @param  null       $version
	 * @param  bool       $direct
	 * @return string
	 */
	protected function getDownloadUrl($fileName, $version = null, $direct = false) {
		$versionPath = (empty($version)) ? '' : rawurlencode($version).'/';
		
		return $this->options['media_upload_url'] . $versionPath . rawurlencode($fileName);
	}
	
	/**
	 * @param  string $name
	 * @param  string $type
	 * @return string
	 */
	protected function fixFileExtension($name, $type)
	{
		if (preg_match($this->options['accept_media_types'], $type, $matches)) {
			$name .= $matches[1];
		}
		
		return $name;
	}
	
	/**
	 * @param  string $name
	 * @return array
	 */
	protected function getFileNameAndExtension($name)
	{
		$fileBaseName = substr($name, 0, strrpos($name, '.'));
		$fileExtension = str_replace($fileBaseName . '.', '', $name);
		
		return [$fileBaseName, $fileExtension];
	}
	
	/**
	 * @param  string $fileBaseName
	 * @return mixed
	 */
	protected function prettifyFileBaseName($fileBaseName)
	{
		$fileBaseName = preg_replace('/[^a-z0-9]/i', '-', $fileBaseName);
		$fileBaseName = preg_replace('/(\-)\1+/i', '-', $fileBaseName);
		$fileBaseName = substr($fileBaseName, 0, 255);
		
		return $fileBaseName;
	}
}