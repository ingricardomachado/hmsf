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
                <a href="{{url('partners')}}"><i class="fa fa-users"></i> <span class="nav-label">Socios de Negocio</span></a>
            </li>
            <li class="{{ set_active(['customers']) }}">
                <a href="{{url('customers')}}"><i class="fa fa-users"></i> <span class="nav-label">Clientes</span></a>
            </li>
            <li class="{{ set_active(['accounts', 'income_types', 'payments', 'incomes', 'expense_types', 'expenses', 'transfers']) }}">
                <a href="#"><i class="fa fa-line-chart"></i><span class="nav-label">Finanzas</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['accounts']) }}">
                        <a href="{{url('accounts')}}">Operaciones</a>
                    </li>
                    <li class="{{ set_active(['expense_types', 'expenses']) }}">
                        <a href="#">Egresos <span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li class="{{ set_active(['expense_types']) }}">
                                <a href="{{url('expense_types')}}">Tipos de Egresos</a>
                            </li>
                            <li class="{{ set_active(['expenses']) }}">
                                <a href="{{url('expenses')}}">Egresos</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="{{ set_active(['projects']) }}">
                <a href="{{url('projects')}}"><i class="fa fa-wrench"></i> <span class="nav-label">Proyectos</span></a>
            </li>
            <li class="{{ set_active(['document_types', 'documents']) }}">
                <a href="#"><i class="fa fa-folder-o"></i><span class="nav-label">Documentos</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['document_types']) }}">
                        <a href="{{url('document_types')}}">Clasificación</a>
                    </li>
                    <li class="{{ set_active(['documents']) }}">
                        <a href="{{url('documents')}}">Documentos</a>
                    </li>
                </ul>
            </li>
            <li class="{{ set_active(['users', 'settings']) }}">
                <a href="#"><i class="fa fa-cogs"></i><span class="nav-label">Configuración</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['users']) }}">
                        <a href="{{url('users')}}">Usuarios</a>
                    </li>
                    <li class="{{ set_active(['settings']) }}">
                        <a href="{{url('settings')}}">Datos generales</a>
                    </li>
                </ul>
            </li>
        @endif
        <!-- /Menu ADM Administrador -->        
        
        <!-- Menu OWN Propietario -->
        @if(session('role')=='OWN')
            @if(Auth::user()->properties()->count()>1)
                <li class="{{ set_active(['properties']) }}">
                    <a href="{{url('properties')}}"><i class="fa fa-home"></i> <span class="nav-label">Propiedades</span></a>
                </li>
            @else
                <li class="{{ set_active(['statement']) }}">
                    <a href="{{url('statement', Crypt::encrypt(Auth::user()->properties()->first()->id) )}}"><i class="fa fa-files-o"></i> <span class="nav-label">Estado de Cuenta</span></a>
                </li>
            @endif
            <li class="{{ set_active(['payments']) }}">
                <a href="{{url('payments')}}"><i class="fa fa-money"></i> <span class="nav-label">Pagos</span></a>
            </li>
            <li class="{{ set_active(['facilities']) }}">
                <a href="{{url('facilities')}}"><i class="fa fa-umbrella"></i> <span class="nav-label">Instalaciones</span></a>
            </li>
            <li class="{{ set_active(['reservations']) }}">
                <a href="{{url('reservations')}}"><i class="fa fa-calendar-o"></i> <span class="nav-label">Reservaciones</span></a>
            </li>
            <li class="{{ set_active(['contacts']) }}">
                <a href="{{url('contacts')}}"><i class="fa fa-address-book-o"></i> <span class="nav-label">Contactos</span></a>
            </li>
            <li class="{{ set_active(['events']) }}">
                <a href="{{url('events')}}"><i class="fa fa-calendar-o"></i> <span class="nav-label">Calendario</span></a>
            </li>
        @endif
        <!-- /Menu OWN Propietario -->
        
        <!-- Menu WAM Vigilante -->
        @if(session('role')=='WAM')
            <li class="{{ set_active(['newsletters']) }}">
                <a href="{{url('newsletters')}}"><i class="fa fa-newspaper-o"></i> <span class="nav-label">Novedades</span></a>
            </li>
            <li class="{{ set_active(['visit_types', 'visits']) }}">
                <a href="#"><i class="fa fa-male"></i><span class="nav-label">Visitas</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['visit_types']) }}">
                        <a href="{{url('visit_types')}}">Tipos de visita</a>
                    </li>
                    <li class="{{ set_active(['visits']) }}">
                        <a href="{{url('visits')}}">Visitas</a>
                    </li>
                </ul>
            </li>
        @endif
        <!-- /Menu WAM Vigilante -->

        </ul>
    </div>
</nav>