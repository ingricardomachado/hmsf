        <div class="modal-body">
            <div class="row">            
                <div class="contact-box center-version">
                    <a href="profile.html">

                        <img alt="image" class="img-circle" src="{{ url('employee_avatar/'.$employee->id) }}">


                        <h3 class="m-b-xs"><strong>{{ $employee->name }}</strong></h3>

                        <div class="font-bold">{{ $employee->NIT }}</div>
                        <address class="m-t-md">
                            <strong>{{ $employee->position }}</strong><br>
                            <p class="text-center">{{ $employee->address }}</p>
                            @if($employee->phone)
                                <i class="fa fa-phone" aria-hidden="true"></i> {{ $employee->phone }}<br>
                            @endif
                            @if($employee->email)
                                <i class="fa fa-envelope-o" aria-hidden="true"></i> {{ $employee->email }}
                            @endif
                            <div style="margin-top: 2mm"><small>{{ $employee->notes }}</small></div>
                        </address>
                    </a>
                    <div class="contact-box-footer">
                        <div class="m-t-xs btn-group">
                            Calificación 
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <i class="fa fa-star" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
