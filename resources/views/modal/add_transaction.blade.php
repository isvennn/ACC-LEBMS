<div id="addModal" class="modal fade" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="addForm" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h3 class="modal-title" id="addModalLabel">Add Transaction</h3>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="user_id" class="form-label fw-bold">User: <span class="text-danger">*</span></label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">Select User</option>
                        @foreach (\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                        @endforeach
                    </select>
                    <div id="user-transaction-warning" class="text-danger mt-2" style="display: none;"></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="itemsTable">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 25%;">Item</th>
                                <th style="width: 20%;">Date of Usage</th>
                                <th style="width: 20%;">Date of Return</th>
                                <th style="width: 15%;">Time of Return</th>
                                <th style="width: 15%;">Reserve Quantity</th>
                                <th style="width: 5%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <tr class="item-row">
                                <td>
                                    <select class="form-select item-select" name="items[0][item_id]" required>
                                        <option value="">Select Item</option>
                                        @foreach (\App\Models\Item::all() as $item)
                                            <option value="{{ $item->id }}">{{ $item->item_name }}
                                                ({{ $item->current_qty }} available)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="date" class="form-control" name="items[0][date_of_usage]" required
                                        min="{{ date('Y-m-d') }}">
                                </td>
                                <td>
                                    <input type="date" class="form-control" name="items[0][date_of_return]" required>
                                </td>
                                <td>
                                    <input type="time" class="form-control" name="items[0][time_of_return]" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="items[0][reserve_quantity]"
                                        min="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mb-3">
                    <button type="button" id="addItemRow" class="btn btn-outline-primary btn-sm"><i
                            class="fas fa-plus"></i> Add Item</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i>
                    Close</button>
            </div>
        </form>
    </div>
</div>
