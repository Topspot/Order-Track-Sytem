<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;

$this->title = 'Order Form';
?>

<section id="orders">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                <div class="panel panel-default col-xs-6 col-sm-12 add-order-block">
                    <div class="panel-heading">
                        <h3> Add an Order</h3>
                        <span class="pull-right"><i class="far fa-address-card fa-2x"></i></span>
                    </div>
                    <div class="panel-body">

                        <?php $form = ActiveForm::begin(['id' => 'order-form']); ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'first_name')->textInput(['placeholder' => "First Name"])->label('First Name <span class="required">*</span>') ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'last_name')->textInput(['placeholder' => "Last Name"])->label('Last Name') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <?= $form->field($model, 'email')->textInput(['placeholder' => "Email"])->label('Email') ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <?= $form->field($model, 'phone_number')->textInput(['placeholder' => "+1 (888) 123-4567"])->label('Phone Number <span class="required">*</span>') ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'order_type')->dropDownList([0 => 'Delivery', 1 => 'Servicing', '2' => 'Installation'], ['prompt' => 'Select Order Types'])->label('Order Type <span class="required">*</span>'); ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'order_value', [
                                    'template' => '{beginLabel}{labelTitle}{endLabel}<div class="input-group">
                                <span class="input-group-addon">$</span>{input}</div>{error}{hint}'
                                ])->textInput(['placeholder' => "Amount"]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <?= $form->field($model, 'date')->widget(DatePicker::className(), [
                                        'pluginOptions' => [
                                            'format' => 'yyyy-mm-dd',
                                            'autoclose' => true,
                                            'todayHighlight' => true,
                                        ]
                                    ])->label('Schedule Date <span class="required">*</span>');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?= $form->field($model, 'address')->textInput(['class' => 'form-control map-input', 'id' => 'address-input','onchange' => "onChangeAddress()"])->label('Schedule Address <span class="required">*</span>') ?>
                                    <?= $form->field($model, 'latitude')->hiddenInput(['id' => 'address-latitude', 'value' => 0])->label(false) ?>
                                    <?= $form->field($model, 'longitude')->hiddenInput(['id' => 'address-longitude', 'value' => 0])->label(false) ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <?= $form->field($model, 'city')->textInput()->label('City <span class="required">*</span>') ?>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <?= $form->field($model, 'state')->textInput()->label('State / Province <span class="required">*</span>') ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'postal')->textInput()->label('Postal/ Zip Code') ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'country')->dropDownList([0 => 'Canada', 1 => 'United States', '2' => 'Mexico'], ['prompt' => 'Select Country'])->label('Country <span class="required">*</span>'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="pull-left">
                                <button type="button" class="btn btn-default preview" disabled="disabled" onclick="previewLocation();">Preview Location</button>
                            </div>
                            <div class="pull-right">
                            <?= Html::resetButton('Cancel', ['class' => 'btn btn-danger reset-data']) ?>
                            <?= Html::submitButton('Submit', ['class' => 'btn btn-success', 'name' => 'order-button']) ?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>


                <div class="panel panel-default col-xs-6 col-sm-12 add-order-block existing-orders mt-10">
                    <div class="panel-heading">
                        <h3>Existing Orders</h3>
                        <span class="pull-right"><i class="far fa-edit fa-2x"></i></span>
                    </div>
                    <div class="panel-body" id="style-4">
                        <table id="orders-tables">
                            <tr>
                                <th onclick="sortTable(0)">First Name</th>
                                <th onclick="sortTable(1)">Last Name</th>
                                <th onclick="sortTable(2)">Date</th>
                                <th></th>
                            </tr>
                            <?php foreach ($orders as $value) { ?>
                                <tr id="row<?= $value->id ?>" onclick="openInfoWindow(<?= $value->id ?>)">
                                    <td><?= $value->first_name ?></td>
                                    <td><?= $value->last_name ?></td>
                                    <td><?= $value->date ?></td>
                                    <td>
                                        <?php
                                        $color_class = 'btn btn-default';
                                        if ($value->status == 0) {
                                            $color_class = 'btn btn-default';
                                        } else if ($value->status == 1) {
                                            $color_class = 'btn btn-primary';
                                        } else if ($value->status == 2) {
                                            $color_class = 'btn btn-warning';
                                        } else if ($value->status == 3) {
                                            $color_class = 'btn btn-success';
                                        } else {
                                            $color_class = 'btn btn-danger';
                                        }
                                        ?>
                                        <select class="form-control  <?= $color_class ?>" name="status" id="status"
                                                onchange="changeOrderStatus(this,'<?= $value->id ?>');">
                                            <option value="0" <?= $value->status == 0 ? 'selected="selected"' : '' ?>>
                                                Pending
                                            </option>
                                            <option value="1" <?= $value->status == 1 ? 'selected="selected"' : '' ?>>
                                                Assigned
                                            </option>
                                            <option value="2" <?= $value->status == 2 ? 'selected="selected"' : '' ?>>On
                                                Route
                                            </option>
                                            <option value="3" <?= $value->status == 3 ? 'selected="selected"' : '' ?>>
                                                Done
                                            </option>
                                            <option value="4" <?= $value->status == 4 ? 'selected="selected"' : '' ?>>
                                                Cancelled
                                            </option>
                                        </select>
                                        <button class="btn btn-danger delete-btn" <?= $value->status != 0 && $value->status != 1 ? 'disabled="disabled"' : '' ?>
                                                data-toggle="modal" data-target="#myModal" data-id="<?= $value->id ?>">
                                            <i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>

                        </table>
                    </div>
                </div>
            </div>
            <div class="panel panel-default col-xs-12 col-sm-6 col-md-6 col-lg-6 map-block add-order-block">
                <div class="panel-heading">
                    <h3>Map</h3>
                    <span class="pull-right"><i class="fas fa-globe-europe fa-2x"></i></span>
                </div>
                <div class="panel-body">
                    <div id="address-map-container">
                        <div id="address-map" aria-label="Address Map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>