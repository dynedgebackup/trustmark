var pageLength='25';
var DIR=$("#DIR").val();
$(document).ready(function(){
    $("#filter_box").click(function(){
        $("#this_is_filter").slideToggle(300);
        $("#filter_box").toggleClass('active');
    });
    $("#btn_clear").on('click', function(e){
        window.location.href=location.pathname;
    });

    if($('.datatable').length) {
        $('.datatable').DataTable();
    }
    if($('.numeric').length) {
        $('.numeric').numeric();
    }

     /* Shorten updated plugin by sunil */
     $(document).on('click', '.morelink', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $this = $(this);
        var parent = $this.closest('.showLess2');
        
        if($this.hasClass('less')) {
            $this.removeClass('less');
            $this.text($this.data('moretext') || "More");
            $this.prev().hide(); // Hide content
            parent.find('.shortcontent').show();
            parent.find('.moreellipses').show();
        } else {
            $this.addClass('less');
            $this.text($this.data('lesstext') || "Less");
            $this.prev().show(); // Show content
            parent.find('.shortcontent').hide();
            parent.find('.moreellipses').hide();
        }
        
        return false;
    });
    /* Shorten updated plugin by sunil */
});

// $(document).ready(function(){
//     $("#filter_box1").click(function(){
//         $("#this_is_filter1").slideToggle(300);
//         $("#filter_box1").toggleClass('active');
//     });
//     $("#btn_clear").on('click', function(e){
//         window.location.href=location.pathname;
//     });

//     if($('.datatable').length) {
//         $('.datatable').DataTable();
//     }
//     if($('.numeric').length) {
//         $('.numeric').numeric();
//     }
// });

$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#add-doc").click(function(){
        $("#doc-form").slideToggle(300);
        $("#add-doc").toggleClass('active');
    });
    $("#btn_clear").on('click', function(e){
        window.location.href=location.pathname;
    });

    if($('.datatable').length) {
        $('.datatable').DataTable();
    }
    if($('.numeric').length) {
        $('.numeric').numeric();
    }
});

function get_page_number(total_cnt,selectedNumb=0){
    setTimeout(function(){ 
        $(".showLess").shorten({
            "showChars" : 30,
            "moreText"	: "More",
            "lessText"	: "Less",
        });
    }, 500);
    var val=total_cnt;
    if(val == "NULL")
    {
        return "";
    }
    var total_cnt=parseInt(total_cnt);
    var arr_num=['10','25','50','75','100','200','300','400'];
    var dropdown_html="";
    var i;
    //alert(total_cnt);
    for(i=0; i<=arr_num.length; i++){
        if(parseInt(arr_num[i])<total_cnt){
            selected="";
            if(i==0 || selectedNumb==arr_num[i])
                selected="selected='selected'";

            dropdown_html+="<option value='"+arr_num[i]+"' "+selected+">Show "+arr_num[i]+" / "+total_cnt+" </option>";
        }
    }
    
    if (jQuery.inArray(total_cnt, arr_num)=='-1'){
        selected="";
        if(selectedNumb==total_cnt)
            selected="selected='selected'";

       dropdown_html+="<option value='"+total_cnt+"' "+selected+">Show "+total_cnt+" / "+total_cnt+" </option>";
    }
    
    if(dropdown_html!=""){
        return "<select class='form-control common_pagesize' id='common_pagesize'>"+dropdown_html+"</select> Per Page";
    }
    return "";
}

function select3Ajax(id,parentId,Url,length=0){
    var DIR = $("#DIR").val();
    $("#"+id).select3({
        allowClear: true,
        dropdownAutoWidth : false,
        dropdownParent: $("#"+parentId),
        minimumInputLength: length,
        placeholder: 'Select Option',
        ajax: {
            url: DIR+Url,
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function (params,val) {
                return {
                    search: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function (data,params) {
                params.page = params.page || 1;
                if (data.data_cnt == 0) { 
                    var showdata = {
                        data_cnt: 0,
                        data: [{ id: "", text: "No Result Found" }]
                    };
                    return {
                        results: showdata.data,
                        pagination: {
                            more: (params.page * 20) < showdata.data_cnt
                        }
                    };
                } 
                else{    
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 20) < data.data_cnt
                    }
                };
               }
            },
            cache: true
        }
    }).val($("#"+id).val()).trigger('change');
}

function select3AjaxCommunity(id,parentId,Url,length=0){
    $("#"+id).select3({
        allowClear: true,
        dropdownAutoWidth : false,
        dropdownParent: $("#"+parentId),
        minimumInputLength: length,
        placeholder: 'Select Option',
        ajax: {
            url: DIR+Url,
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function (params,val) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    payee_type:$("input[type='radio'][name='payee_type']:checked").val(),
                };
            },
            processResults: function (data,params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 20) < data.data_cnt
                    }
                };
            },
            cache: true
        }
    }).val($("#"+id).val()).trigger('change');
}

