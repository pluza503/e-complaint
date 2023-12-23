<?php
/**
 * @filesource modules/repair/views/receive.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Repair\Receive;

use Kotchasan\Html;
use Kotchasan\Language;

/**
 * module=repair-receive
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * เพิ่ม-แก้ไข แจ้งซ่อม
     *
     * @param object $index
     * @param array $login
     *
     * @return string
     */
    public function render($index, $login)
    {
        $form = Html::create('form', array(
            'id' => 'setup_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/repair/model/receive/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true
        ));
        $fieldset = $form->add('fieldset', array(
            'titleClass' => 'icon-tools',
            'title' => '{LNG_Repair job description}'
        ));
        $groups = $fieldset->add('groups', array(
            //'comment' => '{LNG_Find equipment by} {LNG_Equipment}, {LNG_Serial/Registration No.}'
        ));
        // type
        $groups->add('text', array(
            'id' => 'product_no',
            'labelClass' => 'g-input icon-template',
            'itemClass' => 'width50',
            'label' => '{LNG_complain_type}',
            'maxlength' => 20,
            'value' => $index->product_no
        ));
        // suptype
        $groups->add('text', array(
            'id' => 'topic',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'width50',
            'label' => '{LNG_complain_suptype}',
            'maxlength' => 64,
            'value' => $index->topic
        ));
        // complainant_type
        $fieldset->add('date', array(
            'id' => 'complainant_type',
            'labelClass' => 'g-input icon-menus',
            'itemClass' => 'item',
            'label' => '{LNG_complainant_type}',
            'options' => Language::get('Complainant_detail'),
            'value' => $user['sex']
        ));

        $groups = $fieldset->add('groups', array(
            //'comment' => '{LNG_Find equipment by} {LNG_Equipment}, {LNG_Serial/Registration No.}'
        ));
        // date
        $groups->add('date', array(
            'id' => 'topic',
            'labelClass' => 'g-input icon-calendar',
            'itemClass' => 'width50',
            'label' => '{LNG_Date}',
            //'maxlength' => 64,
            //'value' => $index->topic
        ));
        // time
        $groups->add('time', array(
            'id' => 'topic',
            'labelClass' => 'g-input icon-clock',
            'itemClass' => 'width50',
            'label' => '{LNG_Time}',
            //maxlength' => 64,
            //'value' => $index->topic
        ));

        
        $fieldset->add('text', array(
            'id' => 'product_no',
            'labelClass' => 'g-input icon-home',
            'itemClass' => 'item',
            'label' => '{LNG_Agency_name}',
            'maxlength' => 20,
            'value' => $index->product_no
        ));
        // job_description
        $fieldset->add('textarea', array(
            'id' => 'job_description',
            'labelClass' => 'g-input icon-file',
            'itemClass' => 'item',
            'label' => '{LNG_Problems and repairs details}',
            'rows' => 5,
            'value' => $index->job_description
        ));

        $fieldset->add('text', array(
            'id' => 'product_no',
            'labelClass' => 'g-input icon-create-folder',
            'itemClass' => 'item',
            'label' => '{LNG_Requirement}',
            'maxlength' => 20,
            'value' => $index->product_no
        ));

        if ($index->id == 0) {
            // comment
            $fieldset->add('text', array(
                'id' => 'comment',
                'labelClass' => 'g-input icon-comments',
                'itemClass' => 'item',
                'label' => '{LNG_Solution}',
                //'comment' => '{LNG_Note or additional notes}',
                'maxlength' => 255,
                'value' => $index->comment
            ));
            // status_id
            $fieldset->add('hidden', array(
                'id' => 'status_id',
                'value' => $index->status_id
            ));
        }

        $fieldset->add('file', array(
            'id' => 'avatar',
            'labelClass' => 'g-input icon-pdf',
            'itemClass' => 'item',
            'label' => '{LNG_add_file}',
            'comment' => '{LNG_Browse file uploaded, type :type}',
            'dataPreview' => 'avatarImage',
            'previewSrc' => $img,
            'accept' => self::$cfg->member_file_typies
        ));
        \Gcms\Controller::$view->setContentsAfter(array(
            '/:type/' => implode(', ', self::$cfg->member_file_typies)
        ));
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit'
        ));
        // submit
        $fieldset->add('submit', array(
            'id' => 'save',
            'class' => 'button save large icon-save',
            'value' => '{LNG_Save}'
        ));
        // id
        $fieldset->add('hidden', array(
            'id' => 'id',
            'value' => $index->id
        ));
        // Javascript
        $form->script('initRepairGet();');
        // คืนค่า HTML
        return $form->render();
    }
}
