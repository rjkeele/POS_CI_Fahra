<?php
(defined('BASEPATH')) OR exit('No direct script access allowed');
error_reporting(E_ALL);
$cloth_page = true;
?>
<!--<script src="<?= $assets ?>js/jscolor.js"></script>-->
<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">

    function readURL(input, dest) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#' + dest + '')
                        .attr('src', e.target.result)
                        .width(250)
                        .height(250);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">

                <div class="box-body">
                    <div class="col-lg-12">
                        <section class="content-header">
                            <div class="col-md-3 pull-right right">
                                <?php
                                //  print_r($prev_cloth);
                                //   die('gere');
                                $error = $this->session->flashdata('error');
                                if ($error) {
                                    ?>
                                    <div class="alert alert-danger alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                                <?php echo $this->session->flashdata('error'); ?>
                                    </div>
                                <?php } ?>
                                <?php
                                $success = $this->session->flashdata('success');
                                if ($success) {
                                    ?>
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                                <?php echo $this->session->flashdata('success'); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </section>
                        <section class="content">


                            <ul class="nav nav-tabs">
                                <li><a data-toggle="tab" href="#cloth_type_tab">Cloth Types</a></li>
                                <li><a data-toggle="tab" href="#cloth_sub_type_tab">Cloth Sub Types</a></li>
                                <li><a data-toggle="tab" href="#cloth_pattern_tab">Pattern</a></li>
                                <li><a data-toggle="tab" href="#cloth_material_tab">Material</a></li>
                                <li class="active"><a data-toggle="tab" href="#cloth_color_tab">Color</a></li>
                                <li><a data-toggle="tab" href="#cloth_upcharges_tab">Upcharges</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="cloth_type_tab" class="tab-pane ">
                                    <div>
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div class="form-group">
                                                    <span class="btn btn-primary" data-toggle="modal"
                                                          data-target="#addLocationModal"><i class="fa fa-plus"></i> Add
                                                        New</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"></h3>
                                                        <div class="box-tools">

                                                        </div>
                                                    </div><!-- /.box-header -->
                                                    <div class="box-body table-responsive no-padding">
                                                        <table class="table table-hover" id="example1">
                                                            <thead>
                                                                <tr>
                                                                    <th>Cloth For</th>
                                                                    <th>Name</th>
                                                                    <th>Image</th>
                                                                    <th>Created On</th>
                                                                    <th class="text-center">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                if (!empty($cloth_types)) {
                                                                    foreach ($cloth_types->result() as $record) {
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $record->cloth_type; ?></td>
                                                                            <td><?php echo $record->name; ?></td>
                                                                            <?php $vehImg = base_url() . 'uploads/' . $record->image; ?>
                                                                            <td><img src="<?php echo $vehImg; ?>" height="100px"
                                                                                     width="150px" alt="" /></td>

                                                                            <td><?php echo date("d-m-Y", strtotime($record->created_on)); ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="btn btn-sm btn-info update_cloth_type_btn"
                                                                                    title="Edit"
                                                                                    data-cloth_type="<?php echo $record->cloth_type; ?>"
                                                                                    data-name="<?php echo $record->name; ?>"
                                                                                    data-id="<?php echo $record->id; ?>"
                                                                                    data-image="<?php echo $record->image; ?>"><i
                                                                                        class="fa fa-pencil"></i></span>
                                                                                <span class="btn btn-sm btn-danger delete_btn"
                                                                                      href="#"
                                                                                      data-del_tbl="<?php echo $tbl_cloth_types; ?>"
                                                                                      data-del_id="<?php echo $record->id; ?>"
                                                                                      title="Delete"><i
                                                                                        class="fa fa-trash"></i></span>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>

                                                        </table>

                                                    </div><!-- /.box-body -->

                                                </div><!-- /.box -->
                                            </div>
                                            <div id="addLocationModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!--<form action="<?php // echo base_url();                                                                          ?>settings/doAddClothTypes" method="post">-->
                                                    <?php echo form_open_multipart('settings/doAddClothTypes'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Add Cloth Types</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Cloth Type</label>
                                                                <select class="form-control" name="cloth_type">
                                                                    <option value="">--Select--</option>
                                                                    <option value="men">For Men</option>
                                                                    <option value="women">For Women</option>
                                                                    <option value="other">Other households</option>
                                                                    <!--<option class="any">Any</option>-->
                                                                </select>
                                                            </div>


                                                            <div class="form-group"><label>Name</label>
                                                                <input type="text" class="form-control" name="name"
                                                                       placeholder="Cloth Type Name">
                                                            </div>

                                                            <div class="form-group"><label for="vehImg">Cloth
                                                                    Image</label>
                                                                <input type="file" class="" name="userfile"
                                                                       onchange="readURL(this, 'veh1_img');">
                                                                <img id="veh1_img" class="photo_update" src="#"
                                                                     height="100px" width="150px" alt="" />
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Create</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div id="updateClothTypeModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">

                                                    <?php echo form_open_multipart('settings/doUpdateClothTypes'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Update Cloth Type</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Cloth Type</label>
                                                                <select class="form-control update_cloth_type"
                                                                        name="cloth_type">
                                                                    <option value="">--Select--</option>
                                                                    <option value="men">For Men</option>
                                                                    <option value="women">For Women</option>
                                                                    <option value="other">Other households</option>
                                                                </select>
                                                            </div>


                                                            <div class="form-group"><label>Name</label>
                                                                <input type="text"
                                                                       class="form-control update_cloth_type_name"
                                                                       name="name" placeholder="Cloth Name">
                                                            </div>

                                                            <div class="form-group"><label for="vehImg">Cloth
                                                                    Image</label>
                                                                <input type="file" class="" name="userfile"
                                                                       onchange="readURL(this, 'update_cloth_type_img');">
                                                                <img id="update_cloth_type_img" class="update_cloth_type_image"
                                                                     src="" height="100px" width="150px" alt="" />
                                                            </div>
                                                            <input type="hidden" name="cloth_type_id"
                                                                   id="update_cloth_type_id" value="">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Update</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div id="cloth_sub_type_tab" class="tab-pane">
                                    <div>
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div class="form-group">
                                                    <span class="btn btn-primary" data-toggle="modal"
                                                          data-target="#addClothSubType"><i class="fa fa-plus"></i> Add
                                                        New</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"></h3>
                                                        <div class="box-tools">

                                                        </div>
                                                    </div><!-- /.box-header -->
                                                    <div class="box-body table-responsive no-padding">
                                                        <table class="table table-hover" id="example2">
                                                            <thead>
                                                                <tr>
                                                                    <th>Cloth For</th>
                                                                    <th>Cloth Name</th>
                                                                    <th>Image</th>
                                                                    <th>Sub Type</th>
                                                                    <th>Price</th>
                                                                    <th>Created On</th>
                                                                    <th class="text-center">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                if (!empty($cloth_sub_types)) {
                                                                    foreach ($cloth_sub_types->result() as $record) {
                                                                        $rowdata = $this->db->query("select * from tec_cloth_types where id =" . $record->cloth_type_id . "")->row();
                                                                        //   $rowdata = $prev_cloth[$record->cloth_type_id];
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $rowdata->cloth_type; ?></td>
                                                                            <td><?php echo $rowdata->name; ?></td>
                                                                            <?php $vehImg = base_url() . 'uploads/' . $rowdata->image; ?>
                                                                            <td><img src="<?php echo $vehImg; ?>" height="100px"
                                                                                     width="150px" alt="" /></td>
                                                                            <td><?php echo $record->name; ?></td>
                                                                            <td><?php echo $record->price; ?></td>
                                                                            <td><?php echo date("d-m-Y", strtotime($record->created_on)); ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="btn btn-sm btn-info update_cloth_sub_type_btn"
                                                                                    title="Edit"
                                                                                    data-cloth_type="<?php echo $rowdata->cloth_type; ?>"
                                                                                    data-name="<?php echo $record->name; ?>"
                                                                                    data-price="<?php echo $record->price; ?>"
                                                                                    data-cloth_type_name="<?php echo $rowdata->id; ?>"
                                                                                    data-id="<?php echo $record->id; ?>"
                                                                                    data-image="<?php echo $rowdata->image; ?>"><i
                                                                                        class="fa fa-pencil"></i></span>
                                                                                <span class="btn btn-sm btn-danger delete_btn"
                                                                                      href="#"
                                                                                      data-del_tbl="<?php echo $tbl_cloth_types; ?>"
                                                                                      data-del_id="<?php echo $record->id; ?>"
                                                                                      title="Delete"><i
                                                                                        class="fa fa-trash"></i></span>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>
                                                            <tfoot>

                                                            </tfoot>
                                                        </table>

                                                    </div><!-- /.box-body -->

                                                </div><!-- /.box -->
                                            </div>
                                            <div id="addClothSubType" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!--<form action="<?php // echo base_url();                                                                          ?>settings/doAddClothTypes" method="post">-->
                                                    <?php echo form_open_multipart('settings/doAddClothSubType'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Add Cloth Sub Type</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Cloth Type</label>
                                                                <select class="form-control cloth_type"
                                                                        name="cloth_type">
                                                                    <option value="">--Select--</option>
                                                                    <option value="men">For Men</option>
                                                                    <option value="women">For Women</option>
                                                                    <option value="other">Other households</option>
                                                                    <!--<option class="any">Any</option>-->
                                                                </select>
                                                            </div>


                                                            <div class="form-group"><label>Cloth Name</label>

                                                                <select class="form-control cloth_type_name"
                                                                        name="cloth_type_id">

                                                                </select>
                                                            </div>

                                                            <div class="form-group"><label for="vehImg">Cloth
                                                                    Image</label>
                                                                <img class="cloth_type_image" src="#"
                                                                     height="100px" width="150px" alt="" />
                                                            </div>
                                                            <div class="form-group"><label>Cloth Sub Type Name</label>

                                                                <input type="text"
                                                                       class="form-control cloth_sub_type_name"
                                                                       name="cloth_sub_type">
                                                            </div>
                                                            <div class="form-group"><label>Price</label>

                                                                <input type="text"
                                                                       class="form-control cloth_sub_type_price"
                                                                       name="cloth_sub_type_price">
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Create</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div id="updateClothSubTypeModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <?php echo form_open_multipart('settings/doUpdateClothSubType'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Update Cloth Sub Type</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Cloth Type</label>
                                                                <select class="form-control cloth_type update_cloth_type"
                                                                        name="cloth_type">
                                                                    <option value="">--Select--</option>
                                                                    <option value="men">For Men</option>
                                                                    <option value="women">For Women</option>
                                                                    <option value="other">Other households</option>
                                                                </select>
                                                            </div>


                                                            <div class="form-group"><label>Cloth Name</label>

                                                                <select class="form-control cloth_type_name"
                                                                        name="cloth_type_id">

                                                                </select>
                                                            </div>


                                                            <div class="form-group"><label for="vehImg">Cloth
                                                                    Image</label>
                                                                <input type="file" class="" name="userfile"
                                                                       onchange="readURL(this, 'veh1_img');">
                                                                <img id="veh1_img" class="cloth_type_image"
                                                                     src="" height="100px" width="150px" alt="" />
                                                            </div>
                                                            <input type="hidden" name="cloth_sub_type_id"
                                                                   id="update_cloth_sub_type_id" value="">
                                                            <div class="form-group"><label>Cloth Sub Type Name</label>

                                                                <input type="text"
                                                                       class="form-control cloth_sub_type_name"
                                                                       name="cloth_sub_type">
                                                            </div>
                                                            <div class="form-group"><label>Price</label>

                                                                <input type="text"
                                                                       class="form-control cloth_sub_type_price"
                                                                       name="price">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Update</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div id="cloth_pattern_tab" class="tab-pane">
                                    <div>
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div class="form-group">
                                                    <span class="btn btn-primary" data-toggle="modal"
                                                          data-target="#addClothPatternModal"><i class="fa fa-plus"></i> Add
                                                        New</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"></h3>
                                                        <div class="box-tools">

                                                        </div>
                                                    </div><!-- /.box-header -->
                                                    <div class="box-body table-responsive no-padding">
                                                        <table class="table table-hover" id="example3">
                                                            <thead>
                                                                <tr>
                                                                    <th>Pattern Name</th>
                                                                    <th>Image</th>
                                                                    <th>Price</th>
                                                                    <th>Created On</th>
                                                                    <th class="text-center">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                if (!empty($cloth_patterns)) {
                                                                    foreach ($cloth_patterns->result() as $record) {
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $record->name; ?></td>
                                                                            <td><?php echo $record->price; ?></td>
                                                                            <?php $vehImg = base_url() . 'uploads/' . $record->image; ?>
                                                                            <td><img src="<?php echo $vehImg; ?>" height="100px"
                                                                                     width="150px" alt="" /></td>

                                                                            <td><?php echo date("d-m-Y", strtotime($record->created_on)); ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="btn btn-sm btn-info update_pattern_btn"
                                                                                    title="Edit"
                                                                                    data-name="<?php echo $record->name; ?>"
                                                                                    data-price="<?php echo $record->price; ?>"
                                                                                    data-id="<?php echo $record->id; ?>"
                                                                                    data-image="<?php echo $record->image; ?>"><i
                                                                                        class="fa fa-pencil"></i></span>
                                                                                <span class="btn btn-sm btn-danger delete_btn"
                                                                                      href="#"
                                                                                      data-del_tbl="<?php echo $tbl_cloth_patterns; ?>"
                                                                                      data-del_id="<?php echo $record->id; ?>"
                                                                                      title="Delete"><i class="fa fa-trash"></i></span>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>

                                                        </table>

                                                    </div><!-- /.box-body -->

                                                </div><!-- /.box -->
                                            </div>
                                            <div id="addClothPatternModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!--<form action="<?php // echo base_url();                                                                          ?>settings/doAddClothTypes" method="post">-->
                                                    <?php echo form_open_multipart('settings/doAddClothPattern'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Add Cloth Pattern</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Name</label>
                                                                <input type="text" class="form-control" name="name"
                                                                       placeholder="Cloth Pattern Name">
                                                            </div>
                                                            <div class="form-group"><label>Price</label>
                                                                <input type="text" class="form-control" name="price"
                                                                       placeholder="Cloth Pattern Price">
                                                            </div>

                                                            <div class="form-group"><label for="vehImg">Cloth
                                                                    Image</label>
                                                                <input type="file" class="" name="userfile"
                                                                       onchange="readURL(this, 'cloth_pat_img');">
                                                                <img id="cloth_pat_img" class="photo_update" src="#"
                                                                     height="100px" width="150px" alt="" />
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Create</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div id="updateClothPatternModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">

                                                    <?php echo form_open_multipart('settings/doUpdateClothPattern'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Update Cloth Pattern</h4>
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="form-group"><label>Pattern Name</label>
                                                                <input type="text"
                                                                       class="form-control update_cloth_pattern_name"
                                                                       name="name" placeholder="Pattern Name">
                                                            </div>
                                                            <div class="form-group"><label>Price</label>
                                                                <input type="text" class="form-control update_cloth_pattern_price" name="price"
                                                                       placeholder="Cloth Pattern Price">
                                                            </div>

                                                            <div class="form-group"><label for="vehImg">Pattern
                                                                    Image</label>
                                                                <input type="file" class="" name="userfile"
                                                                       onchange="readURL(this, 'update_cloth_pat_img');">
                                                                <img id="update_cloth_pat_img" class="update_cloth_pattern_image"
                                                                     src="" height="100px" width="150px" alt="" />
                                                            </div>
                                                            <input type="hidden" name="cloth_pattern_id"
                                                                   id="update_cloth_pattern_id" value="">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Update</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div id="cloth_material_tab" class="tab-pane">
                                    <div>
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div class="form-group">
                                                    <span class="btn btn-primary" data-toggle="modal"
                                                          data-target="#addClothMaterialModal"><i class="fa fa-plus"></i> Add
                                                        New</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"></h3>
                                                        <div class="box-tools">

                                                        </div>
                                                    </div><!-- /.box-header -->
                                                    <div class="box-body table-responsive no-padding">
                                                        <table class="table table-hover" id="example4">
                                                            <thead>
                                                                <tr>
                                                                    <th>Material Name</th>
                                                                    <th>Price</th>
                                                                    <th>Created On</th>
                                                                    <th class="text-center">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                if (!empty($cloth_materials)) {
                                                                    foreach ($cloth_materials->result() as $record) {
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $record->name; ?></td>
                                                                            <td><?php echo $record->price; ?></td>
                                                                            <td><?php echo date("d-m-Y", strtotime($record->created_on)); ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="btn btn-sm btn-info update_cloth_material_btn"
                                                                                    title="Edit"
                                                                                    data-name="<?php echo $record->name; ?>"
                                                                                    data-price="<?php echo $record->price; ?>"
                                                                                    data-id="<?php echo $record->id; ?>"
                                                                                    <i class="fa fa-pencil"></i>Edit</span>
                                                                                <span class="btn btn-sm btn-danger delete_btn"
                                                                                      href="#"
                                                                                      data-del_tbl="<?php echo $tbl_cloth_materials; ?>"
                                                                                      data-del_id="<?php echo $record->id; ?>"
                                                                                      title="Delete"><i class="fa fa-trash"></i>Delete</span>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>

                                                        </table>

                                                    </div><!-- /.box-body -->

                                                </div><!-- /.box -->
                                            </div>
                                            <div id="addClothMaterialModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!--<form action="<?php // echo base_url();                                                                          ?>settings/doAddClothTypes" method="post">-->
                                                    <?php echo form_open_multipart('settings/doAddClothMaterial'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Add Cloth Material</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Name</label>
                                                                <input type="text" class="form-control" name="name"
                                                                       placeholder="Cloth Material Name">
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Price</label>
                                                                <input type="text" class="form-control" name="price"
                                                                       placeholder="Cloth Material Price">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Create</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div id="updateClothMaterialModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">

                                                    <?php echo form_open_multipart('settings/doUpdateClothMaterial'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Update Cloth Material</h4>
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="form-group"><label>Material Name</label>
                                                                <input type="text"
                                                                       class="form-control update_cloth_material_name"
                                                                       name="name" placeholder="Material Name">
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group"><label>Price</label>
                                                                    <input type="text" class="form-control update_cloth_material_price" name="price"
                                                                           placeholder="Cloth Material Price">
                                                                </div>
                                                            </div>

                                                            <input type="hidden" name="cloth_material_id"
                                                                   id="update_cloth_material_id" value="">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Update</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div id="cloth_color_tab" class="tab-pane fade in active">
                                    <div>
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div class="form-group">
                                                    <span class="btn btn-primary" data-toggle="modal"
                                                          data-target="#addClothColorModal"><i class="fa fa-plus"></i> Add
                                                        New</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"></h3>
                                                        <div class="box-tools">

                                                        </div>
                                                    </div><!-- /.box-header -->
                                                    <div class="box-body table-responsive no-padding">
                                                        <table class="table table-hover" id="example5">
                                                            <thead>
                                                                <tr>
                                                                    <th>Color Name</th>
                                                                    <th>Hash Code</th>
                                                                    <th>Price</th>
                                                                    <th>Created On</th>
                                                                    <th class="text-center">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                if (!empty($cloth_colors)) {
                                                                    foreach ($cloth_colors->result() as $record) {
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $record->name; ?></td>
                                                                            <td><?php echo $record->hash_code; ?></td>
                                                                            <td><?php echo $record->price; ?></td>
                                                                            <td><?php echo date("d-m-Y", strtotime($record->created_on)); ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="btn btn-sm btn-info update_cloth_color_btn"
                                                                                    title="Edit"
                                                                                    data-name="<?php echo $record->name; ?>"
                                                                                    data-hash_code="<?php echo $record->hash_code; ?>"
                                                                                    data-price="<?php echo $record->price; ?>"
                                                                                    data-id="<?php echo $record->id; ?>"
                                                                                    <i class="fa fa-pencil"></i>Edit</span>
                                                                                <span class="btn btn-sm btn-danger delete_btn"
                                                                                      href="#"
                                                                                      data-del_tbl="<?php echo $tbl_cloth_colors; ?>"
                                                                                      data-del_id="<?php echo $record->id; ?>"
                                                                                      title="Delete"><i class="fa fa-trash"></i>Delete</span>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>

                                                        </table>

                                                    </div><!-- /.box-body -->

                                                </div><!-- /.box -->
                                            </div>
                                            <div id="addClothColorModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!--<form action="<?php // echo base_url();                                                                          ?>settings/doAddClothTypes" method="post">-->
                                                    <?php echo form_open_multipart('settings/doAddClothColor'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Add Cloth Color</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Name</label>
                                                                <input type="text" class="form-control" name="name"
                                                                       placeholder="Cloth Color Name">
                                                            </div>

                                                            <div class="form-group"><label>Hash Code</label>
                                                                <input type="text" class="form-control jscolor" name="hash_code"
                                                                       placeholder="Color Hash Code">
                                                            </div>
                                                            <div class="form-group"><label>Price</label>
                                                                <input type="text" class="form-control" name="price"
                                                                       placeholder="Price">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Create</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div id="updateClothColorModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">

                                                    <?php echo form_open_multipart('settings/doUpdateClothColor'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Update Color Material</h4>
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="form-group"><label>Color Name</label>
                                                                <input type="text"
                                                                       class="form-control update_cloth_color_name"
                                                                       name="name" placeholder="Color Name">
                                                            </div>
                                                            <div class="form-group"><label>Hash Code</label>
                                                                <input type="text" class="form-control jscolor update_cloth_color_hash_code" name="hash_code"
                                                                       placeholder="Color Hash Code">
                                                            </div>
                                                            <div class="form-group"><label>Color Name</label>
                                                                <input type="text"
                                                                       class="form-control update_cloth_color_price"
                                                                       name="price" placeholder="Color Price">
                                                            </div>

                                                            <input type="hidden" name="cloth_color_id"
                                                                   id="update_cloth_color_id" value="">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Update</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div id="cloth_upcharges_tab" class="tab-pane">
                                    <div>
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div class="form-group">
                                                    <span class="btn btn-primary" data-toggle="modal"
                                                          data-target="#addClothUpchargeModal"><i class="fa fa-plus"></i> Add
                                                        New</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"></h3>
                                                        <div class="box-tools">

                                                        </div>
                                                    </div><!-- /.box-header -->
                                                    <div class="box-body table-responsive no-padding">
                                                        <table class="table table-hover" id="example6">
                                                            <thead>
                                                                <tr>
                                                                    <th>Upcharge Name</th>
                                                                    <th>Price</th>
                                                                    <th>Created On</th>
                                                                    <th class="text-center">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                if (!empty($cloth_upcharges)) {
                                                                    foreach ($cloth_upcharges->result() as $record) {
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $record->name; ?></td>
                                                                            <td><?php echo $record->price; ?></td>
                                                                            <td><?php echo date("d-m-Y", strtotime($record->created_on)); ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="btn btn-sm btn-info update_cloth_upcharge_btn"
                                                                                    title="Edit"
                                                                                    data-name="<?php echo $record->name; ?>"
                                                                                    data-price="<?php echo $record->price; ?>"
                                                                                    data-id="<?php echo $record->id; ?>"
                                                                                    <i class="fa fa-pencil"></i>Edit</span>
                                                                                <span class="btn btn-sm btn-danger delete_btn"
                                                                                      href="#"
                                                                                      data-del_tbl="<?php echo $tbl_cloth_upcharges; ?>"
                                                                                      data-del_id="<?php echo $record->id; ?>"
                                                                                      title="Delete"><i class="fa fa-trash"></i>Delete</span>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>

                                                        </table>

                                                    </div><!-- /.box-body -->

                                                </div><!-- /.box -->
                                            </div>
                                            <div id="addClothUpchargeModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!--<form action="<?php // echo base_url();                                                                          ?>settings/doAddClothTypes" method="post">-->
                                                    <?php echo form_open_multipart('settings/doAddClothUpcharge'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Add Cloth Upcharge</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group"><label>Name</label>
                                                                <input type="text" class="form-control" name="name"
                                                                       placeholder="Upcharge Name">
                                                            </div>

                                                            <div class="form-group"><label>Price</label>
                                                                <input type="text" class="form-control jscolor" name="price"
                                                                       placeholder="Upcharge Price">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Create</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div id="updateClothUpchargeModal" class="modal fade" role="dialog">
                                                <div class="modal-dialog">

                                                    <?php echo form_open_multipart('settings/doUpdateClothUpcharge'); ?>
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Update Upcharge</h4>
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="form-group"><label>Name</label>
                                                                <input type="text"
                                                                       class="form-control update_cloth_upcharge_name"
                                                                       name="name" placeholder="Upcharge Name">
                                                            </div>
                                                            <div class="form-group"><label>Price</label>
                                                                <input type="text" class="form-control jscolor update_cloth_upcharge_price" name="price"
                                                                       placeholder="Upcharge Price">
                                                            </div>

                                                            <input type="hidden" name="cloth_upcharge_id"
                                                                   id="update_cloth_upcharge_id" value="">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                    class="btn btn-default">Update</button> &nbsp;
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>


                        </section>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js" charset="utf-8">
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css"
      rel="stylesheet" type="text/css" />


<script type="text/javascript">


    $(document).on('click', '.edit_vehicle', function () {
        $("#updateAbsenceModal input[name='vehType'][value='" + veh_type + "']").prop('checked', true);

    });


    $(document).on('click', '.delete_btn', function () {
        var del_id = $(this).data('del_id');
        var del_tbl = $(this).data('del_tbl');
        var del_this = $(this);
        var x = confirm('are you sure you want to delete this ?');
        if (x) {
            $.ajax({
                url: "<?php echo base_url() . 'settings/deleteRow'; ?>",
                type: 'GET',
                dataType: 'JSON',
                data: {
                    del_id: del_id,
                    del_tbl: del_tbl,
                },
                success: function (data) {
                    del_this.parent().parent().hide();
                }
            });
        }
    });



    jQuery(document).ready(function () {
        $.extend($.fn.dataTable.defaults, {
            buttons: ['copy', 'csv', 'excel'],
            "serverSide": false,
            "searching": true,

        });

        $('#example1').dataTable({
            "serverSide": false,
            "searching": true,
            dom: 'lBfrtip',
            "order": [
                [0, 'asc']
            ],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
        $('#example2').DataTable({
            "serverSide": false,
            "searching": true,
            dom: 'lBfrtip',
            "order": [
                [0, 'asc']
            ],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
        $('#example3').DataTable({
            "serverSide": false,
            "searching": true,
            dom: 'lBfrtip',
            "order": [
                [0, 'asc']
            ],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
        $('#example4').DataTable({
            "serverSide": false,
            "searching": true,
            dom: 'lBfrtip',
            "order": [
                [0, 'asc']
            ],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
        $('#example5').dataTable({
            "serverSide": false,
            "searching": true,
            dom: 'lBfrtip',
            "order": [
                [0, 'asc']
            ],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
//        $('#example5').dataTable({
//            "searching": true
//        });
        $('#example6').DataTable({
            "serverSide": false,
            "searching": true,
            dom: 'lBfrtip',
            "order": [
                [0, 'asc']
            ],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
        //        var table = $('#example1').DataTable({"order": [[0, 'asc']]});
//        var table = $('#example2').DataTable({"order": [[0, 'asc']]});
//        var table = $('#example3').DataTable({"order": [[0, 'asc']]});
//        var table = $('#example4').DataTable({"order": [[0, 'asc']]});

        $('.content').on('click','.update_cloth_type_btn', function () {
            var id = $(this).data('id');
            var cloth_type = $(this).data('cloth_type');
            var name = $(this).data('name');
            var image = $(this).data('image');
            $('#updateClothTypeModal .update_cloth_type').val(cloth_type);
            $('#updateClothTypeModal .update_cloth_type_name').val(name);
            $('#updateClothTypeModal .update_cloth_type_image').attr('src',
                    '<?php echo base_url(); ?>/uploads/' + image + '');
            $('#updateClothTypeModal #update_cloth_type_id').val(id);
            $('#updateClothTypeModal').modal('show');
        });
        $('.content').on('click', '.update_pattern_btn', function () {
            var id = $(this).data('id');
            //  var cloth_type = $(this).data('name');
            var name = $(this).data('name');
            var image = $(this).data('image');
            var price = $(this).data('price');
            $('#updateClothPatternModal .update_cloth_pattern_name').val(name);
            $('#updateClothPatternModal .update_cloth_pattern_price').val(price);
            $('#updateClothPatternModal .update_cloth_pattern_image').attr('src',
                    '<?php echo base_url(); ?>/uploads/' + image + '');
            $('#updateClothPatternModal #update_cloth_pattern_id').val(id);
            $('#updateClothPatternModal').modal('show');
        });
        $('.content').on('click','.update_cloth_material_btn', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var price = $(this).data('price');
            $('#updateClothMaterialModal .update_cloth_material_price').val(price);
            $('#updateClothMaterialModal .update_cloth_material_name').val(name);
            $('#updateClothMaterialModal #update_cloth_material_id').val(id);
            $('#updateClothMaterialModal').modal('show');
        });
        $('.content').on('click','.update_cloth_color_btn', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var hash_code = $(this).data('hash_code');
            var price = $(this).data('price');
            $('#updateClothColorModal .update_cloth_color_price').val(price);
            $('#updateClothColorModal .update_cloth_color_name').val(name);
            $('#updateClothColorModal .update_cloth_color_hash_code').val(hash_code);
            $('#updateClothColorModal #update_cloth_color_id').val(id);
            $('#updateClothColorModal').modal('show');
        });

        $('.content').on('click','.update_cloth_upcharge_btn', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var price = $(this).data('price');
            $('#updateClothUpchargeModal .update_cloth_upcharge_name').val(name);
            $('#updateClothUpchargeModal .update_cloth_upcharge_price').val(price);
            $('#updateClothUpchargeModal #update_cloth_upcharge_id').val(id);
            $('#updateClothUpchargeModal').modal('show');
        });


        $('.content').on('click', '.update_cloth_sub_type_btn',function () {
            var id = $(this).data('id');
            var cloth_type = $(this).data(' . /;/');
            var name = $(this).data('name');
            var cloth_type_name = $(this).data('cloth_type_name');
            var price = $(this).data('price');
            var image = $(this).data('image');
            $('#updateClothSubTypeModal .cloth_type').val(cloth_type);
            $('#updateClothSubTypeModal .cloth_sub_type_price').val(price);
            $('#updateClothSubTypeModal .cloth_sub_type_name').val(name);
            $('#updateClothSubTypeModal .cloth_type_image').attr('src',
                    '<?php echo base_url(); ?>/uploads/' + image + '');
            $('#updateClothSubTypeModal #update_cloth_sub_type_id').val(id);
            $('#updateClothSubTypeModal').modal('show');
            $.ajax({
                url: "<?php echo base_url() . 'settings/getClothNames'; ?>",
                type: 'GET',
                dataType: 'JSON',
                data: {
                    cloth_type: cloth_type,
                },
                success: function (data) {
                    $('#updateClothSubTypeModal .cloth_type_name').html(data.html);
                    $('#updateClothSubTypeModal .cloth_type_name').val(cloth_type_name);
                }
            });


        });

// get cloth name and image as per cloth type
        $('#cloth_sub_type_tab').on('change', '.cloth_type', function () {
            var cloth_type = $(this).val();
            $.ajax({
                url: "<?php echo base_url() . 'settings/getClothNames'; ?>",
                type: 'GET',
                dataType: 'JSON',
                data: {
                    cloth_type: cloth_type,
                },
                success: function (data) {
                    $('.cloth_type_name').html();
                    $('.cloth_type_name').html(data.html);
                }
            });
        });
        $('#cloth_sub_type_tab').on('change', '.cloth_type_name', function () {
            var cloth_type = $('#cloth_sub_type_tab .cloth_type').val();
            var cloth_type_id = $(this).val();

            $.ajax({
                url: "<?php echo base_url() . 'settings/getClothNameImage'; ?>",
                type: 'GET',
                dataType: 'JSON',
                data: {
                    cloth_type: cloth_type,
                    cloth_type_id: cloth_type_id
                },
                success: function (data) {
                    $('.cloth_type_image').attr('src', data.src);
                    //   $('.cloth_type_name').html(data.html);
                }
            });
        });

    });
</script>