<?php

class Cronks_System_StaticContentSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$templateFile = sprintf(
			'%s/%s.xml', 
			AgaviConfig::get('de.icinga.web.xml_template_folder'), 
			$rd->getParameter('template')
		);

		$model = $this->getContext()->getModel('System.StaticContent', 'Cronks');
		$content = $model->getContent($templateFile);

		return $content;
	}
}

?>