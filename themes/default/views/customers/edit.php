<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('update_info'); ?></h3>
                </div>
                <div class="box-body">
                    <?php echo form_open("customers/edit/".$customer->id);?>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="code"><?= $this->lang->line("name"); ?></label>
                            <?= form_input('name', set_value('name', $customer->name), 'class="form-control input-sm" id="name"'); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label"
                                for="email_address"><?= $this->lang->line("email_address"); ?></label>
                            <?= form_input('email', set_value('email', $customer->email), 'class="form-control input-sm" id="email_address"'); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="phone"><?= $this->lang->line("phone"); ?></label>
                            <?= form_input('phone', set_value('phone', $customer->phone), 'class="form-control input-sm" id="phone"');?>
                        </div>
                        <div class="row">
                            <h3>Address Information</h3>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="code">
                                        Address
                                    </label>
                                    <?=form_input('address', set_value('phone', $customer->address), 'class="form-control input-sm kb-text" id="address" type="textarea"');?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="code">
                                        City
                                    </label>
                                    <?=form_input('city', set_value('city', $customer->city), 'class="form-control" id="city" type="text"');?>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <h3>Shirt Starch <?php echo set_radio('shirt_starch','Extra'); ?></h3>
                                <div class="form-group">
                                <?php 
                                $shirt_starch = $customer->shirt_starch;
                                ?>
                                    <label class="control-label" for="code">
                                        None 
                                    </label>
                                    <?php echo form_radio('shirt_starch', 'None', ('None' == $shirt_starch) ? TRUE : FALSE, "id='None'"); ?>

                                    <label class="control-label" for="code">
                                        Light
                                    </label>
                                    <?php echo form_radio('shirt_starch', 'Light', ('Light' == $shirt_starch) ? TRUE : FALSE, "id='Light'"); ?>

                                    <label class="control-label" for="code">
                                        Medium
                                    </label>
                                    <?php echo form_radio('shirt_starch', 'Medium', ('Medium' == $shirt_starch) ? TRUE : FALSE, "id='Medium'"); ?>

                                    <label class="control-label" for="code">
                                        Heavy
                                    </label>
                                    <?php echo form_radio('shirt_starch', 'Heavy', ('Heavy' == $shirt_starch) ? TRUE : FALSE, "id='Heavy'"); ?>
                                    <label class="control-label" for="code">
                                        Extra
                                    </label>
                                    <?php echo form_radio('shirt_starch', 'Extra', ('Extra' == $shirt_starch) ? TRUE : FALSE, "id='Extra'"); ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h3>Packing</h3>
                                <div class="form-group">
                                <?php 
                                 $packing = $customer->packing;
                                ?>
                                    <label class="control-label" for="code">
                                        Hanger
                                    </label>
                                    <?php echo form_radio('packing', 'Hanger', ('Hanger' == $packing) ? TRUE : FALSE, "id='Hanger'"); ?> <label
                                        class="control-label" for="code">
                                        Box
                                    </label>
                                    <?php echo form_radio('packing', 'Box', ('Box' == $packing) ? TRUE : FALSE, "id='Box'"); ?>
                                </div>

                            </div>

                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cf1"><?= $this->lang->line("ccf1"); ?></label>
                            <?= form_input('cf1', set_value('cf1', $customer->cf1), 'class="form-control input-sm" id="cf1"'); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cf2"><?= $this->lang->line("ccf2"); ?></label>
                            <?= form_input('cf2', set_value('cf2', $customer->cf2), 'class="form-control input-sm" id="cf2"');?>
                        </div>


                        <div class="form-group">
                            <?php echo form_submit('edit_customer', $this->lang->line("edit_customer"), 'class="btn btn-primary"');?>
                        </div>
                    </div>
                    <?php echo form_close();?>
                </div>
            </div>
        </div>
    </div>
</section>