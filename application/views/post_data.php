<?php if (!empty($posts)): ?>

    <?php foreach ($posts as $post): ?>

        <div class="post-card" data-id="<?= $post->id ?>">

            <!-- Actions -->
           

            <!-- Priority -->
            <div class="priority-badge">#<?= $post->priority ?></div>

            <!-- Image -->
            <?php if (!empty($post->image_url)): ?>
                <img src="<?= $post->image_url ?>" class="thumb">
            <?php endif; ?>

            <!-- Content -->
            <div class="content">

                <h3 class="title" id="title<?= $post->id ?>">
                    <?= $post->title ?>
                </h3>

                <p class="desc" id="desc<?= $post->id ?>">
                    <?= strip_tags($post->content) ?>
                </p>

                <div class="meta">
                    <span><?= date('d M Y', strtotime($post->pub_date)) ?></span>
                    <span id="char_count<?= $post->id ?>">
                        <?= $post->char_count ?> chars
                    </span>
                </div>

                <!-- Platforms -->
                <div class="platforms">
                    <div id="social<?= $post->id ?>">

                        <?php foreach ($all_platforms as $sp): ?>
                            <?php $isLinked = in_array($sp->id, $post->linked_platform_ids); ?>

                            <span class="badge"
                                style="background: <?= $sp->color ?>;">
                                <i class="<?= $sp->icon ?>"></i>
                            </span>

                        <?php endforeach; ?>

                    </div>
                </div>

            </div>
        </div>

    <?php endforeach; ?>

<?php else: ?>
    <p>No Posts Found</p>
<?php endif; ?>


