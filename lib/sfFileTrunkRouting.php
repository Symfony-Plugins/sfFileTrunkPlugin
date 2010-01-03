<?php
class sfFileTrunkRouting
{
	static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
	{
		$routing = $event->getSubject();
		// add plug-in routing rules on top of the existing ones
		$routing->prependRoute(
	      	'sf_file_trunk', 
			new sfRoute(
	      		'/file/:id', 
				array(
	      			'module' 		=> 'sfFileTrunk',
					'action' 		=> 'index'
	      		),
	      		array('id' => '\d+')
	      	)
      	);
      	      	
      	$routing->prependRoute(
	      	'sf_file_trunk_image', 
			new sfRoute(
	      		'/file/image/:id/:width/:height', 
				array(
	      			'module'		=> 'sfFileTrunk',
					'action'		=> 'image',
					'width' 		=> 0,
					'height'		=> 0
	      		),
	      		array('id' => '\d+', 'width' => '\d+', 'height' => '\d+')
	      	)
      	);
	}

}