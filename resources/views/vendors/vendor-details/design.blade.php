@extends('payroll.layouts.base')

@section('content')
    <div id="app">
        <div class="fbox-shadow mt-2 fd-card respo-margin-top">
            <div class="m-4">
                <h5 class="mb-4" style="color: rgb(29, 29, 29); font-size: 1.53125rem;">New Purchase Entry</h5>

                <div class="row align-items-end brw-form-la mt-4">
                    <div class="form-group col-md-4">
                        <label for="supplier">Select Supplier</label>
                        <select class="form-control" id="supplier">
                            <option value="" selected>-Select-</option>
                            <option value="dsd">dsd</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="filter_start_date">Purchase Date</label>
                        <input type="date" id="filter_start_date" class="form-control">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="invoice_number">Invoice Number</label>
                        <input type="text" id="invoice_number" class="form-control">
                    </div>

                    <div class="form-group col-md-12">
                        <label for="notes">Reference / Notes</label>
                        <textarea id="notes" class="form-control"></textarea>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table brownz-table-list-v1 mt-4">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Tax (%)</th>
                                <th>Discount</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <tr>
                                <td>1</td>
                                <td>
                                    <select class="form-control">
                                        <option>Select product</option>
                                    </select>
                                </td>
                                <td><input type="number" class="form-control" placeholder=""></td>
                                <td><input type="number" class="form-control" placeholder=""></td>
                                <td><input type="number" class="form-control" value="0.00"></td>
                                <td><input type="number" class="form-control" placeholder=""></td>
                                <td><input type="text" class="form-control" value="0.00" readonly></td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" class="text-danger delete-row"><i
                                            class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <a href="javascript:void(0)" class="text-primary" id="addProductBtn">+ Add Product</a>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label>Payment Method</label>
                        <select class="form-control">
                            <option>Cash</option>
                            <option>Bank Transfer</option>
                            <option>Credit</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex justify-content-between text-muted"><span>Sub total</span><span>₹ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted"><span>Tax Total</span><span>₹ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted"><span>Discount Total</span><span>₹
                                0.00</span></div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold" style="font-size: 1.1rem;"><span>Grand
                                Total</span><span>0.00</span></div>
                        <div class="d-flex justify-content-between fw-bold" style="font-size: 1.1rem;"><span>Due
                                Amount</span><span>0.00</span></div>
                    </div>

                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary">Save & Print</button>
                    <button class="btn btn-success">Save</button>
                    <button class="btn btn-secondary">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            let rowCount = 1;

            // Add new row
            $('#addProductBtn').click(function() {
                rowCount++;
                let newRow = `
                <tr>
                    <td>${rowCount}</td>
                    <td>
                        <select class="form-control">
                            <option>Select product</option>
                        </select>
                    </td>
                    <td><input type="number" class="form-control" placeholder=""></td>
                    <td><input type="number" class="form-control" placeholder=""></td>
                    <td><input type="number" class="form-control" value="0.00"></td>
                    <td><input type="number" class="form-control" placeholder=""></td>
                    <td><input type="text" class="form-control" value="0.00" readonly></td>
                    <td class="text-center">
                        <a href="javascript:void(0)" class="text-danger delete-row"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            `;
                $('#productTableBody').append(newRow);
            });

            // Remove row
            $('#productTableBody').on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
                updateRowNumbers();
            });

            // Update row numbers after deletion
            function updateRowNumbers() {
                rowCount = 0;
                $('#productTableBody tr').each(function() {
                    rowCount++;
                    $(this).find('td:first').text(rowCount);
                });
            }
        });
        
    </script>

@endsection
