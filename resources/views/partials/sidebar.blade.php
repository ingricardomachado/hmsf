<style type="text/css">
  .img-shadow {
    box-shadow: 0 2px 4px 0 rgba(255, 255, 255, 0.8);
  }
</style>

<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            
            <!-- Profile -->
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <span><img alt="image" class="img-circle" style="max-height:70px; max-width:70px;" src="{{ url('user_avatar/'.Auth::user()->id) }}"/></span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">{{ Auth::user()->name }}</strong>
                    </span> <span class="text-muted text-xs block">{{ Auth::user()->role_description }} <b class="caret"></b></span> </span> </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile') }}"><i class="fa fa-user"></i> Mi perfil</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> Salir</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                        </li>
                    </ul>
                </div>
                <div class="logo-element">
                    CB+
                </div>
            </li>
            <!-- /Profile -->
        
        <!-- Menu ADM Administrador  -->
        @if(session('role')=='ADM')
            <li class="{{ set_active(['home']) }}">
                <a href="{{url('home')}}"><i class="fa fa-laptop"></i> <span class="nav-label">Dashboard</span></a>
            </li>
            <li class="{{ set_active(['partners']) }}">
                <a href="{{url('partners')}}"><i class="fa fa-users"></i> <span class="nav-label">Socios Comerciales</span></a>
            </li>
            <li class="{{ set_active(['customers']) }}">
                <a href="{{url('customers')}}"><i class="fa fa-users"></i> <span class="nav-label">Clientes</span></a>
            </li>
            <li class="{{ set_active(['operations']) }}">
                <a href="{{url('operations')}}"><i class="fa fa-truck"></i> <span class="nav-label">Operaciones</span></a>
            </li>
            <li class="{{ set_active(['expense_types', 'expenses']) }}">
                <a href="#"><i class="fa fa-folder-o"></i><span class="nav-label">Gastos</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['expense_types']) }}">
                        <a href="{{url('expense_types')}}">Tipos de Gastos</a>
                    </li>
                    <li class="{{ set_active(['expenses']) }}">
                        <a href="{{url('expenses')}}">Gastos</a>
                    </li>
                </ul>
            </li>            
            <li class="{{ set_active(['users', 'settings']) }}">
                <a href="#"><i class="fa fa-cogs"></i><span class="nav-label">Configuración</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['users']) }}">
                        <a href="{{url('users')}}">Usuarios</a>
                    </li>
                    <li class="{{ set_active(['centers']) }}">
                        <a href="{{url('centers')}}">Oficinas</a>
                    </li>
                    <li class="{{ set_active(['settings']) }}">
                        <a href="{{url('settings')}}">Datos generales</a>
                    </li>
                </ul>
            </li>
        @endif
        <!-- /Menu ADM Administrador -->        
        
        <!-- Menu Socio de Negocios -->
        @if(session('role')=='SOC')
            <li class="{{ set_active(['home']) }}">
                <a href="{{url('home')}}"><i class="fa fa-laptop"></i> <span class="nav-label">Dashboard</span></a>
            </li>
        @endif
        <!-- /Menu Socio de Negocios -->
        
        <!-- Menu Supervisor -->
        @if(session('role')=='SUP')
            <li class="{{ set_active(['home']) }}">
                <a href="{{url('home')}}"><i class="fa fa-laptop"></i> <span class="nav-label">Dashboard</span></a>
            </li>
        @endif
        <!-- /Menu Supervisor -->

        <!-- Menu Mensajero -->
        @if(session('role')=='MEN')
            <li class="{{ set_active(['home']) }}">
                <a href="{{url('home')}}"><i class="fa fa-laptop"></i> <span class="nav-label">Dashboard</span></a>
            </li>
        @endif
        <!-- /Menu Mensajero -->


        </ul>
    </div>
</nav>