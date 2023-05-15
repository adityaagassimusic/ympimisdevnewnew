@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<style type="text/css">
    tbody>tr>td {
        vertical-align: top;
        padding: 3px;
    }

    hr {
        margin: 5px 0px 5px 0px;
    }

    a {
        color: black;
        text-decoration: underline;
    }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
    <div class="row">
        <div class="col-xs-12" style="text-align: center;margin-bottom: 10px;">
            <h3 class="box-title"
            style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #3f51b5;padding: 10px">
            Smart TPM (Total Productive Maintenance)<span class="text-purple"></span></h3>
        </div>

        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table style="width: 100%; border-collapse: separate;">
                        <tr>
                            <td style="background-color: #fcc58d">
                                <hr>
                                <div class="box-title text-purple" style='font-weight : bold; font-size : 1.2vw'>Logbook
                                    Facility <br> 設備台帳</div>
                                    <hr>
                                    <ul>
                                        <li>
                                            <a href="#">Log Production Machine</a>
                                        </li>
                                        <li><a href="#" style="color: red">E-Maintenance Standard</a><br></li>
                                    </ul>
                                </td>
                                <td style="background-color: #cccccc">
                                    <hr>
                                    <div class="box-title text-purple" style='font-weight : bold; font-size : 1.2vw'>Planned Maintenance - TBM (Time Base Maintenance) <br> 計画保全</div>
                                    <hr>
                                    <ul>
                                        <li><a href="#" style="color: red">Planned Maintenance Schedule</a><br></li>
                                        <li><a href="{{ url('index/maintenance/planned/form') }}" style="color: red">Planned
                                        Maintenance Form</a><br></li>
                                        <li>Utility
                                            <ul>
                                                <li><a href="{{ secure_url('index/maintenance/aparCheck') }}">APAR Check</a>
                                                </li>
                                                <li><a href="{{ url('index/maintenance/apar') }}">APAR Check Schedule</a>
                                                </li>
                                                <li><a href="{{ secure_url('index/maintenance/apar/expire') }}">APAR Expire
                                                List</a></li>
                                                <li><a href="{{ secure_url('index/maintenance/apar/ng_list') }}">APAR
                                                Negative Finding</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="{{ url('index/maintenance/pm/finding') }}">Maintenance Finding</a><br>
                                        </li>
                                        <li><a href="{{ url('index/maintenance/patrol/building') }}">Patrol Building</a>
                                        </li>
                                        <li><a href="{{ url('index/maintenance/tbm/maintenance-ut') }}">TBM Utility</a>
                                        </li>
                                        <li><a href="{{ url('index/maintenance/tbm/maintenance-mp') }}">TBM Mesin
                                        Produksi</a>
                                        <li><a href="{{ url('index/maintenance/tbm/maintenance-vendor-ut') }}" style="color: red">TBM Vendor Utility</a>
                                        </li>
                                        <li><a href="{{ url('index/maintenance/tbm/maintenance-vendor-mp') }}" style="color: red">TBM Vendor Mesin
                                        Produksi</a>
                                    </li>
                                </ul>
                            </td>
                            <td style="background-color: #c3fca4">
                                <hr>
                                <div class="box-title text-purple" style='font-weight : bold; font-size : 1.2vw'>
                                    Breakdown Maintenance（BM） <br> 事後保全</div>
                                    <hr>
                                    <ul>
                                        <li><a href="{{ url('index/maintenance/machine_report/report') }}">Maintenance
                                        Trouble report</a><br></li>
                                        <li><a href="{{ url('index/maintenance/machine/log') }}">Machine History</a><br>
                                        </li>
                                        <li>Maintenance SPK (Surat Perintah Kerja)
                                            <ul>
                                                <li><a href="{{ url('index/maintenance/list/user') }}">Create SPK</a></li>
                                                <li><a href="{{ url('index/maintenance/list_spk') }}">SPK List</a></li>
                                                <li><a href="{{ secure_url('index/maintenance/spk') }}">SPK Execution</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><a href="#" style="color: red">E-Troubleshoting</a><br></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color: #fcf2a4">
                                    <hr>
                                    <div class="box-title text-purple" style='font-weight : bold; font-size : 1.2vw'>Support
                                        Disc <br> サポートディスク</div>
                                        <hr>
                                        <ul>
                                            <li><a href="{{ url('index/maintenance/operator') }}">Sign to Area</a><br></li>
                                            <li><a href="{{ url('index/skill_map/maintenance-mp') }}">Skill Map MP (Mesin Produksi)</a><br></li>
                                            <li><a href="{{ url('index/skill_map/maintenance-ut') }}">Skill Map UT (Utility)</a><br></li>
                                            <li><a href="#" style="color: red">E-Project</a><br></li>
                                            <li><a href="#" style="color: red">E-Jishu Hozen</a><br></li>
                                        </ul>
                                    </td>
                                    <td style="background-color: #fbc2ff">
                                        <hr>
                                        <div class="box-title text-purple" style='font-weight : bold; font-size : 1.2vw'>Spare
                                            part Control <br> 予備部品管理</div>
                                            <hr>
                                            <ul>
                                                <li><a href="{{ url('index/maintenance/inven/list') }}">Spare part</a><br></li>
                                                <li><a href="{{ url('index/maintenance/machine/part_list') }}">Machine Spare
                                                list</a><br></li>
                                                <li><a href="{{ url('index/maintenance/machine/part_graph') }}">Machine Spare
                                                Cost</a><br></li>
                                            </ul>
                                        </td>
                                        <td style="background-color: #b3b4fc">
                                            <hr>
                                            <div class="box-title text-purple" style='font-weight : bold; font-size : 1.2vw'>SMART CBM (Condition Base Maintenance)<br> 予知保全</div>
                                            <hr>
                                            <ul>
                                                <!-- <li><a href="{{ url('index/ph/data') }}">pH Sensor Monitoring</a><br></li> -->
                                                <li><a href="{{ url('index/maintenance/tpm/temperature') }}">Temp. Chiller
                                                Storage</a><br></li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #fcf6c0">
                                            <hr>
                                            <div class="box-title text-purple" style='font-weight : bold; font-size : 1.2vw'>
                                                Dashboard Maintenance Result <br> メンテ活動のダシュボード</div>
                                                <hr>
                                                <ul>
                                                    <li><a href="http://10.109.52.7/zed/trouble/map">Overall Equipment Efficiency
                                                    (OEE)</a><br></li>
                                                    <li><a href="{{ url('index/maintenance/operator/position') }}">Maintenance Operator
                                                    Location</a><br></li>
                                                    <li>SPK Monitoring
                                                        <ul>
                                                            <li><a href="{{ url('index/maintenance/spk/grafik') }}">SPK Monitoring</a>
                                                            </li>
                                                            <li><a href="{{ url('index/maintenance/spk/workload') }}">SPK Workload</a>
                                                            </li>
                                                            <li><a href="{{ url('index/maintenance/operator/workload') }}">SPK Workload
                                                            (in time)</a></li>
                                                        </ul>
                                                    </li>
                                                    <li><span style="color: red">Mean Time to Repair (MTTR) & Mean Time Between Failures (MTBF)</span>
                                                        <ul>
                                                            <li><a href="{{ url('index/maintenance/machine_report/list') }}"><span style="color: red;">Data</span></a><br></li>
                                                            <li><a href="{{ url('index/maintenance/machine_report/graph') }}"><span style="color: red;">Monitoring Graph</span></a><br></li>
                                                        </ul>
                                                    </li>
                                                    <li><a href="{{ url('index/maintenance/pm/monitoring') }}"><span style="color: red;">Planned
                                                    Monitoring</span></a><br></li>
                                                    <li><a href="{{ url('maintenance/Planned_2022.xlsx') }}" style="color: red;">Result Planned
                                                    Monthly</a><br></li>
                                                    <li><a href="{{ url('index/maintenance/pm/trendline') }}" style="color: red;">Planned Monitoring
                                                    Graph</a><br></li>
                                                    <li><a href="{{ url('index/audit_patrol_monitoring/patrol_bangunan') }}">Monitoring
                                                    patrol Building</a><br></li>
                                                </ul>
                                            </td>
                                            <td style="background-color: ##fcf8d2">
                                                <hr>
                                                <div class="box-title text-purple" style='font-weight : bold; font-size : 1.2vw'>Smart
                                                    control <br> スマート 管理</div>
                                                    <hr>
                                                    <ul>
                                                        <li><a href="{{ url('index/maintenance/tpm/pressure') }}">Machine Utility &
                                                        Pressure Control</a><br></li>
                                                        <li><a href="{{ url('index/temperature/room_temperature') }}">Room
                                                        temperature Map</a><br></li>
                                                        <li><a href="{{ url('index/temperature/log') }}">Room
                                                        temperature Monitoring</a><br></li>
                                                        <li><a href="{{ url('machinery_monitoring?mesin=machining%2Cpress%2Cinjeksi%2Csenban%2Czpro') }}">Machinery Monitoring</a><br></li>
                                                        <li><a href="{{ url('machinery_stop') }}">Machine Trouble Status</a><br></li>
                                                    </ul>
                                                </td>
                                                <td style="background-color: #d2dafc">
                                                    <hr>
                                                    <div class="box-title text-purple" style='font-weight : bold; font-size : 1.2vw'>Energy
                                                        saving <br> 省エネ</div>
                                                        <hr>
                                                    <ul>
                                                        <li>Electricity
                                                            <ul>
                                                                <li>
                                                                    <a href="{{ url('index/maintenance/electricity') }}">Electricity</a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ url('index/maintenance/electricity/daily_consumption_ratio') }}">
                                                                        Electricity Consumption
                                                                    </a>
                                                                </li>
                                                                {{-- <li>
                                                                    <a href="{{ url('index/maintenance/electricity/consumption') }}">
                                                                        Electricity Consumption By Area
                                                                    </a>
                                                                </li> --}}
                                                                <li>
                                                                    <a href="{{ url('index/maintenance/electricity/saving_monitor') }}">
                                                                        Electricity Saving Monitor
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ url('index/maintenance/electricity/kaizen_monitor') }}">
                                                                        Electricity Kaizen Monitor
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                    <ul>
                                                        <li>Compressor
                                                            <ul>
                                                                <li><a href="{{ url('index/maintenance/compressor/monitoring') }}">Monitoring Audit
                                                                Compressor</a><br></li>
                                                                <li><a href="{{ url('index/maintenance/compressor') }}">Temuan Audit
                                                                Compressor</a><br></li>
                                                            </ul>
                                                        </li>
                                                    </ul>

                                                    <ul>
                                                        <li>Steam
                                                            <ul>
                                                                <li><a href="{{ url('index/maintenance/steam/monitoring') }}" style="color: red;">Monitoring Audit Steam</a><br></li>
                                                                <li><a href="{{ url('index/maintenance/steam') }}" style="color: red;">Temuan Audit
                                                                Steam</a><br></li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                    <ul>
                                                        <li>Water
                                                            <ul>
                                                                <li>
                                                                    <a href="{{ url('index/maintenance/domestic_pump') }}">Domestic Pump
                                                                    Consumtion</a>
                                                                </li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    @endsection
                    @section('scripts')
                    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
                    <script>
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        jQuery(document).ready(function() {
                            $('body').toggleClass("sidebar-collapse");
                        });

                        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

                        function openSuccessGritter(title, message) {
                            jQuery.gritter.add({
                                title: title,
                                text: message,
                                class_name: 'growl-success',
                                image: '{{ url('images/image-screen.png') }}',
                                sticky: false,
                                time: '3000'
                            });
                        }

                        function openErrorGritter(title, message) {
                            jQuery.gritter.add({
                                title: title,
                                text: message,
                                class_name: 'growl-danger',
                                image: '{{ url('images/image-stop.png') }}',
                                sticky: false,
                                time: '3000'
                            });
                        }
                    </script>
                    @endsection
