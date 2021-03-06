<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


if (!empty($error_message)) {
    echo '<div class="alert">' . $error_message . '</div>';
}

if (!empty($excluded) && is_array($excluded) && count($excluded) > 0) {
    $editlink = '';
    if (!empty($has_write_access)) {
        $editlink = '<span class="lbl lbl-disabled"><a href="' . base_url() . 'manage/arpsexcl/idp/' . $idpid . '">' . lang('rr_editarpexc') . '</a></span>';
    }
    echo '<div id="excarpslist"><b>' . lang('rr_arpexclist_title') . '</b> ' . $editlink;
    echo '<ol>';
    foreach ($excluded as $v) {
        echo '<li>' . $v . '</li>';
    }
    echo '</ol></div>';
}
?>

    <div class="row">
        <div class="small-12 columns">
            <div class="medium-3  medium-offset-9 columns end">
                <label class="hide-for-small-only"><input id="tablesearchinput" type="text"
                                                          placeholder="<?php echo lang('rr_filter'); ?>"/></label>
            </div>
        </div>
    </div>
<?php

echo '<div id="matrixloader" data-jagger-link="' . base_url() . 'reports/idpmatrix/getarpdata/' . $idpid . '" data-jagger-providerdetails="' . base_url() . 'providers/detail/show"  class="row hidden"></div>';

echo '<div id="idpmatrixdiv" class="row" style="margin-top: 20px"></div>';


echo '<div id="policyupdater" class="reveal small" data-reveal data-jagger-link="' . base_url('manage/attribute_policyajax/getattrpath/' . $idpid . '') . '">
  <h3>' . lang('confirmupdpolicy') . '</h3>
 <p class="message">' . lang('rr_tbltitle_requester') . ':  <span class="mrequester"></span><br />' . lang('attrname') . ': <span class="mattribute"></span></p>
  <div>
 ';
echo '<div class="attrflow row"></div>';
echo form_open(base_url() . 'manage/attribute_policyajax/submit_sp/' . $idpid);
echo form_input(array('name' => 'attribute', 'type' => 'hidden', 'value' => ''));
echo form_input(array('name' => 'idpid', 'type' => 'hidden', 'value' => '' . $idpid . ''));
echo form_input(array('name' => 'requester', 'type' => 'hidden', 'value' => ''));
$dropdown = $this->config->item('policy_dropdown');
echo '<div class="row">';
$dropdown = array_merge(array('' => lang('rr_select')), $dropdown);
$dropdown['100'] = lang('droprmspecpol');
echo '<div class="medium-3 columns medium-text-right"><label for="policy" class="inline" >' . lang('policy') . '</label></div>';
echo '<div class="medium-9 columns">' . form_dropdown('policy', $dropdown, '') . '</div>';
echo '</div>';
echo '<div class="row">';
$buttons = array(
    '<button type="button" name="cancel" value="cancel" class="button alert" data-close>' . lang('rr_cancel') . '</button>',
    '<div class="yes button">' . lang('btnupdate') . '</div>'
);
echo revealBtnsRow($buttons);
echo '</div>';
echo '    
</form>
  <a class="close-button" data-close>&#215;</a>
</div>';




