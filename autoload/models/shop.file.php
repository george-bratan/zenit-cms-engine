<?php

	// Models\ShopFile

	namespace Models;

	class ShopFile extends \Model {

		private
			$custom	= Array();

		function __construct($id = NULL)
		{
			parent::__construct('shop_files');

			$this->def('user', "(SELECT IF(firstname != '' AND lastname != '', CONCAT(lastname, ', ', firstname), CONCAT(lastname, firstname)) FROM support_users WHERE id = iduser)");

			$this->def('path');
			$this->def('size');
			$this->def('extension');

			if ($id) {
				//
				$this->where('id = ?', $id);
				$this->execute();
			}

			return $this;
		}

		function get_path()
		{
			return \Conf::get('APP:UPLOAD') . $this->file;
		}

		function get_size()
		{
			if (file_exists($this->path)) {
				return filesize($this->path);
			}

			return 0;
		}

		function get_extension()
		{
			return substr($this->original, strrpos($this->original, '.') + 1);
		}

		function defaults()
		{
			parent::defaults();

			$this->idadmin = \Session::Get('ACCOUNT.ID');

			$this->status = 1;
		}

	}

?>