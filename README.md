sbp_web
=======

Steve Beyer Productions Web Database
Wooot!

Install
-------
* git clone git://github.com/paulgorman/sbp_web.git
* mkdir -p sbp_web/i/artist
* mkdir sbp_web/i/category
* mkdir sbp_web/i/pages
* mkdir sbp_web/m
* chown -R www:www sbp_web/m sbp_web/i
* mysqladmin create sbpweb
* GRANT ALL PRIVILEGES ON sbpweb.* TO username@'localhost' IDENTIFIED BY 'password';
* FLUSH PRIVILEGES
* mysql sbp_web < sbp_web/sbp_schema.sql
* edit sbp_web/db.php: $user and $pass
* edit system httpd.conf and add
```
	<LocationMatch "/(i|m)/.*\.(php|cgi)$">
		Order Deny,Allow
		Deny from All
	</LocationMatch>
```

* check sbp_web/php.ini

Notes
-----
alias ci='git add -A;git commit;git push origin master;'
