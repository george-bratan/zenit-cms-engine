<?php

	// Models\CmsArticleComment

	namespace Models;

	class CmsArticleComment extends \Model {

		static
			$public = Array(
				'article' => 'Article',
				'content' => 'Comment',
				'name' => 'Contact Name',
				'email' => 'Email',
				'url' => 'Website',
				'date' => 'Date',
			);

		static
			$default = Array(
				'name', 'content', 'email', 'date',
			);

		static
			$filters = Array(
				'article' => 'Article Name',
				'name' => 'Contact Name',
				'email' => 'Email',
				'url' => 'Website',
				'date' => 'Date',
				'content' => 'Containing',
			);

		static
			$schema = Array(
				'idarticle' => 'Article',
				'name' => 'Contact Name',
				'email' => 'Email',
				'url' => 'Website',
				'date' => 'Date',
				'content' => 'Comment',
			);

		function __construct($id = NULL)
		{
			parent::__construct('cms_articles_comments');

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

			$this->status = 0;
			$this->date = '@NOW()';
			$this->url = '';
		}

		function filter()
		{
			return $this;
		}

	}

?>