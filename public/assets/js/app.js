// Kizo SOP Manager Global JS

document.addEventListener('DOMContentLoaded', function() {
    
    // Checklist Row Interaction
    const checklistRows = document.querySelectorAll('.checklist-row');
    const submitSopBtn = document.getElementById('submitSopBtn');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    if (checklistRows.length > 0) {
        checklistRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Prevent double trigger if clicking directly on checkbox
                if (e.target.tagName.toLowerCase() === 'input') return;
                
                if (this.classList.contains('disabled') || this.hasAttribute('disabled')) return;
                
                const checkbox = this.querySelector('.step-checkbox');
                if (checkbox.disabled) return;
                
                const stepId = this.dataset.stepId;
                const isCompleted = !checkbox.checked; // Toggle state
                
                // Optimistic UI update
                checkbox.checked = isCompleted;
                if (isCompleted) {
                    this.classList.add('completed');
                } else {
                    this.classList.remove('completed');
                }
                
                updateProgressUI();
                
                // AJAX Update
                fetch('index.php?action=toggle_step', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `log_id=${window.sopLogId}&step_id=${stepId}&completed=${isCompleted}`
                })
                .then(response => response.json())
                .catch(error => console.error('Error syncing step:', error));
            });
        });
    }

    function updateProgressUI() {
        if (!progressBar) return;
        
        const total = window.sopTotalSteps || 0;
        const completed = document.querySelectorAll('.step-checkbox:checked').length;
        
        if (progressText) {
            progressText.innerText = `${completed} / ${total}`;
        }
        
        const percent = total > 0 ? (completed / total) * 100 : 0;
        progressBar.style.width = percent + '%';
        
        if (submitSopBtn) {
            submitSopBtn.disabled = (completed !== total || total === 0);
        }
    }

    // Photo Upload Handling
    const photoInputs = document.querySelectorAll('.photo-upload-input');
    photoInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const stepId = this.dataset.stepId;
                const file = this.files[0];
                const section = this.closest('.photo-upload-section');
                const progressDiv = section.querySelector('.upload-progress');
                const label = section.querySelector('label');
                
                label.classList.add('d-none');
                progressDiv.classList.remove('d-none');
                
                const formData = new FormData();
                formData.append('log_id', window.sopLogId);
                formData.append('step_id', stepId);
                formData.append('photo', file);
                
                fetch('index.php?action=upload_proof', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        section.innerHTML = `<div class="d-flex align-items-center text-success fw-500"><i class="fas fa-image me-2 fs-4"></i> Photo uploaded!</div>`;
                        
                        // Enable the checklist row
                        const row = document.querySelector(`.checklist-row[data-step-id="${stepId}"]`);
                        row.classList.remove('requires-photo-pending');
                        row.querySelector('.step-checkbox').disabled = false;
                        
                        showToast('Photo uploaded successfully');
                    } else {
                        label.classList.remove('d-none');
                        progressDiv.classList.add('d-none');
                        showToast(data.message || 'Upload failed');
                    }
                })
                .catch(error => {
                    console.error('Error uploading photo:', error);
                    label.classList.remove('d-none');
                    progressDiv.classList.add('d-none');
                    showToast('Network error during upload');
                });
            }
        });
    });

    // Submit SOP Flow
    if (submitSopBtn) {
        submitSopBtn.addEventListener('click', function() {
            // Because the final step triggers the completion in the backend automatically,
            // we just show the success overlay and redirect visually.
            const overlay = document.getElementById('globalSuccessOverlay');
            if (overlay) {
                overlay.classList.add('show');
            }
        });
    }

    // Auto-hiding toasts (if any are manually triggered)
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    const toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, { delay: 3000 });
    });
});

function showToast(message) {
    const toastBody = document.getElementById('generalToastBody');
    const toastEl = document.getElementById('generalToast');
    if (toastBody && toastEl) {
        toastBody.innerText = message;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
}
