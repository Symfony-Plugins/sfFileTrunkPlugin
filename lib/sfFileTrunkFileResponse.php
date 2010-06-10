<?php
class sfFileTrunkFileResponse
{
	protected
	$file,
	$filename,
	$size,
	$type,
	$last_modified;

	/**
	 * sfFileTrunkFileResponse Constructor
	 * 
	 * @param string $file Full path of the file that needs to be delivered via the response
	 * @param string $filename A filename that the response will carry
	 * @param string $type The mime-type of the file to be delivered
	 * @param int $size (optional) Size in bytes of the file to be delivered
	 * @param int $last_modified (optional) Timestamp (epoch) of the file's last modified date
	 */
	public function __construct($file, $filename, $type, $size = null, $last_modified = null)
	{
		$this->file = $file;
		$this->filename = $filename;
		$this->type = $type;

		$this->size = ($size === null) ? filesize($file) : $size;
		$this->last_modified = ($last_modified === null) ? filemtime($file) : $last_modified;
	}

	/**
	 * Static method to create a sfFileTrunkFileResponse object from a FileTunk model object
	 * 
	 * @param FileTrunk $file The model object to create the response from
	 * @param string $custom_file_path (optional) A custom file path (used for example on thumbnails)
	 * @return sfFileTrunkFileResponse
	 */
	public static function createFromFileTrunk(FileTrunk $file, $custom_file_path = null)
	{
		// Store the original filename of the trunk file
		$filename = $file->getOriginalName();
		// Store the mime type
		$type = $file->getMimeType();
		// Preset size and last modified to null
		$size = null;
		$last_modified = null;
		
		// Let's see if a custom file path has been passed. If yes we will just take
		// the custom file path and assign it $file.
		if ($custom_file_path)
		{
			$file = $custom_file_path;
		} 
		else 
		{
			// let's populate the proper size, last modified and file name values 
			$size = $file->getSize();
			$last_modified = $file->getCreatedAt('U');
			$file = $file->getPath().DIRECTORY_SEPARATOR.$file->getActualName();
		}
		// return a new sfFileTrunkFileResponse object
		return new sfFileTrunkFileResponse($file, $filename, $size, $last_modified);
	}
	
	
	/**
	 * Static method to create a sfFileTrunkFileResponse from a FileTrunk object
	 * This static method will directly generate a thumbnail from the FileTrunk object if possible
	 * 
	 * @param FileTrunk $file The FileTrunk object to create the response for
	 * @param int $width max width of thumbnail
	 * @param int $height max height of thumbnail
	 * @param string $method (optional)
	 * @param mixed $background (optional)
	 * @param int $quality (optional) sets the quality of the thumbnail
	 * @return sfFileTrunkFileResponse
	 */
	public static function createFromFileTrunkForThumbnail(FileTrunk $file, $width, $height, $method='fit', $background=null, $quality = 75)
	{
		$filename = $file->generateThumbnail($width, $height, $method, $background, $quality);
		$type = $file->getMimeType();
		return new sfFileTrunkFileResponse($filename, $file->getOriginalName(), $file->getMimeType());
	}
	
	/**
	 * Method to prepare the sfWebResponse for file delivery
	 * 
	 * @param sfWebRequest $request The sfWebRequest object. This is needed to check for caching
	 * @param sfWebResponse $response The sfWebResponse object to prepare
	 * @param boolean $send_http_headers (optional) Send http headers directly. Set to false if you need to add additional headers after preparing the response
	 */
	public function prepareResponse(sfWebRequest $request, sfWebResponse $response, $send_http_headers = true)
	{
		$etag_check = $request->getHttpHeader( 'If-None-Match' );
		$etag = md5( $this->last_modified );
		$response->clearHttpHeaders();

		if ($etag == $etag_check)
		{
			$response->setStatusCode('304');
		}
		else
		{
			sfConfig::set( 'sf_web_debug', false );
			$response->setHttpHeader( 'Content-Disposition', 'filename="' . $this->filename . '"' );
			$response->setHttpHeader( 'Content-type', $this->type );
			$response->setHttpHeader( 'Content-Transfer-Encoding', 'binary' );
			$response->setHttpHeader( 'Content-Length', $this->size );
			$response->setHttpHeader( 'Last-Modified', date( 'r', $this->last_modified ) );
			$response->setHttpHeader( 'ETag', $etag );
			$response->setContent( file_get_contents($this->file) );
			if ($send_http_headers)
			{
				$response->sendHttpHeaders();
			}
		}
	}
}