<?php

	// Models\MailerMessage

	namespace Models;

	class MailerMessage extends \Model {

		static
			$public = Array(
				'name' => 'Recipient',
				'email' => 'Email',
			);

		static
			$default = Array(
				'name', 'email',
			);

		static
			$filters = Array(
				'name' => 'Recipient Name',
				'email' => 'Email',
			);

		static
			$schema = Array(
				'name' => 'Recipient Name',
				'email' => 'Email',
			);


		function __construct($id = NULL)
		{
			parent::__construct('mailer_messages');

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

			$this->date = '@NOW()';
			$this->status = 0;
		}

	}

?>