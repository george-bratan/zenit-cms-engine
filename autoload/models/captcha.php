<?php

	// Models\Captcha

	namespace Models;

	class Captcha extends \Model {

		static
			$public = Array(
				'test' => 'Test',
				'answer' => 'Answers',
			);

		static
			$default = Array(
				'test', 'answer',
			);

		static
			$filters = Array(
				'test' => 'Test',
				'answer' => 'Answers',
			);

		static
			$schema = Array(
				'test' => 'Test',
				'answer' => 'Answers',
			);


		function __construct($id = NULL)
		{
			parent::__construct('captcha');

			$this->def('name');
			$this->def('values');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_name()
		{
			return $this->test;
		}

		function get_values()
		{
			return \Util::split($this->answer);
		}

		function defaults()
		{
			parent::defaults();

			$this->status = 1;
		}

	}

?>