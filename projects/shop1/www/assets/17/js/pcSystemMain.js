$(document).ready(function(){
    var bodyMenuLiName = $('.body-menu-li-name'),
        filterBlock = $('#filter-block-js'),
        filterCategory = $('#filter-category'),
        filterBrand = $('#filter-brand'),
        filterDistributor = $('#filter-distributor'),
        clearFilter = $('#clear-filter-js, .clear-all');

    bodyMenuLiName.click(function(){
        var $this = $(this);
        if($this.hasClass('active')){
            $this.removeClass('active');
        } else {
            $this.addClass('active');
        }
    });

    clearFilter.click(function(){
        var clear = function(ul){
            var li = ul.children();
            for(var i = 1; i < li.length; i++){
                li[i].remove();
            }
            ul.hide();
        };

        clear(filterCategory);
        clear(filterBrand);
        clear(filterDistributor);

        var checkedLi = $('.body-sub-menu-li.checked');
        for (var i = 0; i < checkedLi.length; i++){
            $(checkedLi[i]).removeClass('checked');
        }
		return false;
    });

    $('.checkbox-label').click(function(){
        var $this = $(this),
            checkBox = $this.find('.checkbox-img');
        if(checkBox.hasClass('checked')){
            checkBox.removeClass('checked');
        } else {
            checkBox.addClass('checked');
        }
    });

    filterBlock.on('click', '.close', function(){
        var $this = $(this),
            li = $this.parent(),
            ul = li.parent(),
            search = li.data('search');

        // if remove last child in 'filter' ul
        if(ul.attr('class') == 'filter' && (ul.children().length == 2)){
            ul.hide();
        }

        li.remove();

        // remove check from left menu
        var checkedLi = $('.body-sub-menu-li.checked');
        for (var i = 0; i < checkedLi.length; i++){
            var obj = $(checkedLi[i]);
            if ((obj.data('search') == li.data('search')) && (obj.data('id') == li.data('id'))) {
                obj.removeClass('checked');

                // uncheck sub category of category
                if(search == 'category'){
                    obj.find('.sub-category li').removeClass('checked');
                }
            }
        }
    });

    $('.product-tab').click(function(){
        var $this = $(this),
            isActive = $this.hasClass('active');

        if(! isActive){
            $('.product-tab').removeClass('active');
            $this.addClass('active');
        }
    });

    $('.product-view-type').click(function(){
        var $this = $(this),
            type =$this.html();

        $('.product-view-type').removeClass('active');
        $this.addClass('active');

        if (type == 'List'){
            $('.product').addClass('list');

            $('.product-inline')
                .css( "display", "inline-block")
                .css( "width", "auto")
                .css("margin-right", "70px")
                .css("vertical-align", "top");
        } else if (type == 'Grid') {
            $('.product').removeClass('list');
            $('.product-inline')
                .css( "display", "block")
                .css( "width", "100%");
        }
    });

    $('.filter-checkbox').click(function(){
        var addCategory = function(id, search, value, subCategory){
            var html =
                '<li class="filter-category" data-id="' + id + '" data-search="' + search + '">' +
                value +
                '<input type="hidden" name="' + search + '[]" value="' + id + '">' +
                '<span class="close">X</span>' +
                subCategory +
                '</li>';

            $this.addClass('checked');

            if(search == 'category'){
                filterCategory.show();
                filterCategory.append(html);
            }else if(search == 'brand'){
                filterBrand.show();
                filterBrand.append(html);
            }else if(search == 'distributor'){
                filterDistributor.show();
                filterDistributor.append(html);
            }
        };

        var $this = $(this).parent(),
            search = $this.data('search'),
            id = $this.data('id'),
            value = $this.data('value');

        if ($this.hasClass('checked')) return;

        if (search == 'sub-category') {
            var createLi = function(id, search, value, parentId){
                return '<li data-id="' + id + '" data-search="' + search + '">' +
                            value +
                            '<input type="hidden" name="sub_category[' + parentId + '][]" value="' + id + '">' +
                            '<span class="close">X</span>' +
                        '</li>';
            };

            var category = $this.parents('.body-sub-menu-li'),
                categorySearch = category.data('search'),
                categoryId = category.data('id'),
                categoryValue = category.data('value'),
                subCategoryLi = filterCategory.children(),
                subCategoryUl = false;

            category.addClass('checked');

            for(var i = 0; i < subCategoryLi.length; i++){
                var $subCategoryLi = $(subCategoryLi[i]);
                if ($subCategoryLi.data('id') == categoryId) {
                    subCategoryUl = $subCategoryLi.find('.filter-sub-category');
                }
            }

            if(subCategoryUl){
                subCategoryUl.append(createLi(id, search, value, categoryId));
                $this.addClass('checked');
            } else {
                var subCategory =
                    '<ul class="filter-sub-category">' +
                        createLi(id, search, value, categoryId) +
                    '</ul>';

                addCategory(categoryId, categorySearch, categoryValue, subCategory);
            }
        } else {
            addCategory(id, search, value, '<ul class="filter-sub-category">' + '</ul>');
        }
		
		if(window.products_menu_filter_prefill){//retick after page load
		} else {
			$("#filter-block-js").submit();
		}
    });

    $('.body-sub-menu')
        .on('click', '.display-sub-category', function(){
            $(this).removeClass('display-sub-category').addClass('hide-sub-category');
            $(this).siblings('.sub-category').show();
        })
        .on('click', '.hide-sub-category', function(){
            $(this).removeClass('hide-sub-category').addClass('display-sub-category');
            $(this).siblings('.sub-category').hide();
        });

    $('#price').change(function(){
        $('#price-max').html($(this).val());
    });

	if(window.products_menu_filter_prefill){
		if(products_menu_filter_prefill.maxprice){
			$("#price_slider").val([products_menu_filter_prefill.minprice,products_menu_filter_prefill.maxprice]);
		}
		if(products_menu_filter_prefill.category){
			for(var i in products_menu_filter_prefill.category){
				$("li[data-search='category'][data-id='" + products_menu_filter_prefill.category[i] + "']").find('> .filter-checkbox').trigger('click');
			}
		}
		if(products_menu_filter_prefill.sub_category){
			for(var i in products_menu_filter_prefill.sub_category){
				for(var j in products_menu_filter_prefill.sub_category[i]){
					$("li[data-search='sub-category'][data-id='" + products_menu_filter_prefill.sub_category[i][j] + "']").find('> .filter-checkbox').trigger('click');
				}
			}
		}
		if(products_menu_filter_prefill.brand){
			for(var i in products_menu_filter_prefill.brand){
				$("li[data-search='brand'][data-id='" + products_menu_filter_prefill.brand[i] + "']").find('> .filter-checkbox').trigger('click');
			}
		}
		if(products_menu_filter_prefill.distributor){
			for(var i in products_menu_filter_prefill.distributor){
				$("li[data-search='distributor'][data-id='" + products_menu_filter_prefill.distributor[i] + "']").find('> .filter-checkbox').trigger('click');
			}
		}
		window.products_menu_filter_prefill = null;
	}
});
