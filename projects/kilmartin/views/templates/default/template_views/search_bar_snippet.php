<section class="search-block">
    <form action="/course-list.html" method="get" id="search-form">
        <div class="searchBoxes">
            <h2>SEARCH COURSES</h2>
            <div id="search-select-boxes" class="search-select-boxes">
                <label class="selectbox">
                    <select name="location" id="search-location" class="styled">
                        <option value="">ALL LOCATIONS</option>
                        <?=Model_Locations::get_all_locations_html(@$_GET)?>
                    </select>
                </label>
                <span class="arrow-divider"></span>
                <label class="selectbox">
                    <select name="category" id="search-category" class="styled">
                        <option value=''>ALL CLASS TYPES</option>
                        <?=Model_Categories::get_all_categories_html(@$_GET)?><!--KES-151-->
                    </select>
                </label>
                <span class="arrow-divider"></span>
            </div>
            <input name="title" id="search-box" type="text" placeholder="KEYWORDS" value="<?=(@$_GET['title'])?urldecode($_GET['title']):''?>">
        </div>
            <a href="#" id="show_filters" class="show_filters">Show filters</a>
            <span class="findCourse">
            	<input id='search-submit' name="" type="submit" value="FIND COURSE">
            </span>
    </form>
</section>
<!-- /search block -->