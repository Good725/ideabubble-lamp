<div class="upcoming-courses-embed">
    <div class="row d-md-flex">
        <div class="swiper-container" id="upcoming-courses-carousel">
            <div class="swiper-wrapper">
                <?php foreach ($courses as $course): ?>
                    <div class="swiper-slide">
                        <div class="upcoming-course-column d-md-flex mb-4">
                            <?php include 'snippets/course_embed.php'; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="row gutters vertically_center clearfix text-center text-md-left">
        <div class="col-xs-12 col-sm-6">
            <div class="d-inline-block upcoming-courses-carousel-prev" id="upcoming-courses-carousel-prev"></div>
            <div class="d-inline-block upcoming-courses-carousel-next" id="upcoming-courses-carousel-next"></div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <a href="/course-list" class="upcoming-courses-embed-see_more">See all programmes</a>
        </div>
    </div>
</div>
