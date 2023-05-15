@extends('layouts.master')
@section('header')
    <section class="content-header">
        <h1>
            Create {{ $page }}
        </h1>
        <ol class="breadcrumb">

        </ol>
    </section>
@endsection
@section('content')
    <section class="content">


        @if ($errors->has('password'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                {{ $errors->first() }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                {{ session('error') }}
            </div>
        @endif


        <!-- SELECT2 EXAMPLE -->
        <div class="box box-primary">
            <div class="box-header with-border">
                {{-- <h3 class="box-title">Create New User</h3> --}}
            </div>
            <form role="form" method="post" action="{{ url('create/destination') }}">
                <div class="box-body">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Destination Code<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="destination_code"
                                placeholder="Enter Destination Code" required>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Destination Name<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="destination_name"
                                placeholder="Enter Destination Name" required>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Destination Shortname<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="destination_shortname"
                                placeholder="Enter Destination Shortname" required>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Priority<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="priority" placeholder="Enter Priority"
                                required>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="col-sm-4 col-sm-offset-6">
                        <div class="btn-group">
                            <a class="btn btn-danger" href="{{ url('index/destination') }}">Cancel</a>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    @endsection

    @section('scripts')
        <script>
            $(function() {
                //Initialize Select2 Elements
                $('.select2').select2()

            })
        </script>
    @stop
