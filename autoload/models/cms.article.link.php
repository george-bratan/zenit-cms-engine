<?php

	// Models\CmsArticleLink

	namespace Models;

	class CmsArticleLink extends \Model {

		static
			$public = Array(
				'title' => 'Link Title',
				'url' => 'Target URL',
			);

		static
			$default = Array(
				'name', 'url',
			);

		static
			$filters = Array(
				'name' => 'File Name',
				'url' => 'Target URL',
			);

		static
			$schema = Array(
				'title' => 'Link Title',
				'url' => 'Target URL',
			);

		function __construct($id = NULL)
		{
			parent::__construct('cms_articles_links');

			$this->define('article');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_article()
		{
			$article = new CmsArticle( $this->idarticle );

			return $article;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
		}

	}

?>