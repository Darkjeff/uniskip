<?php
/* Copyright (C) 2024 Jeffinfo - Olivier Geffroy <jeff@jeffinfo.com>
 * Copyright (C) 2024 Florian HENRY <florian.henry@scopen.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    uniskip/class/actions_uniskip.class.php
 * \ingroup uniskip
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonhookactions.class.php';

/**
 * Class ActionsUniskip
 */
class ActionsUniskip extends CommonHookActions
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var int		Priority of hook (50 is used if value is not defined)
	 */
	public $priority;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Overloading the addMoreActionsButtons function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

//		if (in_array($parameters['currentcontext'], array('invoicecard'))) {
//
//			$scriptJS = '
//			<script type="text/javascript">
//				$(document).ready(function() {
//					$("div.urllink > input#onlinepaymenturl")
//				}
//			</script>';
//
//		}

		if (!$error) {
			//$this->results = array('myreturn' => 999);
			$this->resprints = $scriptJS;
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}






	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$object		   	Object output on PDF
	 * @param   string	$action     	'add', 'update', 'view'
	 * @return  int 		        	Return integer <0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
	 */
//	public function beforePDFCreation($parameters, &$object, &$action)
//	{
//		global $conf, $user, $langs;
//		global $hookmanager;
//
//		$outputlangs = $langs;
//
//		$ret = 0;
//		$deltemp = array();
//		dol_syslog(get_class($this).'::executeHooks action='.$action);
//
//		/* print_r($parameters); print_r($object); echo "action: " . $action; */
//		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
//		}
//
//		return $ret;
//	}

	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$pdfhandler     PDF builder handler
	 * @param   string	$action         'add', 'update', 'view'
	 * @return  int 		            Return integer <0 if KO,
	 *                                  =0 if OK but we want to process standard actions too,
	 *                                  >0 if OK and we want to replace standard actions.
	 */
//	public function afterPDFCreation($parameters, &$pdfhandler, &$action)
//	{
//		global $conf, $user, $langs;
//		global $hookmanager;
//
//		$outputlangs = $langs;
//
//		$ret = 0;
//		$deltemp = array();
//		dol_syslog(get_class($this).'::executeHooks action='.$action);
//
//		/* print_r($parameters); print_r($object); echo "action: " . $action; */
//		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {
//			// do something only for the context 'somecontext1' or 'somecontext2'
//		}
//
//		return $ret;
//	}


}
