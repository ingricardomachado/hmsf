        <div class="modal-body">
            <div class="row">            
                <div class="contact-box center-version">
                    <a href="profile.html">

                        <img alt="image" class="img-circle" src="{{ url('contact_avatar/'.$contact->id) }}">


                        <h3 class="m-b-xs"><strong>{{ $contact->name }}</strong></h3>

                        <div class="font-bold">{{ $contact->position }}</div>
                        <address class="m-t-md">
                            <strong>{{ $contact->company }}</strong><br>
                            <p class="text-center">{{ $contact->address }}</p>
                            @if($contact->phone)
                                <i class="fa fa-phone" aria-hidden="true"></i> {{ $contact->cell }} {{ $contact->phone }}<br>
                            @endif
                            @if($contact->email)
                                <i class="fa fa-envelope-o" aria-hidden="true"></i> {{ $contact->email }}
                            @endif
                            <div style="margin-top: 2mm"><small>{{ $contact->about }}</small></div>
                        </address>
                    </a>
                    <div class="contact-box-footer">
                        <div class="m-t-xs btn-group">
                            @if($contact->twitter)
                                <i class="fa fa-twitter" aria-hidden="true"></i> {{ $contact->twitter }}
                            @endif
                            @if($contact->facebook)
                                <i class="fa fa-facebook" aria-hidden="true"></i> {{ $contact->facebook }}
                            @endif
                            @if($contact->instagram)
                                <i class="fa fa-instagram" aria-hidden="true"></i> {{ $contact->instagram }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
