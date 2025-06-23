<div id="addModal" class="modal fade">
    <div class="modal-dialog">
        <form id="addForm" class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add User</h3>
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
                <div class="form-group">
                    <label for="username">Username: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password: <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="user_role">User Role: <span class="text-danger">*</span></label>
                    <select class="form-control" id="user_role" name="user_role" required>
                        <option value="">Select Role</option>
                        <option value="Laboratory Head">Laboratory Head</option>
                        <option value="Laboratory In-charge">Laboratory In-charge</option>
                        <option value="Employee">Employee</option>
                        <option value="Borrower">Borrower</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="laboratory_id">Laboratory:</label>
                    <select class="form-control" id="laboratory_id" name="laboratory_id">
                        <option value="">Select Laboratory</option>
                        @foreach (\App\Models\Laboratory::all() as $laboratory)
                            <option value="{{ $laboratory->id }}">{{ $laboratory->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status: <span class="text-danger">*</span></label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
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
