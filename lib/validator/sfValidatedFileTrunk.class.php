<?php
class sfValidatedFileTrunk extends sfValidatedFile
{
	protected $trunk = null;
	
	public function save($file = null, $fileMode = 0666, $create = true, $dirMode = 0777)
	{
		$file = parent::save($file, $fileMode, $create, $dirMode);
		
		$this->trunk = new FileTrunk();
		$this->trunk->setActualName($file);
		$this->trunk->setOriginalName($this->originalName);
		$this->trunk->setSize($this->size);
		$this->trunk->setMimeType($this->type);
		$this->trunk->save();
		return $file;
	}
	
	public function getFileTrunk()
	{
		return $this->trunk;
	}
}