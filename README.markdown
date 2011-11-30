# Installing InStep into InFolio

Installing InStep into Infolio is a simple process:

## 1. Modify the .htaccess file in InFolio

Update the .htaccess file to include the following rule:

```
# InStep
RewriteRule ^([\w\-\_]+)/instep([/?\w-\+]+)/? /instep/index.php?institution=$1&p=$2 [NC,QSA,L]
```

A sample .htaccess file has been provided in this repository to show you how the modifications have been done.


------
## 2. Update the database
#### Make a new table in your database called 'instep'.
Run the contents of install.sql on the database (either in phpMyAdmin or another SQL manager).

#### Modifications to InFolio's database.
InFolio needs to be updated to show content from InStep.
####1. Delete the view 'vassetswithcounts' in the InFolio table.
####2. Run the SQL in update_infolio.sql. This will replace vassetswithcounts with an extra column.


------
## 3. Upload the instep folder into the root InFolio directory on your server

------

## 4. Edit the config file
Open ```instep/app/system/core/conf.php``` and make the following changes:

* Change the INSTEP_INFOLIO_DIR to point to the full path of InFolio on the server.
* Change the INSTEP_SYS_ROOTDIR to point to the path of the instep folder inside the InFolio install.
* Change INSTEP_SYS_REALBASEURL to the URL allocated to InFolio
* Change INSTEP_SYS_MATCHBASEURL to a Regular Expression matching the path that instep will be running from.

------

## 5. Modifications to InFolio
Edit ```system/conf.php``` (in InFolio's directory, not InStep) and add to the bottom of the file before the ?>:

```
// InStep specific
define('INSTEP_ENABLED', true);
```
This option allows you to enable or disable InStep inside the InFolio installation.

To show a link at the bottom of pages to InStep edit ```_includes/footer.inc.php``` and change it to look as follows:

```
<div id="footer">
	<? if(!empty($studentUser) && $studentUser->getPermissionManager()->hasRight(PermissionManager::RIGHT_ALL_ADMIN)) echo('<a href="admin/">Admin</a>'); ?>
	<? if(INSTEP_ENABLED) echo " | <a href='instep/'>InStep</a>"; ?>
</div>
```

Open ```admin/bo.php``` and go to the bottom of the file. Just **after** the ```</script>``` tag, add:

```
<? if(INSTEP_ENABLED) print("<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.5.min.js\"></script> <script type=\"text/javascript\" src=\"/instep/presentation/scripts/import.js\"></script> "); ?>
```

Open ```collection.php``` in the root InFolio folder. Add **before** the ```</body>``` tag:
```
<? if(INSTEP_ENABLED) print("<script type=\"text/javascript\" src=\"/instep/presentation/scripts/import.js\"></script> "); ?>
```


------
## Done!
