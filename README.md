# UNISKIP FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## Features

This module provide a payment URL on Validate Invoice with uniskip API [Uniksip](https://www.uniskip.store/).

## Translations

Translations can be completed manually by editing files into directories *langs*.

## Installation

Prerequisites:

 - You must have the Dolibarr ERP CRM software installed. You can download it from [Dolistore.org](https://www.dolibarr.org).
 - have php-curl extension install

### From the ZIP file and GUI interface

If the module is a ready to deploy zip file, so with a name module_xxx-version.zip (like when downloading it from a market place like [Dolistore](https://www.dolistore.com)),
go into menu ```Home - Setup - Modules - Deploy external module``` and upload the zip file.

Note: If this screen tell you that there is no "custom" directory, check that your setup is correct:

### From a GIT repository

Clone the repository in ```https://github.com/Darkjeff/uniskip.git```

in "custom" folder (check our conf.php settings)
```sh
git clone git@github.com:Darkjeff/uniskip.git uniskip
```

### Final steps

From your browser:

  - Log into Dolibarr as a super-administrator
  - Go to "Setup" -> "Modules"
  - You should now be able to find and enable the module



## Licenses

### Main code

GPLv3 or (at your option) any later version. See file COPYING for more information.

### Documentation

All texts and readmes are licensed under GFDL.
