/*=========================================================
        OLD AGE HOME MANAGEMENT SYSTEM
            USER MANAGEMENT MODULE
=========================================================*/

document.addEventListener("DOMContentLoaded", () => {

    /*=====================================================
                    MODAL
    =====================================================*/

    const addUserBtn = document.querySelector(".add-user-btn");

    const addUserModal = document.getElementById("addUserModal");

    const closeModal = document.querySelector(".close-modal");

    const cancelBtn = document.querySelector(".cancel-btn");

    if(addUserBtn){

        addUserBtn.addEventListener("click", () => {

            addUserModal.classList.add("active");

        });

    }

    if(closeModal){

        closeModal.addEventListener("click", () => {

            addUserModal.classList.remove("active");

        });

    }

    if(cancelBtn){

        cancelBtn.addEventListener("click", () => {

            addUserModal.classList.remove("active");

        });

    }

    window.addEventListener("click",(e)=>{

        if(e.target===addUserModal){

            addUserModal.classList.remove("active");

        }

    });

    /*=====================================================
                    TOAST MESSAGE
    =====================================================*/

    const toast = document.getElementById("toast");

    function showToast(message){

        toast.innerHTML = message;

        toast.classList.add("show");

        setTimeout(()=>{

            toast.classList.remove("show");

        },3000);

    }

    /*=====================================================
                    FORM SUBMIT
    =====================================================*/

    const addUserForm = document.getElementById("addUserForm");

    if(addUserForm){

        addUserForm.addEventListener("submit",(e)=>{

            e.preventDefault();

            showToast("User added successfully.");

            addUserModal.classList.remove("active");

            addUserForm.reset();

        });

    }

    /*=====================================================
                    DELETE MODAL
    =====================================================*/

    const deleteModal = document.getElementById("deleteModal");

    const deleteButtons = document.querySelectorAll(".delete");

    const cancelDelete = document.querySelector(".cancel-delete");

    const confirmDelete = document.querySelector(".confirm-delete");

    deleteButtons.forEach(btn=>{

        btn.addEventListener("click",()=>{

            deleteModal.classList.add("active");

        });

    });

    if(cancelDelete){

        cancelDelete.addEventListener("click",()=>{

            deleteModal.classList.remove("active");

        });

    }

    if(confirmDelete){

        confirmDelete.addEventListener("click",()=>{

            deleteModal.classList.remove("active");

            showToast("User deleted successfully.");

        });

    }

    window.addEventListener("click",(e)=>{

        if(e.target===deleteModal){

            deleteModal.classList.remove("active");

        }

    });

    /*=====================================================
                    SEARCH
    =====================================================*/

    const searchInput = document.getElementById("searchUser");

    if(searchInput){

        searchInput.addEventListener("keyup",()=>{

            const filter = searchInput.value.toLowerCase();

            const rows = document.querySelectorAll("#userTable tr");

            rows.forEach(row=>{

                const text = row.innerText.toLowerCase();

                row.style.display = text.includes(filter) ? "" : "none";

            });

        });

    }

    /*=====================================================
                    FILTERS
    =====================================================*/

    const roleFilter = document.getElementById("roleFilter");

    const statusFilter = document.getElementById("statusFilter");

    function filterTable(){

        const roleValue = roleFilter.value.toLowerCase();

        const statusValue = statusFilter.value.toLowerCase();

        const rows = document.querySelectorAll("#userTable tr");

        rows.forEach(row=>{

            const role = row.children[4].innerText.toLowerCase();

            const status = row.children[5].innerText.toLowerCase();

            const roleMatch = roleValue==="" || role.includes(roleValue);

            const statusMatch = statusValue==="" || status.includes(statusValue);

            row.style.display = (roleMatch && statusMatch) ? "" : "none";

        });

    }

    if(roleFilter){

        roleFilter.addEventListener("change",filterTable);

    }

    if(statusFilter){

        statusFilter.addEventListener("change",filterTable);

    }

    /*=====================================================
                    VIEW BUTTON
    =====================================================*/

    document.querySelectorAll(".view").forEach(btn=>{

        btn.addEventListener("click",()=>{

            showToast("View user feature coming soon.");

        });

    });

    /*=====================================================
                    EDIT BUTTON
    =====================================================*/

    document.querySelectorAll(".edit").forEach(btn=>{

        btn.addEventListener("click",()=>{

            showToast("Edit user feature coming soon.");

        });

    });

    /*=====================================================
                    EXPORT BUTTON
    =====================================================*/

    const exportBtn = document.querySelector(".export-btn");

    if(exportBtn){

        exportBtn.addEventListener("click",()=>{

            showToast("Export feature coming soon.");

        });

    }

});