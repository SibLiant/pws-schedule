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

The MIT License (MIT)
Copyright (c) 2016 Parker William Bradtmiller

Permission is hereby granted, free of charge, to any person obtaining a copy of 
this software and associated documentation files (the "Software"), to deal in 
the Software without restriction, including without limitation the rights to 
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies 
of the Software, and to permit persons to whom the Software is furnished to do 
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all 
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
SOFTWARE.
