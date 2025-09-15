// =====================
// User & Login Handling
// =====================
let currentUser = null; // Store logged-in user info

document.addEventListener('DOMContentLoaded', () => {
  const username = sessionStorage.getItem('username');
  if (!username) {
    // Not logged in, redirect to login page
    window.location.href = 'login.html';
  } else {
    currentUser = { username };
    initializeNavigation();
    initializeModalEvents();
    showNotification(`Logged in as ${username}`, 'success');
  }
});

// =====================
// File Storage (Demo)
// =====================
let fileStorage = {
  webdev: [
    { name: 'index.html', size: '2.5 KB', type: 'text/html', uploadDate: '2025-01-15' },
    { name: 'styles.css', size: '3.2 KB', type: 'text/css', uploadDate: '2025-01-14' },
    { name: 'script.js', size: '1.8 KB', type: 'text/javascript', uploadDate: '2025-01-13' }
  ],
  project: [
    { name: 'project-plan.pdf', size: '125 KB', type: 'application/pdf', uploadDate: '2025-01-12' },
    { name: 'timeline.xlsx', size: '45 KB', type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', uploadDate: '2025-01-11' }
  ],
  hr: [
    { name: 'employee-handbook.pdf', size: '1.2 MB', type: 'application/pdf', uploadDate: '2025-01-10' },
    { name: 'policies.docx', size: '250 KB', type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', uploadDate: '2025-01-09' }
  ],
  digital: [
    { name: 'campaign-banner.png', size: '500 KB', type: 'image/png', uploadDate: '2025-01-08' },
    { name: 'social-media-kit.zip', size: '2.1 MB', type: 'application/zip', uploadDate: '2025-01-07' }
  ],
  social: [
    { name: 'community-report.pdf', size: '800 KB', type: 'application/pdf', uploadDate: '2025-01-06' },
    { name: 'volunteer-list.csv', size: '15 KB', type: 'text/csv', uploadDate: '2025-01-05' }
  ]
};

const domainNames = {
  webdev: 'Web Development',
  project: 'Project Management',
  hr: 'Human Resources',
  digital: 'Digital Marketing',
  social: 'Social Work'
};

// =====================
// Navigation
// =====================
function initializeNavigation() {
  const navLinks = document.querySelectorAll('nav a');
  navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const targetId = this.getAttribute('href').substring(1);
      scrollToSection(targetId);
      updateActiveNavLink(this);
    });
  });
}

