<?php

	// Models\CmsArticle

	namespace Models;

	class CmsArticle extends \Model {

		static
			$public = Array(
				'title' => 'Title',
				'category' => 'Category',
				'keywords' => 'Keywords',
				'pubdate' => 'Published',
				'startdate' => 'Starts',
				'enddate' => 'Ends',
				'location' => 'Location',
				'contact' => 'Contact Name',
				'email' => 'Contact Email',
				'phone' => 'Contact Phone',
				'url' => 'Website',
				'comments' => 'Comments',
			);

		static
			$default = Array(
				'title', 'category', 'keywords', 'pubdate',
			);

		static
			$filters = Array(
				'title' => 'Article Title',
				'idcategory' => 'Category',
				'keywords' => 'Keywords',
				'pubdate' => 'Publication Date',
				'startdate' => 'Starts',
				'enddate' => 'Ends',
				'location' => 'Location Name',
				'body' => 'Containing',
			);

		static
			$schema = Array(
				'title' => 'Article Title',
				'idcategory' => 'Category',
				'keywords' => 'Keywords',
				'pubdate' => 'Publication Date',
				'startdate' => 'Starts',
				'enddate' => 'Ends',
				'location' => 'Location Name',
				'address' => 'Address',
				'coords' => 'Location Map',
				'contact' => 'Contact Name',
				'email' => 'Contact Email',
				'phone' => 'Contact Phone',
				'url' => 'Website',
				'body' => 'Body',
				'other' => 'Other Information',
				'cancomment' => 'Comments Enabled',
			);

		function __construct($id = NULL)
		{
			parent::__construct('cms_articles');

			$this->def('labels'); // multiple
			$this->def('images'); // multiple
			$this->def('links');  // title => link
			$this->def('comments'); // multiple
			$this->def('category');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_name()
		{
			return $this->title;
		}

		function get_labels()
		{
			$labels = new CmsLabel();
			$labels->where('status > 0 AND id IN (SELECT idlabel FROM cms_articles_labels WHERE idarticle = ?)', $this->id)
				->execute();

			return $labels;
		}

		function get_images()
		{
			$images = new CmsArticleFile();
			$images->where('idarticle = ? AND status > -1', $this->id)
				->order('ord ASC')
				->execute();

			return $images;
		}

		function get_links()
		{
			$links = new CmsArticleLink();
			$links->where('idarticle = ?', $this->id)
				->execute();

			return $links;
		}

		function get_comments()
		{
			$comments = new CmsArticleComment();
			$comments->where('idarticle = ?', $this->id)
				->order('date ASC')
				->execute();

			return $comments;
		}

		function get_category()
		{
			 $category = new CmsCategory( $this->idcategory );
			 return $category->name;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 0;
			$this->cancomment = 0;
			$this->pubdate = '@NOW()';
		}

	}

?>