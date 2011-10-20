# Installing InStep into InFolio

Installing InStep into Infolio is a simple process:

## 1. Modify the .htaccess file in InFolio

Update the .htaccess file to include the following rule:

```
# InStep
RewriteRule ^([\w\-\_]+)/instep([/?\w-\+]+)/? /instep/index.php?institution=$1&p=$2 [NC,QSA,L]
```

A sample .htaccess file has been provided in this repository to show you how the modifications have been done.

## 2. Upload the instep folder into the root InFolio directory on your server

## 3. Edit the config file
Open instep/app/system/core/conf.php and make the following changes:

* Change the INSTEP_INFOLIO_DIR to point to the full path of InFolio on the server.
* Change the INSTEP_SYS_ROOTDIR to point to the path of the instep folder inside the InFolio install.
* Change INSTEP_SYS_REALBASEURL to the URL allocated to InFolio
* Change INSTEP_SYS_MATCHBASEURL to a Regular Expression matching the path that instep will be running from.



## Done!
