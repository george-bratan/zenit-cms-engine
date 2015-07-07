<?php

	// Models\CmsArticleFile

	namespace Models;

	class CmsArticleFile extends \Model {

		static
			$public = Array(
				'title' => 'Title',
				'name' => 'File Name',
			);

		static
			$default = Array(
				'title', 'name',
			);

		static
			$filters = Array(
				'title' => 'Title',
				'name' => 'File Name',
			);

		static
			$schema = Array(
				'title' => 'Title',
				'name' => 'File Name',
				//'disk' => 'Disk Name',
				'description' => 'Description',
			);

		function __construct($id = NULL)
		{
			parent::__construct('cms_articles_files');

			$this->def('gallery');
			$this->def('article');
			$this->def('url');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_gallery()
		{
			$gallery = new CmsArticleFile();
			$gallery->where('status > -1 AND idarticle = ?', $this->idarticle)
				->order('ord ASC')
				->execute();

			return $gallery;
		}

		function get_article()
		{
			$article = new CmsArticle( $this->idarticle );

			return $article;
		}

		function get_url()
		{
			return \Conf::Get('WWW:UPLOAD') . $this->disk;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
			$this->ord = time();
			$this->title = '';
			$this->description = '';
		}

	}

?>