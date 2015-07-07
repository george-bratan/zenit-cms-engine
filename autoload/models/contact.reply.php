<?php

	// Models\ContactReply

	namespace Models;

	class ContactReply extends \Model {

		static
			$public = Array(
				'name' => 'Name',
				'content' => 'Content',
			);

		static
			$default = Array(
				'name', 'content',
			);

		static
			$filters = Array(
				'name' => 'Name',
				'content' => 'Containing',
			);

		static
			$schema = Array(
				'name' => 'Name',
				'content' => 'Standard Reply',
			);


		function __construct($id = NULL)
		{
			parent::__construct('contact_replies');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
		}

	}

?>