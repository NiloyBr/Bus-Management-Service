@extends('web.layouts.master')


@section('content')
    <div class="content-wrapper">

    <!-- Main content -->
        <section class="content">
            <!-- Your Page Content Here -->
            <form id="add_booking_form" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><b>Add Booking</b></h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            @csrf
                            <div class="box-body">
                                <div class="form-group">
                                    <label>Available Coach</label>
                                    <select class="form-control select2" onchange="coachInfoGenerate()" id="schedule_id"
                                            name="schedule_id">
                                        <option value="">--Select Coach--</option>
                                        @foreach ($schedules as $schedule)
                                            <option value="{{ $schedule->id }}">
                                                {{"Coach Number: ".$schedule->coach->bus_number."(".$schedule->coach->coach_type."); Route: ".$schedule->start_route."-".$schedule->end_route
                                                  . "; Date: ". date('d/m/Y', strtotime($schedule->departure_date)) . "; Time: " . date('h:i A', strtotime($schedule->departure_time))}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="unit_price">Price per seat</label>
                                    <input type="text" class="form-control" id="unit_price"
                                           placeholder="Unit price" name="unit_price" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Seat Ids</label>
                                    <select class="form-control select2 " onchange="totalCalculation()" multiple
                                            id="seat_ids"
                                            name="seat_ids">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="total_price">Total Price</label>
                                    <input type="text" class="form-control" id="total_price"
                                           placeholder="Total price" name="total_price">
                                </div>
                                <div class="form-group">
                                    <label for="customer_name">Customer Name</label>
                                    <input type="text" class="form-control" id="customer_name"
                                           placeholder="Customer Name" name="customer_name">
                                </div>
                                <div class="form-group">
                                    <label for="customer_mobile">Customer Mobile</label>
                                    <input type="text" class="form-control" id="customer_mobile"
                                           placeholder="Customer Mobile" name="customer_mobile">
                                </div>
                                <div class="form-group">
                                    <label for="customer_address">Customer Address</label>
                                    <input type="text" class="form-control" id="customer_address"
                                           placeholder="Customer Address" name="customer_address">
                                </div>
                                <div class="form-group">
                                    <label>Payment</label>
                                    <select class="form-control select2" id="payment_type" name="payment_type">
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                    </select>
                                </div>
                                <br>
                                <button id="add_booking_form_btn" type="submit" class="btn btn-primary">Submit</button>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                            </div>
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-md-6">
                        <!-- general form elements -->
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title"><b>Seat Map</b></h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->

                            <div class="box-body">
                                <input type="hidden" id="seat_type"/>
                                <div class="row">
                                    <div class="col">
                                        <table id="map"></table>
                                    </div>
                                    <br>
                                    <div class="col">
                                        <label class="text-bold">Seat Mark</label>
                                        <div class="row">
                                            <div class="col">
                                                <button type="button" class="btn btn-success">Available</button>
                                            </div>
                                            <div class="col">
                                                <button type="button" class="btn btn-danger">Booked</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">

                            </div>

                        </div>
                    </div>
                </div>

            </form>

        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->
@endsection

@section('addtional-scripts')
    <script>
        $(document).ready(function () {

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            //  const EndPoint = '@EndPoint.SupplierModuleEndpoint';
            $("#add_booking_form_btn").click(function (e) {
                e.preventDefault();
                $("#add_booking_form").validate({
                    highlight: function (element) {
                        jQuery(element).closest('.form-group').addClass('has-error');
                    },
                    unhighlight: function (element) {
                        jQuery(element).closest('.form-group').removeClass('has-error');
                        jQuery(element).closest('.form-group').addClass('has-success');
                    },
                    errorElement: 'span',
                    errorClass: 'help-block',
                    errorPlacement: function (error, element) {
                        if (element.parent('.input-group').length) {
                            $(element).siblings(".help-block").append(error);
                            error.insertAfter(element.parent());
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
                const booking = {
                    "schedule_id": $("#schedule_id").val(),
                    "seat_ids": $("#seat_ids").val(),
                    "total_price": $("#total_price").val(),
                    "unit_price": $("#unit_price").val(),
                    "payment_type": $("#payment_type").val(),
                    "customer_name": $("#customer_name").val(),
                    "customer_mobile": $("#customer_mobile").val(),
                    "customer_address": $("#customer_address").val(),
                }
                const bookingJson = JSON.stringify(booking);
                console.log(bookingJson);
                $.ajax({
                    // url: EndPoint + 'supplier/CreateSupplier',
                    url: '{{ url('ticket/book-ticket/entry-booking') }}',
                    type: 'POST',
                    data: JSON.stringify(booking),
                    dataType: 'json',
                    contentType: 'application/json; charset=utf-8',
                    success: function (data, textStatus, xhr) {
                        let responseCode = xhr.status;
                        if (responseCode === 201) {
                            $('#add_coach_form').trigger('reset');
                            Toast.fire({
                                icon: 'success',
                                title: "Success " + responseCode,
                                text: "Created Successfully",
                            });
                            setTimeout(redirectFunc, 2000);

                            function redirectFunc() {
                                window.location.href='/ticket/book-ticket/print?ticket_no='+data.ticket_no;
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        console.log(jqXHR);
                        Toast.fire({
                            icon: 'error',
                            title: "Error " + jqXHR.status,
                            text: jqXHR.responseJSON.message,
                        });
                        if (jqXHR.status == 422) {
                            console.log(jqXHR.responseJSON.errors);
                            var validator = $('#add_seat_cofiguration_form').validate();
                            var objErrors = {};
                            $.each(jqXHR.responseJSON.errors, function (key, val) {
                                objErrors[key] = val;
                            });
                            validator.showErrors(objErrors);
                            validator.focusInvalid();
                        }
                    }
                });
            });
        });

        function coachInfoGenerate() {
            $("#map tr").remove();
            $("#seat_id option").remove();
            const data = {
                "schedule_id": $("#schedule_id").val(),
            }
            $.ajax({
                // url: EndPoint + 'supplier/CreateSupplier',
                url: '{{ url('/ticket/book-ticket/get-coach-info') }}',
                type: 'get',
                data: data,
                contentType: 'application/json; charset=utf-8',
                success: function (data, textStatus, xhr) {
                    let responseCode = xhr.status;
                    if (responseCode === 200) {
                        const seatConfigInfo = xhr.responseJSON.coach_info.seat_config;
                        $.each(seatConfigInfo,
                            function (i, info) {
                                $("#unit_price").val(info.price);
                                $("#seat_type").val(info.seat_type);
                            });
                        const coachInfo = xhr.responseJSON.coach_info;
                        console.log(xhr.responseJSON);
                        for (let index = 1; index <= coachInfo.bus_seat_quantity; index++) {
                            if (!xhr.responseJSON.booked_seats.includes(index.toString())){
                                $('#seat_ids').append($('<option>', {
                                    value: index ,
                                    text: index
                                }));
                            }
                        }
                        if ($("#seat_type").val() == 1) {
                            var col = 4;
                            while (col != 40) {
                                var col_1='<button type="button" class="btn btn-success">A' + (col - 3) + '</button>';
                                var col_2='<button type="button" class="btn btn-success">B' + (col - 2) + '</button>';
                                var col_3='<button type="button" class="btn btn-success">C' + (col - 1) + '</button>';
                                var col_4='<button type="button" class="btn btn-success">D' + col + '</button>';
                                if(xhr.responseJSON.booked_seats.includes((col-3).toString())){
                                    col_1='<button type="button" class="btn btn-danger">A' + (col - 3) + '</button>';
                                }
                                if(xhr.responseJSON.booked_seats.includes((col-2).toString())){
                                    col_2='<button type="button" class="btn btn-danger">B' + (col - 2) + '</button>';
                                }
                                if(xhr.responseJSON.booked_seats.includes((col-1).toString())){
                                    col_3='<button type="button" class="btn btn-danger">C' + (col - 1) + '</button>';
                                }
                                if(xhr.responseJSON.booked_seats.includes((col).toString())){
                                    col_4='<button type="button" class="btn btn-danger">D' + col + '</button>';
                                }
                                var row = '<tr>' +
                                    '<td>' +
                                    '<div class="btn-group">' +
                                    col_1 +
                                    col_2 +
                                    '</div>' +
                                    '</td>' +
                                    '<td style="padding: 15px;">' +
                                    'Way' +
                                    '</td>' +
                                    '<td>' +
                                    '<div class="btn-group">' +
                                    col_3 +
                                    col_4 +
                                    '</div>' +
                                    '</td>' +
                                    '</tr>';
                                $('#map').append(row);
                                col = col + 4;
                            }
                        }
                        if ($("#seat_type").val() == 2) {
                            var col = 3;
                            while (col != 30) {
                                var col_1='<button type="button" class="btn btn-success">A' + (col - 2) + '</button>';
                                var col_2='<button type="button" class="btn btn-success">B' + (col - 1) + '</button>';
                                var col_3='<button type="button" class="btn btn-success">C' + col + '</button>';
                                if(xhr.responseJSON.booked_seats.includes((col-2).toString())){
                                    col_1='<button type="button" class="btn btn-danger">A' + (col - 2) + '</button>';
                                }
                                if(xhr.responseJSON.booked_seats.includes((col-1).toString())){
                                    col_2='<button type="button" class="btn btn-danger">B' + (col - 1) + '</button>';
                                }
                                if(xhr.responseJSON.booked_seats.includes((col).toString())){
                                    col_3='<button type="button" class="btn btn-danger">C' + col + '</button>';
                                }

                                var row = '<tr>' +
                                    '<td>' +
                                    '<div class="btn-group">' +
                                    col_1 +
                                    '</div>' +
                                    '</td>' +
                                    '<td style="padding: 15px;">' +
                                    'Way' +
                                    '</td>' +
                                    '<td>' +
                                    '<div class="btn-group">' +
                                    col_2 +
                                    col_3 +
                                    '</div>' +
                                    '</td>' +
                                    '</tr>';
                                $('#map').append(row);
                                col = col + 3;
                            }
                        }

                    }
                }
            });
        }

        function totalCalculation() {
            var qty = $('#seat_ids').val().length;
            var unitPrice = $('#unit_price').val();
            $('#total_price').val('');
            $('#total_price').val(qty * unitPrice);
        }
    </script>
@endsection
