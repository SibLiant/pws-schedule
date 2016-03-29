<!DOCTYPE html>

<html>
    <head>
        <title>PWS Scheduler</title>

<link rel="stylesheet" href="css/app.css">
<style>

body {

	color: white;
}

body ul li a {
	color: white;
}
.container {
	padding: 5px;
}

</style>
    </head>
    <body>
        <div class="container">
        <h1>PWS Scheduler</h1>
        <ul>
        	<li><a href="/RO">Read Only </a></li>
        	<li><a href="/RO/postedSchedule">Last Posted Via API</a></li>
        </ul>
        </div>
    </body>
</html>

@if ( Config::get('app.debug') )
  <script type="text/javascript">
    document.write('<script src="//localhost:35729/livereload.js?snipver=1" type="text/javascript"><\/script>')
  </script> 
@endif
