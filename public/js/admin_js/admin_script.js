$(document).ready(function(){
    $("#current_pwd").keyup(function(){
        var current_pwd = $("#current_pwd").val();
        //alert(current_pwd);
        $.ajax({
            type:'post',
            url:'/admin/check-current-pwd',
            data:{current_pwd:current_pwd},
            success:function(resp){
                //alert(resp);
                if(resp=="false")
                {
                    $("#checkCurrentPassword").html("<font color =red>Cureent Password is Incorrect</font>");
                }
                else if(resp=="true")
                {
                    $("#checkCurrentPassword").html("<font color =green>Cureent Password is correct</font>");
                }
            },error:function(){
                alert("Error");
            }
        });
    });



    //update section status

    $(".updateSectionStatus").click(function()
    {
        var status = $(this).text();
        var section_id = $(this).attr("section_id");
        $.ajax({
            type:'post',
            url:'/admin/update-section-status',
            data:{status:status,section_id:section_id},
            success:function(resp)
            {

                if(resp['status']==0)
                {
                    $("#section-"+section_id).html( "<a class='updateSectionStatus' href='javascript:void(0)'>Inactive</a> ");
                }
                else if(resp['status']==1)
                {
                    $("#section-"+section_id).html( "<a class='updateSectionStatus' href='javascript:void(0)'>Active</a> ");
                }

            },
            error:function()
            {
                alert("Error");
            }
        })
    });


//update category status

    $(".updateCategoryStatus").click(function()
    {
        var status = $(this).text();
        var category_id = $(this).attr("category_id");
        $.ajax({
            type:'post',
            url:'/admin/update-category-status',
            data:{status:status,category_id:category_id},
            success:function(resp)
            {

                if(resp['status']==0)
                {
                    $("#category-"+category_id).html( "<a class='updateCategoryStatus' href='javascript:void(0)'>Inactive</a> ");
                }
                else if(resp['status']==1)
                {
                    $("#category-"+category_id).html( "<a class='updateCategoryStatus' href='javascript:void(0)'>Active</a> ");
                }

            },
            error:function()
            {
                alert("Error");
            }
        })
    });

    //Append Category Level

    $('#section_id').change(function()
    {
        var section_id = $(this).val();
        $.ajax({
            type:'post',
            url:'/admin/append-categories-level',
            data:{section_id:section_id},
            success:function(resp)
            {
                $("#appendCategoriesLevel").html(resp);
            },error:function()
            {
                alert("Error!");
            }
        });
    });

    //Confirm Delete

    /*
    $(".confirmDelete").click(function()
    {
        var name = $(this).attr("name");
        if(confirm("Are you sure to detete this "+name+"?"))
        {
            return true;
        }
        return false;
    });

     */

    //Confirm Delete with sweet alert

    $(".confirmDelete").click(function()
    {
        var record = $(this).attr("record");
        var recordid = $(this).attr("recordid");

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                )
                window.location.href="/admin/delete-"+record+"/"+recordid;
            }
        });

    });


    //update product status
    $(".updateProductStatus").click(function()
    {
        var status = $(this).text();
        var product_id = $(this).attr("product_id");
        $.ajax({
            type:'post',
            url:'/admin/update-product-status',
            data:{status:status,product_id:product_id},
            success:function(resp)
            {

                if(resp['status']==0)
                {
                    $("#product-"+product_id).html( "<a class='updateProductStatus' href='javascript:void(0)'>Inactive</a> ");
                }
                else if(resp['status']==1)
                {
                    $("#product-"+product_id).html( "<a class='updateProductStatus' href='javascript:void(0)'>Active</a> ");
                }

            },
            error:function()
            {
                alert("Error");
            }
        });
    });

    //Products Attributes Add Remove
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var fieldHTML = '<div><input type="text" name="field_name[]" value=""/><a href="javascript:void(0);" class="remove_button"><img src="remove-icon.png"/></a></div>'; //New input field html
    var x = 1; //Initial field counter is 1

    //Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
        if(x < maxField){
            x++; //Increment field counter
            $(wrapper).append(fieldHTML); //Add field html
        }
    });

    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter

});
