<div id="confirmModal" class="modal-overlay" style="display: none;">
    <div class="modal-container max-w-md">
        <div class="modal-header">
            <h3 id="modalTitle" class="font-semibold">Confirm Action</h3>
            <button onclick="closeConfirmModal()" class="modal-close">&times;</button>
        </div>
        <div class="modal-body p-6">
            <p id="modalMessage" class="text-gray-700"></p>
        </div>
        <div class="modal-footer flex justify-end gap-3 p-4 border-t border-gray-100">
            <button onclick="closeConfirmModal()" class="btn-secondary">Cancel</button>
            <button id="modalConfirmBtn" class="btn-primary">Confirm</button>
        </div>
    </div>
</div>

<script>
let confirmCallback = null;

function showConfirmModal(title, message, onConfirm) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalMessage').innerHTML = message;
    confirmCallback = onConfirm;
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
    confirmCallback = null;
}

document.getElementById('modalConfirmBtn').onclick = function() {
    if (confirmCallback) {
        confirmCallback();
    }
    closeConfirmModal();
};
</script>