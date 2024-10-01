<?php
/* Copyright (C) 2024 Jeffinfo - Olivier Geffroy <jeff@jeffinfo.com>
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
 * \file    uniskip/lib/uniskip.lib.php
 * \ingroup uniskip
 * \brief   Library files with common functions for Uniskip
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function uniskipAdminPrepareHead()
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('myobject');

	$langs->load("uniskip@uniskip");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/uniskip/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	$head[$h][0] = dol_buildpath("/uniskip/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	complete_head_from_modules($conf, $langs, null, $head, $h, 'uniskip@uniskip');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'uniskip@uniskip', 'remove');

	return $head;
}
