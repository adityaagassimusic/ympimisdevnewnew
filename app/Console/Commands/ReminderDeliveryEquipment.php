<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\AccPurchaseOrder;
use App\AccPurchaseOrderDetail;
use App\AccSupplier;


class ReminderDeliveryEquipment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:delivery_equipment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

          $date_lokal = date('Y-m-d', strtotime('+ 3 days'));
          $date_import = date('Y-m-d', strtotime('+ 14 days'));

          $po_vendor = AccPurchaseOrder::select('acc_purchase_orders.supplier_code','acc_purchase_orders.supplier_name','supplier_status_fix')
            ->leftJoin('acc_suppliers', 'acc_purchase_orders.supplier_code', '=', 'acc_suppliers.vendor_code')
          ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
          ->whereNull('acc_purchase_order_details.status')
          ->where('delivery_date', '>=', date('Y-m-d'))
          ->distinct()
          ->get();


        foreach ($po_vendor as $po) {

            if ($po->supplier_status_fix == "Lokal") {
              $detail_email = AccPurchaseOrder::select('*', DB::RAW('"lokal" as status_vendor'))
              ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
              ->whereNull('acc_purchase_order_details.status')
              ->where('delivery_date', '=', $date_lokal)
              ->where('supplier_code', '=', $po->supplier_code)
              ->get();
            
            }
            else if($po->supplier_status_fix == "Import"){
              $detail_email = AccPurchaseOrder::select('*', DB::RAW('"import" as status_vendor'))
              ->leftJoin('acc_purchase_order_details', 'acc_purchase_orders.no_po', '=', 'acc_purchase_order_details.no_po')
              ->whereNull('acc_purchase_order_details.status')
              ->where('delivery_date', '=', $date_import)
              ->where('supplier_code', '=', $po->supplier_code)
              ->get();
            }

            //     //kirim email ke Mas Shega & Mas Hamzah
            $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and (employee_id = 'PI1810020'  or employee_id = 'PI0904006' or employee_id = 'PI1506001')";
            $mailtoo = DB::select($mails);

            if (count($detail_email) > 0) {
              Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($detail_email, 'vendor_reminder_delivery_equipment'));
            }
        }
    }
}
