@extends('layouts.app')

@push('stylesheets')
  <!-- CSS Datatables -->
  <link href="{{ URL::asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush

@section('page-header')

@endsection

@section('content')


@endsection

@push('scripts')
	<script src="{{ asset("js/plugins/dataTables/datatables.min.js") }}"></script>

    
    <!-- Custom and plugin javascript -->
    <script src="{{ asset("js/inspinia.js") }}"></script>
    <script src="{{ asset("js/plugins/pace/pace.min.js") }}"></script>    
    
    <!-- Page-Level Scripts -->
    <script>
        path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";
        $(document).ready(function(){
            $('.dataTables-example').DataTable({
                "oLanguage":{"sUrl":path_str_language},
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {extend: 'excel', title: 'ExampleFile'},
                    {extend: 'pdf', title: 'ExampleFile'},
                ]

            });
        });
    </script>
@endpush