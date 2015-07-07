<?php

	// Models\CmsTemplate

	namespace Models;

	class CmsTemplate extends \Model {

		static
			$public = Array(
				'name' => 'Name',
				'url' => 'URL',
			);

		static
			$default = Array(
				'name', 'url',
			);

		static
			$filters = Array(
				'name' => 'Template Name',
				'url' => 'URL',
			);

		static
			$schema = Array(
				'name' => 'Template Name',
				'idparent' => 'Start With',
			);

		static
			$DEFAULT = '<html>

<head>
  <title></title>
</head>

<body>



</body>

</html>';


		function __construct($id = NULL)
		{
			parent::__construct('cms_templates');

			$this->def('js');
			$this->def('css');
			$this->def('html');
			$this->def('parent');
			$this->def('content');
			$this->def('elements');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_js()
		{
			$js = $this->idparent ? $this->parent->js : '';

			return $js . "\n" . $this->content->js;
		}

		function get_css()
		{
			$css = $this->idparent ? $this->parent->css : '';

			return $css . "\n" . $this->content->css;
		}

		function get_content()
		{
			$version = new CmsTemplateVersion();
			$version->where('idtemplate = ? AND number = ?', $this->id, $this->version)
				->execute();

			return $version;
		}

		function get_parent()
		{
			$parent = new CmsTemplate( $this->idparent );

			return $parent;
		}

		function get_elements()
		{
			$html = $this->idparent ? $this->parent->html : '{EDITABLE CONTENT}';

			if (preg_match_all('/\{(EDITABLE )(.*)\}/iU', $html, $matches)) {
				//
				$elements = array();
				foreach ($matches[2] as $match) {
					//
					$elements[ $match ] =
						\DB::Fetch("SELECT content FROM cms_template_elements WHERE idtemplate = ? AND version = ? AND slot = ?", array($this->id, $this->version, $match));
				}

				if (!$this->idparent && !$elements['CONTENT']) {
					//
					$elements['CONTENT'] = self::$DEFAULT;
				}

				return $elements;
			}

			return array();
		}

		function get_html()
		{
			$html = $this->idparent ? $this->parent->html : '{EDITABLE CONTENT}';
			//$elements = \DB::AssociativeColumn("SELECT slot, content FROM cms_template_elements WHERE idtemplate = ? AND version = ?", array($this->id, $this->version));
			$elements = $this->elements;

			foreach ($elements as $key => $element) {
				//
				// if development and is not root (if root there is no visual way to add content)
				if ($this->version < 1 && $this->idparent) {
					//
					$element = '<div class="z-editable"><div class="z-cell" id="z-slot-'.$key.'" slot="'.$key.'"><span class="z-title">'.$key.'</span>'.($element ? $element : "Click to add").'</div></div>';
				}

				if ($element) {
					$html = str_replace('{EDITABLE '.$key.'}', $element, $html);
				}
			}

			return $html;
		}

		function defaults()
		{
			parent::defaults();

			$this->idparent = 0;
			$this->url = '';
			$this->meta_title = '';
			$this->meta_keywords = '';
			$this->meta_description = '';
			$this->status = 1;
		}

	}

?>