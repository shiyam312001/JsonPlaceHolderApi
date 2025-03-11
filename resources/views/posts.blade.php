<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Posts Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
</head>
<body>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-4 fw-bold text-primary mb-0">Posts Management</h1>
                <hr class="my-3 border-2 border-primary opacity-50 w-25">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow border-0 rounded-3 overflow-hidden mb-4">
                    <div class="card-header bg-gradient bg-primary bg-opacity-75 text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Create New Post</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="createPostForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold">Title</label>
                                <input type="text" class="form-control border-secondary-subtle" id="title" name="title" placeholder="Enter post title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control border-secondary-subtle" id="description" name="description" rows="4" placeholder="Enter post description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label fw-semibold">Featured Image</label>
                                <div class="input-group">
                                    <input type="file" class="form-control border-secondary-subtle" id="image" name="image" required>
                                    <span class="input-group-text bg-light"><i class="bi bi-image"></i></span>
                                </div>
                                <small class="text-muted mt-1 d-block">Supported formats: JPG, PNG, GIF (Max: 2MB)</small>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-square me-2"></i>Create Post
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card shadow border-0 rounded-3 overflow-hidden d-none" id="editFormContainer">
                    <div class="card-header bg-gradient bg-warning text-dark py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Post</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="editPostForm" enctype="multipart/form-data">
                            <input type="hidden" id="edit_id" name="id">
                            <div class="mb-3">
                                <label for="edit_title" class="form-label fw-semibold">Title</label>
                                <input type="text" class="form-control border-secondary-subtle" id="edit_title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control border-secondary-subtle" id="edit_description" name="description" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="edit_image" class="form-label fw-semibold">Change Image</label>
                                <div class="input-group">
                                    <input type="file" class="form-control border-secondary-subtle" id="edit_image" name="image">
                                    <span class="input-group-text bg-light"><i class="bi bi-image"></i></span>
                                </div>
                                <small class="text-muted d-block mt-1">Leave empty to keep current image</small>
                                
                                <div class="current-image-preview p-2 bg-light rounded border border-secondary-subtle text-center mt-3">
                                    <p class="text-muted mb-2 small">Current Image:</p>
                                    <img id="current_image" src="" alt="Current Image" class="img-thumbnail" style="max-height: 150px; display: none;">
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" id="cancelEdit">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>Update Post
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-gradient bg-secondary text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-collection me-2"></i>Latest Posts</h5>
                        <div class="input-group input-group-sm w-50">
                            <input type="text" class="form-control" id="searchPosts" placeholder="Search posts...">
                            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="posts-container" id="postsContainer">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-3">Loading posts...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="notificationToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-info-circle me-2"></i><span id="toastMessage"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const toast = new bootstrap.Toast(document.getElementById('notificationToast'));
        loadPosts();
        $('#searchPosts').on('input', function() {
            const searchQuery = $(this).val().toLowerCase();
            filterPosts(searchQuery);
        });

        $('#createPostForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            $.ajax({
                url: '/api/posts',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    showNotification('Post created successfully!', 'bg-success');
                    $('#createPostForm')[0].reset();
                    loadPosts();
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value + '\n';
                    });
                    showNotification('Error: ' + errorMessage, 'bg-danger');
                }
            });
        });

        $('#editPostForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const postId = $('#edit_id').val();
            
            $.ajax({
                url: '/api/posts/' + postId,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    showNotification('Post updated successfully!', 'bg-success');
                    $('#editFormContainer').addClass('d-none');
                    loadPosts();
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value + '\n';
                    });
                    showNotification('Error: ' + errorMessage, 'bg-danger');
                }
            });
        });

        $('#cancelEdit').click(function() {
            $('#editFormContainer').addClass('d-none');
        });

        function loadPosts() {
            $.ajax({
                url: '/api/posts',
                type: 'GET',
                success: function(response) {
                    const posts = response.posts;
                    renderPosts(posts);
                },
                error: function(xhr) {
                    $('#postsContainer').html('<div class="alert alert-danger">Error loading posts.</div>');
                }
            });
        }
        
        function renderPosts(posts) {
            let postsHtml = '';           
            if (posts.length === 0) {
                postsHtml = '<div class="alert alert-info text-center">No posts available.</div>';
            } else {
                postsHtml = '<div class="row g-3">';
                $.each(posts, function(index, post) {
                    postsHtml += `
                    <div class="col-12 post-item">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="/${post.image}" class="img-fluid rounded-start h-100 w-100 object-fit-cover" alt="${post.title}" style="max-height: 160px;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h5 class="card-title text-primary">${post.title}</h5>
                                        <p class="card-text flex-grow-1">${post.description.substring(0, 100)}${post.description.length > 100 ? '...' : ''}</p>
                                        <div class="d-flex justify-content-end gap-2 mt-auto">
                                            <button class="btn btn-warning btn-sm edit-btn" data-id="${post.id}">
                                                <i class="bi bi-pencil-square me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm delete-btn" data-id="${post.id}">
                                                <i class="bi bi-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
                postsHtml += '</div>';
            }
            $('#postsContainer').html(postsHtml);
            $('.edit-btn').click(function() {
                const postId = $(this).data('id');
                editPost(postId);
            });         
            $('.delete-btn').click(function() {
                const postId = $(this).data('id');
                deletePost(postId);
            });
        }
        function filterPosts(query) {
            $('.post-item').each(function() {
                const title = $(this).find('.card-title').text().toLowerCase();
                const description = $(this).find('.card-text').text().toLowerCase();
                
                if (title.includes(query) || description.includes(query)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
        function editPost(postId) {
            $.ajax({
                url: '/api/posts/' + postId,
                type: 'GET',
                success: function(response) {
                    const post = response.post;
                    $('#edit_id').val(post.id);
                    $('#edit_title').val(post.title);
                    $('#edit_description').val(post.description);
                    $('#current_image').attr('src', '/' + post.image).show();
                    $('#editFormContainer').removeClass('d-none');
                    $('html, body').animate({
                        scrollTop: $("#editFormContainer").offset().top - 100
                    }, 500);
                }
            });
        }
        function deletePost(postId) {
            if (confirm('Are you sure you want to delete this post?')) {
                $.ajax({
                    url: '/api/posts/' + postId,
                    type: 'DELETE',
                    success: function(response) {
                        showNotification('Post deleted successfully!', 'bg-success');
                        loadPosts();
                    },
                    error: function(xhr) {
                        showNotification('Error deleting post.', 'bg-danger');
                    }
                });
            }
        }
        function showNotification(message, bgClass) {
            const toastEl = $('#notificationToast');
            toastEl.removeClass('bg-success bg-danger bg-info').addClass(bgClass);
            $('#toastMessage').text(message);
            toast.show();
        }
    });
    </script>
</body>
</html>