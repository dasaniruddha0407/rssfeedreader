<div class="container">
    <div class="row" id="social-platforms">
        <?php foreach ($all_platforms as $platform): ?>
            <div class="col-md-2 col-4 mb-3">
                <div class="card social-card text-center p-3"
                    onclick="selectPlatform(this,<?= $platform->id ?>)">

                    <i class="<?= $platform->icon ?> social-icon mb-2"></i>

                    <div class="social-name">
                        <?= $platform->name ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Post List -->
    <div id="post-container" class="card-container">
        <p>Loading posts...</p>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="pagination" style="margin:20px;"></div>


    <div class="empty-state <?php echo ($total == 0) ? '' : 'd-none' ?>" id="nopost">

        <div class="empty-card">



            <h3>No Posts Yet</h3>

            <p>Start by importing posts from RSS feed</p>

            <a href="<?= base_url('import-feed') ?>" class="btn-import">
                Import Feed
            </a>

        </div>

    </div>

    <div class="empty-state <?php echo ($totalsocial == 0) ? '' : 'd-none' ?>" id="nopostsocial">

        <div class="empty-card">



            <h3>No Posts Assigned to Any Platform</h3>



            <a href="<?= base_url('posts') ?>" class="btn-import">
                Post
            </a>

        </div>

    </div>


    <div class="empty-state d-none" id="nopostsocial14">

        <div class="empty-card">



            <h3>No Posts Assigned to <span id="nopostsocial11"></span></h3>



            <a href="<?= base_url('posts') ?>" class="btn-import">
                Post
            </a>

        </div>

    </div>

</div>

<script>
    let currentPage = 1;

    $(document).ready(function() {
        loadPosts(1);
    });

    function selectPlatform(element, platform_id) {

        // remove active from all
        $('.social-card').removeClass('active');

        // add active to clicked
        $(element).addClass('active');

        // load posts (reset page = 1)
        loadPosts(1, platform_id);
    }

    function loadPosts(page = 1, platform_id = null) {

        currentPage = page;

        $("#post-container").html("Loading...");

        $.post("<?= base_url('fetchPosts') ?>", {
            page: currentPage,
            platform_id: platform_id
        }, function(res) {

            let response = JSON.parse(res);

            if (response.status) {

                if (response.total1 == 0) {

                    $("#nopost").removeClass("d-none");
                    $("#nopostsocial").addClass("d-none");
                    $("#nopostsocial14").addClass("d-none");
                    $("#post-container").html("");

                } else if (response.total == 0 && platform_id !== null) {

                    $("#nopost").addClass("d-none");
                    $("#nopostsocial").addClass("d-none");
                    $("#nopostsocial14").removeClass("d-none");
                  
                    $("#nopostsocial11").html(response.platform_id);
                    
                    $("#post-container").html("");

                } else if (response.total == 0) {

                    $("#nopost").addClass("d-none");
                    $("#nopostsocial").removeClass("d-none");
                    $("#nopostsocial14").addClass("d-none");
                    $("#post-container").html("");

                } else {

                    $("#nopost, #nopostsocial, #nopostsocial14").addClass("d-none");
                    $("#post-container").html(response.html);

                }


                renderPagination(
                    response.total,
                    response.page,
                    response.limit
                );
            }
        });
    }

    function renderPagination(total, currentPage, perPage) {

        let totalPages = Math.ceil(total / perPage);

        let html = '';

        if (currentPage > 1) {
            html += `<a href="javascript:void(0);" onclick="loadPosts(${currentPage - 1})">Prev</a>`;
        }

        for (let i = 1; i <= totalPages; i++) {
            console.log(currentPage, i)
            html += `<a href="javascript:void(0);" onclick="loadPosts(${i})"
                    class="${i === currentPage ? 'active' : ''}">`;
            if (i == currentPage) {
                html += `<strong>${i}</strong>`
            } else {
                html += i;
            }

            html += `</a>`;
        }

        if (currentPage < totalPages) {
            html += `<a href="javascript:void(0);" onclick="loadPosts(${currentPage + 1})">Next</a>`;
        }

        $("#pagination").html(html);
    }
</script>