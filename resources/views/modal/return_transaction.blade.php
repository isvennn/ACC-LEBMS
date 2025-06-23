<div id="returnModal" class="modal fade" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="returnForm" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h3 class="modal-title" id="returnModalLabel">Return Transaction</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6 text-left">
                            <p class="fw-bold">Transaction Details</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" id="addReturnRow" class="btn btn-outline-success btn-sm"><i
                                    class="fas fa-plus"></i> Add Status</button>
                        </div>
                        <div class="col-md-12">
                            <div id="quantity-error" class="text-danger mt-2 mb-2 text-center alert alert-danger text-white" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Transaction No: </label>
                            <span id="transaction_no"></span>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Item: </label>
                            <span id="item_name"></span>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Approved Quantity: </label>
                            <span id="approve_quantity"></span>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped" id="returnItemsTable">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 25%;">Return Status</th>
                            <th style="width: 15%;">Quantity</th>
                            <th style="width: 25%;">Penalty Remarks</th>
                            <th style="width: 25%;">Amount to Pay</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="returnItemsTableBody">
                        <tr class="return-row">
                            <td>
                                <select class="form-select chosen-select return-status" name="returns[0][return_status]"
                                    data-placeholder="Select Return Status" required>
                                    <option value=""></option>
                                    <option value="Good">Good</option>
                                    <option value="Lost">Lost</option>
                                    <option value="Damaged">Damaged</option>
                                    <option value="For Repair">For Repair</option>
                                    <option value="For Disposal">For Disposal</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control return-quantity" name="returns[0][quantity]"
                                    min="1" required>
                            </td>
                            <td>
                                <select class="form-select chosen-select penalty-remarks"
                                    name="returns[0][penalty_remarks]" data-placeholder="Select Penalty Remarks">
                                    <option value=""></option>
                                    <option value="Replace">Replace</option>
                                    <option value="Pay">Pay</option>
                                </select>
                            </td>
                            <td>
                                <span class="amount-to-pay">0.00</span>
                                <input type="hidden" class="amount-input" name="returns[0][amount]">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i
                                        class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i>
                    Close</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let returnRowCount = 1;
        let transactionData = null;
        let itemPrice = 0;

        // Initialize Chosen.js
        $('.chosen-select').chosen({
            width: '100%',
            allow_single_deselect: true,
            placeholder_text_single: 'Select an option'
        });

        // Show modal and fetch transaction data
        window.returnTransaction = function(id) {
            transactionID = id;
            $.ajax({
                method: 'GET',
                url: `/transactions/${id}`,
                dataType: 'json',
                cache: false,
                success: function(response) {
                    transactionData = response;
                    itemPrice = response.item ? parseFloat(response.item.item_price) : 0;
                    $('#transaction_no').text(response.transaction_no);
                    $('#item_name').text(response.item ? response.item.item_name : 'N/A');
                    $('#approve_quantity').text(response.approve_quantity);
                    $('#returnItemsTableBody').html(`
                    <tr class="return-row">
                        <td>
                            <select class="form-select chosen-select return-status" name="returns[0][return_status]" data-placeholder="Select Return Status" required>
                                <option value=""></option>
                                <option value="Good">Good</option>
                                <option value="Lost">Lost</option>
                                <option value="Damaged">Damaged</option>
                                <option value="For Repair">For Repair</option>
                                <option value="For Disposal">For Disposal</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control return-quantity" name="returns[0][quantity]" min="1" required>
                        </td>
                        <td>
                            <select class="form-select chosen-select penalty-remarks" name="returns[0][penalty_remarks]" data-placeholder="Select Penalty Remarks">
                                <option value=""></option>
                                <option value="Replace">Replace</option>
                                <option value="Pay">Pay</option>
                            </select>
                        </td>
                        <td>
                            <span class="amount-to-pay">0.00</span>
                            <input type="hidden" class="amount-input" name="returns[0][amount]">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `);
                    returnRowCount = 1;
                    $('.chosen-select').chosen('destroy').chosen({
                        width: '100%',
                        allow_single_deselect: true,
                        placeholder_text_single: 'Select an option'
                    });
                    $('#returnModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                    });
                },
                error: function(jqXHR) {
                    showErrorMessage(jqXHR.responseJSON?.msg ||
                        "Failed to load transaction details.");
                }
            });
        };

        // Add new return status row
        $('#addReturnRow').on('click', function() {
            const newRow = `
            <tr class="return-row">
                <td>
                    <select class="form-select chosen-select return-status" name="returns[${returnRowCount}][return_status]" data-placeholder="Select Return Status" required>
                        <option value=""></option>
                        <option value="Good">Good</option>
                        <option value="Lost">Lost</option>
                        <option value="Damaged">Damaged</option>
                        <option value="For Repair">For Repair</option>
                        <option value="For Disposal">For Disposal</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control return-quantity" name="returns[${returnRowCount}][quantity]" min="1" required>
                </td>
                <td>
                    <select class="form-select chosen-select penalty-remarks" name="returns[${returnRowCount}][penalty_remarks]" data-placeholder="Select Penalty Remarks">
                        <option value=""></option>
                        <option value="Replace">Replace</option>
                        <option value="Pay">Pay</option>
                    </select>
                </td>
                <td>
                    <span class="amount-to-pay">0.00</span>
                    <input type="hidden" class="amount-input" name="returns[${returnRowCount}][amount]">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
            $('#returnItemsTableBody').append(newRow);
            returnRowCount++;
            updateRemoveButtons();
            $('.chosen-select').chosen('destroy').chosen({
                width: '100%',
                allow_single_deselect: true,
                placeholder_text_single: 'Select an option'
            });
        });

        // Remove return status row
        $(document).on('click', '.remove-row', function() {
            if (returnRowCount > 1) {
                $(this).closest('tr').remove();
                returnRowCount--;
                updateRemoveButtons();
                validateTotalQuantity();
            }
        });

        // Update remove buttons state
        function updateRemoveButtons() {
            $('.remove-row').prop('disabled', returnRowCount === 1);
        }

        // Validate total quantity on input change
        $(document).on('input', '.return-quantity', function() {
            validateTotalQuantity();
            updateAmountToPay($(this).closest('tr'));
        });

        function validateTotalQuantity() {
            if (!transactionData) return;
            const totalQuantity = $('.return-quantity').toArray().reduce((sum, input) => {
                const value = parseInt($(input).val()) || 0;
                return sum + value;
            }, 0);
            const approveQuantity = parseInt(transactionData.approve_quantity);
            if (totalQuantity !== approveQuantity) {
                $('#quantity-error').text(
                        `Total quantity (${totalQuantity}) must equal approved quantity (${approveQuantity}).`)
                    .show();
                $('#returnForm').find('button[type="submit"]').prop('disabled', true);
            } else {
                $('#quantity-error').hide();
                $('#returnForm').find('button[type="submit"]').prop('disabled', false);
            }
        }

        // Enable/disable penalty remarks and update amount to pay
        $(document).on('change', '.return-status, .penalty-remarks', function() {
            const row = $(this).closest('tr');
            const penaltySelect = row.find('.penalty-remarks');
            const status = row.find('.return-status').val();
            if (status === 'Lost' || status === 'Damaged') {
                penaltySelect.prop('disabled', false);
            } else {
                penaltySelect.val('').prop('disabled', true);
            }
            penaltySelect.trigger('chosen:updated');
            updateAmountToPay(row);
        });

        function updateAmountToPay(row) {
            const status = row.find('.return-status').val();
            const penaltyRemarks = row.find('.penalty-remarks').val();
            const quantity = parseInt(row.find('.return-quantity').val()) || 0;
            const amountSpan = row.find('.amount-to-pay');
            const amountInput = row.find('.amount-input');
            let amount = 0;

            if ((status === 'Lost' || status === 'Damaged') && penaltyRemarks === 'Pay') {
                amount = (itemPrice * quantity).toFixed(2);
            }

            amountSpan.text(amount);
            amountInput.val(amount);
        }

        // Form submission
        $('#returnForm').submit(function(event) {
            event.preventDefault();

            const formData = $(this).serializeArray();
            const returns = formData.reduce((acc, field) => {
                const match = field.name.match(/returns\[(\d+)\]\[(\w+)\]/);
                if (match) {
                    const index = match[1];
                    const key = match[2];
                    if (!acc[index]) acc[index] = {};
                    acc[index][key] = field.value;
                }
                return acc;
            }, []);

            $.ajax({
                method: 'POST',
                url: `/transactions/${transactionID}/return`,
                data: {
                    returns: returns
                },
                dataType: 'JSON',
                cache: false,
                success: function(response) {
                    if (response.valid) {
                        $('#returnForm').trigger('reset');
                        $('#returnItemsTableBody').html(`
                        <tr class="return-row">
                            <td>
                                <select class="form-select chosen-select return-status" name="returns[0][return_status]" data-placeholder="Select Return Status" required>
                                    <option value=""></option>
                                    <option value="Good">Good</option>
                                    <option value="Lost">Lost</option>
                                    <option value="Damaged">Damaged</option>
                                    <option value="For Repair">For Repair</option>
                                    <option value="For Disposal">For Disposal</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control return-quantity" name="returns[0][quantity]" min="1" required>
                            </td>
                            <td>
                                <select class="form-select chosen-select penalty-remarks" name="returns[0][penalty_remarks]" data-placeholder="Select Penalty Remarks">
                                    <option value=""></option>
                                    <option value="Replace">Replace</option>
                                    <option value="Pay">Pay</option>
                                </select>
                            </td>
                            <td>
                                <span class="amount-to-pay">0.00</span>
                                <input type="hidden" class="amount-input" name="returns[0][amount]">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `);
                        returnRowCount = 1;
                        $('.chosen-select').chosen('destroy').chosen({
                            width: '100%',
                            allow_single_deselect: true,
                            placeholder_text_single: 'Select an option'
                        });
                        showSuccessMessage(response.msg);
                        $('#returnModal').modal('hide');
                        table1.ajax.reload(null, false);
                    }
                },
                error: function(jqXHR) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                        let errors = jqXHR.responseJSON.errors;
                        let errorMsg = `${jqXHR.responseJSON.msg}\n`;
                        for (const [field, messages] of Object.entries(errors)) {
                            errorMsg += `- ${messages.join(', ')}\n`;
                        }
                        showErrorMessage(errorMsg);
                    } else {
                        showErrorMessage(jqXHR.responseJSON?.msg ||
                            "An unexpected error occurred. Please try again.");
                    }
                }
            });
        });
    });
</script>

<style>
    .modal-content {
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 5px;
    }

    .chosen-container {
        width: 100% !important;
    }

    .amount-to-pay {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
        }

        .table-responsive {
            font-size: 0.9rem;
        }
    }
</style>
