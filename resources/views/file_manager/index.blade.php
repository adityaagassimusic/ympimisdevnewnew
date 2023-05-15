@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/bootstrap-datetimepicker.min.css') }}">
    <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
    <link href="<?php echo e(url('css/jquery.numpad.css')); ?>" rel="stylesheet">

    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            text-align: center !important;
            vertical-align: middle !important;
            /* background-color: #00a65a; */
            background-color: #605CA8;
            color: #fff;
            overflow: hidden;
        }

        tbody>tr>td {
            /*text-align:center;*/
            padding: 5px !important;
        }

        tfoot>tr>th {
            /*text-align:center;*/
        }

        th:hover {
            overflow: visible;
        }

        td:hover {
            overflow: visible;
        }

        table.table-bordered {
            border: 1px solid #333;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid #333;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid #333;
            vertical-align: middle;
            padding: 0;
            margin-bottom: 0px;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid #DEE2E6;
            padding: 0;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #table-reminder>tbody>tr>td {
            padding: 10px;
        }

        input[type=number] {
            -moz-appearance: textfield;
            /* Firefox */
        }

        #select2-add_pic-container {
            text-align: left;
        }

        /*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
                                                                                                                                                                                                              background-color: #ffd8b7;
                                                                                                                                                                                                             }*/

        .table-hover tbody tr:hover td,
        .table-hover tbody tr:hover th {
            background-color: #FFD700;
        }

        #loading,
        #error {
            display: none;
        }

        .file-upload-group {
            margin: 2% 20%;
        }

        input[type="file"] {
            display: none;
        }

        .custom-file-upload {
            text-align: center;
            position: relative;
            border: 1px solid #ccc;
            display: inline-block;
            /* padding: 6px 12px; */
            cursor: pointer;

            width: 100%;
        }

        .file-upload-box {
            margin-left: -5px;
        }

        #tableMaster tr th {
            font-size: 14px !important;
        }

        .tfootSearch {
            color: #333 !important;
        }


        .custom-file-upload span {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            font-size: 16px;
            font-weight: bold;
            color: #555;
        }

        .dataTables_filter {
            display: none;
        }

        .catActive {
            color: #2196F3 !important;
        }

        .dropShadow {
            filter: drop-shadow(0px 2px 2px rgba(85, 85, 85, 0.500));
        }

        .dt-button-background {
            display: none !important;
            position: :static !important;
        }

        .highcharts-credits {
            display: none;
        }


        /* CSS for Modal Emails */

        .recipient-field {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
        }

        .recipient-labels {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 5px;
        }

        .recipient-label {
            display: flex;
            align-items: center;
            /* background-color: #3C8DBC; */
            border-radius: 5px;
            padding: 5px;
        }

        .recipient-label span {
            margin-right: 5px;
            color: #fff;
        }

        #recipient-input {
            flex: 1;
            border: none;
            outline: none;
        }

        .buttonActionCustom button{
            width: 100%;
            margin: 2px 0px;
        }

    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }} <small class="text-purple">{{ $title_jp }}</small>
            <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#add-modal" data-backdrop="static" data-keyboard="false" onclick="cancelAll()" style="margin-right: 5px">
                <i class="fa fa-plus"></i>&nbsp;&nbsp;Add Document
            </button>
            {{-- <a href="{{ route('FileManagerControl')}}" class="btn btn-warning btn-sm pull-right" onclick="" style="margin-right: 5px">
                <i class="fa fa-database"></i>&nbsp;&nbsp;Document Control
            </a> --}}
            <button class="btn btn-primary btn-sm pull-right" onclick="refresh()" style="margin-right: 5px">
                <i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
            </button>
        </h1>
    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>
        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                {{ session('error') }}
            </div>
        @endif
        <div class="row">
            <div class="col-xs-2" style="padding-right: 0">
                <div class="box box-solid dropShadow" style="margin-bottom: 3%;">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 style="font-size: 16px; padding-bottom: 10px; border-bottom:1px solid #33333333; font-weight:800; margin-top: 1% ;">
                                    Categories
                                    <button class="btn-xs" data-toggle="modal" data-target="#edit-categories" style="float: right;"><i class="fa fa-pencil"></i></button>
                                    <button class="btn-xs" onclick="$('.collapse').collapse('hide');" style="float: right;"><i class="fa fa-window-restore"></i></button>
                                </h4>
                                <ol id="bodyCategories" style="list-style: none; padding-left:1%;">
                                </ol>
                                <input type="text" id="file_category" value="" hidden>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-10" style="padding-right: 5px; padding-left: 10px;">
                <div class="box box-solid dropShadow" style="margin-bottom: 10px;">
                    <div class="row" style="margin: 0; padding:5px 5px;">
                        <h4 class="col-xs-12" style="margin:5px 10px; font-weight:600;">
                            <div class="fa fa-search"></div> Search Query

                        </h4>
                        <div class="form-group col-xs-3" style="padding: 0; margin-bottom:0px;">
                            <span class="input-group-addon col-xs-2" style="margin: 0; padding:6px 20px 10px 10px" id="basic-addon-filter-fy"><i class="fa fa-calendar"></i></span>
                            <div class="col-xs-10" style="padding: 0">
                                <select class="form-control select2" data-placeholder="Select Fiscal Year" name="searchBy_fy" id="searchBy_fy" aria-describedby="basic-addon-filter-fy" multiple>
                                    <option></option>
                                    @foreach ($fiscal_year as $fy)
                                        <option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="text" id="hidden_search_fy" hidden>
                        </div>
                        <div class="form-group col-xs-3" style="padding: 0; margin-bottom:0px;">
                            <span class="input-group-addon col-xs-2" style="margin: 0; padding:6px 20px 10px 10px" id="basic-addon-filter-y"><i class="fa fa-calendar"></i></span>
                            <div class="col-xs-10" style="padding: 0">
                                <select class="form-control select2" data-placeholder="Select Year" name="searchBy_y" id="searchBy_y" style="width: 100%" aria-describedby="basic-addon-filter-y" multiple>
                                    <option></option>
                                    @foreach ($year as $yr)
                                        <option value="{{ $yr->year }}">{{ $yr->year }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" id="hidden_search_y" hidden>
                            </div>
                        </div>
                        <div class="form-group col-xs-2" style="padding: 0;">
                            <div class="col-xs-12" style="padding: 0">
                                <div class="input-group date" style="width:100%;z-index: 20000; margin:0 0 0 40px;">
                                    <input type="text" class="form-control datepicker" name="year_start" id="year_start" placeholder="Start" autocomplete="off">
                                </div>
                                <input type="text" id="hidden_search_starty" hidden>
                            </div>
                        </div>
                        <div class="form-group col-xs-2" style="padding: 0;">
                            <div class="col-xs-12" style="padding: 0">
                                <div class="input-group date" style="width:100%;z-index: 20000; margin:0 0 0 20px;">
                                    <input type="text" class="form-control datepicker" name="year_end" id="year_end" placeholder="End" autocomplete="off">
                                </div>
                                <input type="text" id="hidden_search_endy" hidden>
                            </div>
                        </div>
                        <div class="form-group col-xs-2" style="padding: 0; margin-bottom:0px;">
                            <div class="col-xs-12" style="padding:0;">
                                <button class="btn btn-primary btn-sm" onclick="searchYearBetween()" style="width: 100%; padding:5px 10px 9px 30px;"><i class="fa fa-search"></i> Search!</button>
                            </div>
                        </div>
                        <hr class="col-xs-12" style="margin: 5px 10px" />
                        {{-- <div class="col-xs-3" style="padding:0px 5px;">
                            <h4 class="col-xs-12" style="margin:5px 1px; font-weight:600; font-size:14px;"><i class="fa fa-calendar-o"></i> Select Fiscal Year</h4>
                            <div class="form-group col-xs-12" style="padding: 0; margin-bottom:0px;">
                                <div class="" style="margin: 5px 10px;">
                                    <select class="form-control select2" data-placeholder="Select Fiscal Year" name="searchBy_fy" id="searchBy_fy" style="width: 100%" multiple>
                                        <option></option>
                                        @foreach ($fiscal_year as $fy)
                                            <option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" id="hidden_search_fy" hidden>
                                </div>
                            </div>
                            <div class="form-group col-xs-12" style="padding: 0; margin-bottom:0px;">
                                <h4 class="col-xs-12" style="margin:5px 1px; font-weight:600; font-size:14px;"><i class="fa fa-calendar-o"></i> Select Year</h4>
                                <div class="" style="margin: 5px 10px;">
                                    <select class="form-control select2" data-placeholder="Select Year" name="searchBy_y" id="searchBy_y" style="width: 100%" multiple>
                                        <option></option>
                                        @foreach ($year as $yr)
                                            <option value="{{ $yr->year }}">{{ $yr->year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" id="hidden_search_y" hidden>
                                </div>
                            </div>
                            <h4 class="col-xs-12" style="margin:2px 1px; font-weight:600; font-size:14px;"><i class="fa fa-calendar-o"></i> Select Year Between</h4>
                            <div class="form-group col-xs-12" style="padding: 0; margin-bottom:0px;">
                                <div class="" style="margin: 5px 10px;">
                                    <div class="input-group date" style="width:100%;z-index: 20000">
                                        <input type="text" class="form-control datepicker" name="year_start" id="year_start" placeholder="Start" autocomplete="off">
                                    </div>
                                    <input type="text" id="hidden_search_y" hidden>
                                </div>
                            </div>
                            <div class="form-group col-xs-12" style="padding: 0; margin-top:-11px; margin-bottom:0px;">
                                <div class="" style="margin: 5px 10px;">
                                    <div class="input-group date" style="width:100%;z-index: 20000">
                                        <input type="text" class="form-control datepicker" name="year_end" id="year_end" placeholder="End" autocomplete="off">
                                    </div>
                                    <input type="text" id="hidden_search_y" hidden>
                                </div>
                            </div>
                            <div class="form-group col-xs-12" style="padding: 0; margin-top:-11px;">
                                <div class="" style="margin: 5px 10px;">
                                    <button class="btn btn-primary btn-sm" onclick="searchYearBetween()" style="width: 100%"><i class="fa fa-search"></i> Search!</button>
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-xs-3">
                            <div id="container-table-reminder" style="height: 60vh">
                                <table id="table-reminder" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="background-color: #90ED7D">
                                            <td><b>(> 30 days) Due Date</b></td>
                                            <td id="statusDueDate-1" style="text-align: center"></td>
                                        </tr>
                                        <tr style="background-color: #F7A35C">
                                            <td><b>(< 30 days) Due Date</b>
                                            </td>
                                            <td id="statusDueDate-2" style="text-align: center"></td>
                                        </tr>
                                        <tr style="background-color: #F15C80; color:white;">
                                            <td><b>(< 15 days) Due Date</b>
                                            </td>
                                            <td id="statusDueDate-3" style="text-align: center"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="text" id="hidden_due_date" hidden>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>FY</th>
                                            <th>Range</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fiscal_year_details as $fyd)
                                            <tr>
                                                <td>{{ $fyd->fiscal_year }}</td>
                                                <td>{{ $fyd->start_end_date }}</td>
                                                {{-- <td>{{ $fyd->end_date }}</td>                                                 --}}
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <div class="col-xs-9">
                            <div id="container-highchart" style="height: 60vh"></div>
                        </div>
                    </div>
                    <br>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12" style="padding: 0px 5px 0px 15px">
                <div class="box box-solid dropShadow">
                    <div class="rightSearchBox Yearbreadcrumb float-sm-right" style="margin: 0">
                        <ol id="breadcrumb" class="breadcrumb" style="background-color: #f3f3f3; color: #000">
                            <li class="breadcrumb-item"><a href="#" onclick="unselectCategory();">Files</a></li>
                            <li id="bread_category" class="breadcrumb-item"></li>
                            <li id="bread_fiscalyear" class="breadcrumb-item"></li>
                        </ol>
                        <div class="input-group" style="margin: 1%">
                            <span class="input-group-addon" id="basic-addon1"><i class="fa fa-search"></i></span>
                            <input id="searchDocs" type="search" class="form-control" placeholder="Search Here" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="col-xs-12">
                            <div id="tableMasterContainer" class="row">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal edit categories --}}
        <div class="modal modal-default fade" id="edit-categories">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="font-size:20px; text-align: center; margin:5px; font-weight: bold;color: white">Edit
                                Categories</h1>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box-body">
                                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                                    <h4 style="font-weight: 600;">Add New Category</h4>
                                    <div class="form-group row" align="right">
                                        <div class="col-sm-4" style="padding: 0">
                                            <input type="text" class="form-control" id="add_category_name" placeholder="Category Name...">
                                        </div>
                                        <div class="col-sm-3" style="padding: 0">
                                            <select class="form-control" name="add_category_class" id="add_category_class">
                                                <option value="0">Select Class..</option>
                                                <option value="CONFIDENTIAL">CONFIDENTIAL</option>
                                                <option value="TOP-CONFIDENTIAL">TOP-CONFIDENTIAL</option>
                                                <option value="HIGH-CONFIDENTIAL">HIGH-CONFIDENTIAL</option>
                                                <option value="HIGHLY-CONFIDENTIAL">HIGHLY-CONFIDENTIAL</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3" style="padding: 0">
                                            <select class="form-control" name="add_parent_id" id="add_parent_id">
                                                <option value="">-- Select Sub-Category --</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-1" style="padding: 0">
                                            <button class="btn btn-success" style="width: 100%" onclick="addCategory()"><i class="fa fa-plus"></i> Add</button>
                                        </div>
                                    </div>
                                    {{-- category tables --}}
                                    <table id="tableCategory" class="table table-striped table-hover" style="margin-bottom: 0;">
                                        <thead style="">
                                            <tr>
                                                <th width="1%">#</th>
                                                <th width="1%">Category</th>
                                                <th width="1%">Class</th>
                                                <th width="1%">Sub-Category</th>
                                                <th width="1%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyTableCategory">
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal add document --}}
        <div class="modal modal-default fade" id="add-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="font-size:18px; text-align: center; margin:5px; font-weight: bold;color: white">
                                Add Document</h1>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="box-body">
                                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Document Category<span class="text-red">*</span></label>
                                        <div class="col-sm-5" align="left" id="divAddCategory">
                                            <select class="form-control select2" data-placeholder="Select Category" name="add_category" id="add_file_category" style="width: 100%" required>
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row " align="right">
                                        <label class="col-sm-4">Title<span class="text-red">*</span></label>
                                        <div class="col-sm-5">
                                            {{-- <input type="text" class="form-control" name="file_name" id="add_file_name" placeholder="File Name" required> --}}
                                            {{-- <input type="text" class="form-control" name="file_name" id="add_file_name" placeholder="File Name" required regex="^[a-zA-Z0-9\s\-\_\.]+$" regex-message="File name must be alphanumeric" data-validation="custom" data-validation-regexp="^[a-zA-Z0-9\s\-\_\.]+$" data-validation-error-msg="File name must be alphanumeric"> --}}
                                            <textarea id="add_file_name" class="form-control" placeholder="File Name" style="resize:vertical;" required regex="^[a-zA-Z0-9\s\-\_\.]+$" regex-message="File name must be alphanumeric" data-validation="custom" data-validation-regexp="^[a-zA-Z0-9\s\-\_\.]+$" data-validation-error-msg="File name must be alphanumeric"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Company Name<span class="text-red">*</span></label>
                                        <div class="col-sm-5">
                                            {{-- convert to select2 <input name="add_pic" class="form-control" id="add_pic" placeholder="e.g EY / Bank Mandiri" list="pic" required> --}}
                                            <select class="form-control select2" data-placeholder="Select Company" name="add_pic" id="add_pic" style="width: 100%" required>
                                                <option></option>                                                
                                                @foreach ($pics as $pic)
                                                    <option value="{{ $pic }}">{{ $pic }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Letter No</label>
                                        <div class="col-sm-5">
                                            <input type="text" name="add_letterNo" class="form-control" id="add_letterNo" placeholder="Letter No">
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Letter Date</label>
                                        <div class="col-sm-5">
                                            <input type="date" name="add_letterDate" class="form-control" id="add_letterDate" placeholder="Letter Date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="box-body">
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Fiscal Year<span class="text-red">*</span></label>
                                        <div class="col-sm-5" align="left" id="divAddCategory">
                                            <select class="form-control select2" data-placeholder="Select Fiscal Year" name="add_fy" id="add_fy" style="width: 100%" required>
                                                <option></option>
                                                @foreach ($fiscal_year_details as $fyd)
                                                    <option value="{{ $fyd->fiscal_year }}"> ({{ $fyd->fiscal_year }}) {{ $fyd->start_end_date }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Received date</label>
                                        <div class="col-sm-5">
                                            <input type="date" name="add_receivedDate" class="form-control" id="add_receivedDate" placeholder="-- date --">
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Period</label>
                                        <div class="col-sm-5">
                                            <input type="text" name="add_period" class="form-control" id="add_period" placeholder="-- date --">
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Due Date</label>
                                        <div class="col-sm-5">
                                            <input type="date" name="add_dueDate" class="form-control" id="add_dueDate" placeholder="add due date">
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Remark</label>
                                        <div class="col-sm-5">
                                            <textarea name="add_remark" class="form-control" id="add_remark" placeholder="add remark" style="resize: vertical"></textarea>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group row" align="left">
                                        <label class="col-sm-4" style="left:50px;">Reminder</label>
                                        <div class="col-sm-5 input-group">
                                            <input type="text" value="0" class="numpad form-control" placeholder="Reminder" id="add_createReminder" style="margin:0 0 0 15px; width:85%;" readonly>
                                            <div class="input-group-addon" style="">
                                                <i class="fa fa-clock-o"></i> Day(s)
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group row file-upload-group modal-footer">
                                <label class="col-sm-4" style="margin-left: -48px; margin-right:35px;">Attachment<span class="text-red">*</span></label>
                                <div class="col-sm-8 file-upload-box" style="padding: 0 !important; width:63%;">
                                    <label for="file-upload" class="custom-file-upload">
                                        <ol class="jLabel" style="list-style:none; padding: 5px; color:grey;">
                                            <li><i class="fa fa-upload"></i> Browse File Here...</li>
                                        </ol>
                                    </label>
                                    <input id="file-upload" type="file" multiple />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
                                <button class="btn btn-success" onclick="uploadFile()"><i class="fa fa-plus"></i>
                                    Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal view attachment --}}
        <div class="modal modal-default fade" id="view-attachment-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="font-size:20px; text-align: center; margin:5px; font-weight: bold;color: white">View Attachment</h1>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="box-body">
                                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                                    <input type="text" id="edit_id" hidden>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Document Category<span class="text-red">*</span></label>
                                        <div class="col-sm-5" align="left" id="divAddCategory">
                                            <select class="form-control select2" data-placeholder="Select Category" id="edit_file_category" style="width: 100%" required>
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Company Name<span class="text-red">*</span></label>
                                        <div class="col-sm-5" style="text-align:left !important;">
                                            {{-- <input type="text" class="form-control" id="edit_pic" placeholder="e.g EY / Bank Mandiri" required > --}}
                                            <select class="form-control select2" data-placeholder="Select Company" id="edit_pic" style="width: 100%" required>
                                                <option></option>
                                                @foreach ($pics as $pic)
                                                    <option value="{{ $pic }}">{{ $pic }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row " align="right">
                                        <label class="col-sm-4">Title<span class="text-red">*</span></label>
                                        <div class="col-sm-5">
                                            {{-- <input type="text" class="form-control" name="file_name" id="edit_file_name" placeholder="File Name" required> --}}
                                            <textarea class="form-control" id="edit_file_name" style="resize:vertical"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Letter No</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="edit_letterNo" placeholder="Letter No">
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Letter Date</label>
                                        <div class="col-sm-5">
                                            <input type="date" name="edit_letterDate" class="form-control" id="edit_letterDate" placeholder="Letter Date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="box-body">
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Fiscal Year<span class="text-red">*</span></label>
                                        <div class="col-sm-5" align="left" id="divAddCategory">
                                            <select class="form-control select2" data-placeholder="Select Fiscal Year" id="edit_fy" style="width: 100%" required disabled>
                                                <option></option>
                                                @foreach ($fiscal_year_details as $fy)
                                                    <option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Received Date</label>
                                        <div class="col-sm-5">
                                            <input type="date" class="form-control" id="edit_receivedDate">
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Period</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="edit_period" placeholder="e.g Q1, Q2">
                                        </div>
                                    </div>
                                    <div class="form-group row" align="right">
                                        <label class="col-sm-4">Remark</label>
                                        <div class="col-sm-5">
                                            {{-- <input type="text" class="form-control" id="edit_remark" placeholder="add remark"> --}}
                                            <textarea id="edit_remark" class="form-control" placeholder="add remark" style="resize: vertical"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row" style="background-color:padding:5px 10px;">
                            <center>                                
                                <button type="button" class="btn btn-danger" style="margin:0;" data-dismiss="modal"><i class="fa fa-close"></i>Close</button>
                                <button type="button" class="btn btn-warning" style="margin:0;" onclick="editFileAttachment()" id="btnEditFile"><i class="fa fa-edit"></i> Edit</button>
                            </center>
                        </div>
                        <hr>
                        <div class="row modal-footer">
                            <div class="form-group" align="right">
                                <label class="col-sm-4">Due Date</label>
                                <div class="col-sm-5">
                                    <input type="date" class="form-control" id="edit_dueDate" placeholder="due date">
                                    <div class="btn-group" align="center">
                                        <button class="btn btn-success" onclick="DueDateControll('Done')"><i class="fa fa-check"></i> Done</button>
                                        <button class="btn btn-warning" onclick="DueDateControll('Extend')"><i class="fa fa-clock-o"></i> Extend</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group file-upload-group modal-footer">
                                <label class="col-sm-4" style="margin-left: -48px; margin-right:35px;">Add Attachment<span class="text-red">*</span></label>
                                <div class="col-sm-8 file-upload-box" style="padding: 0 !important; width:63%;">
                                    <label for="file-upload" class="custom-file-upload">
                                        <ol class="jLabel" style="list-style:none; padding: 5px; color:grey;">
                                            <li><i class="fa fa-upload"></i> Browse File Here...</li>
                                        </ol>
                                    </label>
                                    <input id="file-upload" type="file" multiple />
                                    <button class="btn btn-info" onclick="uploadAttachment()"><i class="fa fa-upload"></i> Upload</button>
                                </div>
                            </div>
                        </div>
                        <div class="row box-body">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />

                            {{-- category tables --}}
                            <table id="tableViewAttachment" class="table table-striped table-hover" style="margin-bottom: 0;">
                                <thead style="">
                                    <tr>
                                        <th width="1%">#</th>
                                        <th style="text-align: left !important;" width="5%">Docs name</th>
                                        <th width="1%">#</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTableViewAttachment">
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- tabindex="-1" role="dialog" --}}
        {{-- modal send email --}}        
        <div class="modal modal-default fade" data-backdrop="static" id="send-email-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color: #3C8DBC; padding-right: 1%;">
                            {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button> --}}
                            <h1 style="font-size:20px; text-align: center; margin:5px; font-weight: bold;color: white">
                                Send Email
                            </h1>
                        </div>
                        <button class="btn btn-md btn-warning" data-dismiss="modal" style="margin-top:2%;">
                            <i class="fa fa-arrow-left"></i> Back
                        </button>                        
                    </div>
                    <div class="modal-body">
                        {{-- OPTIMIZE FILE_ID --}}
                        <input type="hidden" id="email_file_id" value="">

                        <div class="row" style="padding: 10px 20px">
                            <div id="selectEmpRecipient">
                                <select class="form-control selectEmpRecipient" data-placeholder="Pilih Karyawan" name="" id="add_recipient_by_nik" style="width: 100%">
                                    <option value=""></option>
                                    @foreach($employees as $emp)
                                    <option value="{{$emp->email}}">{{$emp->username}} - {{$emp->name}}</option>
                                    @endforeach
                                </select>
                            </div>                            
                        </div>

                        <div class="row" style="padding:10px 20px;">
                            <label for="recipient-input">Add Recipient</label>
                            <div class="recipient-field">
                                <div class="recipient-labels">

                                </div>
                                <input type="email" id="recipient-input" class="form-control" placeholder=" -- Add recipients --">
                            </div>                                                        
                        </div>
                        <div class="row" style="padding: 10px 20px">                            
                            <label for="subject-email">Subject Email</label>
                            <input type="text" id="subject-email" class="form-control" placeholder=" -- Add Subject Email -- ">                            
                        </div>
                        <div class="row" style="padding: 10px 20px">                            
                            <label for="body-email">Body Email</label>
                            <textarea id="body-email" class="form-control" placeholder=" -- Add body email -- " style="resize: vertical"></textarea>
                        </div>
                        <div class="row" style="padding: 10px 20px">
                            <button class="btn btn-md btn-primary" onclick="dcsSendEmail()"><i class="fa fa-paper-plane"></i> Send Email</button>
                        </div>
                        <div class="row box-body">
                            <label for="tableEmailAttachment">File Attachment</label>
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />

                            <table id="tableEmailAttachment" class="table table-striped table-hover" style="margin-bottom: 0;">
                                <thead style="">
                                    <tr>
                                        <th width="1%">#</th>
                                        <th style="text-align: left !important;" width="5%">Docs name</th>                                        
                                    </tr>
                                </thead>
                                <tbody id="bodyTableEmailAttachment">
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
@section('scripts')

    <script src="{{ url('js/moment.min.js') }}"></script>
    <script src="{{ url('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    {{-- <script src="{{ url('js/highstock.js') }}"></script> --}}
    <script src="<?php echo e(url('js/jquery.numpad.js')); ?>"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
        $.fn.numpad.defaults.buttonNumberTpl =
            '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl =
            '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function() {
            $(this).find('.done').addClass('btn-primary');
        };

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });

            $('.datepicker').datepicker({
                <?php $tgl_max = date('m-Y'); ?>
                format: "mm-yyyy",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                endDate: '<?php echo $tgl_max; ?>'
            });

            $('.select2').select2({
                allowClear: false,                
            });

            $("#add_pic").select2({
                tags: true,
                dropdownParent: $('#add-modal')
            });            

            $('.selectEmpRecipient').select2({
                dropdownParent: $('#selectEmpRecipient'),
                allowClear:true
            });

            $(".catIndex").click(function() {
                $(".catIndex").removeClass("catActive");
                $(this).addClass("catActive");
            });

            $('#file_group_toggle').on('change', function() {
                if ($(this).is(":checked")) {
                    $('.isGroupToggleActive').css('display', 'block');
                } else {
                    $('.isGroupToggleActive').css('display', 'none');
                }
            });

            $("#searchBy_fy").on("change", function() {
                $('#hidden_due_date').val("");
                $("#hidden_search_fy").val($(this).val());

                if ($(this).val().length > 1) {
                    for (var i = 0; i < $(this).val().length; i++) {
                        if (i == 0) {
                            $("#bread_fiscalyear").html($(this).val()[i]);
                        } else {
                            $("#bread_fiscalyear").append(" / " + $(this).val()[i]);
                        }
                    }
                } else {
                    $("#bread_fiscalyear").html($(this).val());
                }

                fillList();
            });

            $("#searchBy_y").on("change", function() {
                $('#hidden_due_date').val("");
                $("#hidden_search_y").val($(this).val());
                fillList();
            });

            $('#searchDocs').keyup(function() {
                var table = $('#tableMaster').DataTable();
                table.search($(this).val()).draw();
            });

            $('.selectEmpRecipient').on('change', function() {
                let email = $(this).val();
                addRecipientLabel(email);
            });

            fillCategories();
            


        });

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '2000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '2000'
            });
        }

        document.getElementById("file-upload").onchange = function() {

            $('.jLabel').html('');
            $('.jLabel').css('text-align', 'left');
            $('.jLabel').css('list-style', 'decimal');
            $('.jLabel').css('margin-left', '20px');

            let fileList = [];
            for (var i = 0; i < this.files.length; i++) {
                fileList.push("<li>" + this.files[i].name + "</li>");
            }
            $('.jLabel').append(fileList);

        };

        const refresh = () => {
            fillList();
            fillCategories();
            fetchChartData();
        }

        function requestCategory(category) {
            $("#hidden_due_date").val("");
            $("#file_category").val(category);
            $("#bread_category").html(category);
            fillList();
        }

        function unselectCategory() {
            requestCategory('');
            $("#searchBy_fy").val("").trigger('change');
            $("#searchBy_y").val("").trigger('change');
            $("#hidden_search_fy").val("");
            $("#hidden_search_y").val("");
            $("#bread_fiscalyear").html("");
            $("#bread_category").html("");
            $(".catIndex").removeClass("catActive");
            fillList();
        }

        function initTable() {
            $('#tableMasterContainer').html("");

            var tableData = '';
            tableData += '<table id="tableMaster" class="table table-bordered table-striped table-hover" style="margin-bottom: 0">';
            tableData += '<thead>';
            tableData += '<tr>';
            tableData += '<th width="0.1%">#</th>';
            tableData += '<th width="0.1%">Fiscal Year</th>';
            tableData += '<th width="0.1%">Period</th>';
            tableData += '<th style="text-align: left !important;" width="1%">Category</th>';
            tableData += '<th style="text-align: left !important;" width="5%">Title</th>';
            tableData += '<th style="text-align: left !important;" width="5%">Company Name</th>';
            tableData += '<th width="3%">Letter No</th>';
            tableData += '<th width="3%">Letter Date</th>';
            tableData += '<th width="3%">Received Date</th>';
            tableData += '<th width="2%">Remark</th>';
            tableData += '<th width="1%">Due Date</th>';
            tableData += '<th width="0.1%">upload_date</th>';
            tableData += '<th width="2%">#</th>';
            tableData += '</tr>';
            tableData += '</thead>';
            tableData += '<tbody id="bodyTableMaster">';
            tableData += '</tbody>';
            tableData += '<tfoot>';
            tableData += '<tr>';
            tableData += '<th width="0.1%">#</th>';
            tableData += '<th width="0.1%">Fiscal Year</th>';
            tableData += '<th width="0.1%">Period</th>';
            tableData += '<th style="text-align: left !important;" width="1%">Category</th>';
            tableData += '<th style="text-align: left !important;" width="5%">Title</th>';
            tableData += '<th style="text-align: left !important;" width="5%">Company Name</th>';
            tableData += '<th width="3%">Letter No</th>';
            tableData += '<th width="3%">Letter Date</th>';
            tableData += '<th width="3%">Received Date</th>';
            tableData += '<th width="2%">Remark</th>';
            tableData += '<th width="1%">Due Date</th>';
            tableData += '<th width="0.1%">upload_date</th>';
            tableData += '<th width="2%">#</th>';
            tableData += '</tfoot>';
            tableData += '</table>';

            $('#tableMasterContainer').append(tableData);

            tableData = "";
        }

        function fillList() {
            initTable();
            // $('#loading').show();

            var data = {
                file_category: $("#file_category").val(),
                fiscal_year: $("#hidden_search_fy").val(),
                year: $("#hidden_search_y").val(),
                start_year: $("#year_start").val(),
                end_year: $("#year_end").val(),
                due_date_filter: $("#hidden_due_date").val(),
            }
            $.get('{{ url('fetch/filemanager/') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#tableMaster').DataTable().clear();
                    $('#tableMaster').DataTable().destroy();
                    $('#bodyTableMaster').html("");
                    var tableData = "";
                    var index = 1;

                    $.each(result.data, function(key, value) {
                        tableData += '<tr>';
                        tableData += '<td style="text-align:center;">' + index + '</td>';
                        tableData += '<td style="text-align:center;">' + value.fiscal_year + '</td>';
                        tableData += '<td style="text-align:center;">' + (value.period != null ? value.period : '-') + '</td>';
                        tableData += '<td><b>' + value.file_category + '</b></td>';
                        tableData += '<td><b>' + value.file_name + '</b></td>';
                        tableData += '<td><b>' + value.file_pic + '</b></td>';
                        tableData += '<td>' + (value.letter_no != null ? value.letter_no : '-') + '</td>';
                        
                        tableData += '<td style="text-align:right;">' + (value.letter_date != null ? moment(value.letter_date).format('DD/MM/YYYY') : '-') + '</td>';
                        
                        tableData += '<td style="text-align:right;">' + (value.received_date != null ? moment(value.received_date).format('DD/MM/YYYY') : '-') + '</td>';
                        tableData += '<td>' + (value.remark != null ? value.remark : '-') + '</td>';

                        let newValueDueDate = new Date(value.due_date);
                        let today = new Date();
                        let differenceInDays = Math.round((newValueDueDate - today) / (1000 * 60 * 60 * 24));

                        tableData += '<td style="text-align:right;';
                        if (differenceInDays <= 15 && value.due_date != null) {
                            tableData += 'background-color:#DD4B39; color:white;';
                        } else if (differenceInDays <= 30 && value.due_date != null) {
                            tableData += 'background-color:#F39C12; color:black;';
                        } else {
                            tableData += 'background-color:#00a65a; color:black;';
                        }

                        if (differenceInDays < 0 && value.due_date != null) {
                            tableData += 'background-color:gray; color:black;';
                            differenceInDays = 'Expired';
                        } else if (differenceInDays == 0 && value.due_date != null) {
                            differenceInDays = 'Today';
                        } else {
                            differenceInDays = differenceInDays + ' days';
                        }
                        
                        tableData += '">' + (value.due_date != null ? moment(value.due_date).format('DD/MMM/YYYY') + '<br/>  <b>(' + differenceInDays + ')</b>' : '-') + '</td>';

                        let newValueUploadDate = new Date(value.created_at);
                        let todayUploadDate = new Date();
                        let differenceInDaysUploadDate = Math.round((todayUploadDate - newValueUploadDate) / (1000 * 60 * 60 * 24));

                        if (differenceInDaysUploadDate == 0) {
                            differenceInDaysUploadDate = 'Today';
                        } else {
                            differenceInDaysUploadDate = differenceInDaysUploadDate + ' days ago';
                        }
                        
                        tableData += '<td style="text-align:right;">' + moment(value.created_at).format('DD/MMM/YYYY') + '<br/> <b>(' + differenceInDaysUploadDate + ')</b>' + '</td>';

                        // tableData += '<td style="text-align:right;">' + value.created_at + '<br/> <b>(' +  +')</b>': + '</td>';
                        tableData += '<td class="buttonActionCustom">';
                        tableData += '<button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#view-attachment-modal" onclick="viewAttachment(\'' + value.id + '\',\'' + value.fiscal_year + '\',\'' + value.period + '\',\'' + value.file_category + '\',\'' + value.file_name + '\',\'' + value.file_pic + '\',\'' + value.letter_no + '\',\'' + value.letter_date + '\',\'' + value.received_date + '\',\'' + value.remark + '\',\'' + value.due_date + '\',\'' + value.created_at + '\',\'' + value.docs_name_origin + '\',\'' + value.docs_name + '\',\'' + value.file_url + '\')"><i class="fa fa-search"></i>&nbsp;Detail</button>';
                        // tableData += '<button class="btn btn-primary btn-sm" onclick="downloadFile(\'' + value.file_url + '\')"><i class="fa fa-download"></i></button>';
                        tableData += '<button class="btn btn-primary btn-sm" onclick="sendEmailModal(\'' + value.id + '\')" data-toggle="modal" data-target="#send-email-modal"><i class="fa fa-envelope"></i> Send Email</button>';
                        tableData += '<button class="btn btn-danger btn-sm" onclick="deleteFile(\'' + value.id + '\')"><i class="fa fa-trash"></i> Delete</button></td>';
                        tableData += '</tr>';
                        index++;
                    });

                    safety = result.safety;
                    $('#bodyTableMaster').append(tableData);

                    // $('#tableMaster tfoot th').each(function() {
                    //     var title = $(this).text();
                    //     $(this).html('<input style="text-align: Left; width:100%; color:black;" type="text" placeholder="Search ' + title + '" size="20"/>');
                    // });

                    $('#tableMaster tfoot th').each(function(index) {
                        if (index == 0) {
                            var title = $(this).text();
                            $(this).html(' ');
                        }
                        else if (index == 1) {
                            var title = $(this).text();
                            $(this).html('<select class="tfootSearch"><option value="">All</option>@foreach ($fiscal_year as $fy)<option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>@endforeach</select>');
                        }
                        else if (index == 3) {
                            var title = $(this).text();
                            $(this).html('<select class="tfootSearch" style="width:100%;"><option value="">All</option>@foreach ($categories as $category)<option value="{{ $category->category_name }}">{{ $category->category_name }}</option>@endforeach</select>');
                        }                        
                        else if (index == 12) {
                            $(this).html(' ');
                        } 
                        else {
                            var title = $(this).text();
                            $(this).html('<input class="tfootSearch" style="text-align: center; font-size:12px;" type="text" placeholder="Search ' + title + '" />');
                        }
                    });

                    var table = $('#tableMaster').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, 50, -1],
                            ['10 rows', '25 rows', '50 rows', 'Show all']
                        ],
                        'buttons': {
                            buttons: [{
                                    extend: 'pageLength',
                                    className: 'btn btn-default',
                                    tabIndex: -1
                                },
                                {
                                    extend: 'copy',
                                    className: 'btn btn-success',
                                    text: '<i class="fa fa-copy"></i> Copy',
                                    exportOptions: {
                                        columns: ':not(.notexport)'
                                    }
                                },
                                {
                                    extend: 'excel',
                                    className: 'btn btn-info',
                                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                                    exportOptions: {
                                        columns: ':not(.notexport)'
                                    }
                                },
                                {
                                    extend: 'print',
                                    className: 'btn btn-warning',
                                    text: '<i class="fa fa-print"></i> Print',
                                    exportOptions: {
                                        columns: ':not(.notexport)'
                                    }
                                }
                            ]
                        },
                        'paging': true,
                        'lengthChange': true,
                        'pageLength': 10,
                        'searching': true,
                        'ordering': true,
                        'order': [],
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                    table.columns().every(function() {
                        var that = this;

                        $('input', this.footer()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });

                        $('select', this.footer()).on('change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#tableMaster tfoot tr').appendTo('#tableMaster thead');


                    $('#loading').hide();
                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function fillCategories() {
            $('#loading').show();
            var data = {}

            $.get('{{ url('fetch/filemanager/files/getCategory') }}', data, function(result, status, xhr) {
                if (result.status) {

                    // Side Categories
                    $("#bodyCategories").html("");
                    var categoriesData = "";
                    var sub_category_array = result.data_sub;

                    categoriesData += '<li><span class="catIndex" style="cursor:pointer;" onclick="requestCategory(\'All\')">';
                    categoriesData += '<i class="fa fa-folder"></i> All </span><small id="total_document_by_category" style="color: #33333366"></small></li>';

                    $.each(result.data, function(key, value) {
                        categoriesData += '<li><span class="catIndex" style="cursor:pointer;" onclick="requestCategory(\'' + value.category_name + '\')"';
                        var subCategoryData = "";
                        $.each(sub_category_array, function(key, value_sub) {
                            if (value_sub.parent_id === value.id) {
                                subCategoryData += '<li><span class="catIndex" style="cursor:pointer;" onclick="requestCategory(\'' + value_sub.category_name + '\')">';
                                subCategoryData += '<i class="fa fa-folder"></i> ' + value_sub.category_name + ' </span></li>';
                            }
                        });

                        if (subCategoryData) {
                            categoriesData += 'data-toggle="collapse" data-target="#collapse_' + value.id + '">';
                            categoriesData += '<i class="fa fa-folder"></i> ' + value.category_name + ' </span>';
                            categoriesData += '<div class="collapse collapse_children" id="collapse_' + value.id + '">';
                            categoriesData += '<ul style="background-color:#f3f3f3; border-radius:4px; list-style:none; padding-left:4px; font-size:12px; white-space:nowrap;overflow-x:hidden;">' + subCategoryData + '</ul>';
                            categoriesData += '</div>';
                        } else {
                            categoriesData += '><i class="fa fa-folder"></i> ' + value.category_name + ' </span>';
                        }
                        categoriesData += '</li>';
                    });

                    $("#bodyCategories").html(categoriesData);

                    // modal add file Category
                    $('add_file_category').html("");
                    var modalAddCategory = "";
                    var index = 1;
                    modalAddCategory += '<option value="0">Select Category</option>';
                    $.each(result.data_edit, function(key, value) {
                        modalAddCategory += '<option value="' + value.category_name + '">' + value.category_name + '</option>';
                    });
                    $("#add_file_category").append(modalAddCategory);

                    // modal edit file Category
                    $('edit_file_category').html("");
                    var modalEditCategory = "";
                    var index = 1;
                    modalEditCategory += '<option value="0">Select Category</option>';
                    $.each(result.data_edit, function(key, value) {
                        modalEditCategory += '<option value="' + value.category_name + '">' + value.category_name + '</option>';
                    });

                    $('edit_file_category').html("");
                    var modalAddCategory = "";
                    var index = 1;
                    modalAddCategory += '<option value="0">Select Category</option>';
                    $.each(result.data_edit, function(key, value) {
                        modalAddCategory += '<option value="' + value.category_name + '">' + value.category_name + '</option>';
                    });
                    $("#edit_file_category").append(modalAddCategory);

                    // modal edit category
                    $('#bodyTableCategory').html("");
                    var tableData = "";
                    var index = 1;

                    $.each(result.data_edit, function(key, value) {
                        tableData += '<tr>';
                        tableData += '<td>' + index + '</td>';
                        tableData += '<td>' + value.category_name + '</td>';
                        tableData += '<td>' + value.category_class + '</td>';
                        tableData += '<td>';
                        tableData += '<select class="form-control select2" id="sub_category_' + value.id + '" onchange="updateSubCategory(\'' + value.id + '\')">';
                        tableData += '<option value="0">-- Select Sub-Category --</option>';
                        $.each(result.data, function(key, value2) {
                            if (value2.category_class == value.category_class) {
                                tableData += '<option value="' + value2.id + '" ' + (value2.id == value.parent_id ? 'selected' : '') + '>' + value2.category_name + '</option>';
                            }
                        });
                        tableData += '</select>';
                        tableData += '</td>';
                        tableData += '<td>';
                        tableData += '<button class="btn btn-danger btn-sm" onclick="deleteCategory(\'' + value.id + '\')"><i class="fa fa-trash"></i></button>';
                        tableData += '</td>';
                        tableData += '</tr>';
                        index++;
                    });
                    $("#bodyTableCategory").append(tableData);

                    // add_parent_id
                    $('#add_parent_id').html("");
                    var addParentId = "";
                    var index = 1;
                    addParentId += '<option value="0">-- Select Parent Category --</option>';
                    $.each(result.data, function(key, value) {
                        addParentId += '<option value="' + value.id + '">' + value.category_name + '</option>';
                    });
                    $("#add_parent_id").append(addParentId);


                    $(".catIndex").click(function() {
                        $(".catIndex").removeClass("catActive");
                        $(this).addClass("catActive");
                    });

                    fetchChartData();
                    $('#loading').hide();
                } else {
                    $('#loading').hide();
                    alert('Attempt to retrieve data failed');
                }
            });
        }

        function fetchChartData() {
            $('#loading').show();

            $.get('{{ url('fetch/filemanager/files/chart') }}', function(result, status, xhr) {
                if (result.status) {
                    var chartData = [];
                    var chartLabel = [];
                    var chartColor = [];
                    var chartHoverColor = [];
                    var chartHoverBorderColor = [];
                    var categories = [];

                    $.each(result.fiscal_year, function(key, value) {
                        categories.push(value.fiscal_year);
                    });

                    var series = [];
                    for (let i = 0; i < result.file_categories.length; i++) {
                        var data = [];
                        for (let j = 0; j < categories.length; j++) {
                            var isFound = false;
                            for (let k = 0; k < result.total_docs.length; k++) {
                                if (result.file_categories[i].category_name == result.total_docs[k].file_category &&
                                    categories[j] == result.total_docs[k].fiscal_year) {
                                    data.push(parseInt(result.total_docs[k].total));
                                    isFound = true;
                                    break;
                                }
                            }
                            if (!isFound) {
                                data.push(null);
                            }
                        }

                        series.push({
                            name: result.file_categories[i].category_name,
                            data: data,
                        });
                    }

                    Highcharts.chart('container-highchart', {
                        chart: {
                            type: 'column',
                            backgroundColor: '#f3f3f3',
                            color: 'white',
                            borderRadius: '5px',
                        },
                        title: {
                            text: '',
                            align: 'center'
                        },
                        xAxis: {
                            categories: categories
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: ''
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    color: (
                                        Highcharts.defaultOptions.title.style &&
                                        Highcharts.defaultOptions.title.style.color
                                    ) || 'white',
                                    textOutline: 'none'
                                }
                            }
                        },
                        legend: {
                            enabled: true,
                            layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'top',
                        },
                        tooltip: {
                            headerFormat: '<b>{point.x}</b><br/>',
                            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 0,
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: 0,
                                        color: 'white'
                                    }
                                }
                            },
                            column: {
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: true
                                },
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchFilter(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: series,
                    });

                    $('#statusDueDate-1').html("<span class='label label-success'>0</span>")
                    $('#statusDueDate-2').html("<span class='label label-warning'>0</span>")
                    $('#statusDueDate-3').html("<span class='label label-danger'>0</span>")

                    $.each(result.due_date, function(key, value) {
                        if (value.time_difference == "More than 30 days") {
                            $('#statusDueDate-1').html("<button href='#' onclick='dueDateFilter(\"" + value.time_difference + "\")' class='label label-success btn'>" + value.total + "</button>")
                        } else if (value.time_difference == "Less than 30 days") {
                            $('#statusDueDate-2').html("<button href='#' onclick='dueDateFilter(\"" + value.time_difference + "\")' class='label label-warning btn'>" + value.total + "</button>")
                        } else if (value.time_difference == "Less than 15 days") {
                            $('#statusDueDate-3').html("<button href='#' onclick='dueDateFilter(\"" + value.time_difference + "\")' class='label label-danger btn'>" + value.total + "</button>")
                        }
                    });

                    $('#loading').hide();
                } else {
                    $('#loading').hide();
                    alert('Attempt to retrieve data failed');
                }
            });

        }

        function dueDateFilter(time_difference) {
            $('#hidden_due_date').val("");
            $('#hidden_due_date').val(time_difference);
            fillList();
        }


        function uploadFile() {
            $('#loading').show();

            if ($('#add_file_category').val() == '') {
                openErrorGritter('Error!', 'Kategori Harus Diisi');
                $('#loading').hide();
                return false;
            }
            if ($('#add_file_name').val() == '') {
                openErrorGritter('Error!', 'Nama File Harus Diisi');
                $('#loading').hide();
                return false;
            }
            if ($('#add_pic').val() == '') {
                openErrorGritter('Error!', 'Company Name Harus Diisi');
                $('#loading').hide();
                return false;
            }
            if ($('#add_fy').val() == '') {
                openErrorGritter('Error!', 'Fiscal Year Harus Diisi');
                $('#loading').hide();
                return false;
            }
            if ($('#file-upload').val() == '') {
                openErrorGritter('Error!', 'Attachment Harus Diisi');
                $('#loading').hide();
                return false;
            }

            var formData = new FormData();

            var att_count = 0;
            for (var i = 0; i < $('#file-upload').prop('files').length; i++) {
                formData.append('file_upload_' + i, $('#file-upload').prop('files')[i]);
                att_count++;
            }

            formData.append('file_category', $('#add_file_category').val());
            formData.append('file_name', $('#add_file_name').val());
            formData.append('file_pic', $('#add_pic').val());
            formData.append('fiscal_year', $('#add_fy').val());
            formData.append('period', $('#add_period').val());
            formData.append('letter_no', $('#add_letterNo').val());
            formData.append('due_date', $('#add_dueDate').val());
            formData.append('remark', $('#add_remark').val());
            formData.append('att_count', att_count);

            $.ajax({
                method: "POST",
                dataType: 'JSON',
                url: "{{ url('index/filemanager/files/upload') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    if (result.status) {
                        openSuccessGritter('Success!', 'Success Add Data');
                        $('#loading').hide();
                        $('#add-modal').modal('hide');
                        fillList();
                        cancelAll();
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', result.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading').hide();
                    openErrorGritter('Error!', 'Could not save the record: ' + error);
                }
            });
        }

        function cancelAll() {
            $('#file_category').val('').trigger('change');
            $('#add_category').val('');
            $('#add_fy').val('').trigger('change');
            $('#file-upload').val('');
            $('.jLabel').html('');
            $('.jLabel').append('<i class="fa fa-upload"></i> Choose File');
            $('#add_file_name').val('');
            $('#add_file_category').val('').trigger('change');
            $('#add_pic').val('').trigger('change');
            $('#add_period').val('');
            $('#add_category_name').val('');
            $('#add_category_class').val('');
            $('#add_parent_id').val('');
            $('#add_letterNo').val('');
            $('#add_dueDate').val('');
            $('#add_remark').val('');
            $('#edit_file_name').val('');
            $('#edit_pic').val('').trigger('change');
            $('#edit_period').val('');
            $('#edit_category_name').val('');
            $('#edit_category_class').val('');
            $('#edit_parent_id').val('');
            $('#edit_letterNo').val('');
            $('#edit_letterDate').val('');
            $('#edit_dueDate').val('');
            $('#edit_remark').val('');
        }

        function fetchFilter(FY, file_category) {
            $('#loading').show();
            $("#file_category").val(file_category);
            $("#hidden_search_fy").val(FY);
            fillList();
        }

        function searchYearBetween() {
            var year_start = $('#year_start').val();
            var year_end = $('#year_end').val();

            if (year_end == '') {
                var date = new Date();
                var month = (date.getMonth() + 1);
                month = (month < 10) ? '0' + month : month;
                year_end = month + "-" + date.getFullYear();
                $('#year_end').val(year_end);
            }

            if (year_start == '' || year_end == '') {
                openErrorGritter('Error!', 'Year Harus Diisi');
                return false;
            }

            if (year_start > year_end) {
                openErrorGritter('Error!', 'Year Start Tidak Boleh Lebih Besar Dari Year End');
                return false;
            }
            requestCategory('All');
            fillList();
            fetchChartData();
        }

        function clearYear() {
            $('#year_start').val('');
            $('#year_end').val('');
            $('#fiscal_year').val('');
            fillList();
        }

        function deleteData(id) {
            var data = {
                id: id
            }
            $.get('{{ url('index/filemanager/files/delete') }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success!', 'Success Delete Data');
                    fillList();
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function viewAttachment(id, fiscal_year, period, file_category, file_name, file_pic, letter_no, letter_date, received_date , remark, due_date, created_at, docs_name_origin, docs_name, file_url) {
            $("#bodyTableViewAttachment").html("");
            cancelAll();

            $('#edit_id').val(id);
            $('#edit_fy').val(fiscal_year).trigger('change');
            $('#edit_file_category').val(file_category).trigger('change');
            $('#edit_file_name').val(file_name);
            $('#edit_pic').append('<option value="' + file_pic + '"> ' + file_pic + ' <option>');
            $('#edit_pic').val(file_pic).trigger('change');

            $('#edit_period').val(period != 'null' ? period : '');
            $('#edit_letterNo').val(letter_no != 'null' ? letter_no : '');
            $('#edit_letterDate').val(letter_date != 'null' ? letter_date : '');
            $('#edit_receivedDate').val(received_date != 'null' ? received_date : '');
            $('#edit_remark').val(remark != 'null' ? remark : '');
            $('#edit_dueDate').val(due_date != 'null' ? due_date : '');

            let index = 1;
            let docs = docs_name_origin.split('|');
            let url = file_url.split('|');

            var tableData = '';
            docs.forEach(function(value, key) {
                tableData += '<tr>';
                tableData += '<td style="text-align:center !important;">' + index + '</td>';
                tableData += '<td>' + value + '</td>';
                tableData += '<td><div class="btn-group">';
                tableData += '<a href="{{ url('') }}' + url[key] + '" class="btn btn-primary btn-xs" target="_blank"><i class="fa fa-download"></i> Download</a>';
                tableData += '<a class="btn btn-danger btn-xs" onclick="deleteAttachment(\'' + id + '\', \'' + value + '\')"><i class="fa fa-trash"></i> Delete</a>';
                tableData += '</div></td>';
                tableData += '</tr>';
                index++;
            });

            $("#bodyTableViewAttachment").append(tableData);
        }

        function downloadFile(file_url) {
            let url = file_url.split('|');

            if (confirm('Are you sure want to download All this file?')) {
                url.forEach(function(value, key) {
                    window.open('{{ url('') }}' + value, '_blank');
                });
            }
        }

        function addCategory() {
            var data = {
                category_name: $('#add_category_name').val(),
                category_class: $('#add_category_class').val(),
                parent_id: $('#add_parent_id').val(),
            }
            $.post('{{ url('index/filemanager/files/addCategory') }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success!', 'Success Add Data');
                    cancelAll();
                    fillCategories();
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function updateSubCategory(id) {
            if (confirm('Are you sure want to update this category?')) {

                var target = $('#sub_category_' + id).val();
                var data = {
                    id: id,
                    parent_id: target
                }

                $.post('{{ url('index/filemanager/files/updateSubCategory') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success!', 'Success Update Data');
                        fillCategories();
                    } else {
                        openErrorGritter('Error!', result.message);
                    }
                });
            } else {
                fillCategories();
            }
        }

        function deleteCategory(id) {
            if (confirm('Are you sure want to delete this category?')) {
                var data = {
                    id: id
                }
                $.post('{{ url('index/filemanager/files/deleteCategory') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success!', 'Success Delete Data');
                        fillCategories();
                    } else {
                        openErrorGritter('Error!', result.message);
                    }
                });
            }
        }

        function editFileAttachment() {
            var data = {
                id: $('#edit_id').val(),
                fiscal_year: $('#edit_fy').val(),
                file_category: $('#edit_file_category').val(),
                file_name: $('#edit_file_name').val(),
                file_pic: $('#edit_pic').val(),
                period: $('#edit_period').val(),
                letter_no: $('#edit_letterNo').val(),
                letter_date: $('#edit_letterDate').val().replaceAll('/', '-'),
                received_date: $('#edit_receivedDate').val(),
                due_date: $('#edit_dueDate').val(),
                remark: $('#edit_remark').val(),
            }

            $.post('{{ url('index/filemanager/files/editFile') }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success!', 'Success Edit Data');                    
                    fillList();
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function deleteFile(id) {
            if (confirm('Are you sure want to delete this file?')) {
                var data = {
                    id: id
                }
                $.post('{{ url('index/filemanager/files/deleteFile') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success!', 'Success Delete Data');
                        fillList();
                    } else {
                        openErrorGritter('Error!', result.message);
                    }
                });
            }
        }

        function uploadAttachment() {
            $('#loading').show();

            if ($('#file-upload').val() == '') {
                openErrorGritter('Error!', 'Attachment Harus Diisi');
                $('#loading').hide();
                return false;
            }

            var formData = new FormData();

            var att_count = 0;
            for (var i = 0; i < $('#file-upload').prop('files').length; i++) {
                formData.append('file_upload_' + i, $('#file-upload').prop('files')[i]);
                att_count++;
            }

            formData.append('file_id', $('#edit_id').val());
            formData.append('file_category', $('#edit_file_category').val());
            formData.append('file_name', $('#edit_file_name').val());
            formData.append('file_pic', $('#edit_pic').val());
            formData.append('fiscal_year', $('#edit_fy').val());
            formData.append('period', $('#edit_period').val());
            formData.append('letter_no', $('#edit_letterNo').val());
            formData.append('due_date', $('#edit_dueDate').val());
            formData.append('remark', $('#edit_remark').val());
            formData.append('att_count', att_count);

            $.ajax({
                method: "POST",
                dataType: 'JSON',
                url: "{{ url('index/filemanager/files/uploadAttachment') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    if (result.status) {
                        openSuccessGritter('Success!', 'Success Add Data');
                        $('#loading').hide();
                        // $('#add-modal').modal('hide');
                        fillList();
                        cancelAll();
                        // $('#view-attachment-modal').modal('hide');
                        $('#loading').hide();
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', result.message);
                    }
                }
            });
        }

        function deleteAttachment(id, docs) {
            if (confirm('Are you sure want to delete this attachment?')) {
                var data = {
                    id: id,
                    docs_name_origin: docs
                }
                $.post('{{ url('index/filemanager/files/deleteAttachment') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success!', 'Success Delete Data');
                        cancelAll();
                        fillList();
                        fetchChartData();
                    } else {
                        openErrorGritter('Error!', result.message);
                    }
                });
            }
        }

        function DueDateControll(status) {
            var data = {
                id: $('#edit_id').val(),
                due_date: $('#edit_dueDate').val(),
                status: status,
            }

            $.post('{{ url('index/filemanager/files/dueDateControll') }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success!', 'Success Edit Data');
                    cancelAll();
                    fillList();
                    fetchChartData();
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function sendEmailModal(file_id) {                                    

            // $file_id = $('#edit_id').val();
            $file_id = file_id;

            
            $('#email_file_id').val($file_id);

            var data = {
                file_id: $file_id
            }

            $.get('{{ url('fetch/filemanager/files/viewAttachment') }}', data, function(result, status, xhr) {
                if (result.status) {
                    let index = 1;                                        
                    var docs = result.data;

                    var tableData = '';
                    docs.forEach(function(value, key) {
                        tableData += '<tr>';
                        tableData += '<td style="text-align:center !important;">' + index + '</td>';
                        tableData += '<td>' + value.docs_name_origin + '</td>';                        
                        tableData += '</tr>';
                        index++;
                    });

                    $('#bodyTableEmailAttachment').html(tableData);                    
                    // $('#send-email-modal').modal('show');
                    
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });            
        }

        // function SendModalBacktoAttachment() {
        //     $('#send-email-modal').modal('hide');
        //     // $('#view-attachment-modal').modal('show');
        // }

        function dcsSendEmail() {
            if (confirm('Apakah anda yakin ingin mengirim email?')) {
                var recipientLabel = document.querySelectorAll('.recipient-label');
                var recipient = [];
                for (var i = 0; i < recipientLabel.length; i++) {
                    recipient.push(recipientLabel[i].textContent.trim());
                }
                
                var id = $('#email_file_id').val();
                var email_subject = $('#subject-email').val();
                var email_body = $('#body-email').val();
                            
                var data = {
                    file_id: id,
                    recipient: recipient,
                    email_subject: email_subject,
                    email_body: email_body
                }            
    
                $.post('{{ url('send/filemanager/files/sendEmail') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success!', 'Success Send Email');                                        
                    } else {
                        openErrorGritter('Error!', result.message);
                    }
                });
            }
        }


        // OPTIMIZE JS FOR MODAL SEND EMAIL

        const recipientField = document.querySelector('.recipient-field');
        const recipientLabels = recipientField.querySelector('.recipient-labels');
        const recipientInput = recipientField.querySelector('#recipient-input');

        recipientInput.addEventListener('keydown', event => {
            if (event.key === 'Tab' || event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                const email = recipientInput.value.trim();
                if (email) {
                    addRecipientLabel(email);
                    recipientInput.value = '';
                }
            } else if (event.key === 'Backspace' && recipientInput.value === '') {
                removeLastRecipientLabel();
            }
        });

        recipientInput.addEventListener('paste', event => {
            event.preventDefault();
            const pastedText = event.clipboardData.getData('text/plain');
            const emails = pastedText.split(';').map(email => email.trim());
            emails.forEach(addRecipientLabel);
        });

        function addRecipientLabel(email) {
            const label = document.createElement('div');
            label.classList.add('recipient-label', 'btn', 'btn-xs', 'btn-primary');
            label.innerHTML = `<span>${email}</span><button type="button" class="remove-recipient btn btn-xs btn-danger"><i class="fa fa-times"></i></button>`;
            recipientLabels.appendChild(label);
            label.querySelector('.remove-recipient').addEventListener('click', () => {
                recipientLabels.removeChild(label);
            });
        }

        function removeLastRecipientLabel() {
            const labels = recipientLabels.querySelectorAll('.recipient-label');
            const lastLabel = labels[labels.length - 1];
            if (lastLabel) {
                recipientLabels.removeChild(lastLabel);
            }
        }
    </script>
@endsection
