<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            solid 1px black;
            flex-direction: column;
        }

        table,
        th,
        td {
            border: 2px solid black;
            border-collapse: collapse;
            padding: 15px;
        }

        .table-header {
            display: flex;
            justify-content: center;
        }

        .table-header a {
            text-decoration: none;
        }

        .bulk-delete-button {
            color: red;
        }

        .add-new-button {
            color: blue;
        }

        .disabled-link {
            pointer-events: none;
            /* Disable pointer events */
            cursor: default;
        }

        /*.main {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background-color: black;
            } */
    </style>
</head>

<body>
    <div class="container">
        <div class="table-header">
            <div>
                <a href="#" class="add-new-button" id="add-new-button">
                    Add new
                </a>
            </div>
            <div id="bulk-delete-button">
                |
                <a href="#" class="bulk-delete-button">
                    &nbsp;Bulk Delete
                </a>
            </div>
        </div>
        <div id="table">
            <form action="#" id="inlineForm" name="inlineForm">
                <table style="width:100%">
                    <thead>
                        <tr>
                            <th>Srno</th>
                            <th>Select</th>
                            <th>Name</th>
                            <th>Contact no</th>
                            <th>hobby</th>
                            <th>Category</th>
                            <th>Profile Pic </th>
                            <th>edit</th>
                        </tr>
                    </thead>
                    <tbody style="text-align:right" id="table-body">

                    </tbody>
                </table>
            </form>
        </div>
        <div id="no-data-found" class="no-data-found">
            No Data Found
        </div>
        <div id="add-new-form" style="padding-top:20px;display:none">
            <form action="#" id="userform" name="userform">
                <table>
                    <tr>
                        <td>
                            Name
                        </td>
                        <td>
                            <input type="text" name="name">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Contact No
                        </td>
                        <td>
                            <input type="text" name="contact_no">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Hobby
                        </td>
                        <td>
                            <div id="form-hobby">

                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Category
                        </td>
                        <td>
                            <select id="form-category" name="category">

                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Profile Pic
                        </td>
                        <td>
                            <input type="file" id="profile_pic" name="profile_pic">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a href="#" id="formSubmit">Submit</a> | <a href="#" class="cancel-btn">Cancel</a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <script>
        $(function() {
            let category = [];
            let hobby = [];
            getUser();
            getCategory();
            getHobby();

            function buildTable(response) {
                $('#table-body').html("");
                if (response.status == 'success' && response.data.length > 0) {
                    $('#table').show()
                    $('#no-data-found').hide()
                    $.each(response.data, function(key, item) {
                        let row = prepareTr(item, key + 1)
                        $('#table-body').append(row);
                    });
                } else {
                    $('#table').hide()
                    $('#no-data-found').show()
                }
            }

            $(document).on('click', '.editClick', function(e) {
                e.preventDefault();
                let item = $(this).data('obj');
                let srno = $(this).data('no');
                let row = prepareTr(item, srno, 'edit')
                $('#tr' + item.id).html(row)
                $('.editClick').addClass("disabled-link");

            })

            function prepareTr(item, key, type = 'add') {
                let userHobby = '';
                let profile_pic = '-';
                let hiddenProfilepic = '';
                if (item.userhobby && item.userhobby.length > 0) {
                    userHobby = item.userhobby.map(hitem => hitem.hobby.name)
                }
                if (item.profile_pic) {
                    profile_pic = `<img src="${item.profile_pic}"  height="50" width="50">`;
                    hiddenProfilepic = item.profile_pic;
                } else {
                    hiddenProfilepic = ''
                }
                let obj = JSON.stringify(item);
                if (type == 'edit') {
                    categoryOption = '';
                    if (category.length > 0) {
                        category.forEach(catItem => {
                            categoryOption += `<option value="${catItem.id}" ${catItem.name == item.category.name?'selected':''}>${catItem.name}</option>`;
                        });
                    }

                    hobbyOption = '';
                    if (hobby.length > 0) {
                        hobby.forEach(hobItem => {
                            hobbyOption += `<option value="${hobItem.id}" ${userHobby.includes(hobItem.name) ?'selected':''}>${hobItem.name}</option>`;
                        });
                    }
                    return `<td>${key}</td>
                            <td><input type="checkbox" value="${item.id}" class="selectCheckBox"></td>
                            <td>
                                <div>
                                    <input name="name" value="${item.name}" type="text" />
                                </div>
                            </td>
                            <td>
                                <div>
                                    <input name="contact_no" value="${item.contact_no}" type="number" > 
                                </div>
                            </td>
                            <td>
                             <div>
                                <select multiple name="hobby[]">
                                    ${hobbyOption}
                                </select>
                             </td>
                            <td> 
                                <div>
                                    <select name="category">
                                        ${categoryOption}
                                    </select>
                                </td>
                            </td>
                            <td>
                                <input type="file" name="profile_pic">
                                <input type="hidden" name="hidden_profile_pic" value="${hiddenProfilepic}">
                            </td>
                            <td> 
                                <a href="#" class="submitInlineClick" data-id="${item.id}" type="submit">Submit</a> | 
                                <a href="#" class="cancel-btn" data-id="${item.id}" >cancel</a> 
                            </td>`
                }
                return `<tr id="tr${item.id}">
                            <td>${key}</td>
                            <td><input type="checkbox" value="${item.id}" name="checkRow[]"  class="selectCheckBox"></td>
                            <td>${item.name}</td>
                            <td>${item.contact_no}</td>
                            <td>${userHobby}</td>
                            <td>${item.category.name}</td>
                            <td>${profile_pic}</td>
                            <td> <a href="#" class="editClick" data-obj='${obj}' data-no="${key}">Edit</a> | <a href="#" class="singleDeleteClick" data-id="${item.id}">Delete</a> </td>
                        </tr>`
            }

            $(document).on('click', '.submitInlineClick', function(e) {
                e.preventDefault();

                $(this).text('updating..');
                let id = $(this).data('id');
                var formData = new FormData($('#inlineForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "/edit-user/" + id,
                    data: formData,
                    contentType: false,
                    context: document.body,
                    mimeType: "multipart/form-data",
                    cache: false,
                    processData: false,
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 'error') {
                            alert(response.msg)
                        } else {
                            getUser();
                        }
                    }
                });
            })

            function getUser() {
                $('.editClick').addClass("disabled-link");
                $('#bulk-delete-button').show()
                $.ajax({
                    type: "GET",
                    url: "/user",
                    dataType: "json",
                    success: function(response) {
                        buildTable(response);
                    }
                });
            }

            function getCategory() {
                $.ajax({
                    type: "GET",
                    url: "/get-category",
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 'success' && response.data.length > 0) {
                            category = response.data
                            let option = '<option value="">select option</option>';
                            category.forEach(element => {
                                option += `<option  value="${element.id}">${element.name}</option>`
                            });
                            $('#form-category').html(option);
                        }
                    }
                });
            }

            function getHobby() {
                $.ajax({
                    type: "GET",
                    url: "/get-hobby",
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 'success' && response.data.length > 0) {
                            hobby = response.data
                            let checkBox = '';
                            hobby.forEach(element => {
                                checkBox += `<label> <input type="checkBox" name="hobby[]" value="${element.id}">${element.name} </label>`
                            });
                            $('#form-hobby').html(checkBox);
                        }
                    }
                });
            }

            $('#add-new-button').click(function(e) {
                e.preventDefault();
                $('#table').hide()
                $('#add-new-form').show()
                $('#bulk-delete-button').hide()
            })

            $(document).on('click', '#formSubmit', function(e) {
                e.preventDefault();;
                var formData = new FormData($('#userform')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "/add-user/",
                    data: formData,
                    contentType: false,
                    context: document.body,
                    mimeType: "multipart/form-data",
                    cache: false,
                    processData: false,
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 'error') {
                            alert(response.msg)
                        } else {
                            getUser();
                            $("#userform")[0].reset();
                            $("#add-new-form").hide();
                        }
                    }
                });
            })

            $(document).on('click', '.cancel-btn', function(e) {
                e.preventDefault()
                getUser();
                $("#userform")[0].reset();
                $("#add-new-form").hide();
                $('#bulk-delete-button').show()
            })

            $('.bulk-delete-button').click(function(e) {
                e.preventDefault()
                var allVals = [];
                $('.selectCheckBox:checked').each(function() {
                    allVals.push($(this).val());
                });
                if (allVals.length > 0) {
                    if (confirm('Are you sure you want to delete user')) {
                        deleteUser(allVals)
                    }
                } else {
                    alert('please select al teast on row!!');
                }
            })

            $(document).on('click', '.singleDeleteClick', function(e) {
                e.preventDefault()
                var allVals = $(this).data('id');
                if (confirm('Are you sure you want to delete user')) {
                    deleteUser([allVals])
                }
            })


            function deleteUser(userId) {
                console.log(userId)
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                let payload = {
                    user_id: userId
                }
                $.ajax({
                    type: "POST",
                    url: "/delete-user/",
                    data: payload,
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 'error') {
                            alert(response.msg)
                        } else {
                            getUser();
                            $("#userform")[0].reset();
                            $("#add-new-form").hide();
                        }
                    }
                })
            }
        })
    </script>
</body>

</html>