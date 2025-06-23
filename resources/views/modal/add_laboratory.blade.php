<div id="addModal" class="modal fade">
    <div class="modal-dialog">
        <form id="addForm" class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add Laboratory</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Name: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="description" name="description" required>
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
