
$(document).ready(function() {
    const API_URL = 'https://jsonplaceholder.typicode.com/posts';
    let currentView = 'grid';
    let posts = [];
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    loadPosts();
        $('#createPostForm').on('submit', function(e) {
        e.preventDefault();
        createPost();
    });
    
    $('#submitPost').on('click', function() {
        if (validateForm('#createPostForm')) {
            createPost();
        }
    });
    
    $('#updatePost').on('click', function() {
        if (validateForm('#editPostForm')) {
            updatePost();
        }
    });
    
    $('#confirmDelete').on('click', deletePost);
    
    $('#searchInput').on('keyup', function() {
        filterPosts($(this).val().toLowerCase());
    });
    
    $('#gridViewBtn').on('click', function() {
        setView('grid');
    });
    
    $('#listViewBtn').on('click', function() {
        setView('list');
    });
    
    function loadPosts() {
        showLoading(true);
        
        $.ajax({
            url: `${API_URL}?_limit=10`,
            method: 'GET',
            success: function(data) {
                posts = data;
                displayPosts(posts);
                showLoading(false);
            },
            error: function(err) {
                showToast('Error loading posts. Please try again.', 'danger');
                console.error('Error:', err);
                showLoading(false);
            }
        });
    }
    
    function displayPosts(postsToDisplay) {
        const container = $('#postsContainer');
        container.empty();
        
        if (postsToDisplay.length === 0) {
            $('#noPostsMessage').removeClass('d-none');
            return;
        }
        
        $('#noPostsMessage').addClass('d-none');
        
        postsToDisplay.forEach(post => {
            if (currentView === 'grid') {
                container.append(`
                    <div class="card" data-post-id="${post.id}">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title text-truncate">${post.title}</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item edit-post" href="#" data-id="${post.id}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                        <li><a class="dropdown-item delete-post" href="#" data-id="${post.id}"><i class="bi bi-trash me-2"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            <p class="card-text flex-grow-1">${truncateText(post.body, 120)}</p>
                            <div class="mt-auto">
                                <small class="text-muted">Post ID: ${post.id}</small>
                            </div>
                        </div>
                    </div>
                `);
            } else {
                container.append(`
                    <div class="card mb-3" data-post-id="${post.id}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">${post.title}</h5>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary me-1 edit-post" data-id="${post.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-post" data-id="${post.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="card-text mt-3">${post.body}</p>
                            <small class="text-muted">Post ID: ${post.id}</small>
                        </div>
                    </div>
                `);
            }
        });
        
        $('.edit-post').on('click', function(e) {
            e.preventDefault();
            const postId = $(this).data('id');
            openEditModal(postId);
        });
        
        $('.delete-post').on('click', function(e) {
            e.preventDefault();
            const postId = $(this).data('id');
            openDeleteModal(postId);
        });
    }
    
    function createPost() {
        const newPost = {
            title: $('#title').val(),
            body: $('#body').val(),
            userId: 1 
        };
        
        $('#submitSpinner').removeClass('d-none');
        $('#submitPost').attr('disabled', true);
        
        $.ajax({
            url: API_URL,
            method: 'POST',
            data: newPost,
            success: function(response) {
                newPost.id = Math.max(...posts.map(p => p.id)) + 1;
                posts.unshift(newPost);
                displayPosts(posts);
                
                $('#createPostModal').modal('hide');
                resetForm('#createPostForm');
                showToast('Post created successfully!', 'success');
            },
            error: function(err) {
                showToast('Error creating post. Please try again.', 'danger');
                console.error('Error:', err);
            },
            complete: function() {
                $('#submitSpinner').addClass('d-none');
                $('#submitPost').attr('disabled', false);
            }
        });
    }
    
    function openEditModal(postId) {
        const post = posts.find(p => p.id === postId);
        if (post) {
            $('#editPostId').val(postId);
            $('#editTitle').val(post.title);
            $('#editBody').val(post.body);
            
            const editModal = new bootstrap.Modal(document.getElementById('editPostModal'));
            editModal.show();
        }
    }
    
    function updatePost() {
        const postId = parseInt($('#editPostId').val());
        const updatedPost = {
            id: postId,
            title: $('#editTitle').val(),
            body: $('#editBody').val(),
            userId: 1
        };
        
        $('#updateSpinner').removeClass('d-none');
        $('#updatePost').attr('disabled', true);
        
        $.ajax({
            url: `${API_URL}/${postId}`,
            method: 'PUT',
            data: updatedPost,
            success: function(response) {
                const index = posts.findIndex(p => p.id === postId);
                if (index !== -1) {
                    posts[index] = updatedPost;
                    displayPosts(posts);
                }
                
                $('#editPostModal').modal('hide');
                showToast('Post updated successfully!', 'success');
            },
            error: function(err) {
                showToast('Error updating post. Please try again.', 'danger');
                console.error('Error:', err);
            },
            complete: function() {
                $('#updateSpinner').addClass('d-none');
                $('#updatePost').attr('disabled', false);
            }
        });
    }
    
    function openDeleteModal(postId) {
        $('#deletePostId').val(postId);
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
    }
    
    function deletePost() {
        const postId = parseInt($('#deletePostId').val());
        
        $('#deleteSpinner').removeClass('d-none');
        $('#confirmDelete').attr('disabled', true);
        
        $.ajax({
            url: `${API_URL}/${postId}`,
            method: 'DELETE',
            success: function() {
                posts = posts.filter(p => p.id !== postId);
                displayPosts(posts);
                
                $('#deleteConfirmModal').modal('hide');
                showToast('Post deleted successfully!', 'success');
                
                if (posts.length === 0) {
                    $('#noPostsMessage').removeClass('d-none');
                }
            },
            error: function(err) {
                showToast('Error deleting post. Please try again.', 'danger');
                console.error('Error:', err);
            },
            complete: function() {
                $('#deleteSpinner').addClass('d-none');
                $('#confirmDelete').attr('disabled', false);
            }
        });
    }
    
    function filterPosts(query) {
        if (!query) {
            displayPosts(posts);
            return;
        }
        
        const filtered = posts.filter(post => 
            post.title.toLowerCase().includes(query) || 
            post.body.toLowerCase().includes(query)
        );
        
        displayPosts(filtered);
    }
    
    function setView(view) {
        currentView = view;
        
        if (view === 'grid') {
            $('#postsContainer').addClass('post-grid').removeClass('d-block');
            $('#gridViewBtn').addClass('active');
            $('#listViewBtn').removeClass('active');
        } else {
            $('#postsContainer').removeClass('post-grid').addClass('d-block');
            $('#listViewBtn').addClass('active');
            $('#gridViewBtn').removeClass('active');
        }
        
        displayPosts(posts);
    }
    
    function validateForm(formSelector) {
        const form = document.querySelector(formSelector);
        if (!form.checkValidity()) {
            $(formSelector).addClass('was-validated');
            return false;
        }
        return true;
    }
    
    function resetForm(formSelector) {
        $(formSelector)[0].reset();
        $(formSelector).removeClass('was-validated');
    }
    
    function showLoading(show) {
        if (show) {
            $('#loadingSpinner').removeClass('d-none');
        } else {
            $('#loadingSpinner').addClass('d-none');
        }
    }
    
    function showToast(message, type = 'success') {
        const toastId = 'toast-' + Date.now();
        const toast = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        $('.toast-container').append(toast);
        const toastElement = bootstrap.Toast.getOrCreateInstance(document.getElementById(toastId), {
            delay: 3000
        });
        toastElement.show();
        
        $(`#${toastId}`).on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
    
    function truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substr(0, maxLength) + '...';
    }
});