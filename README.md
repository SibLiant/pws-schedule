Author: Parker Bradtmiller
Web Site: parekrws.com
email: pwbradtmiller+github@gmail.com

base software - ubuntu 15.4 | php7 | laravel 5.2

PWS Scheduler is written in PHP 7 on Ubuntu 15.4.  I had a use case where
the client had data in a postgresql database and needed a nice  schedule 
view on that data.  The client had a series of installation teams that were
on a job site for anywhere from 1 day to a few weeks for installation.  

Objective - After installation a client coder should be able to either manually
post schedule data to the database ( via chrome extension postman for example ).
Or write some php code that pulls json data from their own data source and
posts that data to the web server api then forwards the user to a url that can
display that data nicely.  


some applications installed to my server:
apt-get install php7.0 php7.0-fpm php7.0-pgsql php7.0-pgsql php7.0-mcrypt php7.0-cli curl php7.0-mbstring php-xml postgresql git 



github repo - git@github.com:SibLiant/pws-schedule.git