function scrollToSection(sectionId) {
  const section = document.getElementById(sectionId);
  if (section) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function updateActiveNavLink(activeLink) {
  document.querySelectorAll('nav a').forEach(link => link.classList.remove('active'));
  if (activeLink) activeLink.classList.add('active');
}

// =====================
// Modal Handling
// =====================
function initializeModalEvents() {
  const modal = document.getElementById('fileModal');
  window.addEventListener('click', event => { if (event.target === modal) closeModal(); });
  document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
}

function openModal() {
  const modal = document.getElementById('fileModal');
  if (modal) {
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
  }
}

function closeModal() {
  const modal = document.getElementById('fileModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
  }
}

// =====================
// File Operations
// =====================
function viewFiles(domain) {
  const modalTitle = document.getElementById('modalTitle');
  const modalBody = document.getElementById('modalBody');
  if (!modalTitle || !modalBody) return;

  modalTitle.textContent = `${domainNames[domain]} - Files`;
  const files = fileStorage[domain] || [];

  if (files.length === 0) {
    modalBody.innerHTML = `
      <div style="text-align:center; padding:2rem;">
        <p style="color:#666; font-size:1.1rem;">No files found in ${domainNames[domain]}</p>
        <button onclick="uploadFiles('${domain}')" style="margin-top:1rem; padding:0.8rem 1.5rem; background:#1E90FF; color:white; border:none; border-radius:6px; cursor:pointer;">Upload Files</button>
      </div>
    `;
  } else {
    let fileListHTML = `<div class="file-list">`;
    files.forEach((file, index) => {
      fileListHTML += `
        <div class="file-item">
          <div class="file-info">
            <div class="file-name">${getFileIcon(file.type)} ${file.name}</div>
            <div class="file-size">${file.size} - Uploaded: ${formatDate(file.uploadDate)}</div>
          </div>
          <div class="file-actions-modal">
            <button onclick="downloadFile('${domain}', ${index})">Download</button>
            ${currentUser && currentUser.username === 'admin' ? `<button onclick="deleteFile('${domain}', ${index})" style="background:#f44336;">Delete</button>` : ''}
          </div>
        </div>
      `;
    });
    fileListHTML += `</div>
      <div style="text-align:center; margin-top:1rem;">
        <button onclick="uploadFiles('${domain}')" style="padding:0.8rem 1.5rem; background:#1E90FF; color:white; border:none; border-radius:6px; cursor:pointer; margin-right:1rem;">Add More Files</button>
        <button onclick="downloadAllFiles('${domain}')" style="padding:0.8rem 1.5rem; background:#4CAF50; color:white; border:none; border-radius:6px; cursor:pointer;">Download All</button>
      </div>
    `;
    modalBody.innerHTML = fileListHTML;
  }

  openModal();
}

function uploadFiles(domain) {
  const modalTitle = document.getElementById('modalTitle');
  const modalBody = document.getElementById('modalBody');
  if (!modalTitle || !modalBody) return;

  modalTitle.textContent = `${domainNames[domain]} - Upload Files`;
  modalBody.innerHTML = `
    <div class="upload-area" onclick="triggerFileInput()" ondrop="handleFileDrop(event, '${domain}')" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
      <div style="font-size:3rem; margin-bottom:1rem;">📁</div>
      <h4>Click to select files or drag and drop</h4>
      <p style="color:#666; margin-top:0.5rem;">Support for all file types</p>
      <input type="file" id="fileInput" class="upload-input" multiple onchange="handleFileSelect(event, '${domain}')">
    </div>
    <div style="text-align:center; margin-top:1rem;">
      <button onclick="viewFiles('${domain}')" style="padding:0.8rem 1.5rem; background:#666; color:white; border:none; border-radius:6px; cursor:pointer;">View Existing Files</button>
    </div>
  `;

  openModal();
}

function downloadFile(domain, index) {
  const file = fileStorage[domain] && fileStorage[domain][index];
  if (file) {
    const blob = new Blob(['This is a demo file: ' + file.name], { type: file.type });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = file.name;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showNotification(`Downloaded: ${file.name}`, 'success');
  }
}

function downloadAllFiles(domain) {
  const files = fileStorage[domain] || [];
  if (!files.length) { showNotification('No files to download', 'info'); return; }

  files.forEach((file, index) => {
    setTimeout(() => downloadFile(domain, index), index * 200);
  });
  showNotification(`Downloading ${files.length} files from ${domainNames[domain]}`, 'success');
}

function deleteFile(domain, index) {
  const file = fileStorage[domain] && fileStorage[domain][index];
  if (file && currentUser && currentUser.username === 'admin') {
    if (confirm(`Are you sure you want to delete "${file.name}"?`)) {
      fileStorage[domain].splice(index, 1);
      showNotification(`Deleted: ${file.name}`, 'success');
      viewFiles(domain);
    }
  } else {
    showNotification('Only admin can delete files', 'error');
  }
}

// =====================
// File Input Handling
// =====================
function triggerFileInput() { document.getElementById('fileInput')?.click(); }
function handleFileSelect(event, domain) { processFiles(Array.from(event.target.files), domain); }
function handleFileDrop(event, domain) {
  event.preventDefault();
  event.target.closest('.upload-area')?.classList.remove('dragover');
  processFiles(Array.from(event.dataTransfer.files), domain);
}
function handleDragOver(event) { event.preventDefault(); event.target.closest('.upload-area')?.classList.add('dragover'); }
function handleDragLeave(event) { event.preventDefault(); event.target.closest('.upload-area')?.classList.remove('dragover'); }
function processFiles(files, domain) {
  if (!files.length) return;
  files.forEach(file => {
    const fileObj = {
      name: file.name,
      size: formatFileSize(file.size),
      type: file.type || 'application/octet-stream',
      uploadDate: new Date().toISOString().split('T')[0]
    };
    if (!fileStorage[domain]) fileStorage[domain] = [];
    fileStorage[domain].push(fileObj);
  });
  showNotification(`${files.length} file(s) uploaded to ${domainNames[domain]}`, 'success');
  setTimeout(() => viewFiles(domain), 1500);
}

// =====================
// Utilities
// =====================
function getFileIcon(mimeType) {
  if (!mimeType) return '📄';
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
  if (!bytes) return '0 Bytes';
  const k = 1024, sizes=['Bytes','KB','MB','GB'], i=Math.floor(Math.log(bytes)/Math.log(k));
  return (bytes/Math.pow(k,i)).toFixed(1)+' '+sizes[i];
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US',{ year:'numeric', month:'short', day:'numeric' });
}

// =====================
// Notifications
// =====================
function showNotification(message, type='info') {
  const notification = document.getElementById('notification');
  if (!notification) return;
  notification.textContent = message;
  notification.className = `notification ${type}`;
  notification.classList.add('show');
  setTimeout(() => notification.classList.remove('show'), 3000);
}

// =====================
// Smooth Scroll & Active Link
// =====================
document.addEventListener('scroll', () => {
  document.querySelectorAll('section').forEach(section => {
    const rect = section.getBoundingClientRect();
    const link = document.querySelector(`nav a[href="#${section.id}"]`);
    if (rect.top >= 0 && rect.top < window.innerHeight/2) updateActiveNavLink(link);
  });
});

// =====================
// Logout Function
// =====================
function logout() {
  sessionStorage.removeItem('username');
  window.location.href = 'login.html';
}