function select3AjaxDefaultlocation(id,parentId,Url,length=0){
    $("#"+id).select3({
        allowClear: true,
        dropdownAutoWidth : false,
        dropdownParent: $("#"+parentId),
        minimumInputLength: length,
        placeholder: 'Select Option',
        ajax: {
            url: DIR+Url,
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function (params,val) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    mun_no:$("#id").val(),
                };
            },
            processResults: function (data,params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 20) < data.data_cnt
                    }
                };
            },
            cache: true
        }
    }).val($("#"+id).val()).trigger('change');
}  

function select3AjaxTicketno(id,parentId,Url,length=0){
    $("#"+id).select3({
        allowClear: true,
        dropdownAutoWidth : false,
        dropdownParent: $("#"+parentId),
        minimumInputLength: length,
        placeholder: 'Select Option',
        ajax: {
            url: DIR+Url,
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function (params,val) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    bookletid:$("#bookletid option:selected").val(),
                };
            },
            processResults: function (data,params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 20) < data.data_cnt
                    }
                };
            },
            cache: true
        }
    }).val($("#"+id).val()).trigger('change');
} 

function select3AjaxBookletno(id,parentId,Url,length=0){
    $("#"+id).select3({
        allowClear: true,
        dropdownAutoWidth : false,
        dropdownParent: $("#"+parentId),
        minimumInputLength: length,
        placeholder: 'Select Option',
        ajax: {
            url: DIR+Url,
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function (params,val) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    apphrendid:$("#aprehend_officer_id option:selected").val(),
                };
            },
            processResults: function (data,params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 20) < data.data_cnt
                    }
                };
            },
            cache: true
        }
    }).val($("#"+id).val()).trigger('change');
} 

function select3AjaxFalcno(id,parentId,Url,length=0){
    $("#"+id).select3({
        allowClear: true,
        dropdownAutoWidth : false,
        dropdownParent: $("#"+parentId),
        minimumInputLength: length,
        placeholder: 'Select Falc No.',
        ajax: {
            url: DIR+Url,
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function (params,val) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    cleintid :$("#client_id option:selected").val(),
                    isrefrence :$('#clientrefenere').is(':checked'),
                };
            },
            processResults: function (data,params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 20) < data.data_cnt
                    }
                };
            },
            cache: true
        }
    }).val($("#"+id).val()).trigger('change');
} 


function select3AjaxPermitno(id,parentId,Url,length=0){
    $("#"+id).select3({
        allowClear: true,
        dropdownAutoWidth : false,
        dropdownParent: $("#"+parentId),
        minimumInputLength: length,
        placeholder: 'Select Permit No',
        ajax: {
            url: DIR+Url,
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function (params,val) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    cleintid :$("#p_code").val(),
                };
            },
            processResults: function (data,params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 20) < data.data_cnt
                    }
                };
            },
            cache: true
        }
    }).val($("#"+id).val()).trigger('change');
}  

function select3Ajaxorno(id,parentId,Url,length=0){
    $("#"+id).select3({
        allowClear: true,
        dropdownAutoWidth : false,
        dropdownParent: $("#"+parentId),
        minimumInputLength: length,
        ajax: {
            url: DIR+Url,
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            data: function (params,val) {
                return {
                    search: params.term,
                    page: params.page || 1, 
                    id:$("#rpc_requestor_code option:selected").val(),
                    taxpayerid:$("#rpc_owner_code option:selected").val(),
                };
            },
            processResults: function (data,params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 20) < data.data_cnt
                    }
                };
            },
            cache: true
        }
    }).val($("#"+id).val()).trigger('change');
}  
  /* Shorten Updated Plugin By Sunil */
  (function($) {
    $.fn.shortenNew = function (options) {
        var settings = $.extend({
            "showChars": 30,
            "moreText": "More",
            "lessText": "Less",
            "ellipsesText": "...",
            "newLineAfter": 20
        }, options);
    
        function addNewLines(text, newLineAfter) {
            var result = '';
            for (let i = 0; i < text.length; i += newLineAfter) {
                result += text.substr(i, newLineAfter) + (i + newLineAfter < text.length ? '<br>' : '');
            }
            return result;
        }
    
        return this.each(function () {
            var $this = $(this);
            var content = $this.data('original'); // use saved version
    
            if (!content) {
                content = $this.html().trim();
                $this.data('original', content); // save for future
            }
    
            var formattedContent = addNewLines(content, settings.newLineAfter);
    
            if (content.length > settings.showChars) {
                var shortContent = content.substr(0, settings.showChars);
                var hiddenContent = content.substr(settings.showChars);
    
                shortContent = addNewLines(shortContent, settings.newLineAfter);
                hiddenContent = addNewLines(hiddenContent, settings.newLineAfter);
    
                var html = '<span class="shortcontent">' + shortContent + '</span>' +
                    '<span class="moreellipses">' + settings.ellipsesText + '</span>' +
                    '<span class="morecontent">' +
                    '<span style="display:none;">' + hiddenContent + '</span>' +
                    '<a href="#" class="morelink">' + settings.moreText + '</a>' +
                    '</span>';
    
                $this.html(html);
            } else {
                $this.html(formattedContent);
            }
        });
    };
})(jQuery);
/* Shorten Updated Plugin By Sunil */
