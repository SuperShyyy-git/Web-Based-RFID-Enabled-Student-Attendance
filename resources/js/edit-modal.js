document.addEventListener("DOMContentLoaded", function () {

    // Helper function to safely set element value
    function setValueIfExists(elementId, value) {
        const el = document.getElementById(elementId);
        if (el) el.value = value || '';
    }

    // Edit Year Level Modal
    document.querySelectorAll(".edit-year-level-btn").forEach(button => {
        button.addEventListener("click", function () {
            setValueIfExists("edit-year-level-id", this.getAttribute("data-id"));
            setValueIfExists("edit-year-level-name", this.getAttribute("data-name"));
            setValueIfExists("edit-year-level-description", this.getAttribute("data-description"));
        });
    });

    // Edit School Year Modal
    document.querySelectorAll(".edit-school-year-btn").forEach(button => {
        button.addEventListener("click", function () {
            setValueIfExists("edit-school-year-id", this.getAttribute("data-id"));
            setValueIfExists("edit-school-year-name", this.getAttribute("data-name"));
            setValueIfExists("edit-school-year-description", this.getAttribute("data-description"));
            setValueIfExists("edit-school-year-code", this.getAttribute("data-code"));
        });
    });

    // Edit Section Modal
    document.querySelectorAll(".edit-section-btn").forEach(button => {
        button.addEventListener("click", function () {
            setValueIfExists("edit-section-id", this.getAttribute("data-id"));
            setValueIfExists("edit-section-name", this.getAttribute("data-name"));
            setValueIfExists("edit-section-description", this.getAttribute("data-description"));
            setValueIfExists("edit-section-code", this.getAttribute("data-code"));
            setValueIfExists("edit-section-department", this.getAttribute("data-department-id"));
            setValueIfExists("edit-section-program", this.getAttribute("data-program-id"));
            setValueIfExists("edit-section-year-level", this.getAttribute("data-year-level-id"));
        });
    });

    // Edit Program Modal
    document.querySelectorAll(".edit-program-btn").forEach(button => {
        button.addEventListener("click", function () {
            setValueIfExists("edit-program-id", this.getAttribute("data-id"));
            setValueIfExists("edit-program-name", this.getAttribute("data-name"));
            setValueIfExists("edit-program-description", this.getAttribute("data-description"));
            setValueIfExists("edit-program-code", this.getAttribute("data-code"));
            setValueIfExists("edit-program-department", this.getAttribute("data-department-id"));
        });
    });

    // Edit Department Modal
    document.querySelectorAll(".edit-department-btn").forEach(button => {
        button.addEventListener("click", function () {
            setValueIfExists("edit-id", this.getAttribute("data-id"));
            setValueIfExists("edit-name", this.getAttribute("data-name"));
            setValueIfExists("edit-description", this.getAttribute("data-description"));
            setValueIfExists("edit-code", this.getAttribute("data-code"));
        });
    });

    // Edit Student Modal
    document.querySelectorAll(".edit-student-btn").forEach(button => {
        button.addEventListener("click", function () {
            setValueIfExists("edit-record-id", this.getAttribute("data-record-id"));
            setValueIfExists("edit-student-id", this.getAttribute("data-id"));
            setValueIfExists("edit-student-first-name", this.getAttribute("data-first-name"));
            setValueIfExists("edit-student-last-name", this.getAttribute("data-last-name"));
            setValueIfExists("edit-student-middle-name", this.getAttribute("data-middle-name"));
            setValueIfExists("edit-student-section", this.getAttribute("data-section-id"));
            setValueIfExists("edit-student-rfid", this.getAttribute("data-rfid"));
            setValueIfExists("edit-student-school-year", this.getAttribute("data-school-year-id"));
            setValueIfExists("edit-student-year-level", this.getAttribute("data-year-level-id"));
            setValueIfExists("edit-student-department", this.getAttribute("data-department-id"));
            setValueIfExists("edit-student-program", this.getAttribute("data-program-id"));
        });
    });
});
