<?php

class sfFileTrunkComponents extends sfComponents
{
	public function executeFiletrunk_file()
	{
		if (!$this->file)
		{
			$this->file = FileTrunkPeer::retrieveByPk($this->id);
		}
	}
	
	public function executeFiletrunk_image()
	{
		if (!$this->file)
		{
			$this->file = FileTrunkPeer::retrieveByPk($this->id);
		}
		if (!$this->width)
		{
			$this->width = 0;
		}
		if (!$this->height)
		{
			$this->height = $this->width;
		}
		if (!$this->quality)
		{
			$this->quality = 75;
		}
		if (!$this->method)
		{
			$this->method = 'fit';
		}
	}
}