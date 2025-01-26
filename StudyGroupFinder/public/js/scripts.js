// Show registration modal
function showRegisterModal() {
    var registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    registerModal.show();
}

// Send chat message using AJAX
function sendMessage(event) {
    event.preventDefault();
    const message = document.getElementById('message').value.trim();
    const fileInput = document.getElementById('imageUpload');
    const groupId = document.getElementById('group_id').value;

    if (message === "" && fileInput.files.length === 0) {
        alert("Please enter a message or upload an image.");
        return;
    }

    const formData = new FormData();
    formData.append('group_id', groupId);

    if (message !== "") {
        formData.append('message', message);
        formData.append('type', 'text');
    }

    if (fileInput.files.length > 0) {
        formData.append('image', fileInput.files[0]);
        formData.append('type', 'image');
    }

    fetch('php/chat.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById('message').value = "";
            document.getElementById('imageUpload').value = "";
            fetchMessages(groupId); // Reload messages
        } else {
            alert("Error sending message: " + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Periodically load latest chat messages
function fetchMessages(groupId) {
    fetch(`php/fetch_chat_messages.php?group_id=${groupId}`)
        .then(response => response.json())
        .then(data => {
            const chatBox = document.getElementById('chat-box');
            chatBox.innerHTML = "";
            data.forEach(message => {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message');

                let messageContent = '';
                if (message.type === 'image') {
                    messageContent = `
                        <div class="message-header">
                            <span class="username">${message.username}</span>
                            <span class="time">${new Date(message.sent_at).toLocaleTimeString()}</span>
                        </div>
                        <div class="message-content">
                            <img src="${message.content}" alt="Shared image" 
                                style="max-width: 200px; cursor: pointer;" 
                                onclick="handleImageClick('${message.content}')" />
                        </div>`;
                } else {
                    messageContent = `
                        <div class="message-header">
                            <span class="username">${message.username}</span>
                            <span class="time">${new Date(message.sent_at).toLocaleTimeString()}</span>
                        </div>
                        <div class="message-content">
                            ${message.content}
                        </div>`;
                }
                
                messageElement.innerHTML = messageContent;
                chatBox.appendChild(messageElement);
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => console.error('Error fetching messages:', error));
}

// Create session schedule
function scheduleSession(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    fetch('php/schedule_session.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('scheduleModal'));
        modal.hide();
        
        // Show success message
        showAlert('Session scheduled successfully!', 'success');
        
        // Refresh the sessions list
        loadSessions();
        
        // Reset the form
        form.reset();
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error scheduling session', 'danger');
    });
}

// Remove member function
function removeMember(groupId, userId) {
    if (!confirm("Are you sure you want to remove this member?")) return;

    fetch('php/remove_member.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `group_id=${groupId}&user_id=${userId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('remove-message').innerText = 'Member removed successfully!';
            setTimeout(() => { document.getElementById('remove-message').innerText = ''; }, 2000);
            updateMembersList();
        } else {
            document.getElementById('remove-message').innerText = 'Error: ' + data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('remove-message').innerText = 'An error occurred. Please try again.';
    });
}

// Join group
function joinGroup(groupId) {
    fetch('php/join_group.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `group_id=${groupId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Successfully joined the group!');
            updateJoinedGroupsList();
            updateAvailableGroupsList();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error joining group:', error));
}

// Leave group
function leaveGroup(groupId) {
    fetch('php/leave_group.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `group_id=${groupId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Successfully left the group!');
            updateJoinedGroupsList();
            updateAvailableGroupsList();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error leaving group:', error));
}

// Delete group
function deleteGroup(groupId) {
    fetch('php/delete_group.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `group_id=${groupId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Group deleted successfully!');
            updateJoinedGroupsList();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error deleting group:', error));
}

// Update joined groups list
function updateJoinedGroupsList() {
    fetch('php/dashboard_data.php?type=joined')
        .then(response => response.text())
        .then(html => {
            document.getElementById('joined-groups-list').innerHTML = html;
        })
        .catch(error => console.error('Error fetching joined groups:', error));
}

// Update available groups list
function updateAvailableGroupsList() {
    fetch('php/dashboard_data.php?type=available')
        .then(response => response.text())
        .then(html => {
            document.getElementById('available-groups-list').innerHTML = html;
        })
        .catch(error => console.error('Error fetching available groups:', error));
}

// Update members list
function updateMembersList() {
    const groupId = document.getElementById('group_id').value;
    fetch(`php/group_data.php?group_id=${groupId}&type=members`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('members-list').innerHTML = html;
        })
        .catch(error => console.error('Error fetching members list:', error));
}

// Update sessions list
function updateSessionsList() {
    const groupId = document.getElementById('group_id').value;
    fetch(`php/get_sessions.php?group_id=${groupId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('sessions-list').innerHTML = html;
        })
        .catch(error => console.error('Error fetching sessions list:', error));
}

// Check membership status every 5 seconds
function checkMembershipStatus() {
    const groupId = document.getElementById('group_id').value;
    fetch(`php/check_membership.php?group_id=${groupId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.is_member) {
                window.location.href = 'dashboard.php?message=You are no longer a member of this group';
            }
        })
        .catch(error => console.error('Error checking membership:', error));
}

// Initialize auto-refresh
function initAutoRefresh() {
    const groupId = document.getElementById('group_id').value;
    setInterval(() => fetchMessages(groupId), 5000);
    setInterval(checkMembershipStatus, 5000);
}

// Load group lists when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateJoinedGroupsList();
    updateAvailableGroupsList();
});

// Create group
function createGroup(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    fetch('php/create_group.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Close modal and show success message
            const modal = bootstrap.Modal.getInstance(document.getElementById('createGroupModal'));
            modal.hide();
            form.reset();
            showAlert('Group created successfully!', 'success');
            
            // Update group lists
            updateJoinedGroupsList();
            updateAvailableGroupsList();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error creating group', 'danger');
    });
}

// Delete session
function deleteSession(sessionId) {
    if (!confirm('Are you sure you want to delete this session?')) {
        return;
    }

    fetch('php/delete_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `session_id=${sessionId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('Session deleted successfully!', 'success');
            updateSessionsList();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error deleting session', 'danger');
    });
}

// Handle image upload
function uploadImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        displayImage(e.target.result);
    };
    reader.readAsDataURL(file);
}

// Display image preview
function displayImage(url) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = `<img src="${url}" style="max-width: 200px; margin: 10px 0;" />`;
}

// Show image modal
function showImageModal(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('fullImage');
    modalImg.src = imageUrl;
    modal.style.display = "flex";  // Use "flex" for center alignment
}

// Close modal
function closeModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = "none";
}

// Show alert message
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const alertContainer = document.getElementById('alert-container');
    if (alertContainer) {
        alertContainer.appendChild(alertDiv);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Load sessions
function loadSessions() {
    const groupId = document.getElementById('group_id').value;
    fetch(`php/get_sessions.php?group_id=${groupId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('sessions-list').innerHTML = html;
        })
        .catch(error => console.error('Error loading sessions:', error));
}

// Handle image click
function handleImageClick(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('fullImage');
    modalImg.src = imageUrl;
    modal.style.display = "block";
}

// Display message
function displayMessage(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'alert alert-info';
    messageDiv.textContent = message;
    document.body.insertBefore(messageDiv, document.body.firstChild);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}