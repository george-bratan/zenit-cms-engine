<?php

	// Models\MailerList

	namespace Models;

	class MailerList extends \Model {

		static
			$public = Array(
				'name' => 'List Name',
			);

		static
			$default = Array(
				'name',
			);

		static
			$filters = Array(
				'name' => 'List Name',
			);

		static
			$schema = Array(
				'name' => 'Name',
			);

		function __construct($id = NULL)
		{
			parent::__construct('mailer_lists');

			$this->def('filters');

			if ($id) {
				//
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_filters()
		{
			parse_str($this->params, $result);

			return $result;
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
		}

	}

?>