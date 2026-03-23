
<div class="card-container" id="sortable-posts">

    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>

            <div class="post-card" data-id="<?= $post->id ?>">
                <!-- Actions -->
                <div class="actions">
                    <span onclick="editPost(<?= $post->id ?>)" title="Edit">✏️</span>
                    <span onclick="deletePost(<?= $post->id ?>)" title="Delete">🗑️</span>
                </div>
                <!-- Priority -->
                <div class="priority-badge">#<?= $post->priority ?></div>

                <!-- Image -->
                <?php if (!empty($post->image_url)): ?>
                    <img src="<?= $post->image_url ?>" class="thumb">
                <?php endif; ?>

                <!-- Content -->
                <div class="content">

                    <h3 class="title" id="title<?php echo $post->id; ?>"><?= $post->title ?></h3>

                    <p class="desc" id="desc<?php echo $post->id; ?>">
                        <?= strip_tags($post->content) ?>
                    </p>

                    <div class="meta">
                        <span><?= date('d M Y', strtotime($post->pub_date)) ?></span>
                        <span id="char_count<?php echo $post->id; ?>"><?= $post->char_count ?> chars</span>

                    </div>

                    <!-- Platforms -->
                    <div class="platforms">
                        <!-- Social Icons -->
                        <div class="social-icons">
                            <div id="social<?php echo $post->id; ?>">
                                <?php foreach ($all_platforms as $sp): ?>
                                    <?php
                                    $isLinked = in_array($sp->id, $post->linked_platform_ids);
                                    ?>

                                    <span class="icon <?= $isLinked ? 'active' : 'inactive' ?>"
                                        style="<?= 'background:' . $sp->color . '; color:#fff;'  ?>"
                                        title="<?= $sp->name ?>">

                                        <i class="<?= $sp->icon ?>"></i>

                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <?php
                            $over = (int)$post->char_count - 280;
                            $isExceeded = $over < 0;

                            ?>

                            <div id="x_warning<?= $post->id ?>"
                                class="x_warning"
                                style="<?= $isExceeded ? '' : 'display:none;' ?>">

                                ❌ Exceeds Twitter limit by <b><?= $over ?></b> characters
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        <?php endforeach; ?>
   
    <?php endif; ?>

</div>

<!-- Pagination -->
<div style="margin:20px;">
    <?= isset($pagination) ? $pagination : '' ?>
