@extends('layouts.master')
@section('header')
    <section class="content-header">
        <h1>
            Edit {{ $page }}
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
                {{-- <h3 class="box-title">Edit New User</h3> --}}
            </div>
            <form role="form" method="post" action="{{ url('edit/material_volume', $material_volume->id) }}">
                <div class="box-body">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Material Number<span class="text-red"></span></label>
                        <div class="col-sm-4" align="left">
                            <input type="text" class="form-control" name="material_number" placeholder="Enter Length"
                                value="{{ $material_volume->material_number }}" disabled>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Material Description<span class="text-red"></span></label>
                        <div class="col-sm-4" align="left">
                            <input type="text" class="form-control" name="material_description"
                                placeholder="Enter Length" value="{{ $material_volume->material->material_description }}"
                                disabled>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Category<span class="text-red">*</span></label>
                        <div class="col-sm-4" align="left">
                            <select class="form-control select2" name="category" style="width: 100%;"
                                data-placeholder="Choose a Category..." required>
                                @if ($material_volume->category == 'FG')
                                    <option value="FG" selected>FG - Finished Goods</option>
                                    <option value="KD">KD - Knock Down Parts</option>
                                @else
                                    <option value="FG">FG - Finished Goods</option>
                                    <option value="KD" selected>KD - Knock Down Parts</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Lot Pallet<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input min="0" type="number" class="form-control" name="lot_pallet"
                                    placeholder="Enter Lot Pallet" value="{{ $material_volume->lot_pallet }}" required>
                                <span class="input-group-addon">PC(s)</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Length Pallet<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input step=".01" min="0" type="number" class="form-control"
                                    name="length_pallet" placeholder="Enter Length"
                                    value="{{ $material_volume->length_pallet }}" required>
                                <span class="input-group-addon">M</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Width Pallet<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input step=".01" min="0" type="number" class="form-control" name="width_pallet"
                                    placeholder="Enter Width" value="{{ $material_volume->width_pallet }}" required>
                                <span class="input-group-addon">M</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Height Pallet<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input step=".01" min="0" type="number" class="form-control"
                                    name="height_pallet" placeholder="Enter Height"
                                    value="{{ $material_volume->height_pallet }}" required>
                                <span class="input-group-addon">M</span>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Lot Carton<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input min="0" type="number" class="form-control" name="lot_carton"
                                    placeholder="Enter Lot Carton" value="{{ $material_volume->lot_carton }}" required>
                                <span class="input-group-addon">PC(s)</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Length Pallet<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input step=".01" min="0" type="number" class="form-control" name="length"
                                    placeholder="Enter Length" value="{{ $material_volume->length }}" required>
                                <span class="input-group-addon">M</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Width Pallet<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input step=".01" min="0" type="number" class="form-control" name="width"
                                    placeholder="Enter Width" value="{{ $material_volume->width }}" required>
                                <span class="input-group-addon">M</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Height Pallet<span class="text-red">*</span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input step=".01" min="0" type="number" class="form-control" name="height"
                                    placeholder="Enter Height" value="{{ $material_volume->height }}" required>
                                <span class="input-group-addon">M</span>
                            </div>
                        </div>
                    </div>
                    <br>
                    <!-- /.box-body -->
                    <div class="col-sm-4 col-sm-offset-6">
                        <div class="btn-group">
                            <a class="btn btn-danger" href="{{ url('index/material_volume') }}">Cancel</a>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

        });
        $(document).on("wheel", "input[type=number]", function(e) {
            $(this).blur();
        });
    </script>
@stop
