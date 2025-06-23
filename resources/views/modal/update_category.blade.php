<div id="updateModal" class="modal fade">
    <div class="modal-dialog">
        <form id="updateForm" class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Update Category</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="category_name">Category Name: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="category_name" name="category_name" required>
                </div>
                <div class="form-group">
                    <label for="category_type">Category Type: <span class="text-danger">*</span></label>
                    <select class="form-control" id="category_type" name="category_type" required>
                        <option value="">Select Type</option>
                        <option value="Tools">Tools</option>
                        <option value="Equipment">Equipment</option>
                        <option value="Apparatus">Apparatus</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="laboratory_id">Laboratory: <span class="text-danger">*</span></label>
                    <select class="form-control" id="laboratory_id" name="laboratory_id" required>
                        <option value="">Select Laboratory</option>
                        @foreach (\App\Models\Laboratory::all() as $laboratory)
                            <option value="{{ $laboratory->id }}">{{ $laboratory->name }}</option>
                        @endforeach
                    </select>
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
