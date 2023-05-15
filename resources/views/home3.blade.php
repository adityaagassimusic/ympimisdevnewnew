@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
    thead>tr>th{
        text-align:center;
    }
    tbody>tr>td{
        text-align:center;
    }
    tfoot>tr>th{
        text-align:center;
    }
    td:hover {
        overflow: visible;
    }
    table.table-bordered{
        border:1px solid black;
        margin-bottom: 5px;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        margin:0;
        padding:0;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(180,180,180);
        font-size: 0.8vw;
        background-color: rgb(240,240,240);
        padding-top: 2px;
        padding-bottom: 2px;
        padding-left: 3px;
        padding-right: 3px;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
    }
    #loading, #error { display: none; }
    .marquee {
        width: 100%;
        overflow: hidden;
        margin: 0px;
        padding: 0px;
        text-align: center;
        height: 35px;
    }
    .td_hover:hover{
        background-color: #f0f0f0;
    }
</style>
@stop
@section('header')
<section class="content-header" style="padding: 0; margin:0;">
    <div class="marquee">
        <span style="font-size: 16px;" class="text-purple"><span style="font-size:22px;"><b>M</b></span>anufactur<span style="font-size:23px;"><b>i</b></span>ng <span style="font-size:22px;"><b>R</b></span>ealtime <span style="font-size:22px;"><b>A</b></span>cquisition of <span style="font-size:22px;"><b>I</b></span>nformation</span>
        <br>
        <b><span style="font-size: 20px;" class="text-purple">
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="24px">
            製 造 の リ ア ル タ イ ム 情 報
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="24px">
        </span></b>
    </div>
</section>
@endsection

@section('content')

