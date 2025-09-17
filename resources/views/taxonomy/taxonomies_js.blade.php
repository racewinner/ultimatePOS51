<script type="text/javascript">
  var curr_meli_attributes = [];
  var curr_meli_item_condition = '';

  function get_ml_category(cat_id) {
    $("#mercardolibre-required-attributes").html("");
    $("#mercadolibre_item_condition").html('');
    $("#mercadolibre_category_id").val('');
    $("#mercadolibre-category-selected").addClass("hidden");
    $("#mercardolibre-product-detail-setting").addClass("hidden");
    $("#meli_sub_categories").prop("disabled", true);

    $.ajax({
          method: 'GET',
          url: `/mercadolibre/categories/${cat_id ? cat_id : 'top'}`,
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              $meli_sub_categories = $("select[name='meli_sub_categories']");
              $meli_sub_categories.html('');

              // path from root of the category
              html = `<li class="breadcrumb-item cursor-pointer"><a data-cat-id='top'>Top Category</a></li>`;
              if(response.path_from_root?.length > 0) {
                  response.path_from_root.forEach(function(cat) {
                      html += `<li class="breadcrumb-item cursor-pointer"><a data-cat-id='${cat.id}'>${cat.name}</a></li>`
                  })
              }
              $("#category_path ol.breadcrumb").html(html);

              if(response.sub_categories?.length > 0) {
                let html = '<option value="">Select</option>';
                response.sub_categories.forEach(function(cat) {
                  html += `<option value="${cat.id}">${cat.name}</option>`;
                });
                $meli_sub_categories.html(html);

              } else {
                // if it is a leaf category?
                $("#mercadolibre_category_id").val(cat_id);
                $("#mercadolibre-category-selected").removeClass("hidden");
                $("#mercardolibre-product-detail-setting").removeClass("hidden");

                // mercardolibre-required-attributes
                let count = 0;
                response.attributes.forEach(function(attr) {
                  if(attr.tags['required'] == true) {
                    const attribute = document.createElement("div");
                    $(attribute).addClass("col-sm-3");

                    let attr_val = "";
                    const a = curr_meli_attributes?.find(a => a.id == attr.id);
                    if(a) attr_val = a.value_name;

                    let html = `<label class="me-2" style="text-wrap:nowrap;">${attr.name}</label>`;
                    html += `<input type='hidden' name='mercadolibre_attributes[${count}][id]' value='${attr.id}' />`;
                    html += `<input class="form-control" name="mercadolibre_attributes[${count}][value_name]" value="${attr_val}" type="text" required />`;

                    $(attribute).html(html)
                    $("#mercardolibre-required-attributes").append(attribute);

                    count++;
                  }
                })

                // mercadolibre item conditions
                if(response.item_conditions?.length > 0) {
                  html = '<option value="">Select</option>';
                  response.item_conditions.forEach(function(cond) {
                    html += `<option value="${cond}">${cond}</option>`;
                  })
                  $("#mercadolibre_item_condition").html(html);
                  $("#mercadolibre_item_condition").val(curr_meli_item_condition);
                }
              }
            } else {
              toastr.error(response.message)
            }
            $("#meli_sub_categories").prop("disabled", false);
          },
      });
  }

  function onSubCategorySelected(e) {
    if(e.target.value) {
      // To add the selected category to breadcumb
      const cat_id = e.target.value;
      const option = $(e.target).find(`option[value=${cat_id}]`);
      const cat_name = option.text();

      $ol = $("ol.breadcrumb");
      $ol.find("li").removeClass('active');

      innerHtml = $ol.html();
      innerHtml += `<li class='breadcrumb-item cursor-pointer active'><a data-cat-id='${cat_id}'>${cat_name}</a></li>`;
      $ol.html(innerHtml);

      // To get sub categories of the selected category
      get_ml_category(cat_id);
    }
  }

  function onCategoryPathClicked(e) {
    const cat_id = $(e.target).closest('a').data('cat-id');
    const li = $(e.target).closest("li.breadcrumb-item").nextAll().remove();
    get_ml_category(cat_id);
  }

  function getTaxonomiesIndexPage () {
    var data = {category_type : $('#category_type').val()};
    $.ajax({
        method: "GET",
        dataType: "html",
        url: '/taxonomies-ajax-index-page',
        data: data,
        async: false,
        success: function(result){
            $('.taxonomy_body').html(result);
        }
    });
  }

  function initializeTaxonomyDataTable() {
    //Category table
    if ($('#category_table').length) {
        var category_type = $('#category_type').val();
        category_table = $('#category_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/taxonomies?type=' + category_type,
            columns: [
                { data: 'name', name: 'name' },
                @if($cat_code_enabled)
                    { data: 'short_code', name: 'short_code' },
                @endif
                {data: 'mercadolibre_category_id', name: 'mercadolibre_category_id'},
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action', orderable: false, searchable: false},
            ],
        });
    }
  }

  $(document).ready( function() {
    @if(empty(request()->get('type')))
        getTaxonomiesIndexPage();
    @endif

    $(document).on('change', '#meli_sub_categories', onSubCategorySelected);
    $(document).on('click', "nav#category_path li.breadcrumb-item a", onCategoryPathClicked)

    $(document).on('change', "input[name='add_as_sub_cat']", function(e) {
      if(e.target.checked) {
        
      }
    });

    $(document).on('submit', 'form#category_add_form', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();

        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            beforeSend: function(xhr) {
                __disable_submit_button(form.find('button[type="submit"]'));
            },
            success: function(result) {
                if (result.success === true) {
                    $('div.category_modal').modal('hide');
                    toastr.success(result.msg);
                    if(typeof category_table !== 'undefined') {
                        category_table.ajax.reload();
                    }

                    var evt = new CustomEvent("categoryAdded", {detail: result.data});
                    window.dispatchEvent(evt);

                    //event can be listened as
                    //window.addEventListener("categoryAdded", function(evt) {}
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    $(document).on('click', 'button.edit_category_button', function() {
        $('div.category_modal').load($(this).data('href'), function() {
            $(this).modal('show');

            $('form#category_edit_form').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var data = form.serialize();

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    beforeSend: function(xhr) {
                        __disable_submit_button(form.find('button[type="submit"]'));
                    },
                    success: function(result) {
                        if (result.success === true) {
                            $('div.category_modal').modal('hide');
                            toastr.success(result.msg);
                            category_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });

    $(document).on('click', 'button.delete_category_button', function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            toastr.success(result.msg);
                            category_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    initializeTaxonomyDataTable();
  });
</script>