<!DOCTYPE html>
<html lang="en">
<head>
  <title>CRUD</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <!-- Bootstrap Select Picker CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css"/>

  <!-- Datatable CSS -->
 <link href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>
 <style type="text/css">
 	.dataTables_wrapper{
 		margin-top: 50px;
 	}
 </style>
</head>
<body>

<div class="container">
  <h2 class="text-center">Employees Details</h2>
  <button type="button" class="btn btn-info" id="add_button">Add Employee</button>
  <table class="table" id="employee_table">
    <thead>
      <tr>
        <th>S.No</th>
        <th>Employee Code</th>
        <th>Employee Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Designation</th>
        <th>Roles</th>
        <th>Photo</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
    	
    </tbody>
  </table>
</div>

<!-- Users Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
   <form id="user_form" method="POST" enctype="multipart/form-data">
    <div class="modal-content">
      <div class="modal-header">
        <span id="success_message"></span>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body row">
          <div class="form-group col-sm-4">
            <label>Employee Name</label>
            <input type="text" name="employee_name" id="employee_name" class="form-control">
            <div id="employee_name_error" class="text-danger"></div>
          </div>
          <div class="form-group col-sm-4">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control">
            <span id="email_error" class="text-danger"></span>
          </div>
          <div class="form-group col-sm-4">
            <label>Mobile</label>
            <input type="number" name="mobile" id="mobile" class="form-control">
            <span id="mobile_error" class="text-danger"></span>
          </div>
          <div class="form-group col-sm-4">
            <label>Designation</label>
            <input type="text" name="designation" id="designation" class="form-control">
            <span id="designation_error" class="text-danger"></span>
          </div>
          <div class="form-group col-sm-8">
            <label>Roles</label>
            <select name="roles[]" class="form-control multi_select" multiple id="roles">
            	<option value=" ">Please select...</option>
            	<option value="Admin">Admin</option>
            	<option value="Sub-Admin">Sub-Admin</option>
            	<option value="B2C">B2C</option>
            	<option value="Agent">Agent</option>
            	<option value="Supplier">Supplier</option>
            </select>
            <span id="roles_error" class="text-danger"></span>
          </div>
          <div class="form-group col-sm-4">
            <label>Photo</label>
            <input type="file" name="photo" id="photo" class="form-control">
            <input type="hidden" name="hidden_photo" id="hidden_photo">
            <img src="" id="display_image" height="50" width="100">
            <span id="photo_error" class="text-danger"></span>
          </div>
      </div> 
      <div class="modal-footer">
        <input type="hidden" name="origin" id="origin">
        <input type="hidden" name="data_action" id="data_action" value="Insert">
        <input type="submit" name="action" id="action" value="Add" class="btn btn-success">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
   </form>

  </div>
</div>

<!-- Bootstrap Select Picker JS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

 <!-- Datatable JS -->
 <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
</body>
</html>

<script type="text/javascript">
  $(document).ready(function(){

  	// Mutiple select dropdown
    $('.multi_select').selectpicker();

    // Datatable severside pagination
    var dataTable = $('#employee_table').DataTable({
          "processing":true,
          "serverside":true,
          "order":[],
          "ajax":{
          	url:"<?php echo base_url()?>index.php/welcome/fetch_employees",
          	type:"POST"
          },
          "columnDefs":[
          	{
          		"target":[0,3,4],
          		"orderable":false
          	}
          ]
        });

    //Add Button
    $(document).on('click','#add_button',function(){
      $('#user_form')[0].reset();
      $('.modal-title').text('Add Employee');
      $('#data_action').val('Insert');
      $('#action').val('Add');
      $('#success_message').html('');
      $('#origin').val('');
      $('#employee_name').val('');
      $('#email').val('');
      $('#mobile').val('');
      $('#designation').val('');
      $('#roles').val('').change();
      $('#hidden_photo').val('');
      $('#display_image').attr('src',' ');
      $('#display_image').hide();
      $('#myModal').modal('show');
    });

    // Insert & Update
    $(document).on('submit','#user_form',function(event){
      event.preventDefault();
      $.ajax({
        url:'<?php echo base_url()?>index.php/welcome/action',
        method:'POST',
        data:new FormData(this),
        dataType:'json',
        contentType: false,
        cache: false,
        processData:false,
        success:function(data){
          if(data.success) {
            $('#user_form')[0].reset();
            $('#myModal').modal('show');
              $('#success_message').html('<div class="alert alert-success">'+data.msg+'</div>');
              $('#myModal').delay(3000).modal('hide');
              location.reload();
          } 
          if(data.error) {
            $('#employee_name_error').html(data.employee_name_error);
            $('#email_error').html(data.email_error);
            $('#mobile_error').html(data.mobile_error);
            $('#designation_error').html(data.designation_error);
            $('#roles_error').html(data.roles_error);
            $('#photo_error').html(data.photo_error);
          }
        }
      });
    });

    //Fetch Single User Data
    $(document).on('click','.edit',function(){
      var origin = $(this).attr('id');
      console.log(origin);
      $.ajax({
          url:'<?php echo base_url()?>index.php/welcome/action',
          method:'POST',
          data:{origin:origin,data_action:'fetch_single_user_data'},
          dataType:'json',
          success:function(data){
            console.log(data);
          $('#myModal').modal('show');
          $('.modal-title').text('Edit Employee');
          $('#data_action').val('Update');
          $('#action').val('Edit');
          $('#success_message').html('');
          $('#origin').val(data.origin);
          $('#employee_name').val(data.employee_name);
          $('#email').val(data.email);
          $('#mobile').val(data.mobile);
          $('#designation').val(data.designation);
          $('#roles').val(data.roles).change();
          $('#hidden_photo').val(data.photo);
          $('#display_image').attr('src','<?php echo base_url().display_path?>'+data.photo);
          $('#display_image').show();
        }
      });
    });

    // Delete
    $(document).on('click','.delete',function(){
      var origin = $(this).attr('id');
      console.log(origin);
      if (confirm("Are You Sure You Want To Delete This ? ")) {
        $.ajax({
            url:'<?php echo base_url()?>index.php/welcome/action',
            method:'POST',
            data:{origin:origin,data_action:'Delete'},
            dataType:'json',
            success:function(data){
              if (data.success) {
              	location.reload();
              }
            }
        });
      }
    });
  });
</script>
