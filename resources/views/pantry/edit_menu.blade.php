@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Edit Minuman
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
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
      {{-- <h3 class="box-title">Create Menu Minuman</h3> --}}
    </div>  
    <form role="form" class="form-horizontal form-bordered" method="post" action="{{url('index/pantry/edit_menu', $menus->id)}}" enctype="multipart/form-data">

      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">Nama Menu (Minuman)</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="menu" placeholder="Enter Menu" value="{{$menus->menu}}">
          </div>
        </div>
        <div class="form-group row" align="right">
          <label class="col-sm-4">Gambar Minuman</label>
          <div class="col-sm-4">
            <input type="file" class="form-control" name="gambar" value="{{$menus->gambar}}">
            <!-- <img src="{{url('images/minuman/'.$menus->gambar)}}" width="100" height="100"> -->
          </div>
        </div>
        <!-- /.box-body -->
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/pantry/menu') }}">Cancel</a>
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
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

  })
</script>
@stop

