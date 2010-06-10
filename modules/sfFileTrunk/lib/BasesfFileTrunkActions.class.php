<?php

/**
 * Base actions for the sfFileTrunkPlugin sfFileTrunk module.
 * 
 * @package     sfFileTrunkPlugin
 * @subpackage  sfFileTrunk
 * @author      Khair-ed Din Mozzy Husseini
 * @version     SVN: $Id: BaseActions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
abstract class BasesfFileTrunkActions extends sfActions
{
	public function preExecute()
	{
		$request = $this->getRequest();
		
		// ID parameter is required
		$this->forward404Unless($request->hasParameter('id'));
		
		// Get our id make sure it is an integer
		$id = (int)$request->getParameter('id');
		
		// Retrieve the FileTrunk object
		$this->file_trunk = FileTrunkPeer::retrieveByPk($id);
		
		// We can only deliver objects that actually exist
		$this->forward404Unless($this->file_trunk);
	}
	
	public function executeIndex(sfWebRequest $request)
	{
		// We have to set the layout to false
		$this->setLayout( false );
		
		// Create a sfFileTrunkFileResponse object
		$file_response = sfFileTrunkFileResponse::createFromFileTrunk($this->file_trunk);
		
		// Prepare the reponse
		$file_response->prepareResponse($request, $this->getResponse());
		
		return sfView::NONE;
	}
	
	public function executeImage(sfWebRequest $request)
	{
		// We have to set the layout to false
		$this->setLayout( false );
		
		// Get the requested width and height parameters
		$width = $request->getParameter('width', 0);
		$height = $request->getParameter('height', 0);
		$method = $request->getParameter('method', 'fit');
		$quality = $request->getParameter('quality', 75);
		
		// if only a width was specified then we will set the height equal to the width
		if ($width && !$height)
		{
			$height = $width;
		}
		
		
		// if we have a width and a height specified then we will generate a thumbnail
		// else we just put out the original image
		if ($width && $height)
		{
			$file_response = sfFileTrunkFileResponse::createFromFileTrunkForThumbnail($this->file_trunk, $width, $height, $method, null, $quality);	
		}
		else
		{
			// Create a sfFileTrunkFileResponse object
			$file_response = sfFileTrunkFileResponse::createFromFileTrunk($this->file_trunk);
		}
		
		// Prepare the reponse
		$file_response->prepareResponse($request, $this->getResponse());
		
		return sfView::NONE;
	}
}
