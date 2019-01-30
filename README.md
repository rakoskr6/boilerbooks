# boilerbooks
The ultimate IEEE record keeping system!

This program is written using PHP. The website is displayed using the bootstrap framework. It is currently hosted at money.pieee.org.

In order to properly develop code you will likely need access to the database.

It is also important to setup a mysqldump cron job for database backups (I suggest removing backups after 30 days to save space).

Use rsync (or a better solution) to make remote backups of the mysqldump's, certs, and the uploaded receipts.

Make sure to auto renew the SSL cert

[Setup ssmtp](http://www.havetheknowhow.com/Configure-the-server/Install-ssmtp.html) for sending emails.

# TODO
- dockerize
- email helper function
- better committee handling
- remove bad API crap
- convert everything to real PDO
 - Use DB library / better handling of connection
- try to separate the frontend/backend
  - use vue for frontend templating
- record database schema
- add master user role
- get rid of first/last name
- add configurable settings to DB
  - make committees a table
  - make years a table
  - funding source table?
  - soft delete purchases or users?
- rewrite the table library?
- handle files better
- tests?
