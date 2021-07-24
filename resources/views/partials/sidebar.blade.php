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
        
        <!-- Menu SAM Super Administrador -->
        @if(session('role')=='SAM')
            <li class="{{ set_active(['condominiums', 'demos']) }}">
                <a href="#"><i class="fa fa-building-o"></i><span class="nav-label">Condominios</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['condominiums']) }}">
                        <a href="{{url('condominiums')}}">Permanentes</a>
                    </li>
                    <li class="{{ set_active(['demos']) }}">
                        <a href="{{url('demos')}}">Demos</a>
                    </li>
                </ul>
            </li>
            <li class="{{ set_active(['global']) }}">
                <a href="{{url('global')}}"><span class="nav-label"><i class="fa fa-cogs"></i> Configuraciones</span></a>
            </li>
        @endif
        <!-- /Menu SAM Super Administrador -->

        <!-- Menu ADM Administrador  -->
        @if(session('role')=='ADM')
            <li class="{{ set_active(['home']) }}">
                <a href="{{url('home')}}"><i class="fa fa-laptop"></i> <span class="nav-label">Dashboard</span></a>
            </li>
            <li class="{{ set_active(['properties', 'owners', 'taxes', 'facilities', 'reservations', 'cars', 'assets']) }}">
                <a href="#"><i class="fa fa-building-o"></i><span class="nav-label">Condominio</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['properties']) }}">
                        <a href="{{url('properties')}}">Propiedades</a>
                    </li>
                    <li class="{{ set_active(['owners']) }}">
                        <a href="{{url('owners')}}">Propietarios</a>
                    </li>
                    <li class="{{ set_active(['taxes']) }}">
                        <a href="{{url('taxes')}}">Alicuotas</a>
                    </li>
                    <li class="{{ set_active(['facilities']) }}">
                        <a href="{{url('facilities')}}">Instalaciones</a>
                    </li>
                    <li class="{{ set_active(['reservations']) }}">
                        <a href="{{url('reservations')}}">Reservaciones</a>
                    </li>
                    <li class="{{ set_active(['cars']) }}">
                        <a href="{{url('cars')}}">Vehículos</a>
                    </li>
                    <li class="{{ set_active(['assets']) }}">
                        <a href="{{url('assets')}}">Activos</a>
                    </li>
                </ul>
            </li>
            <li class="{{ set_active(['accounts', 'income_types', 'payments', 'incomes', 'expense_types', 'expenses', 'transfers']) }}">
                <a href="#"><i class="fa fa-line-chart"></i><span class="nav-label">Finanzas</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['accounts']) }}">
                        <a href="{{url('accounts')}}">Caja y Banco</a>
                    </li>
                    <li class="{{ set_active(['income_types', 'payments', 'incomes']) }}">
                        <a href="#">Ingresos <span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li class="{{ set_active(['income_types']) }}">
                                <a href="{{url('income_types')}}">Tipos de Ingresos</a>
                            </li>
                            <li class="{{ set_active(['payments']) }}">
                                <a href="{{url('payments')}}">Pagos de Cuotas</a>
                            </li>
                            <li class="{{ set_active(['incomes']) }}">
                                <a href="{{url('incomes')}}">Extraordinarios</a>
                            </li>
                        </ul>
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
                    <li class="{{ set_active(['transfers']) }}">
                        <a href="{{url('transfers')}}">Transferencias</a>
                    </li>
                    <li class="{{ set_active(['fees']) }}">
                        <a href="{{url('fees')}}">Cuotas</a>
                    </li>
                </ul>
            </li>
            <li class="{{ set_active(['supplier_categories', 'suppliers', 'services', 'contacts', 'employees']) }}">
                <a href="#"><i class="fa fa-address-book-o"></i><span class="nav-label">Directorios</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['supplier_categories', 'suppliers']) }}">
                        <a href="#">Proveedores <span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li class="{{ set_active(['supplier_categories']) }}">
                                <a href="{{url('supplier_categories')}}">Categorías</a>
                            </li>
                            <li class="{{ set_active(['suppliers']) }}">
                                <a href="{{url('suppliers')}}">Proveedores</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ set_active(['employees']) }}">
                        <a href="{{url('employees')}}">Empleados</a>
                    </li>
                    <li class="{{ set_active(['contacts']) }}">
                        <a href="{{url('contacts')}}">Contactos</a>
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
            <li class="{{ set_active(['emails']) }}">
                <a href="#"><i class="fa fa-envelope-o"></i><span class="nav-label">Correos</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ set_active(['emails']) }}">
                        <a href="{{url('emails')}}">Correo Libre</a>
                    </li>
                    <li class="{{ set_active(['emails']) }}">
                        <a href="{{url('emails')}}">Avisos de Cobro</a>
                    </li>
                </ul>
            </li>
            <li class="{{ set_active(['events']) }}">
                <a href="{{url('events')}}"><i class="fa fa-calendar-o"></i> <span class="nav-label">Calendario</span></a>
            </li>
            <li class="{{ set_active(['settings']) }}">
                <a href="{{url('settings')}}"><i class="fa fa-cogs"></i> <span class="nav-label">Configuración</span></a>
            </li>
        @endif
        <!-- /Menu ADM Administrador -->        
        
        <!-- Menu OWN Propietario -->
        @if(session('role')=='OWN')
            <li class="{{ set_active(['home']) }}">
                <a href="{{url('home')}}"><i class="fa fa-laptop"></i> <span class="nav-label">Dashboard</span></a>
            </li>
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
        
        </ul>
    </div>
</nav>