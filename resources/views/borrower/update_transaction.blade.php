<div id="updateModal" class="modal fade">
    <div class="modal-dialog">
        <form id="updateForm" class="modal-content">
            <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
            <div class="modal-header">
                <h3 class="modal-title">Update Transaction</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="item_id">Item: <span class="text-danger">*</span></label>
                    <select class="form-control" id="item_id" name="item_id" required>
                        <option value="">Select Item</option>
                        @foreach (\App\Models\Item::all() as $item)
                            <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="reserve_quantity">Reserve Quantity: <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="reserve_quantity" name="reserve_quantity" required>
                </div>
                <div class="form-group">
                    <label for="date_of_usage">Date of Usage: <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date_of_usage" name="date_of_usage" required>
                </div>
                <div class="form-group">
                    <label for="date_of_return">Date of Return: <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date_of_return" name="date_of_return" required>
                </div>
                <div class="form-group">
                    <label for="time_of_return">Time of Return: <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" id="time_of_return" name="time_of_return" required>
                </div>
            </div>
            <div class="modal-footer text-right">
                <button type="submit" class="btn btn-primary btn-md"><i class="fa fa-save"></i> Save</button>
                <button type="button" class="btn btn-danger btn-md" data-dismiss="modal"><i class="fa fa-times"></i>
                    Close</button>
            </div>
        </form>
    </div>
</div>
