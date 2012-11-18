<?php
$base = base_url();
$attributes = array('class' => 'span-15', 'id' => 'login');
?>


<?php echo form_open("auth/login", $attributes); ?>

<fieldset>
    <legend><?php echo lang('login_form') ?></legend>
    <?php
    $v_errors = validation_errors('<div>', '</div>');
    if (!empty($v_errors))
    {
        echo '<div class="error">';
        echo $v_errors;
        echo "</div>";
    }
    elseif (!empty($message))
    {
        echo '<div class="error">';
        echo $message;
        echo "</div>";
    }
    ?>
    <ol>
        <li>
            <?php
            echo form_label(lang('rr_username'), 'username');
            echo form_input('username');
            ?>
        </li>
        <li>

            <label for="password"><?php echo lang('rr_password'); ?></label>
            <?php
            echo form_password('password');
            ?>
        </li>
    </ol>
    <div class="buttons">

        <?php
        if (!empty($shib_url))
        {
            echo anchor($shib_url, '<button type="button" name="faderated" value="faderated" class="btn" onclick="window.open(\'' . $shib_url . '\',\'_self\')">' . lang('federated_access') . '</button>');
        }
        ?> 
        <button type="submit" name="submit" value="Login" class="btn"><?php echo lang('rr_local_authn'); ?></button>
    </div>
</fieldset>
<?php echo form_close(); ?>
    
