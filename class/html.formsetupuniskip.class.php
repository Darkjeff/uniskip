<?php
/* Copyright (C) 2021  John BOTELLA    <john.botella@atm-consulting.fr>
 * Copyright (C) 2024 Jeffinfo - Olivier Geffroy <jeff@jeffinfo.com>
 * Copyright (C) 2024 Florian HENRY <florian.henry@scopen.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

if (!class_exists('FormSetup')) {
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formsetup.class.php';
}

/**
 * This class help you create setup render (Back port for password management from develop version)
 */

class FormSetupUniskip extends FormSetup {
	/**
	 * Create a new item
	 * the target is useful with hooks : that allow externals modules to add setup items on good place
	 *
	 * @param string	$confKey 				the conf key used in database
	 * @param string	$targetItemKey    		target item used to place the new item beside
	 * @param bool		$insertAfterTarget		insert before or after target item ?
	 * @return FormSetupItem the new setup item created
	 */
	public function newItem($confKey, $targetItemKey = '', $insertAfterTarget = false)
	{
		$item = new FormSetupItemUniskip($confKey);

		$item->entity = $this->entity;

		// set item rank if not defined as last item
		if (empty($item->rank)) {
			$item->rank = $this->getCurentItemMaxRank() + 1;
			$this->setItemMaxRank($item->rank); // set new max rank if needed
		}

		// try to get rank from target column, this will override item->rank
		if (!empty($targetItemKey)) {
			if (isset($this->items[$targetItemKey])) {
				$targetItem = $this->items[$targetItemKey];
				$item->rank = $targetItem->rank; // $targetItem->rank will be increase after
				if ($targetItem->rank >= 0 && $insertAfterTarget) {
					$item->rank++;
				}
			}

			// calc new rank for each item to make place for new item
			foreach ($this->items as $fItem) {
				if ($item->rank <= $fItem->rank) {
					$fItem->rank = $fItem->rank + 1;
					$this->setItemMaxRank($fItem->rank); // set new max rank if needed
				}
			}
		}

		$this->items[$item->confKey] = $item;
		return $this->items[$item->confKey];
	}
}

class FormSetupItemUniskip extends FormSetupItem
{

	/**
	 * generate input field
	 *
	 * @return bool|string
	 */
	public function generateInputField(){
		$out = '';
		if ($this->type == 'password') {
			$out .= $this->generateInputFieldPassword('dolibarr');
		} elseif ($this->type == 'genericpassword') {
			$out .= $this->generateInputFieldPassword('generic');
		} else {
			return parent::generateInputField();
		}
		return $out;
	}

	/**
	 * generateOutputField
	 *
	 * @return bool|string 		Generate the output html for this item
	 */
	public function generateOutputField()
	{
		$out = '';
		if ($this->type == 'password' || $this->type == 'genericpassword') {
			$out .= str_repeat('*', strlen($this->fieldValue));
		} else {
			return parent::generateOutputField();
		}

		return $out;
	}

	/**
	 * Set type of input as a password with dolibarr password rules apply.
	 * Hide entry on display.
	 *
	 * @return self
	 */
	public function setAsPassword()
	{
		$this->type = 'password';
		return $this;
	}

	/**
	 * Set type of input as a generic password without dolibarr password rules (for external passwords for example).
	 * Hide entry on display.
	 *
	 * @return self
	 */
	public function setAsGenericPassword()
	{
		$this->type = 'genericpassword';
		return $this;
	}

	/**
	 * generate input field for a password
	 *
	 * @param string $type 'dolibarr' (dolibarr password rules apply) or 'generic'
	 *
	 * @return  string
	 */
	public function generateInputFieldPassword($type = 'generic')
	{
		global $conf, $langs, $user;

		$min = 6;
		$max = 50;
		if ($type == 'dolibarr') {
			$gen = getDolGlobalString('USER_PASSWORD_GENERATED', 'standard');
			if ($gen == 'none') {
				$gen = 'standard';
			}
			$nomclass = "modGeneratePass" . ucfirst($gen);
			$nomfichier = $nomclass . ".class.php";
			require_once DOL_DOCUMENT_ROOT . "/core/modules/security/generate/" . $nomfichier;
			$genhandler = new $nomclass($this->db, $conf, $langs, $user);
			$min = $genhandler->length;
			$max = $genhandler->length2;
		}
		$out = '<input required="required" type="password" class="flat" id="' . $this->confKey . '" name="' . $this->confKey . '" value="' . (GETPOST($this->confKey, 'alpha') ? GETPOST($this->confKey, 'alpha') : $this->fieldValue) . '"';
		if ($min) {
			$out .= ' minlength="' . $min . '"';
		}
		if ($max) {
			$out .= ' maxlength="' . $max . '"';
		}
		$out .= '>';
		return $out;
	}
}
