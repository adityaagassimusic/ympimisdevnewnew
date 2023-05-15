<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">

            @foreach (Auth::user()->role->permissions as $perm)
                @php
                    $navs[] = $perm->navigation_code;
                @endphp
            @endforeach

            @php
                print_r('<li class="header">Menu</li>');
                
                if (str_contains(Auth::user()->role_code, 'BUYER')) {
                    if (isset($page) && $page == 'Extra Order') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/extra_order') . '"><i class="fa fa-pencil-square-o"></i> <span>Create Extra Order</span></a>');
                    print_r('</li>');
                } else {
                    if (isset($page) && $page == 'Dashboard') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/home') . '"><i class="fa fa-tv"></i> <span>Dashboard</span></a>');
                    print_r('</li>');
                }
                
                if (in_array('READ_OTHER', $navs)) {
                    if (isset($page) && $page == 'MIS Ticket') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/ticket/monitoring/mis') . '"><i class="fa fa-pencil-square-o"></i> <span>MIS Ticket</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Mirai Approval') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/mirai/approval') . '"><i class="fa fa-pencil-square-o"></i> <span>MIRAI Approval</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Safety Riding') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/safety_riding') . '"><i class="fa fa-pencil-square-o"></i> <span>Safety Riding</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Document Archive') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/document_archive') . '"><i class="fa fa-th-list"></i> <span>Document Archive</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'YMPI Stamp Log') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/YMPI_stamp_log') . '"><i class="fa fa-pencil-square-o"></i> <span>Corporate Stamp Records</span></a>');
                    print_r('</li>');
                
                    if (Auth::user()->role_code != 'JPN' || in_array('READ_GA', $navs)) {
                        if (isset($page) && $page == 'Japanese Food Order') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/ga_control/bento') . '"><i class="fa fa-pencil-square-o"></i> <span>Japanese Food Order (NS)</span></a>');
                        print_r('</li>');
                    }
                }
                
                if (Auth::user()->role_code == 'JPN' || Auth::user()->role_code == 'YEMI' || in_array('READ_GA', $navs)) {
                    if (isset($page) && $page == 'Japanese Food Order Japanese') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/ga_control/bento_japanese/' . date('F Y')) . '"><i class="fa fa-pencil-square-o"></i> <span>Japanese Food Order (JP)</span></a>');
                    print_r('</li>');
                }
                
                // if(isset($page) && $page == 'Order Makan Ramadhan'){
                //     print_r('<li class="active">');
                // }
                // else{
                //     print_r('<li>');
                // }
                // print_r('<a href="'.url('/index/ga_report/order/makan').'"><i class="fa fa-pencil-square-o"></i> <span>Ramadhan Food Order</span></a>');
                // print_r('</li>');
                
                if (in_array('READ_OTHER', $navs)) {
                    if (isset($page) && $page == 'Petty Cash') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/settlement/user') . '"><i class="fa fa-pencil-square-o"></i> <span>Petty Cash Settlement</span></a>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>PR & Investment</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Purchase Requisition') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_requisition') . '"><i class="fa fa-pencil-square-o"></i> <span>Create PR</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Investment') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/investment') . '"><i class="fa fa-pencil-square-o"></i> <span>Create Investment</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase Requisition Control') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_requisition/monitoring') . '"><i class="fa fa-bar-chart"></i> <span>Monitoring PR</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Investment Control') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/investment/control') . '"><i class="fa fa-bar-chart"></i> <span>Monitoring Investment</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Budget') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/budget/info') . '"><i class="fa fa-th-list"></i> <span>Budget Information</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Budget Monthly') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/budget/monthly') . '"><i class="fa fa-th-list"></i> <span>Budget Monthly</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase Item') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/purchase_item') . '"><i class="fa fa-th-list"></i> <span>Master Item</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cek Kedatangan') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/warehouse/cek_kedatangan') . '"><i class="fa fa-th-list"></i> <span>Receive Item</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Sakurentsu-3M-Trial</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Upload Sakurentsu') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/sakurentsu/upload_sakurentsu') . '"><i class="fa fa-pencil-square-o"></i> <span>Upload Sakurentsu</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Sakurentsu Translate List') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/sakurentsu/list_sakurentsu_translate') . '"><i class="fa fa-th-list"></i> <span>Translation List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Sakurentsu List') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/sakurentsu/list_sakurentsu') . '"><i class="fa fa-th-list"></i> <span>Sakurentsu List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == '3M List') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/sakurentsu/list_3m') . '"><i class="fa fa-th-list"></i> <span>3M List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Trial Request') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/trial_request') . '"><i class="fa fa-th-list"></i> <span>Trial Request List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Trial Request Leader') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/trial_request_leader') . '"><i class="fa fa-pencil-square-o"></i> <span>Trial Request Result</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Sakurentsu Monitoring') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/sakurentsu/monitoring/3m') . '"><i class="fa fa-th-list"></i> <span>Monitoring</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Pantry') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/pantry/pesanmenu') . '"><i class="fa fa-pencil-square-o"></i> <span>Pantry Order</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Visitor Confirmation By Manager') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/visitor_confirmation_manager') . '"><i class="fa fa-th-list"></i> <span>Visitor Confirmation</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Resume Approval') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/general/resume_approval/new') . '"><i class="fa fa-th-list"></i> <span>Resume Approval</span></a>');
                    print_r('</li>');
                }
                
                if (in_array('READ_GA', $navs) || in_array('READ_PCH', $navs)) {
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Admin Petty Cash</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Suspend') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/suspense') . '"><i class="fa fa-pencil-square-o"></i> <span>Suspense</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Settlement') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/settlement') . '"><i class="fa fa-pencil-square-o"></i> <span>Settlement</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                }
                
                if (in_array('READ_FIN', $navs) || Auth::user()->role_code == 'M' || Auth::user()->role_code == 'M-HR' || Auth::user()->role_code == 'DGM' || Auth::user()->role_code == 'GM' || Auth::user()->role_code == 'D' || Auth::user()->role_code == 'JPN') {
                    if (isset($page) && $page == 'Budget Report') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/budget/report') . '"><i class="fa fa-th-list"></i> <span>Report Budget Expense - Asset</span></a>');
                    print_r('</li>');
                }
                
                if (in_array('READ_FIN', $navs)) {
                    print_r('<li class="header">Accounting</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Investment - Expense</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Investment') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/investment') . '"><i class="fa fa-pencil-square-o"></i> <span>Request Investment</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Investment Control') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/investment/control') . '"><i class="fa fa-th-list"></i> <span>Investment Control</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Budget</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Budget Information') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/budget/info') . '"><i class="fa fa-th-list"></i> <span>Budget Information</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Budget Report') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/budget/report') . '"><i class="fa fa-th-list"></i> <span>Budget Summary</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Budget Monthly') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/budget/monthly') . '"><i class="fa fa-th-list"></i> <span>Budget Monthly</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Budget Log') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/budget/log') . '"><i class="fa fa-th-list"></i> <span>Budget Log</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Transfer Budget') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/transfer/budget') . '"><i class="fa fa-pencil-square-o"></i> <span>Budget Transfer</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Budget Report') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/budget/report') . '"><i class="fa fa-th-list"></i> <span>Budget Report</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Data Master</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Purchase Item') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/purchase_item') . '"><i class="fa fa-th-list"></i> <span>(Equipment) Master Item</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Supplier') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/supplier') . '"><i class="fa fa-th-list"></i> <span>(Equipment) Supplier</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Bank') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/bank') . '"><i class="fa fa-th-list"></i> <span>(E-Billing) Bank</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'GL account') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/gl_account') . '"><i class="fa fa-th-list"></i> <span>(E-Billing) GL Account</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cost center') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/cost_center') . '"><i class="fa fa-th-list"></i> <span>(E-Billing) Cost center</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Payment Process</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Payment Request') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/payment_request/all') . '"><i class="fa fa-th-list"></i> <span>Payment Request</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'General Payment Request') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/payment_request/general') . '"><i class="fa fa-th-list"></i> <span>Payment Request General</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Payment Request Monitoring') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/payment_request/monitoring') . '"><i class="fa fa-th-list"></i> <span>Payment Request Monitoring</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Check Payment Request') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/check/payment_request') . '"><i class="fa fa-th-list"></i> <span>Check Payment Request</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Jurnal') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/jurnal') . '"><i class="fa fa-th-list"></i> <span>Jurnal</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Maintain List Bank') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/index/list_bank') . '"><i class="fa fa-th-list"></i> <span>Maintain List Bank</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Exchange Rate') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/exchange_rate') . '"><i class="fa fa-th-list"></i> <span>Exchange Rate</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Receive Goods') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/receive_goods') . '"><i class="fa fa-pencil-square-o"></i> <span>Upload Receive</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Upload Transaksi') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/upload_transaksi') . '"><i class="fa fa-pencil-square-o"></i> <span>Upload Transaction (Non-PO)</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Outstanding') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/outstanding_all_equipment') . '"><i class="fa fa-th-list"></i> <span>Outstanding (PR-PO-Investment)</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cek Kedatangan') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/warehouse/cek_kedatangan') . '"><i class="fa fa-th-list"></i> <span>Receive Warehouse</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Report Petty Cash') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/report/petty_cash') . '"><i class="fa fa-th-list"></i> <span>Petty Cash Report</span></a>');
                    print_r('</li>');
                }
                if (in_array('READ_GA', $navs)) {
                    print_r('<li class="header">General Affairs</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Clinic</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Diagnose') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/diagnose') . '"><i class="fa fa-pencil-square-o"></i> <span>Diagnose</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Medicines') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/medicines') . '"><i class="fa fa-th-list"></i> <span>Medicines</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Visit Logs') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/clinic_visit_log') . '"><i class="fa fa-th-list"></i> <span>Visit Logs</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Canteen & Household</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Purchase Requisition Canteen') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/canteen/purchase_requisition') . '"><i class="fa fa-pencil-square-o"></i> <span>Create PR</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase Requisition Control') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/canteen/purchase_requisition/monitoring') . '"><i class="fa fa-bar-chart"></i> <span>PR Monitoring</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Item Canteen') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/canteen/purchase_item') . '"><i class="fa fa-th-list"></i> <span>Master Item</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Receive GA Kantin') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/ga/receive_kantin') . '"><i class="fa fa-pencil-square-o"></i> <span>Receive Canteen</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Receive GA') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/warehouse/receive_ga') . '"><i class="fa fa-pencil-square-o"></i> <span>Receive Household</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cek Kedatangan Kantin') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/ga/cek_kedatangan/kantin') . '"><i class="fa fa-th-list"></i> <span>Receive Report Canteen</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cek Kedatangan GA') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/ga/cek_kedatangan') . '"><i class="fa fa-th-list"></i> <span>Receive Report Household</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cek Kedatangan GA') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/ga/cek_kedatangan') . '"><i class="fa fa-th-list"></i> <span>Receive Report Household</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cancel Item Kantin') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/canteen/cancel_item') . '"><i class="fa fa-th-list"></i> <span>Cancel Item Kantin</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Pantry</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Pantry') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/pantry/pesanmenu') . '"><i class="fa fa-pencil-square-o"></i> <span>Item Order</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Pantry Menu') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/pantry/menu') . '"><i class="fa fa-th-list"></i> <span>Menu List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Pantry Confirmation') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/pantry/confirmation') . '"><i class="fa fa-th-list"></i> <span>Order Confirmation</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'GA Report') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/report/ga_report') . '"><i class="fa fa-th-list"></i> <span>GA Overtime Report</span></a>');
                    print_r('</li>');
                }
                if (in_array('READ_HR', $navs)) {
                    print_r('<li class="header">Human Resources</li>');
                
                    if (isset($page) && $page == 'Smart Recruitment') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/smartrecruitment') . '"><i class="fa fa-th-list"></i> <span>Smart Recruitment</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Resume All HR') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/resume_all_hr') . '"><i class="fa fa-th-list"></i> <span>Report Human Resouce</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'qna') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/qnaHR') . '"><i class="fa fa-th-list"></i> <span>HR Q&A</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Report Attendances & Tsransportations') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/general/report_transportation') . '"><i class="fa fa-th-list"></i> <span>Transport Report</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Report Surat Dokter') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/general/report_surat_dokter') . '"><i class="fa fa-th-list"></i> <span>Surat Dokter Report</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Employee Candidate') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/fetch/calon/karyawan') . '"><i class="fa fa-th-list"></i> <span>Employee Candidate</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Master Employee') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/emp_data') . '"><i class="fa fa-th-list"></i> <span>Employee Data</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Resume End Contract') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/employee_end_contract') . '"><i class="fa fa-th-list"></i> <span>Resume End Contract</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Resume Tunjangan') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/resume/tunjangan/karyawan') . '"><i class="fa fa-th-list"></i> <span>Tunjangan Resume</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Resume Shift Schedule') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/shift_schedule/karyawan') . '"><i class="fa fa-th-list"></i> <span>Shift Schedule Resume</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Overtime Check') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/report/overtime_check') . '"><i class="fa fa-th-list"></i> <span>Overtime Check</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Overtime Control') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/report/overtime_control') . '"><i class="fa fa-th-list"></i> <span>Overtime Report</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'User Document') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/user_document') . '"><i class="fa fa-pencil-square-o"></i> <span>User Document</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Problem Employee') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/karyawan_bermasalah') . '"><i class="fa fa-pencil-square-o"></i> <span>Problem Employee</span></a>');
                    print_r('</li>');
                }
                if (in_array('READ_LOG', $navs) || in_array('READ_PCH', $navs)) {
                    print_r('<li class="header">Logistic</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Receive Equipment</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Receive Warehouse') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/warehouse/receive_equipment') . '"><i class="fa fa-pencil-square-o"></i> <span>Receive Item</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Print Warehouse') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/warehouse/print_equipment') . '"><i class="fa fa-print"></i> <span>Print Label</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Receive Bukti') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/warehouse/cetak_bukti') . '"><i class="fa fa-pencil-square-o"></i> <span>Send Item</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cek Kedatangan') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/warehouse/cek_kedatangan') . '"><i class="fa fa-th-list"></i> <span>Receive Report</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                }
                if (in_array('READ_MTC', $navs)) {
                    print_r('<li class="header">Maintenance</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>SPK</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Maintenance Form') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/maintenance/list/user') . '"><i class="fa fa-pencil-square-o"></i> <span>SPK - Create</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Maintenance List') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/maintenance/list_spk') . '"><i class="fa fa-th-list"></i> <span>SPK - List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'SPK') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . secure_url('/index/maintenance/spk') . '"><i class="fa fa-pencil-square-o"></i> <span>SPK - Execution</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Planned Maintenance</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Planned Maintenance Form') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . secure_url('/index/maintenance/planned/form') . '"><i class="fa fa-pencil-square-o"></i> <span>PM - Check</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Planned Maintenance Data') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/maintenance/planned/master') . '"><i class="fa fa-th-list"></i> <span>PM - Data</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Utility</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'APAR Check') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . secure_url('/index/maintenance/aparCheck') . '"><i class="fa fa-pencil-square-o"></i> <span>APAR - Check</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'APAR Expired') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . secure_url('/index/maintenance/apar/expire') . '"><i class="fa fa-th-list"></i> <span>APAR - Expired</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'APAR NG') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . secure_url('/index/maintenance/apar/ng_list') . '"><i class="fa fa-th-list"></i> <span>APAR - Not Good (NG)</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'APAR order') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . secure_url('/index/maintenance/apar/orderList') . '"><i class="fa fa-pencil-square-o"></i> <span>APAR - Order List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'APAR MAP') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/maintenance/apar/map') . '"><i class="fa fa-tv"></i> <span>APAR - MAP</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'APAR') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/maintenance/aparTool') . '"><i class="fa fa-th-list"></i> <span>Utility List</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Spare Part') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/maintenance/inven/list') . '"><i class="fa fa-th-list"></i> <span>Spare Part</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Machine Logs') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/maintenance/machine/log') . '"><i class="fa fa-th-list"></i> <span>Machine History</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'MP Position') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . secure_url('/index/maintenance/operator') . '"><i class="fa fa-pencil-square-o"></i> <span>Sign to Area</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Skill Map Maintenance UT') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/skill_map', 'maintenance-ut') . '"><i class="fa fa-th-list"></i> <span>Skill Map UT</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Skill Map Maintenance MP') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/skill_map', 'maintenance-mp') . '"><i class="fa fa-th-list"></i> <span>Skill Map MP</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Part Machine') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/maintenance/machine/part_list') . '"><i class="fa fa-th-list"></i> <span>Machine Part List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Electricity') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/maintenance/electricity') . '"><i class="fa fa-th-list"></i> <span>Electricity</span></a>');
                    print_r('</li>');
                }
                if (in_array('READ_MIS', $navs)) {
                    print_r('<li class="header">Management Information System</li>');
                
                    if (isset($page) && $page == 'Code Generator') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/code_generator') . '"><i class="fa fa-th-list"></i> <span>Code Generator</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'User') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/user') . '"><i class="fa fa-th-list"></i> <span>User</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Batch Setting') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/batch_setting') . '"><i class="fa fa-cog"></i> <span>Batch Setting</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'XIBO') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/general/xibo') . '"><i class="fa fa-television"></i> <span>XIBO</span></a>');
                    print_r('</li>');
                
                    // if(isset($page) && $page == 'KOSONG'){
                    //     print_r('<li class="active">');
                    // }
                    // else{
                    //     print_r('<li>');
                    // }
                    // print_r('<a href="'.url('/KOSONG').'"><i class="fa fa-pencil-square-o"></i> <span>KOSONG</span></a>');
                    // print_r('</li>');
                }
                if (in_array('READ_PCH', $navs)) {
                    print_r('<li class="header">Procurement & Purchasing</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Purchase Requisition</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Purchase Requisition') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_requisition') . '"><i class="fa fa-pencil-square-o"></i> <span>Purchase Requisition</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase Requisition Control') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_requisition/monitoringpch') . '"><i class="fa fa-th-list"></i> <span>Purchase Requisition Control</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase Requisition Control') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_requisition/monitoring') . '"><i class="fa fa-bar-chart"></i> <span>Purchase Requisition Control</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Purchase Order</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Purchase Order') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_order') . '"><i class="fa fa-pencil-square-o"></i> <span>Purchase Order (PR)</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase Order Investment') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_order_investment') . '"><i class="fa fa-pencil-square-o"></i> <span>Purchase Order (Investment)</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase Order Monitoring') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_order/monitoring') . '"><i class="fa fa-bar-chart"></i> <span>Purchase Order Monitoring</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Jurnal PO') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_order/jurnal_po') . '"><i class="fa fa-th-list"></i> <span>Purchase Order Journal</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Delivery Control') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_order/delivery_control') . '"><i class="fa fa-th-list"></i> <span>Delivery Control</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Purchase Order Canteen</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Purchase Order Canteen') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/purchase_order_canteen') . '"><i class="fa fa-pencil-square-o"></i> <span>Purchase Order (Canteen)</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase Requisition Control') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/canteen/purchase_requisition/monitoring') . '"><i class="fa fa-pencil-square-o"></i> <span>Purchase Requisition Monitoring</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase Order Canteen Monitoring') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/canteen/purchase_order/monitoring') . '"><i class="fa fa-bar-chart"></i> <span>Purchase Order Monitoring</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Item Canteen') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/canteen/purchase_item') . '"><i class="fa fa-th-list"></i> <span>Canteen Item</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Receive GA Kantin') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/ga/receive_kantin') . '"><i class="fa fa-pencil-square-o"></i> <span>Receive Canteen</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cek Kedatangan Kantin') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/ga/cek_kedatangan/kantin') . '"><i class="fa fa-th-list"></i> <span>Receive Check</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Data Master</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Purchase Item') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/purchase_item') . '"><i class="fa fa-th-list"></i> <span>Purchase Item</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Purchase New Item') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/purchase_new_item') . '"><i class="fa fa-th-list"></i> <span>Check New Item</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Supplier') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/supplier') . '"><i class="fa fa-pencil-square-o"></i> <span>Supplier</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Payment Process</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Receive Material') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/receive_material') . '"><i class="fa fa-th-list"></i> <span>Receive Material</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Receive Non Material') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/receive_non_material') . '"><i class="fa fa-th-list"></i> <span>Receive Equipment</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Tanda Terima') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/invoice/tanda_terima') . '"><i class="fa fa-th-list"></i> <span>Tanda Terima General</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Tanda Terima Equipment') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/tanda_terima/equipment') . '"><i class="fa fa-th-list"></i> <span>Tanda Terima Equipment</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Tanda Terima Material') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/tanda_terima/material') . '"><i class="fa fa-th-list"></i> <span>Tanda Terima Material</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Payment Request') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/payment_request/all') . '"><i class="fa fa-th-list"></i> <span>Payment Request</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Payment Request') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/billing/payment_request/general') . '"><i class="fa fa-th-list"></i> <span>Payment Request General</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Payment Request Monitoring') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/payment_request/monitoring') . '"><i class="fa fa-th-list"></i> <span>Payment Request Monitoring</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Cek Kedatangan') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/warehouse/cek_kedatangan') . '"><i class="fa fa-th-list"></i> <span>Receive Equipment</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Outstanding') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/outstanding_all_equipment') . '"><i class="fa fa-th-list"></i> <span>Outstanding (PR-PO- Investment)</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Catalog Item') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/catalog_item') . '"><i class="fa fa-th-list"></i> <span>Catalog</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Raw Material') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/raw_material_dashboard') . '"><i class="fa fa-th-list"></i> <span>Raw Material</span></a>');
                    print_r('</li>');
                }
                if (in_array('READ_PC', $navs)) {
                    print_r('<li class="header">Production Control</li>');
                
                    if (isset($page) && $page == 'Extra Order') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/extra_order') . '"><i class="fa fa-pencil-square-o"></i> <span>EO - Create</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Material List') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/sakurentsu/list_material') . '"><i class="fa fa-th-list"></i> <span>EO - Material List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Serial Number') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/serial_number') . '"><i class="fa fa-pencil-square-o"></i> <span>JAN-EAN-UPC</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Shipment') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/shipment_menu') . '"><i class="fa fa-th-list"></i> <span>Shipment</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'BOM Multilevel') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/bom_multilevel') . '"><i class="fa fa-pencil-square-o"></i> <span>BOM Multilevel</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'GRGI Data') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material/report/grgi') . '"><i class="fa fa-pencil-square-o"></i> <span>GRGI Data</span></a>');
                    print_r('</li>');
                }
                if (in_array('READ_PE', $navs)) {
                    print_r('<li class="header">Production Engineering</li>');
                
                    if (isset($page) && $page == 'WJO Form') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/workshop/create_wjo') . '"><i class="fa fa-pencil-square-o"></i> <span>WJO - Create</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'WJO List') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/workshop/list_wjo') . '"><i class="fa fa-th-list"></i> <span>WJO - List</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'WJO Execution') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/workshop/wjo') . '"><i class="fa fa-pencil-square-o"></i> <span>WJO - Execution</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'WJO Receipt') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/workshop/receipt') . '"><i class="fa fa-pencil-square-o"></i> <span>WJO - Receipt</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'WJO History') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/workshop/job_history') . '"><i class="fa fa-th-list"></i> <span>Job History</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'WJO Report') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/workshop/perolehan') . '"><i class="fa fa-bar-chart"></i> <span>WJO - Report</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'WJO Flow') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/workshop/flow_master') . '"><i class="fa fa-th-list"></i> <span>WJO - Master Flow</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'WJO PIC') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/workshop/pic_master') . '"><i class="fa fa-th-list"></i> <span>WJO - Master PIC</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'WJO Jig') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/workshop/jig_master') . '"><i class="fa fa-th-list"></i> <span>WJO - Master Jig</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Leader Task Monitoring') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/production_report/index/13') . '"><i class="fa fa-pencil-square-o"></i> <span>Field Report</span></a>');
                    print_r('</li>');
                }
                if (in_array('READ_QA', $navs)) {
                    print_r('<li class="header">Quality Assurance</li>');
                }
                if (in_array('READ_STD', $navs)) {
                    print_r('<li class="header">Standardization</li>');
                }
                
                if (in_array('READ_OTHER', $navs)) {
                    print_r('<li class="header">Production</li>');
                
                    if (isset($page) && $page == 'Operator Loss Time') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/efficiency/operator_loss_time') . '"><i class="fa fa-pencil-square-o"></i> <span>Operator Loss Time Record</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Unusual Material Slip') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/transaction/slip_unusual') . '"><i class="fa fa-print"></i> <span>Print Slip Khusus</span></a>');
                    print_r('</li>');
                
                    // ----- START IN-OUT MATERIAL
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Control In-Out Material</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    // START REPORT
                    if (isset($page) && $page == 'IN-OUT Monitoring') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/in_out_monitoring') . '"><i class="fa fa-tv"></i> <span>In-Out Monitoring</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'IN-OUT Log') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/in_out_log') . '"><i class="fa fa-th-list"></i> <span>Log In-Out Material</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'IN-OUT Stock') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/in_out_stock') . '"><i class="fa fa-th-list"></i> <span>Stock In-Out Material</span></a>');
                    print_r('</li>');
                
                    // if (isset($page) && $page == 'IN-OUT Compare') {
                    //     print_r('<li class="active">');
                    // } else {
                    //     print_r('<li>');
                    // }
                    // print_r('<a href="' . url('/index/in_out_compare') . '"><i class="fa fa-th-list"></i> <span>Log Comparison</span></a>');
                    // print_r('</li>');
                    // END REPORT
                
                    // ----- START BPP
                    if (isset($page) && $page == 'BPP-IN') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=bpp-in') . '"><i class="fa fa-pencil-square-o"></i> <span>BPP - Masuk</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'BPP-OUT') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=bpp-out') . '"><i class="fa fa-pencil-square-o"></i> <span>BPP - Keluar</span></a>');
                    print_r('</li>');
                    // ----- END BPP
                
                    // ----- START WLD
                    if (isset($page) && $page == 'WLD-IN') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=wld-in') . '"><i class="fa fa-pencil-square-o"></i> <span>WLD - Masuk</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'WLD-OUT') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=wld-out') . '"><i class="fa fa-pencil-square-o"></i> <span>WLD - Keluar</span></a>');
                    print_r('</li>');
                    // ----- END WLD
                
                    // ----- START BFF
                    if (isset($page) && $page == 'BFF-IN') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=bff-in') . '"><i class="fa fa-pencil-square-o"></i> <span>BFF - Masuk</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'BFF-OUT') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=bff-out') . '"><i class="fa fa-pencil-square-o"></i> <span>BFF - Keluar</span></a>');
                    print_r('</li>');
                    // ----- END BFF
                
                    // ----- START LCQ
                    if (isset($page) && $page == 'LCQ-IN') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=lcq-in') . '"><i class="fa fa-pencil-square-o"></i> <span>LCQ - Masuk</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'LCQ-OUT') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=lcq-out') . '"><i class="fa fa-pencil-square-o"></i> <span>LCQ - Keluar</span></a>');
                    print_r('</li>');
                    // ----- END LCQ
                
                    // ----- START PLT
                    if (isset($page) && $page == 'PLT-IN') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=plt-in') . '"><i class="fa fa-pencil-square-o"></i> <span>PLT - Masuk</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'PLT-OUT') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=plt-out') . '"><i class="fa fa-pencil-square-o"></i> <span>PLT - Keluar</span></a>');
                    print_r('</li>');
                    // ----- START PLT
                
                    // ----- START FA
                    if (isset($page) && $page == 'FA-IN') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=fa-in') . '"><i class="fa fa-pencil-square-o"></i> <span>FA - Masuk</span></a>');
                    print_r('</li>');
                    if (isset($page) && $page == 'FA-OUT') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/material_in_out?remark=fa-out') . '"><i class="fa fa-pencil-square-o"></i> <span>FA - Keluar</span></a>');
                    print_r('</li>');
                    // ----- START FA
                
                    print_r('</ul>');
                    print_r('</li>');
                    // ----- END IN-OUT MATERIAL
                
                    if (in_array('READ_PRD', $navs)) {
                        if (isset($page) && $page == 'Safety Stock') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/safety_stock') . '"><i class="fa fa-pencil-square-o"></i> <span>Initial Safety Stock</span></a>');
                        print_r('</li>');
                
                        print_r('<li class="treeview">');
                        print_r('<a href="#">');
                        print_r('<i class="fa fa-pencil-square-o"></i> <span>Repair</span>');
                        print_r('<span class="pull-right-container">');
                        print_r('<i class="fa fa-angle-left pull-right"></i>');
                        print_r('</span>');
                        print_r('</a>');
                        print_r('<ul class="treeview-menu">');
                
                        if (isset($page) && $page == 'Repair') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . secure_url('/index/repair') . '"><i class="fa fa-print"></i> <span>Repair</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Repair Logs') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/repair_logs') . '"><i class="fa fa-th-list"></i> <span>Repair Logs</span></a>');
                        print_r('</li>');
                
                        print_r('</ul>');
                        print_r('</li>');
                
                        print_r('<li class="treeview">');
                        print_r('<a href="#">');
                        print_r('<i class="fa fa-pencil-square-o"></i> <span>Return</span>');
                        print_r('<span class="pull-right-container">');
                        print_r('<i class="fa fa-angle-left pull-right"></i>');
                        print_r('</span>');
                        print_r('</a>');
                        print_r('<ul class="treeview-menu">');
                
                        if (isset($page) && $page == 'Return') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . secure_url('/index/return') . '"><i class="fa fa-print"></i> <span>Return</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Return Logs') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/return_logs') . '"><i class="fa fa-th-list"></i> <span>Return Logs</span></a>');
                        print_r('</li>');

                        if (isset($page) && $page == 'Return Monitoring') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/return/monitoring') . '"><i class="fa fa-television"></i> <span>Return Monitoring</span></a>');
                        print_r('</li>');
                
                        print_r('</ul>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_PRD', $navs) || in_array('READ_LOG', $navs) || in_array('READ_PCH', $navs)) {
                        print_r('<li class="treeview">');
                        print_r('<a href="#">');
                        print_r('<i class="fa fa-pencil-square-o"></i> <span>Scrap & Return MMJR OTHR</span>');
                        print_r('<span class="pull-right-container">');
                        print_r('<i class="fa fa-angle-left pull-right"></i>');
                        print_r('</span>');
                        print_r('</a>');
                        print_r('<ul class="treeview-menu">');
                
                        if (isset($page) && $page == 'Buat Slip Scrap') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/scrap') . '"><i class="fa fa-print"></i> <span>Scrap & Return</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Konfirmasi Warehouse') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/scrap/warehouse') . '"><i class="fa fa-pencil-square-o"></i> <span>Receive Scrap</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Scrap Penarikan Logs') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/log/penarikan/scrap') . '"><i class="fa fa-pencil-square-o"></i> <span>Cancel Scrap Warehouse</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Report Scrap') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/scrap_record') . '"><i class="fa fa-th-list"></i> <span>Scrap Logs</span></a>');
                        print_r('</li>');
                
                        print_r('</ul>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_PRD', $navs) || in_array('READ_CHM', $navs)) {
                        print_r('<li class="treeview">');
                        print_r('<a href="#">');
                        print_r('<i class="fa fa-pencil-square-o"></i> <span>Chemical</span>');
                        print_r('<span class="pull-right-container">');
                        print_r('<i class="fa fa-angle-left pull-right"></i>');
                        print_r('</span>');
                        print_r('</a>');
                        print_r('<ul class="treeview-menu">');
                
                        if (isset($page) && $page == 'Request') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . secure_url('/index/indirect_material_request/chm') . '"><i class="fa fa-pencil-square-o"></i> <span>Request</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Chemical Picking Schedule') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/chm_picking_schedule') . '"><i class="fa fa-th-list"></i> <span>Picking Schedule</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Chemical Solution Control') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/chm_solution_control') . '"><i class="fa fa-bar-chart"></i> <span>Chemical Control</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Larutan') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/chm_larutan') . '"><i class="fa fa-th-list"></i> <span>Chemical List</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'SDS Monitoring') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/chemical/safety_data_sheet') . '"><i class="fa fa-bar-chart"></i> <span>SDS Control</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'WWT - Waste Control') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . secure_url('/index/maintenance/wwt/waste_control/update') . '"><i class="fa fa-pencil-square-o"></i> <span>WWT - Waste Control</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Logs WWT') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/logs/wwt') . '"><i class="fa fa-th-list"></i> <span>WWT - Logs</span></a>');
                        print_r('</li>');
                
                        // if(isset($page) && $page == 'Daily WWT'){
                        //     print_r('<li class="active">');
                        // }
                        // else{
                        //     print_r('<li>');
                        // }
                        // print_r('<a href="'.url('/index/maintenance/wwt/daily').'"><i class="fa fa-th-list"></i> <span>WWT - Daily</span></a>');
                        // print_r('</li>');
                
                        print_r('</ul>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_PRD', $navs) || in_array('READ_PCH', $navs)) {
                        print_r('<li class="treeview">');
                        print_r('<a href="#">');
                        print_r('<i class="fa fa-pencil-square-o"></i> <span>Schedule</span>');
                        print_r('<span class="pull-right-container">');
                        print_r('<i class="fa fa-angle-left pull-right"></i>');
                        print_r('</span>');
                        print_r('</a>');
                        print_r('<ul class="treeview-menu">');
                
                        if (isset($page) && $page == 'Production Request') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/production_request') . '"><i class="fa fa-pencil-square-o"></i> <span>Request</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Production Forecast') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/production_forecast') . '"><i class="fa fa-pencil-square-o"></i> <span>Forecast</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Production Schedule KD') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/production_schedule_kd') . '"><i class="fa fa-pencil-square-o"></i> <span>Schedule KD</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Production Schedule FG') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/production_schedule') . '"><i class="fa fa-pencil-square-o"></i> <span>Schedule FG</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Assy Picking Schedule') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/assy_schedule') . '"><i class="fa fa-pencil-square-o"></i> <span>Schedule Picking Assy</span></a>');
                        print_r('</li>');
                
                        print_r('</ul>');
                        print_r('</li>');
                    }
                }
                
                if (in_array('MONITOR_FG', $navs)) {
                    print_r('<li class="header">Finished Goods Menu</li>');
                    if (in_array('READ_QA', $navs) || in_array('READ_PE', $navs) || in_array('READ_WIP-FA', $navs) || in_array('READ_WIP-INJ', $navs) || in_array('READ_WIP-RC', $navs) || in_array('READ_WIP-MP', $navs) || in_array('READ_WIP-VN', $navs) || in_array('READ_WIP-PN', $navs) || in_array('READ_WIP-KPP', $navs) || in_array('READ_WIP-BPP', $navs) || in_array('READ_WIP-CASE', $navs) || in_array('READ_WIP-CLBODY', $navs) || in_array('READ_WIP-TNP', $navs) || in_array('READ_WIP-WP', $navs) || in_array('READ_WIP-ST', $navs) || in_array('READ_WIP-FA', $navs)) {
                        if (isset($page) && $page == 'Extra Order Completion') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/extra_order/completion_page') . '"><i class="fa fa-pencil-square-o"></i> <span>EO - Completion</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-FA', $navs)) {
                        if (isset($page) && $page == 'FLO Band Instrument') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/flo_view/bi') . '"><i class="fa fa-pencil-square-o"></i> <span>FLO - Band Instrument</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'FLO Maedaoshi BI') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/maedaoshi_bi') . '"><i class="fa fa-pencil-square-o"></i> <span>MAEDAOSHI - Band Instrument</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-PN', $navs) || in_array('READ_WIP-RC', $navs) || in_array('READ_WIP-VN', $navs)) {
                        if (isset($page) && $page == 'FLO Educational Instrument') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/flo_view/ei') . '"><i class="fa fa-pencil-square-o"></i> <span>FLO - Education Instrument</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'FLO Maedaoshi EI') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/maedaoshi_ei') . '"><i class="fa fa-pencil-square-o"></i> <span>MAEDAOSHI - Education Instrument</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-INJ', $navs)) {
                        if (isset($page) && $page == 'KD Venova Injection') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('index/kd_vn_injection/' . 'vn-injection') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Venova Injection</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-PN', $navs)) {
                        if (isset($page) && $page == 'KD Pianica Part') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('index/kd_vn_injection/' . 'pn-part') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Pianica Part</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-RC', $navs)) {
                        if (isset($page) && $page == 'KD Recorder Assy') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('index/kd_vn_injection/' . 'rc-assy') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Recorder Assy</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-VN', $navs)) {
                        if (isset($page) && $page == 'KD Venova Assy') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('index/kd_vn_injection/' . 'vn-assy') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Venova Assy</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-MP', $navs)) {
                        if (isset($page) && $page == 'KD Mouthpiece') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('index/kd_vn_injection/' . 'mouthpiece-packed') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Mouthpiece</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'MP Create Checksheet') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_mouthpiece/checksheet') . '"><i class="fa fa-pencil-square-o"></i> <span>MP - Create Checksheet</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'MP Material Picking') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_mouthpiece/picking') . '"><i class="fa fa-pencil-square-o"></i> <span>MP - Material Picking</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'MP Packing') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_mouthpiece/packing') . '"><i class="fa fa-pencil-square-o"></i> <span>MP - Material Packing</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'MP QA Check') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_mouthpiece/qa_check') . '"><i class="fa fa-pencil-square-o"></i> <span>MP - Quality Check</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'MP LOG') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_vn_injection/' . 'mouthpiece-packed') . '"><i class="fa fa-pencil-square-o"></i> <span>MP - Checksheet Log</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-KPP', $navs)) {
                        if (isset($page) && $page == 'KD Z-PRO') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_zpro/' . 'z-pro') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - ZPRO</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'KD M-PRO') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_mpro/' . 'm-pro') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - KPP</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-BPP', $navs)) {
                        if (isset($page) && $page == 'KD B-PRO') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_bpro/' . 'b-pro') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - BPP</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-WP', $navs)) {
                        if (isset($page) && $page == 'KD Welding Body') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_welding/' . 'welding-body') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Welding Body</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'KD Welding Key Post') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_welding/' . 'welding-keypost') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Welding Keypost</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-CLBODY', $navs)) {
                        if (isset($page) && $page == 'KD CL Body') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_cl_body/' . 'cl-body') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Clarinet Body</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-CASE', $navs)) {
                        if (isset($page) && $page == 'KD CASE') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_case/' . 'case') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Case</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-TNP', $navs)) {
                        if (isset($page) && $page == 'KD TANPO') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_tanpo/' . 'tanpo') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Tanpo</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_WIP-FA', $navs)) {
                        if (isset($page) && $page == 'KD Assy - SubAssy SX') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_subassy/' . 'sub-assy-sx') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Subassy SX</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'KD Sub Assy FL') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_subassy/' . 'sub-assy-fl') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Subassy FL</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'KD Sub Assy CL') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_subassy/' . 'sub-assy-cl') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Subassy CL</span></a>');
                        print_r('</li>');
                    }
                
                    if (in_array('READ_LOG', $navs)) {
                        if (isset($page) && $page == 'Extra Order Delivery') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/extra_order/delivery_page') . '"><i class="fa fa-pencil-square-o"></i> <span>EO - Delivery</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Extra Order Sending Aplication') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/extra_order/sending_application') . '"><i class="fa fa-pencil-square-o"></i> <span>EO - Sending Application</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Extra Order Stuffing') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/extra_order/stuffing_page') . '"><i class="fa fa-pencil-square-o"></i> <span>EO - Stuffing</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'FLO Delivery') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/flo_view/delivery') . '"><i class="fa fa-pencil-square-o"></i> <span>FLO - Delivery</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'FLO Stuffing') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/flo_view/stuffing') . '"><i class="fa fa-pencil-square-o"></i> <span>FLO - Stuffing</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'FLO Shipment') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/flo_view/shipment') . '"><i class="fa fa-pencil-square-o"></i> <span>FLO - Shipment</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'KD Delivery') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_delivery') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Delivery</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'KD Splitter Case') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_splitter/case') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Splitter Case</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'KD Splitter PN Part') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_splitter/pn-part') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Splitter PN Part</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'KD Stuffing') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/kd_stuffing') . '"><i class="fa fa-pencil-square-o"></i> <span>KD - Stuffing</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'FLO Lading') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/flo_view/lading') . '"><i class="fa fa-pencil-square-o"></i> <span>FLO & KDO - On Board</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'Check Sheet') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/CheckSheet') . '"><i class="fa fa-pencil-square-o"></i> <span>Checksheet</span></a>');
                        print_r('</li>');
                    }
                
                    // if(isset($page) && $page == 'Extra Order'){
                    //     print_r('<li class="active">');
                    // }
                    // else{
                    //     print_r('<li>');
                    // }
                    // print_r('<a href="'.url('/index/extra_order').'"><i class="fa fa-th-list"></i> <span>EO - Monitoring</span></a>');
                    // print_r('</li>');
                
                    if (in_array('READ_PRD', $navs)) {
                        if (isset($page) && $page == 'FLO Deletion') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/flo_view/deletion') . '"><i class="fa fa-pencil-square-o"></i> <span>FLO - Deletion</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'FLO Detail') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/flo_view/detail') . '"><i class="fa fa-th-list"></i> <span>FLO - Detail</span></a>');
                        print_r('</li>');
                
                        if (isset($page) && $page == 'FLO Open Destination') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/flo_open') . '"><i class="fa fa-th-list"></i> <span>FLO - Open Destination</span></a>');
                        print_r('</li>');
                    }
                }
                
                if (in_array('READ_PRD', $navs)) {
                    print_r('<li class="header">Transaction Menu</li>');
                
                    if (isset($page) && $page == 'Production Result') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/ymes/production_result') . '"><i class="fa fa-pencil-square-o"></i> <span>Production Result</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Goods Movement') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/ymes/goods_movement') . '"><i class="fa fa-pencil-square-o"></i> <span>Goods Movement</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Production Result Temporary') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/ymes/production_result_temporary') . '"><i class="fa fa-th-list"></i> <span>Temporary</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Transaction History') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/ymes/history') . '"><i class="fa fa-th-list"></i> <span>Transaction History</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Transaction Error') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/ymes/error') . '"><i class="fa fa-th-list"></i> <span>Transaction Error</span></a>');
                    print_r('</li>');
                
                    if (in_array('UPDATE_PC', $navs)) {
                        if (isset($page) && $page == 'YMES Interface Setting') {
                            print_r('<li class="active">');
                        } else {
                            print_r('<li>');
                        }
                        print_r('<a href="' . url('/index/ymes/setting') . '"><i class="fa fa-cog"></i> <span>Interface Setting</span></a>');
                        print_r('</li>');
                    }
                }
                
                if (in_array('READ_KANBAN', $navs)) {
                    print_r('<li class="header">Kanban Menu</li>');
                
                    if (isset($page) && $page == 'Kanban Inventory') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/kanban/inventory') . '"><i class="fa fa-th-list"></i> <span>Inventory</span></a>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-th-list"></i> <span>Master</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Kanban Material') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/kanban/material') . '"><i class="fa fa-th-list"></i> <span>Master Material</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Kanban Completion') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/kanban/completion_master') . '"><i class="fa fa-th-list"></i> <span>Master Completion</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Kanban Material') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/kanban/transfer_master') . '"><i class="fa fa-th-list"></i> <span>Master Transfer</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Transaction</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Kanban Completion') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/kanban/completion') . '"><i class="fa fa-pencil-square-o"></i> <span>Completion</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Kanban Completion Cancel') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/kanban/completion_cancel') . '"><i class="fa fa-pencil-square-o"></i> <span>Completion Cancel</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Kanban Transfer') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/kanban/transfer') . '"><i class="fa fa-pencil-square-o"></i> <span>Transfer</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Kanban Transfer Cancel') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/kanban/transfer_cancel') . '"><i class="fa fa-pencil-square-o"></i> <span>Transfer Cancel</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Kanban Queue</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'welding-queue') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/welding/welding_adjustment') . '"><i class="fa fa-th-list"></i> <span>Welding Queue</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'barrel-queue') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/middle/barrel_adjustment') . '"><i class="fa fa-th-list"></i> <span>Barrel Queue</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'buffing-queue') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/middle/buffing_adjustment') . '"><i class="fa fa-th-list"></i> <span>Buffing Queue</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'buffing-cancel') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/middle/buffing_canceled') . '"><i class="fa fa-th-list"></i> <span>Buffing Cancel</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'wip') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/middle/wip_adjustment') . '"><i class="fa fa-th-list"></i> <span>Middle WIP</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                
                    print_r('<li class="treeview">');
                    print_r('<a href="#">');
                    print_r('<i class="fa fa-pencil-square-o"></i> <span>Kanban Resume</span>');
                    print_r('<span class="pull-right-container">');
                    print_r('<i class="fa fa-angle-left pull-right"></i>');
                    print_r('</span>');
                    print_r('</a>');
                    print_r('<ul class="treeview-menu">');
                
                    if (isset($page) && $page == 'Welding Resume Kanban') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/welding/resume_kanban') . '"><i class="fa fa-th-list"></i> <span>Welding Resume</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Resume of Saxophone Middle Process Kanban') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/middle/resume_kanban/clarinet_key') . '"><i class="fa fa-th-list"></i> <span>Buffing - Stockroom CL Key</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Resume of Clarinet Middle Process Kanban') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/middle/resume_kanban/saxophone_key') . '"><i class="fa fa-th-list"></i> <span>Tumbling - Stockroom SX Key</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'Resume of Saxophone Buffing Process Kanban') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/buffing/resume_kanban/saxophone_key') . '"><i class="fa fa-th-list"></i> <span>Buffing - Store SX Key</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'KPP Resume Kanban') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/tpro/resume_kanban') . '"><i class="fa fa-th-list"></i> <span>Key Part Process</span></a>');
                    print_r('</li>');
                
                    if (isset($page) && $page == 'KPP Kanban Check') {
                        print_r('<li class="active">');
                    } else {
                        print_r('<li>');
                    }
                    print_r('<a href="' . url('/index/tpro/check_kanban') . '"><i class="fa fa-pencil-square-o"></i> <span>KPP Check List</span></a>');
                    print_r('</li>');
                
                    print_r('</ul>');
                    print_r('</li>');
                }
                
            @endphp
        </ul>
    </section>
</aside>
