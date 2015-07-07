<?php

	// Models\CmsTemplateVersion

	namespace Models;

	class CmsTemplateVersion extends \Model {

		static
			$public = Array(
			);

		static
			$default = Array(
			);

		static
			$filters = Array(
			);

		static
			$schema = Array(
			);


		function __construct($id = NULL)
		{
			parent::__construct('cms_template_versions');

			$this->def('template');
			$this->def('elements');
			$this->def('author', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM users WHERE id = idauthor)");

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_template()
		{
			$template = new CmsTemplate( $this->idtemplate );

			return $template;
		}

		function get_elements()
		{
			$elements = new CmsTemplateElement();
			$elements->where('idtemplate = ? AND version = ?', $this->idtemplate, $this->number)
				->execute();

			return $elements;
		}

		function defaults()
		{
			parent::defaults();

			$this->number = 0;
			$this->details = '';
			$this->css = '';
			$this->js = '';
			$this->date = '@NOW()';
			$this->idauthor = \Session::Get('ACCOUNT.ID');
			$this->status = 1;
		}

	}

?>