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
                        广东开放大学教务管理系统
                    </h4>
                    
                    <!--  Prepare menu items -->
                    <?php
                    $aryadminfeatures = array();
	                    
	                   	if (Auth::check()) {	                   		
	                    		// admin features
	                    		if (Entrust::can('manage_users')) {
	                    			$aryadminfeatures[] = array(Lang::get('admin/admin.users'), URL::to('admin/users'));
	                    		}
	                    		if (Entrust::can('manage_roles')) {
	                    			$aryadminfeatures[] = array(Lang::get('admin/admin.roles'), URL::to('admin/roles'));
	                    		}
                            if (Entrust::can('adminschool')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.manageschool'), URL::to('admin/admissions/school'));
                            }
                            if (Entrust::can('adminadmission')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.admissions_province'), URL::to('admin/admissions/approve_admissions'));
                            }
                            if (Entrust::can('adminrewardpunishment')) {
                                $aryapprove[] = array(Lang::get('admin/admin.rewardpunish'), URL::to('admin/admissions/approve_reward_punish'));
                            }
                            if (Entrust::can('adminunifiedexam')) {
                                $aryapprove[] = array(Lang::get('admin/admin.unified_exam_province'), URL::to('admin/unified_exam/approve_unified_exam'));
                            }
                            if (Entrust::can('adminexemption')) {
                                $aryapprove[] = array(Lang::get('admin/admin.exemption_province'), URL::to('admin/exemption/approve_exemption'));
                            }
                            if (Entrust::can('addadmissions')) {
                                $aryapply[] = array(Lang::get('admin/admin.admissions_campus'), URL::to('admin/admissions/admissions_campus'));
                            }

                            if (Entrust::can('adddepart')) {
                                $aryapply[] = array(Lang::get('admin/admin.department'), URL::to('admin/depart'));
                            }

                            if (Entrust::can('addexemption')) {
                                $aryapply[] = array(Lang::get('admin/admin.exemption'), URL::to('admin/exemption'));
                            }

                            if (Entrust::can('addunifiedexam')) {
                                $aryapply[] = array(Lang::get('admin/admin.unified_exam'), URL::to('admin/unified_exam'));
                            }

                            if (Entrust::can('addchangingstudentstatus')) {
                                $aryapply[] = array(Lang::get('admin/admin.change_admissions'), URL::to('admin/admissions/admissions_change_campus'));
                            }

                            if (Entrust::can('addcampus')) {
                                $aryapply[] = array(Lang::get('admin/admin.campus'), URL::to('admin/add_campus'));
                            }
                            if (Entrust::can('addprogram')) {
                                $aryapply[] = array(Lang::get('admin/admin.program'), URL::to('admin/add_program_index'));
                            }
                            if (Entrust::can('addplan')) {
                                $aryapply[] = array(Lang::get('admin/admin.plan'), URL::to('admin/add_plan_index'));
                            }
                            if (Entrust::can('manage_groups')) {
                                $aryapply[] = array(Lang::get('admin/admin.managegroups'), URL::to('admin/admissions/admin_group'));
                            }
                            if (Entrust::can('addcampus')) {
                                $aryapply[] = array(Lang::get('admin/admin.addteacher'), URL::to('admin/teachers/index'));
                            }
                            if (Entrust::can('appointgroup')) {
                                $aryapply[] = array(Lang::get('admin/admin.appoint_group'), URL::to('admin/admissions/admissions_appoint_group'));
                            }
                            if (Entrust::can('changing_appointgroup')) {
                                $aryapply[] = array(Lang::get('admin/admin.change_admissions_appoint_group'), URL::to('admin/admissions/admissions_change_appoint_group'));
                            }
                            if (Entrust::can('recoveryadmissions')) {
                                $aryapply[] = array(Lang::get('admin/admin.recovery_admissions'), URL::to('admin/admissions/application_recovery'));
                            }
                            if (Entrust::can('withdrawaladmissions')) {
                                $aryapply[] = array(Lang::get('admin/admin.withdrawal_admissions'), URL::to('admin/admissions/application_dropout'));
                            }
                            if (Entrust::can('rewardpunish')) {
                                $aryapply[] = array(Lang::get('admin/admin.reward_punish'), URL::to('admin/admissions/admissions_record_reward_punish'));
                            }
                            // approval
                            if (Entrust::can('admincampus')) {
                                $aryapprove[] = array(Lang::get('admin/admin.campus'), URL::to('admin/approve_campus'));
                            }
                            if (Entrust::can('adminprogram')) {
                                $aryapprove[] = array(Lang::get('admin/admin.program'), URL::to('admin/approve_program_index'));
                            }
                            if (Entrust::can('adminplan')) {
                                $aryapprove[] = array(Lang::get('admin/admin.plan'), URL::to('admin/approve_plan_index'));
                            }
                            if (Entrust::can('approvechanging')) {
                                $aryapprove[] = array(Lang::get('admin/admin.approve_admission_changing'), URL::to('admin/admissions/approve_admission_changing'));
                            }
                            // manage student
                            if (Entrust::can('expel_admissions')) {
                                $arymanagestudent[] = array(Lang::get('admin/admin.expel_admissions'), URL::to('admin/admissions/expel_admissions'));
                            }
                            if (Entrust::can('addstudent')) {
                                $arymanagestudent[] = array(Lang::get('admin/admin.admission'), URL::to('admin/student/admission'));
                            }
                            if (Entrust::can('admissiondetails')) {
                                $arymanagestudent[] = array(Lang::get('admin/admin.admissiondetails'), URL::to('admin/student'));
                            }
                            if (Entrust::can('togglestudentuploadstatus')) {
                                $arymanagestudent[] = array(Lang::get('admin/admin.studentupload'), URL::to('admin/student/0/UploadStatus_s0'));
                            }
                            if (Entrust::can('printconfirmnote')) {
                                $arymanagestudent[] = array(Lang::get('admin/admin.printconfirmnote'), URL::to('admin/student/printConfirmNote'));
                            }
                            if (Entrust::can('printstudentlist')) {
                                $arymanagestudent[] = array(Lang::get('admin/admin.printstudentlist'), URL::to('admin/student/printStudentList'));
                            }
                            // admin features
                            if (Entrust::can('admintogglestudentuploadstatus')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.admintogglestudentuploadstatus'), URL::to('admin/student/0/0/UploadedStatus'));
                            }
                            if (Entrust::can('approvestudent')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.approvestudent'), URL::to('admin/student/0/0/ApproveStudent'));
                            }
                            if (Entrust::can('revert_approvestudent')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.revert_approvestudent'), URL::to('admin/student/0/0/RevertS2Student'));
                            }
                            if (Entrust::can('printstudentlist_advance')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.printstudentlist'), URL::to('admin/student/printStudentList'));
                            }
                            if (Entrust::can('printfinalstudentlist_advance')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.printfinalstudentlist'), URL::to('admin/student/printFinalStudentList'));
                            }
                            if (Entrust::can('printconfirmnote')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.printconfirmnote'), URL::to('admin/student/printConfirmNote'));
                            }
                            if (Entrust::can('adminexport')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.adminexport'), URL::to('admin/student/adminExport'));
                            }

                            if (Entrust::can('manage_rawprograms')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.managerawprograms'), URL::to('admin/managerawprograms_index'));
                            }
                            if (Entrust::can('manage_campus')) {
                                $aryadminfeatures[] = array(Lang::get('admin/admin.managecampus'), URL::to('admin/managecampus'));
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
	    									</ul>
	    								@endif
                                            @if (!empty($aryapply))
                                                <li style="padding:3px 20px">{{ Lang::get('admin/admin.function_group_add') }}</li>
                                                <ul>
                                                    @foreach($aryapply as $menuitem)
                                                        <li><a href="{{ $menuitem[1] }}">{{ $menuitem[0] }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            @if (!empty($aryapprove))
                                                <li style="padding:3px 20px">{{ Lang::get('admin/admin.function_group_approve') }}</li>
                                                <ul>
                                                    @foreach($aryapprove as $menuitem)
                                                        <li><a href="{{ $menuitem[1] }}">{{ $menuitem[0] }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            @if (!empty($arymanagestudent))
                                                <li style="padding:3px 20px">{{ Lang::get('admin/admin.function_group_student') }}</li>
                                                <ul>
                                                    @foreach($arymanagestudent as $menuitem)
                                                        <li><a href="{{ $menuitem[1] }}">{{ $menuitem[0] }}</a></li>
                                                    @endforeach
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
    <script src="{{asset('/assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/wysihtml5/wysihtml5-0.3.0.js')}}"></script>
    <script src="{{asset('assets/js/wysihtml5/bootstrap-wysihtml5.js')}}"></script>
    <script src="{{asset('/assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/datatables-bootstrap.js')}}"></script>
    <script src="{{asset('assets/js/datatables.fnReloadAjax.js')}}"></script>
    <script src="{{asset('assets/js/jquery.colorbox.js')}}"></script>
    <script src="{{asset('assets/js/prettify.js')}}"></script>

    <script type="text/javascript">
    	$('.wysihtml5').wysihtml5();
        $(prettyPrint);
    </script>

    @yield('scripts')
    
</body>

</html>
