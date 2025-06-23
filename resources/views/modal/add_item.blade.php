<div id="addModal" class="modal fade">
    <div class="modal-dialog">
        <form id="addForm" class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add Item</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="item_name">Item Name: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="item_name" name="item_name" required>
                </div>
                <div class="form-group">
                    <label for="item_description">Description: <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="item_description" name="item_description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="item_price">Price: <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="item_price" name="item_price"
                        required>
                </div>
                <div class="form-group">
                    <label for="category_id">Category: <span class="text-danger">*</span></label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach (\App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="beginning_qty">Beginning Quantity: <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="beginning_qty" name="beginning_qty" required>
                </div>
                <div class="form-group">
                    <label for="current_qty">Current Quantity: <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="current_qty" name="current_qty" required>
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
