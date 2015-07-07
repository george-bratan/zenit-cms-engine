<?php

	// Models\MailerCampaign

	namespace Models;

	class MailerCampaign extends \Model {

		static
			$public = Array(
				'name' => 'Campaign',
				'subject' => 'Subject',
				'feed' => 'Recipient List',
				'recipients' => 'Recipients',
				'date' => 'Sent on',
			);

		static
			$default = Array(
				'subject', 'feed', 'recipients', 'date',
			);

		static
			$filters = Array(
				'name' => 'Campaign',
				'subject' => 'Subject',
				'date' => 'Sent on',
			);

		static
			$schema = Array(
				'name' => 'Campaign',
				'subject' => 'Subject',
				'date' => 'Sent on',
			);


		function __construct($id = NULL)
		{
			parent::__construct('mailer_campaigns');

			$this->def('name', '(SELECT name FROM cms_templates WHERE id = idtemplate)');
			$this->def('recipients', '(SELECT COUNT(*) FROM mailer_messages WHERE idcampaign = M.id)');

			$this->def('messages');

			if ($id)
			{
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_messages()
		{
			$model = new MailerMessage();
			$model->where('idcampaign = ?', $this->id)
				->execute();

			return $model;
		}

		function defaults()
		{
			parent::defaults();

			$this->date = '@NOW()';
			$this->status = 1;
		}

	}

?>