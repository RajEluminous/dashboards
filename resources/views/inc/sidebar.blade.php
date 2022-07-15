@php
    $affiliate_revenue = array(1, 2, 3, 10);
    $list_growth = array(1, 2, 4);
    $sales_by_ars = array(1, 2, 5);
    $kendago = array(1, 2, 6);
    $kendago_order = array(1, 2, 7);
	$rfs_order = array(1, 2);
    $vendor_order = array(1, 2, 8, 10);
    $affiliate_performance = array(1, 2, 8, 10);
    $cbmaster_list = array(1, 2, 9, 10);
@endphp

<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
      <h3>General</h3>
      <ul class="nav side-menu">
        {{-- <li class={{(Request::route()->getName() == 'home') ? 'active' : '' }}><a href="{{ url('/') }}"><i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard</a></li> --}}
        @if (Auth::user()->user_role_id == 1)
          <li class={{(Request::route()->getName() == 'system_dashboard.index') ? 'active' : '' }}><a href="{{ url('/system_dashboard') }}"><i class="fa fa-tachometer" aria-hidden="true"></i> System Dashboard</a></li>
        @endif

        @if (in_array(Auth::user()->user_role_id, $affiliate_revenue))
            <li class={{(Request::route()->getName() == 'affiliate_revenue') ? 'active' : '' }}><a href="{{ url('/affiliate_revenue/2') }}"><i class="fa fa-pie-chart" aria-hidden="true"></i>Affiliate Revenue</a></li>
        @endif

        @if (in_array(Auth::user()->user_role_id, $list_growth))
            <li class={{(Request::route()->getName() == 'list_growth') ? 'active' : '' }}><a href="{{ url('/list_growth') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i>List Growth</a></li>
        @endif

        @if (in_array(Auth::user()->user_role_id, $sales_by_ars))
            <li class={{(Request::route()->getName() == 'sales_by_ars') ? 'active' : '' }}><a href="{{ url('/ar_sales') }}"><i class="fa fa-line-chart" aria-hidden="true"></i>AR Sales</a></li>
        @endif

        @if (in_array(Auth::user()->user_role_id, $kendago))
            <li class={{(Request::route()->getName() == 'kendago') ? 'active' : '' }}><a href="{{ url('/kendago') }}"><i class="fa fa-area-chart" aria-hidden="true"></i>Kendago</a></li>
        @endif

        @if (in_array(Auth::user()->user_role_id, $kendago_order))
            <li class={{(Request::route()->getName() == 'kendago-order') ? 'active' : '' }}><a href="{{ url('/kendago-order/2') }}"><i class="fa fa-area-chart" aria-hidden="true"></i>15manifest Kendago</a></li>
        @endif

		@if (in_array(Auth::user()->user_role_id, $rfs_order))
            <li class={{(Request::route()->getName() == 'rfs-order') ? 'active' : '' }}><a href="{{ url('/rfs-order/2') }}"><i class="fa fa-area-chart" aria-hidden="true"></i>godfreq RFS</a></li>
        @endif

        @if (in_array(Auth::user()->user_role_id, $cbmaster_list))
            <li class={{(Request::route()->getName() == 'cb-master') ? 'active' : '' }}><a href="{{ url('/cb-master') }}"><i class="fa fa-list" aria-hidden="true"></i>Clickbank ID Master List</a></li>
        @endif

        @if (in_array(Auth::user()->user_role_id, $vendor_order))
            <li class={{(Request::route()->getName() == 'vendor-order') ? 'active' : '' }}><a href="{{ url('/vendor-order/2') }}"><i class="fa fa-dot-circle-o" aria-hidden="true"></i>Vendor Order</a></li>
        @endif

        @if (in_array(Auth::user()->user_role_id, $affiliate_performance))
            <li class={{(Request::route()->getName() == 'affiliate-performance') ? 'active' : '' }}><a href="{{ url('/affiliate-performance/2/Q0hFQ0tEQVRB') }}"><i class="fa fa-line-chart" aria-hidden="true"></i>Affiliate Performance</a></li>
        @endif

        @if (Auth::user()->user_role_id == 1)
            <li class={{(Request::route()->getName() == 'split-test') ? 'active' : '' }}><a href="{{ url('/split-test') }}"><i class="fa fa-columns" aria-hidden="true"></i>Split Test</a></li>
            <li class={{(Request::route()->getName() == 'user_roles') ? 'active' : '' }}><a href="{{ route("user_roles.index") }}"><i class="fa fa-users" aria-hidden="true"></i>User Roles</a></li>
            <li class={{(Request::route()->getName() == 'users') ? 'active' : '' }}><a href="{{ route("users.index") }}"><i class="fa fa-users" aria-hidden="true"></i>Users</a></li>
        @endif
      </ul>
    </div>
  </div>
