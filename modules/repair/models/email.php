<?php
/**
 * @filesource modules/repair/models/email.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Repair\Email;

use Kotchasan\Database\Sql;
use Kotchasan\Date;
use Kotchasan\Language;

/**
 * ส่งอีเมลไปยังผู้ที่เกี่ยวข้อง
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\KBase
{
    /**
     * ส่งอีเมลแจ้งการทำรายการ
     *
     * @param int $id
     */
    public static function send($id)
    {
        // ตรวจสอบรายการที่ต้องการ
        $q1 = \Kotchasan\Model::createQuery()
            ->select('repair_id', Sql::MAX('id', 'max_id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $order = \Kotchasan\Model::createQuery()
            ->from('repair R')
            ->join(array($q1, 'T'), 'LEFT', array('T.repair_id', 'R.id'))
            ->join('repair_status S', 'LEFT', array('S.id', 'T.max_id'))
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
            ->join('category C', 'LEFT', array(array('C.category_id', 'S.status'), array('C.type', 'repairstatus')))
            ->where(array('R.id', $id))
            ->first(
                'R.job_id',
                'R.product_no',
                'V.topic',
                'R.job_description',
                'R.create_date',
                'R.customer_id',
                'C.topic status_text',
                'S.comment',
                'S.operator_id',
                'S.status'
            );
        if ($order) {
            $lines = [];
            $emails = [];
            $name = '';
            $username = '';
            $line_uid = '';
            // ตรวจสอบรายชื่อผู้รับ
            if (self::$cfg->demo_mode) {
                // โหมดตัวอย่าง ส่งหาแอดมินเท่านั้น
                $where = array(
                    array('id', 1)
                );
            } elseif ($order->status == self::$cfg->repair_first_status) {
                // ส่งหาผู้ทำรายการและผู้ที่เกี่ยวข้อง
                $where = array(
                    array('status', 1),
                    array('permission', 'LIKE', '%,can_manage_repair,%'),
                    array('id', $order->customer_id)
                );
            } else {
                // ส่งหาผู้ทำรายการและช่างซ่อม
                $where = array(
                    array('id', [$order->customer_id, $order->operator_id])
                );
            }
            $query = \Kotchasan\Model::createQuery()
                ->select('id', 'username', 'name', 'line_uid')
                ->from('user')
                ->where(array('active', 1))
                ->andWhere($where, 'OR')
                ->cacheOn();
            foreach ($query->execute() as $item) {
                // เจ้าหน้าที่
                if ($item->username != '') {
                    $emails[] = $item->name.'<'.$item->username.'>';
                }
                if ($item->line_uid != '') {
                    $lines[] = $item->line_uid;
                }
                if ($item->id === $order->customer_id) {
                    $name = $item->name;
                    $username = $item->username;
                    $line_uid = $item->line_uid;
                }
            }
            $ret = [];
            // ข้อความ
            $msg = array(
                '{LNG_Job No.} : '.$order->job_id,
                '{LNG_Serial/Registration No.} : '.$order->product_no,
                '{LNG_Equipment} : '.$order->topic,
                '{LNG_Problems and repairs details} : '.$order->job_description,
                '{LNG_Date} : '.Date::format($order->create_date, 'd M Y'),
                '{LNG_Informer} : '.$name
            );
            if ($order->status != self::$cfg->repair_first_status) {
                $msg[] = '{LNG_Status} : '.$order->status_text;
            }
            // ข้อความของ user
            $msg = Language::trans(implode("\n", $msg));
            // ข้อความของแอดมิน
            $admin_msg = $msg."\nURL : ".WEB_URL.'index.php?module=repair-setup';
            // LINE Notify
            if (!empty(self::$cfg->line_api_key)) {
                $err = \Gcms\Line::send($admin_msg, self::$cfg->line_api_key);
                if ($err != '') {
                    $ret[] = $err;
                }
            }
            // LINE ส่วนตัว
            if (!empty($lines)) {
                \Gcms\Line::sendTo($lines, $admin_msg);
            }
            if (!empty($line_uid)) {
                \Gcms\Line::sendTo($line_uid, $msg);
            }
            if (self::$cfg->noreply_email != '') {
                // หัวข้ออีเมล
                $subject = '['.self::$cfg->web_title.'] '.$order->status_text;
                // ส่งอีเมลไปยังผู้ทำรายการเสมอ
                $err = \Kotchasan\Email::send($name.'<'.$username.'>', self::$cfg->noreply_email, $subject, nl2br($msg));
                if ($err->error()) {
                    $ret[] = strip_tags($err->getErrorMessage());
                }
                // รายละเอียดในอีเมล (แอดมิน)
                $admin_msg = nl2br($admin_msg);
                foreach ($emails as $item) {
                    // ส่งอีเมล
                    $err = \Kotchasan\Email::send($item, self::$cfg->noreply_email, $subject, $admin_msg);
                    if ($err->error()) {
                        // คืนค่า error
                        $ret[] = strip_tags($err->getErrorMessage());
                    }
                }
            }
            if (isset($err)) {
                // ส่งอีเมลสำเร็จ หรือ error การส่งเมล
                return empty($ret) ? Language::get('Your message was sent successfully') : implode("\n", array_unique($ret));
            } else {
                // ไม่มีอีเมลต้องส่ง
                return Language::get('Saved successfully');
            }
        }
        // not found
        return Language::get('Sorry, Item not found It&#39;s may be deleted');
    }
}
