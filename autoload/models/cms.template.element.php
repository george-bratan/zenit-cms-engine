<?php

	// Models\CmsTemplateElement

	namespace Models;

	class CmsTemplateElement extends \Model {

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
			parent::__construct('cms_template_elements');

			$this->def('template');

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

		function defaults()
		{
			parent::defaults();

			$this->version = 0;
			$this->status = 1;
		}

	}

?>