<?php

	// Lang

	class Lang
	{
		//@{ Framework details
		const
			TEXT_AppName = 'Zenit Framework',
			TEXT_AppVersion = '2.0.0';
		//@}


		//@{ Locale-specific error/exception messages
		const
			ERR_NoRoutes 	= 'No Routes Specified',
			ERR_Callback 	= 'The callback function %s is invalid',
			ERR_Import 		= 'Import file %s not found',
			ERR_Apache 		= 'Apache mod_rewrite module is not enabled',
			ERR_NotFound 	= 'The URL %s was not found (%s request)',
			ERR_AccDenied 	= 'Access denied on %s (%s request)',
			ERR_Write		= '%s must have write permission on %s',
			ERR_PHPExt		= 'PHP extension %s is not enabled',
			ERR_Static 		= '%s must be a static method',
			ERR_Render 		= 'Unable to render %s - file does not exist',
			ERR_Class		= 'Undefined class %s',
			ERR_Method 		= 'Undefined method %s',
			ERR_HTTPCode	= 'HTTP status code %s is invalid',
			ERR_VirtualMethod = 'Virtual Method called (%s)';
		//@}


		//@{ Locale-specific error/exception messages
		const
			ERR_Backend = 'Cache back-end is invalid',
			ERR_Store 	= 'Unable to save %s to cache',
			ERR_Fetch 	= 'Unable to retrieve %s from cache',
			ERR_Clear 	= 'Unable to clear %s from cache';
		//@}


		//@{ Locale-specific error/exception messages
		const
			ERR_ExecFail 	= 'Unable to execute prepared statement: %s',
			ERR_DBEngine 	= 'Database engine is not supported',
			ERR_Schema 		= 'Schema for % table is not available';
		//@}


		//@{ Locale-specific error/exception messages
		const
			ERR_ModelConnect 	= 'Undefined database',
			ERR_ModelEmpty 		= 'Model is empty',
			ERR_ModelArray 		= 'Must be an array of Model objects',
			ERR_ModelNotMapped 	= 'The field %s does not exist',
			ERR_ModelCantUndef 	= 'Cannot undefine a Model-mapped field (%s)',
			ERR_ModelCantUnset 	= 'Cannot unset a Model-mapped field (%s)',
			ERR_ModelConflict 	= 'Name conflict with Model-mapped field (%s)',
			ERR_ModelInvalid 	= 'Invalid virtual field expression (%s)',
			ERR_ModelReadOnly 	= 'Virtual fields are read-only (%s)';
		//@}


		const
			ERR_LoginInvalid 	= 'Incorrect username or password.<br>Check your CAPS-LOCK key and try again.';
	}

?>