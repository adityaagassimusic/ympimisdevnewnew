<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $remark;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $remark)
    {
        $this->data = $data;
        $this->remark = $remark;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        if ($this->remark == 'union_mail') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Registrasi Kepesertaan Serikat Pekerja')
                ->view('employees.union_mail');
        }

        if ($this->remark == 'notif_end_contract') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Notification Smart Recruitment')
                ->view('human_resource.recruitment.notification_end_contract');
        }

        if ($this->remark == 'attendance_violation') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Indikasi Pelanggaran Kehadiran')
                ->view('human_resource.mail.mail_attendance_violation');
        }

        if ($this->remark == 'calibration_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Calibration Reminder')
                ->view('standardization.calibration_mail');
        }

        if ($this->remark == 'material_check_finding') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Ketidaksesuaian Spesifikasi Kedatangan Indirect Material')
                ->view('materials.check.mail_finding');
        }

        if ($this->remark == 'material_check_report') {
            if ($this->data['position'] == 'Buyer') {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Ketidaksesuaian Spesifikasi Kedatangan Indirect Material')
                    ->view('materials.check.mail_report')
                    ->attach(public_path('files/material_check/' . $this->data['invoice']))
                    ->attach(public_path('files/material_check/' . $this->data['evidence']));
            } else if ($this->data['position'] == 'Vendor') {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Ketidaksesuaian Spesifikasi Kedatangan Indirect Material')
                    ->view('materials.check.mail_report')
                    ->attach(public_path('files/material_check/' . $this->data['invoice']));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Ketidaksesuaian Spesifikasi Kedatangan Indirect Material')
                    ->view('materials.check.mail_report');
            }
        }

        if ($this->remark == 'material_check_notification') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Pengecekan Kedatangan Indirect Material')
                ->view('materials.check.mail_notification');
        }

        if ($this->remark == 'mis_form_approval') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('MIS Form Request')
                ->view('about_mis.form.mail_approval');
        }

        if ($this->remark == 'mis_form_notification') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('MIS Form Notification')
                ->view('about_mis.form.mail_notification');
        }

        if ($this->remark == 'new_checksheet') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('New Checksheet Information')
                ->view('Check_Sheet.mail_new_checksheet');
        }

        if ($this->remark == 'revised_checksheet') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Revised Checksheet Information')
                ->view('Check_Sheet.mail_revised_checksheet');
        }

        if ($this->remark == 'ng_container_checklist_security') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Notification of Checking the Truck Container Condition Checklist')
                ->view('containers.checklist.mail_checklist_security_ng');
        }

        if ($this->remark == 'ng_container_checklist') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Notification of Checking the Container Condition Checklist')
                ->view('Check_Sheet.mail_ng_checklist');
        }

        if ($this->remark == 'raw_material_reminder_delivery') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject($this->data['subject'])
                ->view('materials.mail.reminder_delivery');
        }

        if ($this->remark == 'raw_material_send_po_notification') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject($this->data['subject'])
                ->view('materials.mail.po_notification');
        }

        if ($this->remark == 'raw_material_reminder_po') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject($this->data['subject'])
                ->view('materials.mail.po_reminder');
        }

        if ($this->remark == 'raw_material_send_po') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject($this->data['subject'])
                ->view('materials.mail.po')
                ->attach('http://10.109.52.4/mirai/public/po_list/sap/' . $this->data['attachment']);
        }

        if ($this->remark == 'raw_material_send_bc') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject($this->data['subject'])
                ->view('materials.mail.bc')
                ->attach(public_path('bc/' . $this->data['bc_document']))
                ->attach(public_path('sppb/' . $this->data['sppb']));
        }

        if ($this->remark == 'trade_agreement_cms') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Change Management System (CMS)')
                ->view('licenses.trade_agreement_cms_mail')
                ->attach(public_path('trade_agreements/cms/Addendum1 Definition (1st Edition 1Jan2009).pdf'))
                ->attach(public_path('trade_agreements/cms/changes management and operation manual(ver.1.0).pdf'))
                ->attach(public_path('trade_agreements/cms/Proposal_of_Change_en_edition5.xls'));
        }

        if ($this->remark == 'locker_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject($this->data['title'])->view('general_affairs.locker.locker_mail');
        }

        if ($this->remark == 'license_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject($this->data['title'])->view('licenses.license_mail');
        }

        if ($this->remark == 'trade_agreement') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Master Trade Agreement (取引基本契約書)')->view('licenses.trade_agreement_mail');
        }

        if ($this->remark == 'safety_riding') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Catatan Record Penerapan 『Janji Safety Riding』')->view('general.pointing_call.safety_riding_mail');
        }

        if ($this->remark == 'request_manpower_approval') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Request Manpower Approval (マンパワー依頼の承認)')->view('human_resource.recruitment.mail_request_manpower_approval');
        }

        if ($this->remark == 'request_manpower_approval_pak_ura') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Request Manpower (マンパワー依頼の承認)')->view('human_resource.recruitment.request_manpower_approval_pak_ura');
        }

        if ($this->remark == 'approval_nilai_mp') {
            // return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Request Manpower (マンパワー依頼の承認)')->view('human_resource.recruitment.mail_approval_penilaian_mp');
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Request Manpower (マンパワー依頼の承認)')->view('human_resource.recruitment.report_pdf_penilaian');
        }

        if ($this->remark == 'pengganti_mp') {
            // return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Request Manpower (マンパワー依頼の承認)')->view('human_resource.recruitment.mail_approval_penilaian_mp');
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Request Manpower (マンパワー依頼の承認)')->view('human_resource.recruitment.email_foreman_pengganti_mp');
        }

        if ($this->remark == 'request_magang_approval') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Request Karyawan Magang Approval (マンパワー依頼の承認)')->view('human_resource.magang.mail_request_magang_approval');
        }

        if ($this->remark == 'request_tunjangan_keluarga') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('【Approval】 PERMOHONAN TUNJANGAN KELUARGA')->attach(public_path('hr/uang_keluarga/KK_' . $this->data['data'][0]->request_id . '.jpg'))->attach(public_path('hr/uang_keluarga/LAMPIRAN_' . $this->data['data'][0]->request_id . '.jpg'))->view('human_resource.mails_approver_keluarga');
        }

        if ($this->remark == 'rejected_uang_keluarga') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('REJECTED PERMOHONAN TUNJANGAN KELUARGA')->attach(public_path('hr/uang_keluarga/KK_' . $this->data['data'][0]->request_id . '.jpg'))->attach(public_path('hr/uang_keluarga/LAMPIRAN_' . $this->data['data'][0]->request_id . '.jpg'))->view('human_resource.rejected_uang_keluarga');
        }

        if ($this->remark == 'done_tunjangan_keluarga') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('PERMOHONAN TUNJANGAN KELUARGA')->attach(public_path('hr/uang_keluarga/KK_' . $this->data['data'][0]->request_id . '.jpg'))->attach(public_path('hr/uang_keluarga/LAMPIRAN_' . $this->data['data'][0]->request_id . '.jpg'))->view('human_resource.done_tunjangan_keluarga');
        }

        if ($this->remark == 'request_uang_simpati') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('【Approval】 PERMOHONAN UANG SIMPATI')->attach(public_path('hr/uang_simpati/LAMPIRAN_' . $this->data['data'][0]->request_id . '.jpg'))->view('human_resource.mails_approver_simpati');
        }

        if ($this->remark == 'request_uang_simpati_kematian') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('【Approval】 PERMOHONAN UANG SIMPATI')->attach(public_path('hr/uang_simpati/LAMPIRAN_' . $this->data['data'][0]->request_id . '.jpg'))->attach(public_path('hr/uang_simpati/KK_Kematian_' . $this->data['data'][0]->request_id . '.jpg'))->view('human_resource.mails_approver_simpati');
        }

        if ($this->remark == 'done_uang_simpati_kematian') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('【Approval】 PERMOHONAN UANG SIMPATI')->attach(public_path('hr/uang_simpati/LAMPIRAN_' . $this->data['data'][0]->request_id . '.jpg'))->attach(public_path('hr/uang_simpati/KK_Kematian_' . $this->data['data'][0]->request_id . '.jpg'))->view('human_resource.done_uang_simpati');
        }

        if ($this->remark == 'rejected_uang_simpati') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('REJECTED PERMOHONAN UANG SIMPATI')->attach(public_path('hr/uang_simpati/LAMPIRAN_' . $this->data['data'][0]->request_id . '.jpg'))->view('human_resource.rejected_uang_simpati');
        }

        if ($this->remark == 'request_tunjangan_pekerjaan') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('【Approval】 PERMOHONAN TUNJANGAN PEKERJAAN')->view('human_resource.mails_approver_kerja');
        }

        if ($this->remark == 'tunjangan_kerja_tanpa_ttd') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('【Approval】 PERMOHONAN TUNJANGAN PEKERJAAN')->view('human_resource.done_tunjangan_pekerjaan');
        }

        if ($this->remark == 'done_uang_simpati') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('PERMOHONAN UANG SIMPATI')->attach(public_path('hr/uang_simpati/LAMPIRAN_' . $this->data['data'][0]->request_id . '.jpg'))->view('human_resource.done_uang_simpati');
        }

        if ($this->remark == 'request_magang_done') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Request Karyawan Magang Approval (マンパワー依頼の承認)')->view('human_resource.magang.done_mail_request_magang');
        }

        if ($this->remark == 'sales_report') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Reminder Update Shipment On Board')
                ->view('mails.sales_reminder');
        }

        if ($this->remark == 'eo_shortage') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Reminder Extra Order Shortage')
                ->priority(1)
                ->view('extra_order.mail.mail_reminder_extra_order');
        }

        if ($this->remark == 'eo_minus_wo') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Minus Work Order Notification for Extra Order')
                ->priority(1)
                ->view('extra_order.mail.mail_extra_order_minus_wo');
        }

        if ($this->remark == 'eo_outstanding_eoc') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Reminder Approval Extra Order Confirmation')
                ->priority(1)
                ->view('extra_order.mail.mail_reminder_eoc');
        }

        if ($this->remark == 'eo_waiting_sendapp') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Reminder Extra Order Sending Application')
                ->priority(1)
                ->view('extra_order.mail.mail_reminder_send_app');
        }

        if ($this->remark == 'eo_upload_po') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Upload PO Extra Order (エキストラオーダー発注書のアプロード)')
                ->view('extra_order.mail.mail_upload_po')
                ->attach(public_path('files/extra_order/po_att/' . $this->data['extra_order']->eo_number . '.xlsx'));
        }

        if ($this->remark == 'eo_reupload_po') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Upload PO Extra Order (エキストラオーダー発注書のアプロード)')
                ->view('extra_order.mail.mail_resend_upload_po')
                ->attach(public_path('files/extra_order/po_att/' . $this->data['extra_order']->eo_number . '.xlsx'));
        }

        if ($this->remark == 'hold_comment_eoc') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Approval Extra Order Confirmation (エキストラオーダー申請承認)')
                ->view('extra_order.mail.mail_hold_comment');
        }

        if ($this->remark == 'eo_approval_eoc') {
            if ($this->data['filename'] != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Approval Extra Order Confirmation (エキストラオーダー申請承認)')
                    ->view('extra_order.mail.mail_approval')
                    ->attach(public_path('files/extra_order/attachment/' . $this->data['filename']));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Approval Extra Order Confirmation (エキストラオーダー申請承認)')
                    ->view('extra_order.mail.mail_approval');
            }
        }

        if ($this->remark == 'eo_request_notification') {
            if ($this->data['filename'] != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Extra Order Request (エキストラオーダーリクエスト)')
                    ->view('extra_order.mail.mail_request_notification')
                    ->attach(public_path('files/extra_order/attachment/' . $this->data['filename']));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Extra Order Request (エキストラオーダーリクエスト)')
                    ->view('extra_order.mail.mail_request_notification');
            }
        }

        if ($this->remark == 'eo_approval_eoc_reject') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Approval Extra Order Confirmation (エキストラオーダー申請承認)')
                ->view('extra_order.mail.mail_approval_rejected');
        }

        if ($this->remark == 'eo_material') {
            if ($this->data['filename'] != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('New Item Request Notification (新アイテムリクエストの通知)')
                    ->view('extra_order.mail.mail_bom_material')
                    ->attach(public_path('files/extra_order/attachment/' . $this->data['filename']));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('New Item Request Notification (新アイテムリクエストの通知)')
                    ->view('extra_order.mail.mail_bom_material');
            }

        }

        if ($this->remark == 'eo_trial_request') {

            if ($this->data['filename'] != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('New Item Request Notification (新アイテムリクエストの通知)')
                    ->view('extra_order.mail.mail_trial_request')
                    ->attach(public_path('files/extra_order/attachment/' . $this->data['filename']));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('New Item Request Notification (新アイテムリクエストの通知)')
                    ->view('extra_order.mail.mail_trial_request');
            }

        }

        if ($this->remark == 'eo_price_request') {

            if ($this->data['filename'] != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('New Sales Price Request Notification (新しい販売価格リクエストの通知)')
                    ->view('extra_order.mail.mail_price_request')
                    ->attach(public_path('files/extra_order/attachment/' . $this->data['filename']));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('New Sales Price Request Notification (新しい販売価格リクエストの通知)')
                    ->view('extra_order.mail.mail_price_request');
            }

        }

        if ($this->remark == 'eo_approval_po') {

            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Approval PO Extra Order (エクストラオーダー PO承認)')
                ->view('extra_order.mail.mail_approval_po');

            foreach (json_decode($this->data['extra_order']->po_number) as $filePath) {
                $email->attach(public_path('files/extra_order/po/' . $filePath));
            }

            return $email;
        }

        if ($this->remark == 'eo_new_po_information') {

            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('New PO for Extra Order')
                ->view('extra_order.mail.mail_new_po_information');

            foreach (json_decode($this->data['extra_order']->po_number) as $filePath) {
                $email->attach(public_path('files/extra_order/po/' . $filePath));
            }

            return $email;
        }

        if ($this->remark == 'send_app_measurement') {

            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Sending Application Extra Order')
                ->view('extra_order.mail.mail_measurement_info');

            for ($i = 0; $i < count($this->data['eo_numbers']); $i++) {
                $email->attach(public_path('files/extra_order/eoc/EOC_' . $this->data['eo_numbers'][$i] . '.pdf'));
            }

            for ($i = 0; $i < count($this->data['po_numbers']); $i++) {
                $email->attach(public_path('files/extra_order/po/' . $this->data['po_numbers'][$i]));
            }

            $filename = public_path() . '/files/extra_order/send_app/' . $this->data['send_app_no'] . '.pdf';
            $send_app_file_exist = file_exists($filename);
            if ($send_app_file_exist) {
                $email->attach(public_path('files/extra_order/send_app/' . $this->data['send_app_no'] . '.pdf'));
            }

            return $email;
        }

        if ($this->remark == 'sending_application') {

            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Sending Application Extra Order')
                ->view('extra_order.mail.mail_sending_application');

            for ($i = 0; $i < count($this->data['eo_numbers']); $i++) {
                $email->attach(public_path('files/extra_order/eoc/EOC_' . $this->data['eo_numbers'][$i] . '.pdf'));
            }

            for ($i = 0; $i < count($this->data['po_numbers']); $i++) {
                $email->attach(public_path('files/extra_order/po/' . $this->data['po_numbers'][$i]));
            }

            return $email;
        }

        if ($this->remark == 'delete_sending_application') {

            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Sending Application Extra Order')
                ->view('extra_order.mail.mail_delete_sending_application');

            return $email;
        }

        if ($this->remark == 'request_delete_sending_application') {

            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Sending Application Extra Order')
                ->view('extra_order.mail.mail_request_delete_sending_application');

            return $email;
        }

        if ($this->remark == 'complete_extra_order') {

            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Extra Order Shipment Complete (エキストラオーダー出荷完了)')
                ->view('extra_order.mail.mail_complete_extra_order')
                ->attach(public_path('files/extra_order/invoice/' . $this->data['send_app']->invoice_number . '.pdf'))
                ->attach(public_path('files/extra_order/way_bill/' . $this->data['send_app']->way_bill . '.pdf'));

            return $email;
        }

        if ($this->remark == 'mis_ticket_approval') {
            if ($this->data['filename'] != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('MIS Ticket Request (MISチケット依頼)')
                    ->view('about_mis.ticket.mail_approval')
                    ->attach(public_path('files/mis_ticket/' . $this->data['filename']));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('MIS Ticket Request')
                    ->view('about_mis.ticket.mail_approval');
            }
        }
        if ($this->remark == 'bento_reject') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Japanese Food Order Rejected (和食弁当の予約)')->view('mails.bento.bento_reject');
        }
        if ($this->remark == 'bento_approve') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Japanese Food Order Confirmed (弁当の注文が確認済み)')->view('mails.bento.bento_approve');
        }
        if ($this->remark == 'bento_information') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('弁当の注文が確認済み')->view('mails.bento.bento_information');
        }
        if ($this->remark == 'live_cooking_approve') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Live Cooking Confirmation')->view('mails.live_cooking_approve');
        }
        if ($this->remark == 'live_cooking_reject') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Live Cooking Reject Information')->view('mails.live_cooking_reject');
        }
        if ($this->remark == 'shipment') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Shipment Reminder (情報管理システムの出荷通知)')->view('mails.shipment');
        }
        if ($this->remark == 'overtime') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Overtime Information (情報管理システムの残業情報)')->view('mails.overtime');
        }
        if ($this->remark == 'stuffing') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Stuffing Information (情報管理システムの荷積み情報)')->view('mails.stuffing');
        }
        if ($this->remark == 'min_queue') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Kanban Queue Information (情報管理システムのかんばん待ちの情報)')->view('mails.min_queue');
        }
        if ($this->remark == 'middle_kanban') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Kanban WIP Information (情報管理システムのかんばん待ちの情報)')->view('mails.middle_kanban');
        }
        if ($this->remark == 'duobleserialnumber') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Double Serial Number Information (情報管理システムの二重製造番号の情報)')->view('mails.duobleserialnumber');
        }
        if ($this->remark == 'confirmation_overtime') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Unconfirmed Overtime (情報管理システムの未確認残業)')->view('mails.confirmation_overtime');
        }
        if ($this->remark == 'cpar') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('CPAR ' . $this->data[0]->judul_komplain . ' (是正防止処置要求)')->view('mails.cpar');
        }
        if ($this->remark == 'rejectcpar') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Penolakan Corrective and Preventive Action Request (CPAR) (是正防止処置要求)')->view('mails.rejectcpar');
        }
        if ($this->remark == 'car') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('CAR ' . $this->data[0]->judul_komplain . ' (Corrective Action Report) (是正処置対策)')->view('mails.car');
        }
        if ($this->remark == 'rejectcar') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Penolakan Corrective Action Report (CAR) (是正処置対策)')->view('mails.rejectcar');
        }
        if ($this->remark == 'user_document') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Users Documents Reminder (ユーザ資料関連の催促メール)')->view('mails.user_document');
        }
        if ($this->remark == 'clinic_visit') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Clinic Visit Data')->view('mails.clinic_visit');
        }
        if ($this->remark == 'raw_material_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Alert Stock Material < Stock Policy')->view('mails.raw_material_reminder');
        }
        if ($this->remark == 'raw_material_over') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Warning Raw Material Over Plan Usage')->view('mails.raw_material_over');
        }
        if ($this->remark == 'double_transaction_notification') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Double Transaction Notification')->view('mails.double_transaction_notification');
        }
        if ($this->remark == 'ymes_unmatch') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Unmatch and Interface Error Data')->view('mails.ymes_unmatch');
        }
        if ($this->remark == 'audit') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Audit NG Jelas (生産監査報告)')->view('mails.audit');
        }
        if ($this->remark == 'sampling_check') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Sampling Check Report (抜取検査報告)')->view('mails.sampling_check');
        }
        if ($this->remark == 'laporan_aktivitas') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Laporan Aktivitas Audit IK (監査報告)')->view('mails.laporan_aktivitas');
        }
        if ($this->remark == 'ik_obsolete') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Laporan Audit IK Leader')->view('mails.ik_obsolete');
        }
        if ($this->remark == 'ik_obsolete_cek') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Cek Efektifitas Temuan Audit IK Leader')->view('mails.ik_obsolete_cek');
        }
        if ($this->remark == 'audit_ik_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Reminder Penanganan Audit IK Leader')->view('mails.audit_ik_reminder');
        }
        if ($this->remark == 'audit_ik_schedule_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Informasi Schedule Audit IK Leader Bulan Ini')->view('mails.audit_ik_schedule_reminder');
        }
        if ($this->remark == 'audit_ik_schedule_reminder_before') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Reminder Schedule Audit IK Leader Bulan Sebelumnya')->view('mails.audit_ik_schedule_reminder_before');
        }
        if ($this->remark == 'audit_ik_qc_koteihyo_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Reminder Audit IK dengan Temuan Revisi QC Koteihyo')->view('mails.audit_ik_qc_koteihyo_reminder');
        }
        if ($this->remark == 'daily_audit_warning') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Daily Audit Warning')->view('mails.daily_audit_warning');
        }
        if ($this->remark == 'training') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Training Report (教育報告)')->view('mails.training');
        }
        if ($this->remark == 'interview') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Interview Yubisashikosou Report (指差し呼称面談報告)')->view('mails.interview');
        }
        if ($this->remark == 'daily_check') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Daily Check FG / KD (日次完成品検査)')->view('mails.daily_check');
        }
        if ($this->remark == 'labeling') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Label Safety Mesin (安全ラベル表示)')->view('mails.labeling');
        }
        if ($this->remark == 'audit_process') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Process (監査手順)')->view('mails.audit_process');
        }
        if ($this->remark == 'first_product_audit') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Cek Produk Pertama Monthly Evidence (初物検査の監査　月次証拠)')->view('mails.first_product_audit');
        }
        if ($this->remark == 'first_product_audit_daily') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Cek Produk Pertama Daily Evidence (初物検査の監査　日次証拠)')->view('mails.first_product_audit_daily');
        }
        if ($this->remark == 'area_check') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Cek Kondisi Safety Area Kerja (職場安全状態確認)')->view('mails.area_check');
        }
        if ($this->remark == 'kaizen') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('MIS Unverified Kaizen Teian')->view('mails.kaizen');
        }
        if ($this->remark == 'jishu_hozen') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Audit Implementasi Jishu Hozen (自主保全適用監査)')->view('mails.jishu_hozen');
        }
        if ($this->remark == 'apd_check') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Cek Alat Pelindung Diri (APD) (保護具確認)')->view('mails.apd_check');
        }
        if ($this->remark == 'weekly_report') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Weekly Activity Report (週次活動報告)')->view('mails.weekly_report');
        }
        if ($this->remark == 'push_pull_check') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('NG Report of Push Pull Check Recorder (リコーダーのプッシュプル検査の不良報告)')->view('mails.push_pull_check');
        }
        if ($this->remark == 'height_check') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('NG Report of Height Gauge Check Recorder (リコーダーの高さ検査の不良報告)')->view('mails.height_check');
        }
        if ($this->remark == 'push_pull') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('NG Report of Push Pull & Camera Stamp Check Recorder (リコーダープッシュプールチェック)')->view('mails.push_pull');
        }
        if ($this->remark == 'urgent_wjo') {
            if ($this->data[0]->attachment != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Urgent Workshop Job Order (優先のワークショップ作業依頼書)')
                    ->view('mails.urgent_wjo')
                    ->attach(public_path('workshop/' . $this->data[0]->attachment));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Urgent Workshop Job Order (優先のワークショップ作業依頼書)')
                    ->view('mails.urgent_wjo');
            }

        }
        if ($this->remark == 'driver_request') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Driver Request Approval (運転手依頼承認)')->view('mails.driver_request');
        }
        if ($this->remark == 'driver_approval_notification') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Driver Request Approved (運転手依頼承認)')->view('mails.driver_approval_notification');
        }
        if ($this->remark == 'visitor_confirmation') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Visitor Confirmation (来客の確認)')->view('mails.visitor_confirmation');
        }
        if ($this->remark == 'incoming_visitor') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Incoming Visitor (ご来社のお客様)')->view('mails.incoming_visitor');
        }
        if ($this->remark == 'visitor_to_manager') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Visitor Confirmation To Manager (課長への来訪者確認)')->view('mails.visitor_to_manager');
        }
        if ($this->remark == 'ng_finding') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Laporan Temuan NG')->view('mails.ng_finding');
        }
        if ($this->remark == 'cpar_dept') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Form Laporan Ketidaksesuaian ')->view('mails.cpar_dept');
        }
        if ($this->remark == 'rejectcpar_dept') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Form Laporan Ketidaksesuaian Tidak Disetujui')->view('mails.rejectcpar_dept');
        }

        if ($this->remark == 'std_audit') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Audit ISO Standarisasi')->view('mails.std_audit');
        }

        if ($this->remark == 'audit_all') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Audit MIRAI')->view('mails.audit_all');
        }

        if ($this->remark == 'patrol') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Audit & Patrol MIRAI')->view('mails.patrol');
        }

        if ($this->remark == 'patrol_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Reminder Penanganan Temuan Patrol')->view('mails.patrol_reminder');
        }

        if ($this->remark == 'reminder_approval') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Reminder Approval Pembelian')->view('mails.reminder_approval');
        }

        if ($this->remark == 'reject_std_audit') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Audit ISO Standarisasi')->view('mails.std_audit');
        }

        if ($this->remark == 'machine') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Machine Error Information (設備エラー情報)')->view('mails.machine_notification');
        }

        if ($this->remark == 'urgent_spk') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Urgent Maintenance Job Order')->view('mails.urgent_spk');
        }

        if ($this->remark == 'spk_machine_stop') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject(' Maintenance Job Order with Stopped Machine')->view('mails.spk_machine_stop');
        }

        if ($this->remark == 'resume_loading_wwt') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject(' Request Loading Limbah WWT')
                ->view('maintenance.mails_loading_wwt');
        }

        if ($this->remark == 'done_resume_loading_wwt') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject(' Request Loading Limbah WWT')
                ->view('maintenance.done_mails_loading_wwt');
        }

        if ($this->remark == 'resume_notif_wwt') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject(' Notifikasi Masa Simpan')
                ->view('maintenance.mails_notif_wwt');
        }

        if ($this->remark == 'hrq') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Unanswered HR Question & Answer (HR Q&A)')->view('mails.hrq');
        }

        if ($this->remark == 'update_employee') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Employee Update Data Notification')->view('mails.update_employee');
        }

        if ($this->remark == 'purchase_requisition') {
            if ($this->data[0]->file_pdf != null && $this->data[0]->file != null) {
                $all_file = json_decode($this->data[0]->file);

                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Purchase Requisition (購入申請)')
                    ->view('mails.purchase_requisition')
                    ->attach(public_path('files/pr/' . $all_file[0]))
                    ->attach(public_path('pr_list/' . $this->data[0]->file_pdf));
            } else if ($this->data[0]->file_pdf != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Purchase Requisition (購入申請)')
                    ->view('mails.purchase_requisition')
                    ->attach(public_path('pr_list/' . $this->data[0]->file_pdf));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Purchase Requisition (購入申請)')
                    ->view('mails.purchase_requisition');
            }
        }

        if ($this->remark == 'canteen_purchase_requisition') {
            if ($this->data[0]->file_pdf != null && $this->data[0]->file != null) {
                $all_file = json_decode($this->data[0]->file);

                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Canteen Purchase Requisition (購入申請)')
                    ->view('mails.canteen_purchase_requisition')
                    ->attach(public_path('files/pr_kantin/' . $all_file[0]))
                    ->attach(public_path('kantin/pr_list/' . $this->data[0]->file_pdf));
            } else if ($this->data[0]->file_pdf != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Canteen Purchase Requisition (購入申請)')
                    ->view('mails.canteen_purchase_requisition')
                    ->attach(public_path('kantin/pr_list/' . $this->data[0]->file_pdf));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Canteen Purchase Requisition (購入申請)')
                    ->view('mails.canteen_purchase_requisition');
            }
        }

        if ($this->remark == 'purchase_order') {
            if ($this->data[0]->file_pdf != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Purchase Order (発注依頼)')
                    ->view('mails.purchase_order')
                    ->attach(public_path('po_list/' . $this->data[0]->file_pdf));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Purchase Order (発注依頼)')
                    ->view('mails.purchase_order');
            }
        }

        if ($this->remark == 'canteen_purchase_order') {
            if ($this->data[0]->file_pdf != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Canteen Purchase Order (食堂の購入依頼)')
                    ->view('mails.canteen_purchase_order')
                    ->attach(public_path('kantin/po_list/' . $this->data[0]->file_pdf));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Canteen Purchase Order (食堂の購入依頼)')
                    ->view('mails.canteen_purchase_order');
            }
        }

        if ($this->remark == 'vendor_canteen_purchase_order') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Purchase Order ' . $this->data[0]->no_po . ' ' . $this->data[0]->supplier_name)
                ->view('mails.vendor_canteen_purchase_order')
                ->attach(public_path('kantin/po_list/' . $this->data[0]->file_pdf));
        }

        if ($this->remark == 'vendor_reminder_delivery_equipment') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Reminder Delivery ' . $this->data[0]->supplier_name)
                ->view('mails.vendor_reminder_delivery_equipment');
        }

        if ($this->remark == 'penerimaan_barang_equipment') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Notifikasi Penerimaan Barang')
                ->view('mails.penerimaan_barang_equipment');
        }

        if ($this->remark == 'new_agreement') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('New Agreement (新規契約)')
                ->view('mails.new_agreement');

            // ->attach(public_path('files/agreements/'.$this->data[0]->file_name))
        }

        if ($this->remark == 'update_agreement') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Update Agreement (契約更新)')
                ->view('mails.new_agreement');

            // ->attach(public_path('files/agreements/'.$this->data[0]->file_name))
        }

        if ($this->remark == 'notif_agreement') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Expiration Notification Agreement (契約切れの通知)')
                ->view('mails.notif_agreement');
        }

        if ($this->remark == 'notif_regulation') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Regulation Implementation')
                ->view('mails.notif_regulation');
        }

        if ($this->remark == 'investment') {
            if ($this->data[0]->pdf != null && $this->data[0]->file != null) {
                $all_file = json_decode($this->data[0]->file);

                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Investment - Expense Application (投資・経費申請)')
                    ->view('mails.investment')
                    ->attach(public_path('files/investment/' . $all_file[0]))
                    ->attach(public_path('investment_list/' . $this->data[0]->pdf));
            }
            if ($this->data[0]->pdf != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Investment - Expense Application (投資・経費申請)')
                    ->view('mails.investment')
                    ->attach(public_path('investment_list/' . $this->data[0]->pdf));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Investment - Expense Application (投資申請)')
                    ->view('mails.investment');
            }
        }

        if ($this->remark == 'payment_request') {
            if ($this->data[0]->pdf != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Payment Request (支払リクエスト)')
                    ->view('mails.payment_request')
                    ->attach(public_path('payment_list/' . $this->data[0]->pdf));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Payment Request (支払リクエスト)')
                    ->view('mails.payment_request');
            }
        }

        if ($this->remark == 'suspend') {
            if ($this->data[0]->pdf != null && $this->data[0]->file != null) {

                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Request Cash and Suspense Payment (サスペンス支払い)')
                    ->view('mails.suspend')
                    ->attach(public_path('files/cash_payment/suspend/' . $this->data[0]->file))
                    ->attach(public_path('cash_list/suspend/' . $this->data[0]->pdf));
            } else if ($this->data[0]->pdf != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Request Cash and Suspense Payment (サスペンス支払い)')
                    ->view('mails.suspend')
                    ->attach(public_path('cash_list/suspend/' . $this->data[0]->pdf));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Request Cash and Suspense Payment (サスペンス支払い)')
                    ->view('mails.suspend');
            }
        }

        if ($this->remark == 'suspend_money') {
            if ($this->data[0]->pdf != null && $this->data[0]->file != null) {

                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Suspense Money Notification')
                    ->view('mails.suspend_money')
                    ->attach(public_path('files/cash_payment/suspend/' . $this->data[0]->file))
                    ->attach(public_path('cash_list/suspend/' . $this->data[0]->pdf));
            } else if ($this->data[0]->pdf != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Suspense Money Notification')
                    ->view('mails.suspend_money')
                    ->attach(public_path('cash_list/suspend/' . $this->data[0]->pdf));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Suspense Money Notification')
                    ->view('mails.suspend_money');
            }

        }

        if ($this->remark == 'settlement') {
            if ($this->data[0]->pdf != null && $this->data[0]->file != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Settlement Payment (精算)')
                    ->view('mails.settlement')
                    ->attach(public_path('files/cash_payment/settlement/' . $this->data[0]->file))
                    ->attach(public_path('cash_list/settlement/' . $this->data[0]->pdf));
            } else if ($this->data[0]->pdf != null) {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Settlement Payment (精算)')
                    ->view('mails.settlement')
                    ->attach(public_path('cash_list/settlement/' . $this->data[0]->pdf));
            } else {
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Settlement Payment (精算)')
                    ->view('mails.settlement');
            }
        }

        if ($this->remark == 'settlement_user') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Settlement Payment By User (精算)')
                ->view('mails.settlement_user');
        }

        if ($this->remark == 'translation_assignment') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Translation Assignment')
                ->view('translation.translation_assignment_mail');
        }

        if ($this->remark == 'translation_request') {
            if ($this->data['filenames'] != null) {

                $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Translation Request')
                    ->view('translation.translation_request_mail');

                for ($i = 0; $i < count($this->data['filenames']); $i++) {
                    $email->attach(public_path('files/translation/' . $this->data['filenames'][$i]));
                }

                return $email;
            } else {
                $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Translation Request')
                    ->view('translation.translation_request_mail');

                return $email;
            }
        }

        if ($this->remark == 'translation_result') {
            if ($this->data['filenames'] != null) {

                $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Translation Result')
                    ->view('translation.translation_result_mail');

                for ($i = 0; $i < count($this->data['filenames']); $i++) {
                    $email->attach(public_path('files/translation/' . $this->data['filenames'][$i]));
                }

                return $email;
            } else {
                $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->subject('Translation Result')
                    ->view('translation.translation_result_mail');

                return $email;
            }
        }

        if ($this->remark == 'sakurentsu') {
            if ($this->data[0]->position == 'interpreter' || $this->data[0]->position == 'interpreter2') {
                if ($this->data[0]->file != null) {
                    $all_file = json_decode($this->data[0]->file);

                    $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                        ->priority(1)
                        ->subject('Sakurentsu (作連通)')
                        ->view('mails.sakurentsu');

                    for ($i = 0; $i < count($all_file); $i++) {
                        $email->attach(public_path('files/translation/' . $all_file[$i]));
                    }

                    return $email;
                }
            } else if ($this->data[0]->position == 'PC1' || $this->data[0]->position == 'PC2') {
                if ($this->data[0]->file_translate != null) {
                    $all_file = json_decode($this->data[0]->file_translate);

                    $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                        ->priority(1)
                        ->subject('Sakurentsu (作連通)')
                        ->view('mails.sakurentsu');

                    for ($i = 0; $i < count($all_file); $i++) {
                        $email->attach(public_path('files/translation/' . $all_file[$i]));
                    }

                    return $email;
                }
            } else if ($this->data[0]->position == 'PIC' || $this->data[0]->position == 'PIC2') {
                if ($this->data[0]->category == 'Not Related') {
                    $all_file = json_decode($this->data[0]->file);
                } else {
                    $all_file = json_decode($this->data[0]->file_translate);
                }

                $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                    ->priority(1)
                    ->subject('Sakurentsu (作連通)')
                    ->view('mails.sakurentsu');

                for ($i = 0; $i < count($all_file); $i++) {
                    $email->attach(public_path('files/translation/' . $all_file[$i]));
                }

                if (isset($this->data[0]->trial_file)) {
                    $trial_file = json_decode($this->data[0]->trial_file);
                    for ($a = 0; $a < count($trial_file); $a++) {
                        $email->attach(public_path('uploads/sakurentsu/trial_req/' . $trial_file[$a]));
                    }
                }

                if ($this->data[0]->additional_file) {
                    $file_add = explode(',', $this->data[0]->additional_file);

                    for ($i = 0; $i < count($file_add); $i++) {
                        $email->attach(public_path('uploads/sakurentsu/add_file/' . $file_add[$i]));
                    }
                }

                return $email;
            }
        }

        if ($this->remark == '3m_approval') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject($this->data['subject'])->view('mails.three_M_approval');
        }

        if ($this->remark == '3m_document') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('3M Document(s) Requirement (3M書類の条件)')->view('mails.three_M_document');
        }

        if ($this->remark == '3m_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('3M Reminder (3M変更リマインダー)')->view('mails.three_M_reminder');
        }

        if ($this->remark == '3m_reminder_document') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('3M Document(s) Requirement (3M書類の条件)')->view('mails.three_M_reminder_document');
        }

        if ($this->remark == '3m_specials') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Kebutuhan 3M Item Khusus (??)')->view('mails.three_M_item_khusus');
        }

        if ($this->remark == 'trial_approval') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)
                ->subject('Trial Request Form (試作依頼書)')
                ->view('mails.trial_request_approval')
                ->attach(public_path('uploads/sakurentsu/trial_req/report/Report_' . $this->data['datas']->form_number . '.pdf'));

            if (isset($this->data['datas']->att)) {
                $trial_file = explode(',', $this->data['datas']->att);
                for ($a = 0; $a < count($trial_file); $a++) {
                    $email->attach(public_path('uploads/sakurentsu/trial_req/att/' . $trial_file[$a]));
                }
            }

            if (isset($this->data['datas']->file)) {
                $sakurentsu = str_replace('[', '', $this->data['datas']->file);
                $sakurentsu = str_replace(']', '', $sakurentsu);
                $sakurentsu = str_replace('"', '', $sakurentsu);
                $sakurentsu = explode(',', $sakurentsu);

                for ($a = 0; $a < count($sakurentsu); $a++) {
                    $email->attach(public_path('files/translation/' . $sakurentsu[$a]));
                }
            }

            if (isset($this->data['datas']->file_translate)) {
                $sakurentsu_trans = str_replace('[', '', $this->data['datas']->file_translate);
                $sakurentsu_trans = str_replace(']', '', $sakurentsu_trans);
                $sakurentsu_trans = str_replace('"', '', $sakurentsu_trans);
                $sakurentsu_trans = explode(',', $sakurentsu_trans);
                for ($a = 0; $a < count($sakurentsu_trans); $a++) {
                    $email->attach(public_path('files/translation/' . $sakurentsu_trans[$a]));
                }
            }

            return $email;
        }

        if ($this->remark == 'transfer_budget') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Transfer Budget (予算の流用)')
                ->view('mails.transfer_budget');
        }

        if ($this->remark == 'chemical_spk') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Verify Maintenance Job Order (保全班作業依頼の確認)')->view('mails.verify_spk');
        }

        if ($this->remark == 'apar') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Verify APAR Purchase Requisition (消火器購入依頼の確認)')->view('mails.verify_spk');
        }

        if ($this->remark == 'chemical_not_input') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Input Production Result (Controlling Chart) Reminder (生産高の記入リマインダー（管理チャート）)')
                ->view('mails.chemical_not_input');
        }

        if ($this->remark == 'safety_shoes') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Safety Shoes (安全靴)')
                ->view('mails.safety_shoes');
        }

        if ($this->remark == 'safety_shoes_request') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Safety Shoes Request (安全靴依頼)')
                ->view('mails.safety_shoes_request');
        }

        if ($this->remark == 'spk_urgent') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Maintenance SPK Urgent Notification (保全班の作業依頼書緊急通知)')
                ->view('mails.maintenance_urgent');
        }

        if ($this->remark == 'temperature') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Abnormal Employee Temperature (異常体温の従業員)')
                ->view('mails.temperature');
        }

        if ($this->remark == 'tools_order') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Tools Need Order')
                ->view('mails.tools_order');
        }

        if ($this->remark == 'qa_incoming_check') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('YMPI QA Incoming Check Report')
                ->view('mails.qa_incoming_check');
        }

        if ($this->remark == 'mutasi_satu') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Approval Mutasi Satu Departemen (課内人事異動の承認)')
                ->view('mails.mutasi_satu');
        }

        if ($this->remark == 'done_mutasi_satu') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Approval Mutasi Satu Departemen (課内人事異動の承認)')
                ->view('mails.done_mutasi_satu');
            // ->attach(public_path('mutasi/satu_departemen/Mutasi Satu Departemen - '.$this->data[0]->nama).'.xls');
        }

        // if($this->remark == 'absen_done_mutasi_satu'){
        //     return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
        //     ->priority(1)
        //     ->subject('Aproval Mutasi Satu Departemen')
        //     ->view('mails.done_mutasi_satu')
        //     ->attach(public_path('mutasi/satu_departemen/Mutasi Satu Departemen - '.$this->data[0]->id).'.xls');
        // }

        if ($this->remark == 'rejected_mutasi') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Approval Mutasi Satu Departemen (課内人事異動の承認)')
                ->view('mails.rejected_mutasi');
        }

        if ($this->remark == 'mutasi_ant') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Approval Mutasi Antar Departemen (異なるセクションへの人事異動の承認)')
                ->view('mails.mutasi_antar');
        }

        if ($this->remark == 'mgr_hrga') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Approval Mutasi Antar Departemen (異なるセクションへの人事異動の承認)')
                ->view('mails.mutasi_mgr_hrga');
        }

        if ($this->remark == 'done_mutasi_ant') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Approval Mutasi Antar Departemen (異なるセクションへの人事異動の承認)')
                ->view('mails.done_mutasi_antar');
            // ->attach(public_path('mutasi/antar_departemen/Mutasi Antar Departemen - '.$this->data[0]->nama).'.xls');
        }

        if ($this->remark == 'rejected_mutasi_ant') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Approval Mutasi Antar Departemen (異なるセクションへの人事異動の承認)')
                ->view('mails.rejected_mutasi_antar');
        }

        if ($this->remark == 'send_email') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
            // ->subject('MIRAI APPROVAL (MIRAI 承認システム)')
                ->subject('【Approval】 ' . $this->data['appr_sends']['judul'] . ' ' . $this->data['appr_sends']['jd_japan'] . '')
                ->view('mails.send_email')
                ->attach(public_path('adagio/' . $this->data['appr_sends']['file']))
                ->attach(public_path('adagio/ttd/' . $this->data['appr_sends']['file']));
        }

        if ($this->remark == 'send_email_done') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
            // ->subject('MIRAI APPROVAL (MIRAI 承認システム)')
            // ->subject('【Approval】 ' . $this->data['appr_sends']['description'] . ' ' . $this->data['appr_sends']['jd_japan'] . '')
                ->subject('【' . $this->data['header'] . '】 ' . $this->data['appr_sends']['judul'] . ' ' . $this->data['appr_sends']['jd_japan'] . '')
                ->view('mails.send_email_done')
                ->attach(public_path('adagio/' . $this->data['appr_sends']['file']))
                ->attach(public_path('adagio/ttd/' . $this->data['appr_sends']['file']));
        }

        if ($this->remark == 'request_foreman') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Request ManPower Kebutuhan Produksi')
                ->view('mails.recruitment_manager');
        }

        if ($this->remark == 'end_contract') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Manpower End Contract')
                ->view('mails.end_contract');
        }

        if ($this->remark == 'barang_mis') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Penerimaan Barang MIS (MIS貨物の受入)')
                ->view('mails.barang_mis');
        }

        if ($this->remark == 'request_penarikan') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Request Penarikan Scrap')
                ->view('mails.request_penarikan');
        }

        if ($this->remark == 'email_hiyarihatto') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('【Form Hiyari Hatto】 - ' . $this->data[0]->request_id)
                ->view('mails.email_hiyarihatto');
            // ->attach(public_path('data_file/pengisian_hh/' . $this->data[0]->request_id . '.pdf'));
        }

        if ($this->remark == 'notif_karyawan_bermasalah') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('【Indikasi Karyawan Bermasalah】')
                ->view('mails.notif_karyawan_bermasalah');
        }

        if ($this->remark == 'email_pengajuan_bpjskes') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('【Pengajuan Anggota BPJSKES】')
                ->view('hr_data.mails_confirmation_bpjs');
        }

        if ($this->remark == 'notif_penanganan') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('【Konseling Karyawan Bermasalah】')
                ->view('mails.konseling_karyawan_bermasalah')
                ->attach(public_path('hr/konseling_pelanggaran/' . $this->data['select_emp'][0]->id . '_bukti_konseling.jpg'));
        }

        if ($this->remark == 'penanganan_selesai') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('【Penanganan Form Hiyari Hatto】 - ' . $this->data[0]->request_id)
                ->view('mails.penanganan_selesai');
            // ->attach(public_path('data_file/pengisian_hh/' . $this->data[0]->request_id . '.pdf'));
        }

        if ($this->remark == 'ky_sosialisasi_ulang') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Kiken Yochi Notification [' . $this->data[0]->nama_tim . ']')->view('mails.ky_notif');
        }

        if ($this->remark == 'reminder_ky') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Reminder Belum Melaksanakan KY')
                ->view('standardization.ky_hh.email_notification');
        }

        if ($this->remark == 'reminder_employee_birthday') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Reminder Employee Birthday')
                ->view('human_resource.notification_employee_birthday');
        }

        if ($this->remark == 'hr_notif') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Notifikasi Karyawan Salah Shift')->view('mails.hr_notif');
        }

        if ($this->remark == 'highest_covid') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Highest Survey Covid Report (最大スコアのコロナサーベイリポート)')
                ->view('mails.highest_covid');
        }

        if ($this->remark == 'fixed_asset_registrations') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Fixed Asset Registration (固定資産登録)')
                ->view('mails.fixed_asset');

            if (isset($this->data['att'])) {
                $att = explode(',', $this->data['att']->att);

                for ($i = 0; $i < count($att); $i++) {
                    $email->attach(public_path('files/fixed_asset/' . $att[$i]));
                }
            }

            if ($this->data['assets']['sap_file'] && ($this->data['status'] == 'APPROVAL MANAGER FA' || $this->data['status'] == 'RECEIVE FA')) {
                $email->attach(public_path('files/fixed_asset/sap_file/' . $this->data['assets']['sap_file']));
            }

            return $email;
        }

        if ($this->remark == 'fixed_asset_cip_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Reminder Fixed Asset CIP')
                ->view('mails.fixed_asset_cip_reminder');
        }

        if ($this->remark == 'fixed_asset_cip_approval') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Confirmation Form Fixed Asset CIP')
                ->view('mails.fixed_asset_cip');
        }

        if ($this->remark == 'fixed_asset_cip_transfer') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Transfer Form Fixed Asset CIP')
                ->attach(public_path('files/fixed_asset/sap_file/' . $this->data['data'][0]['sap_file']))
                ->view('mails.fixed_asset_cip_transfer');
        }

        if ($this->remark == 'fixed_asset_invoice') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Fixed Asset Invoice (固定資産インボイス)')
                ->view('mails.fixed_asset_invoice');

            $att = explode(',', $this->data['att']);

            for ($i = 0; $i < count($att); $i++) {

                $email->attach(public_path('files/fixed_asset/' . $att[$i]));
            }

            return $email;
        }

        if ($this->remark == 'fixed_asset_transfer') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Fixed Asset Transfer')
                ->view('mails.fixed_asset_transfer')
                ->attach(public_path('files/fixed_asset/report_transfer/Transfer_' . $this->data['datas']['form_number'] . '.pdf'));

            return $email;
        }

        if ($this->remark == 'fixed_asset_label') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Fixed Asset Label Request (固定資産のラベルリクエスト)')
                ->view('mails.fixed_asset_label');

            return $email;
        }

        if ($this->remark == 'fixed_asset_disposal') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Fixed Asset Disposal (固定資産の処分)')
                ->view('mails.fixed_asset_disposal')
                ->attach(public_path('files/fixed_asset/report_disposal/Disposal_' . $this->data['datas']['form_number'] . '.pdf'));

            if (isset($this->data['status'])) {
                $email = $email->attach(public_path('files/fixed_asset/disposal_payment/' . $this->data['datas']['form_number'] . '.pdf'));
            }

            if ($this->data['datas']['mode'] == 'SALE') {
                $email = $email->attach(public_path('files/fixed_asset/disposal_quotation/' . $this->data['datas']['quotation_file']));
            }

            return $email;
        }

        if ($this->remark == 'fixed_asset_scrap') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Fixed Asset Scrap Disposal Report (固定資産廃却処分報告)')
                ->view('mails.fixed_asset_scrap')
                ->attach(public_path('files/fixed_asset/report_disposal_scrap/DisposalScrap_' . $this->data['datas']['form_number'] . '.pdf'));

            return $email;
        }

        if ($this->remark == 'fixed_asset_missing') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Fixed Asset Missing Report (固定資産廃却処分報告)')
                ->view('mails.fixed_asset_missing')
                ->attach(public_path('files/fixed_asset/report_missing/Missing_' . $this->data['datas']['form_number'] . '.pdf'));

            return $email;
        }

        if ($this->remark == 'fixed_asset_check') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Fixed Asset Check Approval')
                ->view('mails.fixed_asset_check_approval');

            $all_file = $this->data['att'];
            if (count($all_file) > 0) {
                for ($i = 0; $i < count($all_file); $i++) {
                    $email->attach(public_path('files/fixed_asset/asset_report/' . $all_file[$i]));
                }
            }

            return $email;
        }

        if ($this->remark == 'fixed_asset_sp_letter') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Special Letter for Long Outstanding CIP')
                ->view('mails.fixed_asset_sp_letter');

            $all_file = $this->data['att'];
            if (count($all_file) > 0) {
                for ($i = 0; $i < count($all_file); $i++) {
                    $email->attach(public_path('files/fixed_asset/asset_report/' . $all_file[$i]));
                }
            }

            return $email;
        }

        if ($this->remark == 'audit_kanban') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Audit Kanban (かんばん監査)')
                ->view('mails.audit_kanban');
        }

        if ($this->remark == 'audit_guidance') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Schedule Laporan Audit IK (作業手順書監査)')
                ->view('mails.audit_guidance');
        }

        if ($this->remark == 'notification_oxymeter') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Notification Oxygen Rate Below 95 (酸素透過率95以下になる時の通知)')
                ->view('mails.oxygen');
        }

        if ($this->remark == 'leave_request') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Persetujuan Surat Izin Keluar (外出申請書)')
                ->view('mails.surat_ijin_meninggalkan');
        }

        if ($this->remark == 'leave_request_shift') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Pemberitahuan Surat Izin Keluar (外出申請書)')
                ->view('mails.surat_ijin_meninggalkan_shift');
        }

        if ($this->remark == 'leave_request_reject') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Surat Izin Keluar Ditolak')
                ->view('mails.surat_ijin_meninggalkan_reject');
        }

        if ($this->remark == 'car_injection') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Information Injection Visual Check (成形上がり外観検査の情報)')
                ->view('mails.car_injection');
        }
        if ($this->remark == 'visual_check') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Injection Visual Check Not Done')
                ->view('mails.visual_check');
        }

        if ($this->remark == 'skill_map') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Skill Map Reminder')
                ->view('mails.skill_map');
        }

        if ($this->remark == 'injection_cleaning') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Injection Cleaning Not Done')
                ->view('mails.injection_cleaning');
        }

        if ($this->remark == 'request_kanban_mt_urgent') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Approval Request Kanban Material Urgent')
                ->view('mails.persetujuan_kanban_mt_urgent');
        }

        if ($this->remark == 'kecelakaan_all') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Informasi Kecelakaan Kerja Yamaha Group (ヤマハグループの労働災害情報)')
                ->view('mails.kecelakaan_all')
                ->attach(public_path('kecelakaan_list/kecelakaan_kerja/kecelakaan ' . date('d-M-y', strtotime($this->data[0]->date_incident)) . ' ' . $this->data[0]->location . '.pdf'));
        }

        if ($this->remark == 'kecelakaan_foreman') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Yokotenkai Kecelakaan Kerja Yamaha Group (ヤマハグループの交通事故情報の横展開)')
                ->view('mails.kecelakaan_foreman')
                ->attach(public_path('kecelakaan_list/kecelakaan_kerja/kecelakaan ' . date('d-M-y', strtotime($this->data[0]->date_incident)) . ' ' . $this->data[0]->location . '.pdf'));
        }

        if ($this->remark == 'kecelakaan_lalu_lintas') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Informasi Kecelakaan Lalu Lintas YMPI')
                ->view('mails.kecelakaan_lalu_lintas')
                ->attach(public_path('kecelakaan_list/kecelakaan_lalu_lintas/kecelakaan ' . date('d-M-y', strtotime($this->data[0]->date_incident)) . ' ' . $this->data[0]->location . '.pdf'));
        }

        if ($this->remark == 'audit_ng_jelas') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Temuan Audit NG Jelas QA')
                ->view('mails.audit_ng_jelas');
        }

        if ($this->remark == 'audit_special_process') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Temuan Audit Proses Khusus (特殊工程の監査)')
                ->view('mails.audit_special_process');
        }

        if ($this->remark == 'audit_special_process_cek') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Cek Efektifitas Temuan Audit Proses Khusus (特殊工程の監査)')
                ->view('mails.audit_special_process_cek');
        }

        if ($this->remark == 'audit_special_process_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Audit Proses Khusus (特殊工程の監査リマインダー)')
                ->view('mails.audit_special_process_reminder');
        }

        if ($this->remark == 'audit_ik_qa_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Karyawan Belum Dilakukan Audit IK Leader QA')
                ->view('mails.audit_ik_qa_reminder');
        }

        if ($this->remark == 'audit_packing') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Temuan Audit Packing (梱包監査)')
                ->view('mails.audit_packing');
        }

        if ($this->remark == 'audit_fg_ei') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Temuan Audit FG / KD (梱包監査)')
                ->view('mails.audit_fg_ei');
        }

        if ($this->remark == 'handling_audit_packing') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Penanganan Audit Packing (梱包監査)')
                ->view('mails.handling_audit_packing');
        }

        if ($this->remark == 'handling_audit_cpar_car') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Penanganan Audit CPAR & CAR (品証 是正予防策・是正策監視 監査)')
                ->view('mails.handling_audit_cpar_car');
        }

        if ($this->remark == 'tbm_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('TBM Reminder')
                ->view('mails.tbm_reminder');
        }

        if ($this->remark == 'handling_audit_special_process') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Penanganan Audit Proses Khusus (特殊工程の監査リマインダー)')
                ->view('mails.handling_audit_special_process');
        }

        if ($this->remark == 'audit_qc_koteihyo') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Temuan Audit QC Koteihyo (QC工程表 監査)')
                ->view('mails.audit_qc_koteihyo');
        }

        if ($this->remark == 'audit_qc_koteihyo_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Audit QC Koteihyo (QC工程表 監査リマインダー)')
                ->view('mails.audit_qc_koteihyo_reminder');
        }

        if ($this->remark == 'handling_audit_qc_koteihyo') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Penanganan Audit QC Koteihyo (QC工程表 監査リマインダー)')
                ->view('mails.handling_audit_qc_koteihyo');
        }

        if ($this->remark == 'audit_ng_jelas_handling') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Penanganan Audit NG Jelas QA')
                ->view('mails.audit_ng_jelas_handling');
        }

        if ($this->remark == 'audit_ng_jelas_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Audit NG Jelas QA')
                ->view('mails.audit_ng_jelas_reminder');
        }

        if ($this->remark == 'car_document') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Perubahan Document CAR QA')
                ->view('mails.car_document');
        }

        if ($this->remark == 'middle_audit_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Audit Kebocoran Compressor')
                ->view('mails.middle_audit_reminder');
        }

        if ($this->remark == 'daily_attendance') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('YMPI Daily Attendance Summary (YMPI日常出勤まとめ)')
                ->view('mails.daily_attendance');
        }

        if ($this->remark == 'cek_fisik_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Cek Fisik')
                ->view('mails.cek_fisik_reminder');
        }

        if ($this->remark == 'mcu_schedule_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Reminder Medical Check Up')
                ->view('mails.mcu_schedule_reminder');
        }

        if ($this->remark == 'qa_certificate') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('QA Kensa Cetificate Approval (品質保証検査認定承認)')
                ->view('mails.qa_certificate');
        }

        if ($this->remark == 'qa_certificate_collective') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('QA Kensa Cetificate Approval (品質保証検査認定承認)')
                ->view('mails.qa_certificate_collective');
        }

        if ($this->remark == 'qa_certificate_renewal') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('QA Kensa Cetificate Renewal Reminder (品質保証検査認証リマインダー)')
                ->view('mails.qa_certificate_renewal');
        }

        if ($this->remark == 'qa_certificate_expired') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('QA Kensa Cetificate Expired Reminder (品質保証検査認証リマインダー)')
                ->view('mails.qa_certificate_expired');
        }

        if ($this->remark == 'qa_certificate_submission_new') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('New Kensa Cetificate Submission ()')
                ->view('mails.qa_certificate_submission_new');
        }

        if ($this->remark == 'qa_certificate_submission_non') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Kensa Certificate Non-Active Request ()')
                ->view('mails.qa_certificate_submission_non');
        }

        if ($this->remark == 'qa_certificate_collective_inprocess') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('QA Kensa Cetificate Inprocess Approval (工程内検査認証の承認)')
                ->view('mails.qa_certificate_collective_inprocess');
        }

        if ($this->remark == 'mis_complaint') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('MIS Complaint Verification')
                ->view('mails.mis_complaint');
        }

        if ($this->remark == 'car_rc_assy') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Corrective Action Report Audit QA Recorder Assembly')
                ->view('mails.car_rc_assy');
        }

        if ($this->remark == 'car_rc_inj') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Corrective Action Report Audit QA Recorder Injection')
                ->view('mails.car_rc_inj');
        }

        if ($this->remark == 'car_pn_initial') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Corrective Action Report Audit QA Pianica Initial')
                ->view('mails.car_pn_initial');
        }

        if ($this->remark == 'car_pn_final') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Corrective Action Report Audit QA Pianica Final')
                ->view('mails.car_pn_final');
        }

        if ($this->remark == 'audit_screw') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Audit Screw Information')
                ->view('mails.audit_screw');
        }

        if ($this->remark == 'report_kanagata') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Pelaporan Kanagata Retak Approval')
                ->view('mails.mails_pelaporan_kanagata_retak');
        }

        if ($this->remark == 'report_kanagata_reject') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Pelaporan Kanagata Rejected')
                ->view('mails.reject_pelaporan_kanagata_retak');
        }
        if ($this->remark == 'report_kanagata_done') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Pelaporan Kanagata Finished')
                ->view('mails.done_pelaporan_kanagata_retak');
        }

        if ($this->remark == 'sga_approval') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Small Group Activity Approval (スモールグループ活動の承認)')
                ->view('mails.sga_approval');
        }

        if ($this->remark == 'ypm_approval') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('YPM Contest Approval (YPMコンテストの承認)')
                ->view('mails.ypm_approval');
        }

        if ($this->remark == 'ypm_reject') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('YPM Contest Approval (YPMコンテストの承認)')
                ->view('mails.ypm_reject');
        }

        if ($this->remark == 'sga_approval_final') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Small Group Activity Approval (スモールグループ活動の承認)')
                ->view('mails.sga_approval_final');
        }

        if ($this->remark == 'sga_reject_final') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Small Group Activity Rejection (SGAを却下)')
                ->view('mails.sga_reject_final');
        }

        if ($this->remark == 'holiday_check') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Pengecekan Safety Saat Akan Libur')
                ->view('mails.holiday_check');
        }

        if ($this->remark == 'lifetime_limit') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject(ucwords($this->data['category']) . ' Repair Information')
                ->view('mails.lifetime_limit');
        }

        if ($this->remark == 'packing_documentation') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject($this->data['title'])->view('mails.packing_documentation');
        }

        if ($this->remark == 'reminder_kendaraan_expired') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->subject('Reminder Expired SIM & STNK')
                ->view('mails.reminder_kendaraan_expired');
        }

        if ($this->remark == 'notifikasi_sds') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('SDS Update Expaired')
                ->view('mails.mails_expaired_sds');

            if (count($this->data['datas']) > 0) {
                for ($i = 0; $i < count($this->data['datas']); $i++) {
                    $email->attach(public_path('files/chemical/documents/' . $this->data['datas'][$i]->file_name_sds));

                }

            }
            return $email;
        }
        if ($this->remark == 'notifikasi_progress_sds') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('SDS Update Expaired NEW')
                ->view('mails.mails_progress_sds');

            if (count($this->data['datas']) > 0) {
                for ($i = 0; $i < count($this->data['datas']); $i++) {
                    $email->attach(public_path('files/chemical/documents/' . $this->data['datas'][$i]->file_name_asli));

                }

            }

            return $email;
        }

        if ($this->remark == 'hr_evaluation') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Form Evaluasi Karyawan Kontrak')->view('human_resource.appraisal.evaluation_approval_email');
            // $email->attach(public_path('files/chemical/documents/' . $this->data['datas'][$i]->file_name_asli));
            return $email;

        }

        if ($this->remark == 'tools_reminder') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->priority(1)->subject('Reminder Pembelian Tools / Equipment')->view('mails.tools_reminder');
        }

        if ($this->remark == 'approval_ejor') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject($this->data['subject'])->view('production_engineering.ejor.approval_email');
            if (isset($this->data['datas']->attachment)) {
                $att = explode(',', $this->data['datas']->attachment);
                foreach ($att as $ats) {
                    $email->attach(public_path('files/ejor/att/' . $ats));
                }
            }

            $email->attach(public_path('files/ejor/form/' . $this->data['datas']->form_id . '.pdf'));

            return $email;
        }

        if ($this->remark == 'verify_ejor') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject($this->data['subject'])->view('production_engineering.ejor.verify_email');
            if (isset($this->data['datas']->attachment)) {
                $att = explode(',', $this->data['datas']->attachment);
                foreach ($att as $ats) {
                    $email->attach(public_path('files/ejor/att/' . $ats));
                }
            }

            $email->attach(public_path('files/ejor/form/' . $this->data['datas']->form_id . '.pdf'));

            $att2 = explode(',', $this->data['evidence']->attachment);
            foreach ($att2 as $ats2) {
                $email->attach(public_path('files/ejor/evidence/' . $ats2));
            }

            return $email;
        }

        if ($this->remark == 'reminder_sanding') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Kualitas Buruk Sanding CVM')
                ->view('kpp.sanding.email_reminder_cvm');
        }

        if ($this->remark == 'document_control_system') {
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject($this->data['email_subject'])
                ->view('mails.document_controlling_system');

            if (isset($this->data['data_docs'])) {
                foreach ($this->data['data_docs'] as $attachment) {
                    $email->attach(public_path($attachment->file_url));
                }
            }
            return $email;
        }

        if($this->remark == 'pengajuan_approval_presdir'){
            $email = $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject($this->data['email_subject'])                
                ->view('mails.pengajuan_approval_presdir');
        }

        if ($this->remark == 'daily_ticket') {
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Daily Ticket Progress')
                ->view('mails.daily_ticket_progress');
        }        
    }
}
