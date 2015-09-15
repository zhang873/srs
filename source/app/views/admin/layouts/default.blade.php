<!DOCTYPE html>

<html lang="en">

<head>

	<meta charset="UTF-8">

	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>
		@section('title')
			Administration
		@show
	</title>

	<meta name="keywords" content="@yield('keywords')" />
	<meta name="author" content="@yield('author')" />
	<!-- Google will often use this as its description of your page/site. Make it good. -->
	<meta name="description" content="@yield('description')" />

	<!-- Speaking of Google, don't forget to set your site up: http://google.com/webmasters -->
	<meta name="google-site-verification" content="">

	<!-- Dublin Core Metadata : http://dublincore.org/ -->
	<meta name="DC.title" content="Project Name">
	<meta name="DC.subject" content="@yield('description')">
	<meta name="DC.creator" content="@yield('author')">

	<!--  Mobile Viewport Fix -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

	<!-- This is the traditional favicon.
	 - size: 16x16 or 32x32
	 - transparency is OK
	 - see wikipedia for info on browser support: http://mky.be/favicon/ -->
	<link rel="shortcut icon" href="{{{ asset('assets/ico/favicon.png') }}}">

	<!-- iOS favicons. -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{{ asset('assets/ico/apple-touch-icon-144-precomposed.png') }}}">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{{ asset('assets/ico/apple-touch-icon-114-precomposed.png') }}}">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{{ asset('assets/ico/apple-touch-icon-72-precomposed.png') }}}">
	<link rel="apple-touch-icon-precomposed" href="{{{ asset('assets/ico/apple-touch-icon-57-precomposed.png') }}}">

	<!-- CSS -->
    <link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('bootstrap/css/bootstrap-theme.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/wysihtml5/prettify.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/wysihtml5/bootstrap-wysihtml5.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/datatables-bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/colorbox.css')}}">

    <link rel="stylesheet" href="{{asset('assets/css/dataTables.tableTools.css')}}">

	<style>
	body {
		padding: 60px 0;
	}
	</style>

	@yield('styles')

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>

<body>
	<!-- Container -->
	<div class="container">
		<!-- Navbar -->
		<div class="navbar navbar-default navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">                    
                    <h4 style="color:white;">
                        This is header title                        
                    </h4>
                    
                    <!--  Prepare menu items -->
                    <?php	                    
	                    $aryadminfeatures = array();
	                    
	                   	if (Auth::check()) {	                   		
	                    		// admin features
	                    		if (Entrust::can('manage_users')) {
	                    			$aryadminfeatures[] = array(Lang::get('admin/admin.manageusers'), URL::to('admin/users'));
	                    		}
	                    		if (Entrust::can('manage_roles')) {
	                    			$aryadminfeatures[] = array(Lang::get('admin/admin.manageroles'), URL::to('admin/roles'));
	                    		}
	                   	}	                  	
                    ?>
                    <!--  end Prepare menu items -->
                    <ul class="nav navbar-nav pull-left">
	    					<li class="dropdown">
	    							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
	    								{{{ Lang::get('site.mainmenu') }}} <span class="caret"></span>
	    							</a>
	    							<ul class="dropdown-menu">	    								
	    								@if (!empty($aryadminfeatures))
	    									<li style="padding:3px 20px">{{ Lang::get('admin/admin.function_group_admin') }}</li>
	    									<ul>
	    									@foreach($aryadminfeatures as $menuitem)
	    									<li><a href="{{ $menuitem[1] }}">{{ $menuitem[0] }}</a></li>
	    									@endforeach
                                                <li><a href="{{ URL::to('admin/edu_department') }}">XYXYXY</a></li>
	    									</ul>
	    								@endif
	    							</ul>
	    					</li>
    					</ul>
                                        
                </div>
    			<div class="collapse navbar-collapse navbar-ex1-collapse">
    			<h4><br></h4>
    				<ul class="nav navbar-nav pull-right">
    					<li><a href="{{{ URL::to('/admin') }}}">{{{ Lang::get('site.backtohome') }}}</a></li>
    					<li class="divider-vertical"></li>
    					<li class="dropdown">
    							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
    								<span class="glyphicon glyphicon-user"></span>{{{ Lang::get('site.logged_in') }}} {{{ Auth::user()->username }}}	<span class="caret"></span>
    							</a>
    							<ul class="dropdown-menu">
    								<li><a href="{{ URL::to('user') }}"><span class="glyphicon glyphicon-wrench"></span>{{{ Lang::get('user/user.change_password') }}}</a></li>
    								<li class="divider"></li>
    								<li><a href="{{{ URL::to('user/logout') }}}"><span class="glyphicon glyphicon-share"></span>{{{ Lang::get('site.logout') }}}</a></li>
    							</ul>
    					</li>
    				</ul>
    			</div>
            </div>
		</div>
		<!-- ./ navbar -->

		<!-- Notifications -->
		<div style="padding-top:2em">
		@include('notifications')
		</div>
		<!-- ./ notifications -->

		<!-- Content -->
		@yield('content')
		<!-- ./ content -->

		<!-- Footer -->
		<footer class="clearfix">
			@yield('footer')
		</footer>
		<!-- ./ Footer -->

	</div>
	<!-- ./ container -->

	<!-- Javascripts -->
    <script src="{{asset('assets/js/jquery.js')}}"></script>
    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/datatables.fnReloadAjax.js')}}"></script>
    <script src="{{asset('/assets/js/dataTables.tableTools.js')}}"></script>

    <script src="{{asset('bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/wysihtml5/wysihtml5-0.3.0.js')}}"></script>
    <script src="{{asset('assets/js/wysihtml5/bootstrap-wysihtml5.js')}}"></script>

    <script src="{{asset('assets/js/datatables-bootstrap.js')}}"></script>

    <script src="{{asset('assets/js/jquery.colorbox.js')}}"></script>
    <script src="{{asset('assets/js/prettify.js')}}"></script>

    <script type="text/javascript">
    	$('.wysihtml5').wysihtml5();
        $(prettyPrint);
    </script>

    @yield('scripts')
    
</body>

</html>
