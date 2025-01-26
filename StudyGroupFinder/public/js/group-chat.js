// Add these variables at the top of your file
let currentImageFile = null;
let previewModal = null;
let currentFile = null;

// Function to handle image clicks
function handleImageClick(fileUrl, fileName = '', fileType = '') {
    const modalImage = document.getElementById('modalImage');
    const downloadButton = document.getElementById('downloadButton');
    const imagePreviewContainer = document.getElementById('downloadImagePreviewContainer');
    const fileInfoContainer = document.getElementById('downloadFileInfoContainer');
    const fileIcon = document.getElementById('downloadFileIcon');
    const fileNameElement = document.getElementById('downloadFileName');
    const fileTypeElement = document.getElementById('downloadFileType');
    
    // Reset containers
    imagePreviewContainer.style.display = 'none';
    fileInfoContainer.style.display = 'none';

    // Set download button properties
    downloadButton.href = fileUrl;
    downloadButton.download = fileName || fileUrl.split('/').pop();

    // Check if it's an image by file extension
    const isImage = /\.(jpg|jpeg|png|gif|bmp)$/i.test(fileUrl);

    if (isImage) {
        // Show image preview
        imagePreviewContainer.style.display = 'block';
        modalImage.src = fileUrl;
    } else {
        // Show file info
        fileInfoContainer.style.display = 'block';
        fileNameElement.textContent = fileName || fileUrl.split('/').pop();
        fileTypeElement.textContent = `Type: ${fileType || 'Unknown'}`;
        fileIcon.className = 'fas fa-4x mb-3 ' + getFileIcon(fileName || fileUrl);
    }
    
    // Show modal
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

// Function to send a message
function sendMessage(event) {
    event.preventDefault();
    const message = document.getElementById('message').value.trim();
    const groupId = document.getElementById('group_id').value;

    if (message === "") {
        return;
    }

    const formData = new FormData();
    formData.append('group_id', groupId);
    formData.append('message', message);
    formData.append('type', 'text');

    fetch('php/chat.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById('message').value = "";
            fetchMessages(groupId);
        } else {
            showAlert("Error sending message: " + data.message, 'danger');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Function to fetch and display messages
function fetchMessages(groupId) {
    fetch(`php/fetch_chat_messages.php?group_id=${groupId}`)
        .then(response => response.json())
        .then(data => {
            const chatBox = document.getElementById('chat-box');
            if (chatBox) {
                chatBox.innerHTML = "";
                data.forEach(message => {
                    const messageElement = document.createElement('div');
                    messageElement.classList.add('message');

                    let messageContent = '';
                    // Check if it's an image by file extension
                    const isImage = message.content && /\.(jpg|jpeg|png|gif|bmp)$/i.test(message.content);
                    
                    if (message.type === 'image' && isImage) {
                        messageContent = createImageMessage(message);
                    } else if (message.type === 'file' || message.type === 'image') {
                        messageContent = createFileMessage(message);
                    } else {
                        messageContent = createTextMessage(message);
                    }
                    
                    messageElement.innerHTML = messageContent;
                    chatBox.appendChild(messageElement);
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        })
        .catch(error => console.error('Error fetching messages:', error));
}

// Function to load members list
function loadMembers() {
    const groupId = document.getElementById('group_id').value;
    fetch(`php/group_data.php?group_id=${groupId}&type=members`)
        .then(response => response.text())
        .then(data => {
            const membersList = document.getElementById('members-list');
            if (membersList) {
                membersList.innerHTML = data;
            }
        })
        .catch(error => console.error('Error loading members:', error));
}

// Function to load sessions list
function loadSessions() {
    const groupId = document.getElementById('group_id').value;
    fetch(`php/group_data.php?group_id=${groupId}&type=sessions`)
        .then(response => response.text())
        .then(data => {
            const sessionsList = document.getElementById('sessions-list');
            if (sessionsList) {
                sessionsList.innerHTML = data;
            }
        })
        .catch(error => console.error('Error loading sessions:', error));
}

// Function to schedule a session
let isSubmitting = false;

function scheduleSession(event) {
    event.preventDefault();
    
    if (isSubmitting) return;
    
    isSubmitting = true;
    const form = event.target;
    const formData = new FormData(form);

    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) submitButton.disabled = true;

    fetch('php/schedule_session.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const scheduleModal = document.getElementById('scheduleModal');
            const modal = bootstrap.Modal.getInstance(scheduleModal);
            if (modal) modal.hide();
            
            form.reset();
            
            loadSessions();
            
            showAlert('Session scheduled successfully!', 'success');
        } else {
            showAlert(data.message || 'Error scheduling session', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error scheduling session', 'danger');
    })
    .finally(() => {
        if (submitButton) submitButton.disabled = false;
        isSubmitting = false;
    });
}

// Function to show alerts
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alertDiv);
        bsAlert.close();
    }, 2000);
}

// Update the checkGroupMembership function
function checkGroupMembership() {
    const groupId = document.getElementById('group_id').value;
    
    fetch(`php/check_membership.php?group_id=${groupId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'removed') {
                showAlert('You have been removed from this group by the admin', 'warning');
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1000);
            } else if (data.is_member === false) {
                showAlert('You are no longer a member of this group', 'warning');
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1000);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Update the DOMContentLoaded event listener
document.addEventListener('DOMContentLoaded', function() {
    const groupId = document.getElementById('group_id')?.value;
    if (groupId) {
        // Initial loads
        fetchMessages(groupId);
        loadMembers();
        loadSessions();

        // Set up periodic updates with longer intervals
        setInterval(() => {
            fetchMessages(groupId);
            loadMembers();
            loadSessions();
        }, 5000);

        // Check membership less frequently (every 30 seconds)
        setInterval(() => {
            checkGroupMembership();
        }, 5000);

        // Add event listener for schedule form
        const scheduleForm = document.getElementById('schedule-form');
        if (scheduleForm) {
            scheduleForm.removeEventListener('submit', scheduleSession);
            scheduleForm.addEventListener('submit', scheduleSession);
        }

        // Initialize the modal
        previewModal = new bootstrap.Modal(document.getElementById('sendPreviewModal'));

        // Update the file input change handler
        const fileUpload = document.getElementById('fileUpload');
        if (fileUpload) {
            fileUpload.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    previewFileInModal(file);
                }
            });
        }
    }
});

// Function to remove a member
function removeMember(userId) {
    if (!confirm('Are you sure you want to remove this member?')) {
        return;
    }

    const groupId = document.getElementById('group_id').value;
    const formData = new FormData();
    formData.append('group_id', groupId);
    formData.append('user_id', userId);

    fetch('php/remove_member.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // If the removed user is the current user, redirect to dashboard
            if (userId == currentUserId) {
                showAlert('You have been removed from the group', 'warning');
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 2000);
            } else {
                loadMembers(); // Reload the members list
                showAlert('Member removed successfully', 'success');
            }
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error removing member', 'danger');
    });
}

// Function to delete a session
function deleteSession(sessionId) {
    if (!confirm('Are you sure you want to delete this study session?')) {
        return;
    }

    const groupId = document.getElementById('group_id').value;
    const formData = new FormData();
    formData.append('session_id', sessionId);
    formData.append('group_id', groupId);

    fetch('php/delete_session.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            loadSessions(); // Reload the sessions list
            showAlert('Session deleted successfully', 'success');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error deleting session', 'danger');
    });
}

// Function to preview image in modal
function previewImageInModal(file) {
    currentImageFile = file;
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const previewImage = document.getElementById('sendPreviewImage');
        previewImage.src = e.target.result;
        previewModal.show();
    };
    
    reader.readAsDataURL(file);
}

// Function to confirm and send image
function confirmAndSendImage() {
    if (!currentImageFile) return;
    
    const formData = new FormData();
    formData.append('group_id', document.getElementById('group_id').value);
    formData.append('image', currentImageFile);
    formData.append('type', 'image');

    fetch('php/chat.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Clear the input and close modal
            document.getElementById('imageUpload').value = '';
            previewModal.hide();
            currentImageFile = null;
            
            // Refresh messages
            fetchMessages(document.getElementById('group_id').value);
            
            // Show success message
            showAlert('Image sent successfully!', 'success');
        } else {
            showAlert('Error sending image: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error sending image', 'danger');
    });
}

// Function to preview files
function previewFileInModal(file) {
    currentFile = file;
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const fileInfoContainer = document.getElementById('fileInfoContainer');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileType = document.getElementById('fileType');
    const fileIcon = document.getElementById('fileIcon');

    // Reset displays
    if (imagePreviewContainer) imagePreviewContainer.style.display = 'none';
    if (fileInfoContainer) fileInfoContainer.style.display = 'none';

    // Set file information
    fileName.textContent = file.name;
    fileSize.textContent = `Size: ${formatFileSize(file.size)}`;
    fileType.textContent = `Type: ${file.type || 'Unknown'}`;

    // Update icon based on file type
    fileIcon.className = 'fas fa-4x mb-3 ' + getFileIcon(file.name);

    // Show appropriate preview
    if (file.type.startsWith('image/')) {
        imagePreviewContainer.style.display = 'block';
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('sendPreviewImage').src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        fileInfoContainer.style.display = 'block';
    }

    previewModal.show();
}

// Helper function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Helper function to get file icon
function getFileIcon(fileName) {
    const extension = fileName.split('.').pop().toLowerCase();
    const iconMap = {
        pdf: 'fa-file-pdf',
        doc: 'fa-file-word',
        docx: 'fa-file-word',
        xls: 'fa-file-excel',
        xlsx: 'fa-file-excel',
        ppt: 'fa-file-powerpoint',
        pptx: 'fa-file-powerpoint',
        zip: 'fa-file-archive',
        rar: 'fa-file-archive',
        '7z': 'fa-file-archive',
        txt: 'fa-file-alt',
        jpg: 'fa-file-image',
        jpeg: 'fa-file-image',
        png: 'fa-file-image',
        gif: 'fa-file-image'
    };
    return iconMap[extension] || 'fa-file';
}

// Function to send file
function confirmAndSendFile() {
    if (!currentFile) return;
    
    const formData = new FormData();
    formData.append('group_id', document.getElementById('group_id').value);
    formData.append('file', currentFile);
    formData.append('type', 'file');
    formData.append('file_name', currentFile.name);

    fetch('php/chat.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Clear the input and close modal
            const fileUpload = document.getElementById('fileUpload');
            if (fileUpload) {
                fileUpload.value = ''; // Reset the file input
            }
            
            // Reset preview containers
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const fileInfoContainer = document.getElementById('fileInfoContainer');
            if (imagePreviewContainer) imagePreviewContainer.style.display = 'none';
            if (fileInfoContainer) fileInfoContainer.style.display = 'none';
            
            // Close modal and reset current file
            previewModal.hide();
            currentFile = null;
            
            // Refresh messages
            fetchMessages(document.getElementById('group_id').value);
            showAlert('File sent successfully!', 'success');
        } else {
            showAlert('Error sending file: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error sending file', 'danger');
    });
}

// Update the createFileMessage function
function createFileMessage(message) {
    const fileName = message.file_name || message.content.split('/').pop();
    const fileIcon = getFileIcon(fileName);
    
    return `
        <div class="message-header">
            <span class="username">${message.username}</span>
            <span class="time">${new Date(message.sent_at).toLocaleTimeString()}</span>
        </div>
        <div class="message-content">
            <div class="file-message" style="cursor: pointer;" 
                onclick="handleImageClick('${message.content}', '${fileName}', '${message.type}')">
                <i class="fas ${fileIcon}"></i>
                <div class="file-info">
                    <span class="file-name">${fileName}</span>
                    <small class="text-muted">Click to download</small>
                </div>
            </div>
        </div>`;
}

// Update the createImageMessage function
function createImageMessage(message) {
    return `
        <div class="message-header">
            <span class="username">${message.username}</span>
            <span class="time">${new Date(message.sent_at).toLocaleTimeString()}</span>
        </div>
        <div class="message-content">
            <img src="${message.content}" alt="Shared image" 
                style="max-width: 200px; cursor: pointer;" 
                onclick="handleImageClick('${message.content}', '${message.content.split('/').pop()}', 'image')" />
        </div>`;
}

function createTextMessage(message) {
    return `
        <div class="message-header">
            <span class="username">${message.username}</span>
            <span class="time">${new Date(message.sent_at).toLocaleTimeString()}</span>
        </div>
        <div class="message-content">
            ${message.content}
        </div>`;
} 