@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #message {
            /*display:none;*/
            /*background: #f1f1f1;*/
            color: #000;
            position: relative;
            padding: 10px 20px;
            margin: auto;
            margin-top: -20px;
        }

        #message p {
            font-size: 15px;
        }

        .valid {
            color: green;
        }

        .invalid {
            color: red;
        }
    </style>
@endsection
@section('header')
    <section class="content-header">
        <h1>
            User {{ $page }}
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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                {{ session('error') }}
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa-thumbs-o-up"></i> Success!</h4>
                {{ session('status') }}
            </div>
        @endif
        @if (session('attention'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h2><i class="icon fa fa-warning"></i> Himbauan!</h2>
                <h5>{{ session('attention') }}</h5>
            </div>
        @endif
        <!-- SELECT2 EXAMPLE -->
        <div class="box box-primary">
            <div class="box-header with-border">
                {{-- <h3 class="box-title">Create New User</h3> --}}
            </div>
            <form role="form" class="form-horizontal form-bordered" method="post" action="{{ url('setting/user') }}">

                <div class="box-body">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="col-xs-12" style="padding:0">
                        <div class="col-xs-8">
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Nama<span class="text-red">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter Full Name" value="{{ $user->name }}">
                                </div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">E-mail<span class="text-red">*</span></label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter E-mail" value="{{ $user->email }}" required>
                                </div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Password Lama<span class="text-red">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="oldPassword" name="oldPassword"
                                        placeholder="Enter Old Password" required>
                                </div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Password Baru<span class="text-red">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="newPassword" name="newPassword"
                                        placeholder="Enter New Password" required>
                                </div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Ketik Ulang Password Baru<span class="text-red">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                        placeholder="Enter Confirm New Password" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <ul class="login-more p-t-20">
                                <div id="message" class="col-md-12">

                                    <h4>Password harus terdiri dari : </h4>
                                    <p id="letter" class="invalid"><i id="letter_fa" class="fa fa-close"></i>
                                        <b>Huruf besar</b>
                                    </p>
                                    <p id="capital" class="invalid"><i id="capital_fa" class="fa fa-close"></i>
                                        <b>Huruf kecil</b>
                                    </p>
                                    <p id="number" class="invalid"><i id="number_fa" class="fa fa-close"></i>
                                        <b>Angka</b>
                                    </p>
                                    <p id="length" class="invalid"><i id="length_fa" class="fa fa-close"></i>
                                        <b>Minimal 8 karakter</b>
                                    </p>
                                </div>
                            </ul>
                            <input type="hidden" name="validation" id="validation" value="4">
                        </div>
                    </div>

                    <div class="col-sm-4 col-sm-offset-6">
                        <div class="btn-group">
                            <a class="btn btn-danger" href="{{ url('setting/user') }}">Cancel</a>
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
        <script type="text/javascript">
            jQuery(document).ready(function() {
                $('#validation').val(4);
                $('#newPassword').val('');
                $('#confirmPassword').val('');
            });


            var myInput = document.getElementById("newPassword");
            var myInputConfirm = document.getElementById("confirmPassword");
            var letter = document.getElementById("letter");
            var capital = document.getElementById("capital");
            var number = document.getElementById("number");
            var length = document.getElementById("length");

            myInput.onkeyup = function() {
                // Validate lowercase letters
                checkSpell();
            }

            myInputConfirm.onkeyup = function() {
                // Validate lowercase letters
                checkSpellConfirm();
            }

            function checkSpell() {

                // Validate lower letters
                var lowerCaseLetters = /[A-Z]/g;
                if (myInput.value.match(lowerCaseLetters)) {
                    letter.classList.remove("invalid");
                    letter.classList.add("valid");
                    $("#letter_fa").removeAttr("class");
                    $("#letter_fa").addClass("fa fa-check-square");
                } else {
                    letter.classList.remove("valid");
                    letter.classList.add("invalid");
                    $("#letter_fa").removeAttr("class");
                    $("#letter_fa").addClass("fa fa-close");
                }

                // Validate capital letters
                var upperCaseLetters = /[a-z]/g;
                if (myInput.value.match(upperCaseLetters)) {
                    capital.classList.remove("invalid");
                    capital.classList.add("valid");
                    $("#capital_fa").removeAttr("class");
                    $("#capital_fa").addClass("fa fa-check-square");
                } else {
                    capital.classList.remove("valid");
                    capital.classList.add("invalid");
                    $("#capital_fa").removeAttr("class");
                    $("#capital_fa").addClass("fa fa-close");
                }

                // Validate numbers
                var numbers = /[0-9]/g;
                if (myInput.value.match(numbers)) {
                    number.classList.remove("invalid");
                    number.classList.add("valid");
                    $("#number_fa").removeAttr("class");
                    $("#number_fa").addClass("fa fa-check-square");
                } else {
                    number.classList.remove("valid");
                    number.classList.add("invalid");
                    $("#number_fa").removeAttr("class");
                    $("#number_fa").addClass("fa fa-close");
                }

                // Validate length
                if (myInput.value.length >= 8) {
                    length.classList.remove("invalid");
                    length.classList.add("valid");
                    $("#length_fa").removeAttr("class");
                    $("#length_fa").addClass("fa fa-check-square");
                } else {
                    length.classList.remove("valid");
                    length.classList.add("invalid");
                    $("#length_fa").removeAttr("class");
                    $("#length_fa").addClass("fa fa-close");
                }

                checkValidation();
            }

            function checkValidation() {
                var status = 4;
                var
                let = $('#letter_fa').attr('class');
                var cap = $('#capital_fa').attr('class');
                var num = $('#number_fa').attr('class');
                var leg = $('#length_fa').attr('class');
                if (let == "fa fa-check-square") {
                    status--;
                }
                if (cap == "fa fa-check-square") {
                    status--;
                }
                if (num == "fa fa-check-square") {
                    status--;
                }
                if (leg == "fa fa-check-square") {
                    status--;
                }
                $("#validation").val(status);
            }

            function checkSpellConfirm() {

                // Validate lower letters
                var lowerCaseLetters = /[A-Z]/g;
                if (myInputConfirm.value.match(lowerCaseLetters)) {
                    letter.classList.remove("invalid");
                    letter.classList.add("valid");
                    $("#letter_fa").removeAttr("class");
                    $("#letter_fa").addClass("fa fa-check-square");
                } else {
                    letter.classList.remove("valid");
                    letter.classList.add("invalid");
                    $("#letter_fa").removeAttr("class");
                    $("#letter_fa").addClass("fa fa-close");
                }

                // Validate capital letters
                var upperCaseLetters = /[a-z]/g;
                if (myInputConfirm.value.match(upperCaseLetters)) {
                    capital.classList.remove("invalid");
                    capital.classList.add("valid");
                    $("#capital_fa").removeAttr("class");
                    $("#capital_fa").addClass("fa fa-check-square");
                } else {
                    capital.classList.remove("valid");
                    capital.classList.add("invalid");
                    $("#capital_fa").removeAttr("class");
                    $("#capital_fa").addClass("fa fa-close");
                }
                // Validate numbers
                var numbers = /[0-9]/g;
                if (myInputConfirm.value.match(numbers)) {
                    number.classList.remove("invalid");
                    number.classList.add("valid");
                    $("#number_fa").removeAttr("class");
                    $("#number_fa").addClass("fa fa-check-square");
                } else {
                    number.classList.remove("valid");
                    number.classList.add("invalid");
                    $("#number_fa").removeAttr("class");
                    $("#number_fa").addClass("fa fa-close");
                }

                // Validate length
                if (myInputConfirm.value.length >= 8) {
                    length.classList.remove("invalid");
                    length.classList.add("valid");
                    $("#length_fa").removeAttr("class");
                    $("#length_fa").addClass("fa fa-check-square");
                } else {
                    length.classList.remove("valid");
                    length.classList.add("invalid");
                    $("#length_fa").removeAttr("class");
                    $("#length_fa").addClass("fa fa-close");
                }

                if (myInput.value != myInputConfirm.value) {
                    $('#not_match').show();
                    $('#not_match').html('Password baru tidak sesuai.');
                } else {
                    $('#not_match').hide();
                    $('#not_match').html('');
                }
            }
        </script>
    @stop
