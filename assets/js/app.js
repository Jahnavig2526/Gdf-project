// File operations with AJAX
const API = {
    async listFiles(domain) {
        try {
            const response = await fetch(`ajax_handler.php?action=list_files&domain=${domain}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error listing files:', error);
            throw error;
        }
    },

    async uploadFiles(domain, files) {
        try {
            const formData = new FormData();
            Array.from(files).forEach(file => {
                formData.append('files[]', file);
            });
            formData.append('domain', domain);

            const response = await fetch('ajax_handler.php?action=upload', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error uploading files:', error);
            throw error;
        }
    },

    async deleteFile(id) {
        try {
            const response = await fetch('ajax_handler.php?action=delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id })
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error deleting file:', error);
            throw error;
        }
    },

    downloadFile(id) {
        window.location.href = `ajax_handler.php?action=download&id=${id}`;
    }
};

// Authentication system
let isAuthenticated = false;
let currentUser = null;

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    checkAuthStatus();
    initializeNavigation();
    initializeModalEvents();
});

// Authentication functions
async function checkAuthStatus() {
    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: 'check_auth' })
        });
        
        const data = await response.json();
        if (data.success && data.authenticated) {
            isAuthenticated = true;
            currentUser = data.user;
            updateAuthUI();
        }
    } catch (error) {
        console.error('Auth check error:', error);
    }
}

function showLoginModal() {
    document.getElementById('loginModal').style.display = 'block';
    document.getElementById('username').focus();
}

function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
    document.getElementById('loginError').style.display = 'none';
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
}

async function attemptLogin(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('loginError');

    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'login',
                username: username,
                password: password
            })
        });

        const data = await response.json();
        if (data.success) {
            isAuthenticated = true;
            currentUser = data.user;
            updateAuthUI();
            closeLoginModal();
            showNotification('Login successful! You can now upload files.', 'success');
        } else {
            errorDiv.textContent = data.message || 'Invalid username or password';
            errorDiv.style.display = 'block';
            document.getElementById('password').value = '';
        }
    } catch (error) {
        console.error('Login error:', error);
        errorDiv.textContent = 'Login failed. Please try again.';
        errorDiv.style.display = 'block';
    }
}

async function logout() {
    try {
        const response = await fetch('logout.php', {
            method: 'POST'
        });
        const data = await response.json();
        
        if (data.success) {
            isAuthenticated = false;
            currentUser = null;
            updateAuthUI();
            showNotification('Logged out successfully.', 'info');
        }
    } catch (error) {
        console.error('Logout error:', error);
        // Force logout on client side even if server request fails
        isAuthenticated = false;
        currentUser = null;
        updateAuthUI();
        showNotification('Logged out.', 'info');
    }
}

// File Operations
async function viewFiles(domain) {
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');

    modalTitle.textContent = `${domainNames[domain]} - Files`;
    modalBody.innerHTML = '<div style="text-align: center; padding: 2rem;"><div class="loading"></div><p>Loading files...</p></div>';
    openModal();

    try {
        const data = await API.listFiles(domain);
        const files = data.files || [];

        if (files.length === 0) {
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 2rem;">
                    <p style="color: #666; font-size: 1.1rem;">No files found in ${domainNames[domain]}</p>
                    <button onclick="authorizedUpload('${domain}')" style="margin-top: 1rem; padding: 0.8rem 1.5rem; background: #1E90FF; color: white; border: none; border-radius: 6px; cursor: pointer;">Upload Files</button>
                </div>
            `;
            return;
        }

        let fileListHTML = '<div class="file-list">';
        files.forEach((file) => {
            fileListHTML += `
                <div class="file-item">
                    <div class="file-info">
                        <div class="file-name">${getFileIcon(file.type)} ${file.name}</div>
                        <div class="file-size">${formatFileSize(file.size)} - Uploaded: ${formatDate(file.uploadDate)}</div>
                    </div>
                    <div class="file-actions-modal">
                        <button onclick="downloadFile(${file.id})">Download</button>
                        ${isAuthenticated ? `<button onclick="deleteFile(${file.id}, '${domain}')" style="background: #f44336;">Delete</button>` : ''}
                    </div>
                </div>
            `;
        });

        fileListHTML += `</div>
            <div style="text-align: center; margin-top: 1rem;">
                <button onclick="authorizedUpload('${domain}')" style="padding: 0.8rem 1.5rem; background: #1E90FF; color: white; border: none; border-radius: 6px; cursor: pointer; margin-right: 1rem;">Add More Files</button>
                <button onclick="downloadAllFiles('${domain}')" style="padding: 0.8rem 1.5rem; background: #4CAF50; color: white; border: none; border-radius: 6px; cursor: pointer;">Download All</button>
            </div>
        `;

        modalBody.innerHTML = fileListHTML;
    } catch (error) {
        console.error('Error loading files:', error);
        modalBody.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #f44336;">
                <p>Failed to load files. Please try again.</p>
                <button onclick="viewFiles('${domain}')" style="margin-top: 1rem; padding: 0.8rem 1.5rem; background: #1E90FF; color: white; border: none; border-radius: 6px; cursor: pointer;">Retry</button>
            </div>
        `;
    }
}

async function processFiles(files, domain) {
    if (files.length === 0) return;

    const modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <div class="loading"></div>
            <p>Uploading ${files.length} file(s)...</p>
        </div>
    `;

    try {
        const response = await API.uploadFiles(domain, files);
        if (response.success) {
            showNotification(`${files.length} file(s) uploaded successfully`, 'success');
            setTimeout(() => viewFiles(domain), 1500);
        } else {
            throw new Error(response.message || 'Upload failed');
        }
    } catch (error) {
        console.error('Upload error:', error);
        showNotification('Upload failed: ' + error.message, 'error');
        setTimeout(() => uploadFiles(domain), 2000);
    }
}

async function deleteFile(id, domain) {
    if (!isAuthenticated) {
        showNotification('Unauthorized action', 'error');
        return;
    }

    if (!confirm('Are you sure you want to delete this file?')) {
        return;
    }

    try {
        const response = await API.deleteFile(id);
        if (response.success) {
            showNotification('File deleted successfully', 'success');
            viewFiles(domain);
        } else {
            showNotification('Error deleting file: ' + response.message, 'error');
        }
    } catch (error) {
        console.error('Delete error:', error);
        showNotification('Failed to delete file', 'error');
    }
}

function downloadFile(id) {
    API.downloadFile(id);
}

async function downloadAllFiles(domain) {
    try {
        const data = await API.listFiles(domain);
        const files = data.files || [];
        
        if (files.length === 0) {
            showNotification('No files to download', 'info');
            return;
        }

        files.forEach((file, index) => {
            setTimeout(() => API.downloadFile(file.id), index * 200);
        });

        showNotification(`Downloading ${files.length} files`, 'success');
    } catch (error) {
        console.error('Download error:', error);
        showNotification('Failed to download files', 'error');
    }
}

// Utility functions [keep the existing utility functions from the HTML file]
function getFileIcon(mimeType) {
    if (mimeType.startsWith('image/')) return '🖼';
    if (mimeType.startsWith('video/')) return '🎥';
    if (mimeType.startsWith('audio/')) return '🎵';
    if (mimeType.includes('pdf')) return '📄';
    if (mimeType.includes('word') || mimeType.includes('document')) return '📝';
    if (mimeType.includes('sheet') || mimeType.includes('excel')) return '📊';
    if (mimeType.includes('presentation') || mimeType.includes('powerpoint')) return '📈';
    if (mimeType.includes('zip') || mimeType.includes('archive')) return '📦';
    if (mimeType.includes('text/')) return '📄';
    return '📄';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.classList.add('show');
    setTimeout(() => notification.classList.remove('show'), 3000);
}