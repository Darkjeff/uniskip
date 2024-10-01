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
 * \file    core/triggers/interface_99_modUniskip_UniskipTriggers.class.php
 * \ingroup uniskip
 * \brief   Example trigger.
 *
 * Put detailed description here.
 *
 * \remarks You can create other triggers by copying this one.
 * - File name should be either:
 *      - interface_99_modUniskip_MyTrigger.class.php
 *      - interface_99_all_MyTrigger.class.php
 * - The file must stay in core/triggers
 * - The class name must be InterfaceMytrigger
 */

require_once DOL_DOCUMENT_ROOT . '/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for Uniskip module
 */
class InterfaceUniskipTriggers extends DolibarrTriggers
{
	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "demo";
		$this->description = "Uniskip triggers.";
		// 'development', 'experimental', 'dolibarr' or version
		$this->version = 'dolibarr';
		$this->picto = 'uniskip@uniskip';
	}

	/**
	 * Trigger name
	 *
	 * @return string Name of trigger file
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Trigger description
	 *
	 * @return string Description of trigger file
	 */
	public function getDesc()
	{
		return $this->description;
	}


	/**
	 * Function called when a Dolibarrr business event is done.
	 * All functions "runTrigger" are triggered if file
	 * is inside directory core/triggers
	 *
	 * @param string $action Event action code
	 * @param CommonObject $object Object
	 * @param User $user Object user
	 * @param Translate $langs Object langs
	 * @param Conf $conf Object conf
	 * @return int                    Return integer <0 if KO, 0 if no triggered ran, >0 if OK
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		if (!isModEnabled('uniskip')) {
			return 0; // If module is not enabled, we do nothing
		}

		// Put here code you want to execute when a Dolibarr business events occurs.
		// Data and type of action are stored into $object and $action

		// You can isolate code for each action in a separate method: this method should be named like the trigger in camelCase.
		// For example : COMPANY_CREATE => public function companyCreate($action, $object, User $user, Translate $langs, Conf $conf)
		$methodName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($action)))));
		$callback = array($this, $methodName);
		if (is_callable($callback)) {
			dol_syslog(
				"Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id
			);

			return call_user_func($callback, $action, $object, $user, $langs, $conf);
		}

		return 0;
	}

	private function billValidate($action, $object, $user, $langs, $conf)
	{
		$langs->load("uniskip@uniskip");
		if (isModEnabled('uniskip') && function_exists('curl_init')) {
			if (!empty(getDolGlobalString('UNISKIP_LOGIN'))
				&& !empty(getDolGlobalString('UNISKIP_PASSWORD'))
				&& !empty(getDolGlobalString('UNISKIP_URL'))) {

				$api_key = '';
				$compte_interne_id='';

				$curl = curl_init(getDolGlobalString('UNISKIP_URL') . 'login');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

				curl_setopt($curl, CURLOPT_POST, true);

				$dataString = 'email=' . urlencode(getDolGlobalString('UNISKIP_LOGIN'));
				$dataString .= '&password=' . urlencode(getDolGlobalString('UNISKIP_PASSWORD'));
				curl_setopt($curl, CURLOPT_POSTFIELDS, $dataString);

				curl_setopt($curl, CURLOPT_HTTPHEADER, [
					'accept: application/json',
					'Content-Type: application/x-www-form-urlencoded',
				]);

				// Execute cURL request with all previous settings
				$response = curl_exec($curl);
				if ($response === false) {
					setEventMessages(null, [curl_error($curl)], 'warning');
					curl_close($curl);
					return 0;
				}
				$responseData = json_decode($response, true);
				if (is_array($responseData)) {
					if (isset($responseData['status']) && $responseData['status'] == 'OK') {
						if (isset($responseData['data']) && isset($responseData['data']['api_key'])) {
							$api_key = $responseData['data']['api_key'];
						}
					} else {
						setEventMessages(null, [$langs->trans("UniskipFailToGetAPIKey")], 'warning');
						curl_close($curl);
						return 0;
					}
				} else {
					setEventMessages(null, [$langs->trans("UniskipFailToGetAPIKey")], 'warning');
					curl_close($curl);
					return 0;
				}

				curl_close($curl);

				if (!empty($api_key)) {

					$curl = curl_init(getDolGlobalString('UNISKIP_URL') . 'api/compte-interne/all');
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

					curl_setopt($curl, CURLOPT_HTTPHEADER, [
						'accept: application/json',
						'api-token: ' . $api_key
					]);

					// Execute cURL request with all previous settings
					$response = curl_exec($curl);
					if ($response === false) {
						setEventMessages(null, [curl_error($curl)], 'warning');
						curl_close($curl);
						return 0;
					}
					$responseData = json_decode($response, true);

					if (is_array($responseData)) {
						if (isset($responseData['status']) && $responseData['status'] == 'OK') {
							if (isset($responseData['data']) &&
								isset($responseData['data']['compte_interne']) &&
								is_array($responseData['data']['compte_interne']) &&
								!empty($responseData['data']['compte_interne'])) {
								$compte_interne_id = reset($responseData['data']['compte_interne'])['id'];
							}
						} else {
							setEventMessages(null, [$langs->trans("UniskipFailToGetAccountInfo")], 'warning');
							curl_close($curl);
							return 0;
						}
					} else {
						setEventMessages(null, [$langs->trans("UniskipFailToGetAccountInfo")], 'warning');
						curl_close($curl);
						return 0;
					}
				}

				if (!empty($compte_interne_id)) {

					$curl = curl_init(getDolGlobalString('UNISKIP_URL') . 'api/payment-link');
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');

					curl_setopt($curl, CURLOPT_HTTPHEADER, [
						'accept: application/json',
						'api-token: '.$api_key,
						'Content-Type: application/x-www-form-urlencoded',
					]);

					$data['compte_interne_id']= $compte_interne_id;
					$data['devise_libelle_court']= $object->multicurrency_code;
					$data['montant']= $object->total_ttc;
					$data['ticket_id']= '';
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

					// Execute cURL request with all previous settings
					$response = curl_exec($curl);
					if ($response === false) {
						setEventMessages(null, [curl_error($curl)], 'warning');
						curl_close($curl);
						return 0;
					}
					$responseData = json_decode($response, true);
					if (is_array($responseData)) {
						if (isset($responseData['status']) && $responseData['status'] == 'OK') {
							if (isset($responseData['data'])
								&& isset($responseData['data']['url'])
								&& isset($responseData['data']['id'])) {
								$object->array_options['options_uniskip_urlpayment'] = $responseData['data']['url'].'?id='.$responseData['data']['id'];
								$resUpd = $object->insertExtraFields();
								if ($resUpd) {
									setEventMessages($object->error,$object->errors,'errors');
								}
							}
						} else {
							setEventMessages(null, [$langs->trans("UniskipFailToURLPayment")], 'warning');
							curl_close($curl);
							return 0;
						}
					} else {
						setEventMessages(null, [$langs->trans("UniskipFailToURLPayment")], 'warning');
						curl_close($curl);
						return 0;
					}
				}else {
					setEventMessages(null, [$langs->trans("UniskipFailToURLPayment")], 'warning');
					return 0;
				}
			} else {
				setEventMessages(null, [$langs->trans("UniskipSetupNotComplete")], 'warning');
			}
			return 0;
		}
	}
}
