<!-- Edit Multiple Student Record Modal -->
<div class="modal fade" id="edit-multiple-student-modal" tabindex="-1" aria-labelledby="edit-multiple-student-modal-label"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-multiple-student-modal-label">Edit Multiple Student Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-multiple-student-form" action="{{route('process-edit-student-record')}}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">Selected Student Names:</p>
                            <ul id="selected-student-names-display" class="list-group list-unstyled"></ul>
                        
                            <!-- Pagination Buttons -->
                            <nav class="mt-3">
                                <ul id="pagination" class="pagination justify-content-center"></ul>
                            </nav>
                        </div>
                        <!-- Hidden inputs to store selected student IDs and names -->
                        <input type="hidden" id="selected-student-ids" name="selected_student_ids">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="multi-edit-department" class="form-label">Department</label>
                                <select name="department-id" id="multi-edit-department" class="form-control">
                                    <option value="" disabled selected>Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->department_id }}">{{ $department->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="multi-edit-program" class="form-label">Program</label>
                                <select name="program-id" id="multi-edit-program" class="form-control">
                                    <option value="" disabled selected>Select Program</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->program_id }}"
                                            data-department-id="{{ $program->department_id }}">
                                            {{ $program->program_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="multi-edit-year-level" class="form-label">Year Level</label>
                                <select name="year-level-id" id="multi-edit-year-level" class="form-control">
                                    <option value="" disabled selected>Select Year Level</option>
                                    @foreach($yearlevels as $yearlevel)
                                        <option value="{{ $yearlevel->year_level_id }}">{{ $yearlevel->year_level_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="multi-edit-section" class="form-label">Section</label>
                                <select name="section-id" id="multi-edit-section" class="form-control">
                                    <option value="" disabled selected>Select Section</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->section_id }}">{{ $section->section_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success  float-end ms-5">Save Changes</button>
                </form>
                
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for filtering programs based on department -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const multiDepartmentSelect = document.getElementById('multi-edit-department');
        const multiProgramSelect = document.getElementById('multi-edit-program');
        
        if (!multiDepartmentSelect || !multiProgramSelect) return;
        
        const allMultiProgramOptions = Array.from(multiProgramSelect.options);

        multiDepartmentSelect.addEventListener('change', function () {
            const selectedDeptId = this.value;

            // Clear and reset the program dropdown
            multiProgramSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';

            allMultiProgramOptions.forEach(option => {
                const deptId = option.getAttribute('data-department-id');
                if (deptId === selectedDeptId) {
                    multiProgramSelect.appendChild(option.cloneNode(true));
                }
            });
        });
    });
</script>