<section class="content" style="padding-top: 0;">
    <div class="row">
        <?php $tahun = date('Y'); ?>
        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;padding-bottom: 10px;">
            <div class="box box-solid" style="border-radius: 10px;background-color: #f6f6fe;margin-bottom:0px;">
                <div class="box-body">
                    <center style="font-weight: bold;padding: 0px;font-size: 20px;background-color: lightskyblue;border-radius: 5px;margin-bottom: 10px;">
                        <span>QUALITY CONTROL</span>
                    </center>
                    <table style="width: 100%">
                        <tr>
                            <td style="padding-left: 0px;padding-right: 5px;width: 5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>IN</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                                <td class="td_hover" style="border: 1px solid #e3e3e3;font-weight: bold;text-align: left;vertical-align: middle;padding-left: 15px;cursor: pointer;">
                                                    <a href="{{ url('index/qa/display/incoming/lot_status') }}" style="color: black;">
                                                        <i class="fa fa-caret-right"></i> Incoming Check <small class="text-purple">(受入検査</small>)
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 5px;width:5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>PROCESS</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                                <td class="td_hover" style="border: 1px solid #e3e3e3;font-weight: bold;text-align: left;vertical-align: middle;padding-left: 15px;cursor: pointer;">
                                                    <a href="{{ url('/index/fg_production_schedule') }}" style="color: black;">
                                                        <i class="fa fa-caret-right"></i> Prod. Schedule Data <small class="text-purple">(生産スケジュールデータ)</small> 
                                                    </a>
                                                </td>
                                                <td class="td_hover" style="border: 1px solid #e3e3e3;font-weight: bold;text-align: left;vertical-align: middle;padding-left: 15px;cursor: pointer;">
                                                    <a href="{{ url('/index/fg_production_schedule') }}" style="color: black;">
                                                        <i class="fa fa-caret-right"></i> Prod. Schedule Data <small class="text-purple">(生産スケジュールデータ)</small> 
                                                    </a>
                                                </td>
                                                <td class="td_hover" style="border: 1px solid #e3e3e3;font-weight: bold;text-align: left;vertical-align: middle;padding-left: 15px;cursor: pointer;">
                                                    <a href="{{ url('/index/fg_production_schedule') }}" style="color: black;">
                                                        <i class="fa fa-caret-right"></i> Prod. Schedule Data <small class="text-purple">(生産スケジュールデータ)</small> 
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 0px;width:5%;">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>OUT</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                                <td class="td_hover" style="border: 1px solid #e3e3e3;font-weight: bold;text-align: left;vertical-align: middle;padding-left: 15px;cursor: pointer;">
                                                    <a href="{{url('index/fg_shipment_schedule')}}" style="color: black;">
                                                        <i class="fa fa-caret-right"></i> Ship. Schedule Data <small class="text-purple">(出荷スケジュールデータ</small>)
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;padding-bottom: 10px;">
            <div class="box box-solid" style="border-radius: 10px;background-color: #f6f6fe;margin-bottom:0px;">
                <div class="box-body">
                    <center style="font-weight: bold;padding: 0px;font-size: 20px;background-color: lightgreen;border-radius: 5px;margin-bottom: 10px;">
                        <span>PRODUCTION AND SALES CONTROL</span>
                    </center>
                    <table style="width: 100%">
                        <tr>
                            <td style="padding-left: 0px;padding-right: 5px;width: 5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>PRODUCTION RESULT</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                                <!-- <td class="td_hover" style="border: 1px solid #e3e3e3;font-weight: bold;text-align: left;vertical-align: middle;padding-left: 15px;cursor: pointer;">
                                                    <a href="{{ url('index/qa/display/incoming/lot_status') }}" style="color: black;">
                                                        <i class="fa fa-caret-right"></i> Incoming Check <small class="text-purple">(受入検査</small>)
                                                    </a>
                                                </td> -->
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 5px;width:5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>SERIAL NO. CONTROL</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                                
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 5px;width:5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>EFFICIENCY</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 5px;width:5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>EXTRA ORDER</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 0px;width:5%;">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>SALES</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;padding-bottom: 10px;">
            <div class="box box-solid" style="border-radius: 10px;background-color: #f6f6fe;margin-bottom:0px;">
                <div class="box-body">
                    <center style="font-weight: bold;padding: 0px;font-size: 20px;background-color: lightsalmon;border-radius: 5px;margin-bottom: 10px;">
                        <span>INVENTORY CONTROL</span>
                    </center>
                    <table style="width: 100%">
                        <tr>
                            <td style="padding-left: 0px;padding-right: 5px;width: 5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>RAW MATERIAL</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                                <!-- <td class="td_hover" style="border: 1px solid #e3e3e3;font-weight: bold;text-align: left;vertical-align: middle;padding-left: 15px;cursor: pointer;">
                                                    <a href="{{ url('index/qa/display/incoming/lot_status') }}" style="color: black;">
                                                        <i class="fa fa-caret-right"></i> Incoming Check <small class="text-purple">(受入検査</small>)
                                                    </a>
                                                </td> -->
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 5px;width:5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>WIP</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                                
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 5px;width:5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>FINISHED GOODS</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 0px;width:5%;">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>VARIANCE</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;padding-bottom: 10px;">
            <div class="box box-solid" style="border-radius: 10px;background-color: #f6f6fe;margin-bottom:0px;">
                <div class="box-body">
                    <center style="font-weight: bold;padding: 0px;font-size: 20px;background-color: lightsteelblue;border-radius: 5px;margin-bottom: 10px;">
                        <span>MACHINE CONTROL</span>
                    </center>
                    <table style="width: 100%">
                        <tr>
                            <td style="padding-left: 0px;padding-right: 5px;width: 5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>OEE</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                                <!-- <td class="td_hover" style="border: 1px solid #e3e3e3;font-weight: bold;text-align: left;vertical-align: middle;padding-left: 15px;cursor: pointer;">
                                                    <a href="{{ url('index/qa/display/incoming/lot_status') }}" style="color: black;">
                                                        <i class="fa fa-caret-right"></i> Incoming Check <small class="text-purple">(受入検査</small>)
                                                    </a>
                                                </td> -->
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 5px;width:5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>MTTR</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                                
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 5px;width:5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>MTBF</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 5px;width:5%">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>ENERGY MONITORING SYSTEM</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-left: 5px;padding-right: 0px;width:5%;">
                                <div class="box box-solid" style="border-radius: 5px;margin-bottom:0px;">
                                    <div class="box-body">
                                        <center style="font-weight: bold;padding: 0px;font-size: 20px;">
                                            <span>UTILITY MONITORING</span>
                                        </center>
                                        <hr style="font-size: 10px;padding: 0px;margin: 0px;">
                                        <table style="width: 100%;min-height:40px;">
                                            <tr>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
   </section>

   @stop
   @section('scripts')
   <script src="{{ url("js/jquery.marquee.min.js")}}"></script>
   <script>
    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        $('.marquee').marquee({
            duration: 4000,
            gap: 1,
            delayBeforeStart: 0,
            direction: 'up',
            duplicated: true
        });
    });

</script>
@endsection
