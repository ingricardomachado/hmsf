<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i></a>
        </div>
            <ul class="nav navbar-top-links navbar-right">
                @if(Session::get('role')=='SAM')
                    <li>
                        <span class="m-r-sm text-muted welcome-message">
                            Bienvenido al Sistema <strong>{{ config('app.name') }}</strong>
                        </span>
                    </li>
                @else
                    <li>
                        <span class="m-r-sm text-muted welcome-message">
                            Soporte <b>+58 5439974</b>
                            <a href="https://wa.me/584265439974" title="Abrir Whatsapp Web" target="_blank"><i class="fa fa-whatsapp"></i></a>
                            @if($global_condominium->demo)
                                <span class='badge badge-danger'><b>DEMO</b></span>
                            @endif
                        </span>
                    </li>
                    <li>
                        <span class="m-r-sm text-muted welcome-message">
                            <strong>{{ $global_condominium->name }}</strong>
                        </span>
                    </li>
                @endif
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out"></i> Salir
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
    </nav>
</div>