</div>
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="edit_id">

                <div class="mb-2">
                    <label>Title</label>
                    <input type="text" id="edit_title" class="form-control">
                </div>

                <div class="mb-2">
                    <label>Content</label>
                    <textarea id="edit_content" class="form-control" style="height: 177px;"></textarea>
                </div>
                <div class="mb-2">
                    <label>Social Icon</label>


                    <!-- Platforms -->
                    <div class="platform-select">

                        <?php foreach ($all_platforms as $sp): ?>

                            <label class="platform-icon" style="--color: <?= $sp->color ?>;">

                                <input type="checkbox"
                                    class="platform-checkbox"
                                    value="<?= $sp->id ?>">

                                <i class="<?= $sp->icon ?>"></i>

                            </label>

                        <?php endforeach; ?>

                    </div>
                </div>
                <!-- Twitter warning -->
                <div id="x_warning" class="text-danger"></div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" onclick="updatePost()" id="modelbtn">Update</button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script>
    function editPost(id) {

        let formData = new FormData();
        formData.append("id", id);

        fetch("<?= base_url('postController/edit') ?>", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(response => {

                if (response.status) {

                    let p = response.data;

                    // Fill modal fields
                    document.getElementById('edit_id').value = p.id;
                    document.getElementById('edit_title').value = p.title;
                    document.getElementById('edit_content').value = p.content;

                    // Reset checkboxes
                    document.querySelectorAll('.platform-checkbox').forEach(cb => {
                        cb.checked = false;
                    });

                    // Check linked platforms
                    p.platform_ids.forEach(pid => {
                        let el = document.querySelector('.platform-checkbox[value="' + pid + '"]');
                        if (el) el.checked = true;
                    });

                    // 🔥 OPEN BOOTSTRAP MODAL
                    let modal = new bootstrap.Modal(document.getElementById('editModal'));
                    modal.show();



                } else {
                    toastr.error(response.message || "Failed");
                }

            })
            .catch(error => {
                toastr.error("Something went wrong");
                console.error(error);
            });
    }

    function updatePost() {

        let id = document.getElementById('edit_id').value.trim();
        let title = document.getElementById('edit_title').value.trim();
        let content = document.getElementById('edit_content').value.trim();

        // ✅ Collect platforms
        let platforms = [];
        document.querySelectorAll('.platform-checkbox:checked').forEach(cb => {
            platforms.push(cb.value);
        });

        //  VALIDATIONS

        if (!title) {
            toastr.error("Title is required");
            return;
        }

        if (!content) {
            toastr.error("Content is required");
            return;
        }

        if (platforms.length === 0) {
            toastr.error("Select at least one platform");
            return;
        }

        // 🔥 Twitter (X) validation


        // ✅ Send data
        let formData = new FormData();
        formData.append("id", id);
        formData.append("title", title);
        formData.append("content", content);

        platforms.forEach(p => formData.append("platforms[]", p));
        $("#modelbtn").html("please Wait...")
        fetch("<?= base_url('postController/update') ?>", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(response => {

                if (response.status) {
                    toastr.success("Post updated successfully");

                    // Close modal
                    let modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                    modal.hide();
                    let length = content.length;

                    let counter1 = document.getElementById('char_count' + id);

                    counter1.innerText = length + " chars";
                    $("#x_warning" + id).hide();

                    if (length < 280) {
                        console.log(length, "#x_warning" + id)
                        $("#x_warning" + id).show();
                    }

                    $("#title" + id).html(title)
                    console.log(content)
                    // 🔥 remove <img> tags
                    content = content.replace(/<img[^>]*>/gi, '');
                    $("#desc" + id).html(content)
                    $("#social" + id).html(response.html);

                    // Reload
                    //location.reload();

                } else {
                    toastr.error(response.message || "Update failed");
                }
                $("#modelbtn").html("Update")
            })
            .catch(() => {
                toastr.error("Something went wrong");
                $("#modelbtn").html("Update")
            });
    }

    function deletePost(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (result.isConfirmed) {

                $.post("<?= base_url('posts/delete') ?>", {
                    id
                }, function(res) {

                    let response = JSON.parse(res);

                    if (response.status) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Post has been deleted.',
                            timer: 1500,
                            showConfirmButton: false
                        });


                       let deletedId = response.deleted_id;
                        let deletedPriority = response.deleted_priority;

                        //  Remove card from UI
                        let card = document.querySelector(`.post-card[data-id="${deletedId}"]`);
                        console.log(card,deletedId,deletedPriority)
                        if (card) card.remove();

                        // Update remaining priority badges
                        document.querySelectorAll('.post-card').forEach((item) => {
                            let badge = item.querySelector('.priority-badge');
                            let current = parseInt(badge.innerText.replace('#', ''));

                            if (current > deletedPriority) {
                                let newPriority = current - 1;
                                badge.innerText = '#' + newPriority;
                            }
                        });

                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }

                }).fail(() => {
                    Swal.fire('Error', 'Something went wrong', 'error');
                });

            }
        });
    }


    document.addEventListener("DOMContentLoaded", function() {

        let el = document.getElementById('sortable-posts');

        new Sortable(el, {
            animation: 150,
            ghostClass: 'sortable-ghost',

            onEnd: function() {

                let order = [];

                document.querySelectorAll('.post-card').forEach((item, index) => {
                    order.push({
                        id: item.dataset.id,
                        priority: index + 1
                    });
                });

                console.log(order);
                let formData = new FormData();
                formData.append("order", JSON.stringify(order));
                // 🔥 Send updated order to server
                fetch("<?= base_url('updatePriority') ?>", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(response => {
                        if (response.status) {
                            toastr.success("Priority updated");

                            //  update badge UI
                            document.querySelectorAll('.post-card').forEach((item, index) => {
                                item.querySelector('.priority-badge').innerText = '#' + (index + 1);
                            });

                        } else {
                            toastr.error("Failed to update");
                        }
                    })
                    .catch(() => toastr.error("Error updating order"));
            }
        });

    });
</script>