<div id="updateModal" class="modal fade">
    <div class="modal-dialog">
        <form id="updateForm" class="modal-content">
            <input type="hidden" name="user_role" id="user_role" value="Borrower">
            <div class="modal-header">
                <h3 class="modal-title">Update Borrower</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="first_name">First Name: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="middle_name">Middle Name:</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="extension_name">Extension Name:</label>
                    <input type="text" class="form-control" id="extension_name" name="extension_name">
                </div>
                <div class="form-group">
                    <label for="contact_no">Contact Number: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="contact_no" name="contact_no" required>
                </div>
                <div class="form-group">
                    <label for="email">Email: <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required>
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
