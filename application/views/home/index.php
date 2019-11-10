<form class="form-inline" id="pin_form">
  <label class="sr-only" for="pincode">Postal Code</label>
  <input type="text" class="form-control mb-3 mr-sm-3" id="pincode" autofocus="true" placeholder="Postal Code">
  <button type="submit" class="form-control btn btn-primary mb-3">Find</button>
</form>
<div class="alert alert-danger" role="alert" id="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <span id="errormsg"></span>
</div>
<div class="spinner-border" role="status" id="loader">
  <span class="sr-only">Loading...</span>
</div>
  <table id="stable" class="col-sm-5 table">
    <tbody>
      <tr>
        <th scope="row">District</th>
        <td id="tddistrict"></td>
      </tr>
      <tr>
        <th scope="row">State</th>
        <td id="tdstate"></td>
      </tr>
    </tbody>
  </table>


<script type="text/javascript">
  $("#stable").hide();
  $('#alert').hide();
  $("#loader").hide();
  var active = false;
  var req;
  $("#pin_form").submit(function(e){
    e.preventDefault();
    var pin = $("#pincode").val();
    $('#alert').hide();
    $("#loader").show();
    $("#stable").hide();
    if(active) req.abort();
    active = true;
    req = $.ajax({
      url:'<?=base_url()?>home/get_postal_code_details',
      data:{pin:pin},
      type:'post',
      dataType:'json',
      error:function(xhr,status,error){
        active = false;
        if(xhr.status != 0){
          $("#loader").hide();
          if(xhr.status == 428){
            $('#errormsg').html('Please connect to Internet');
          }else
          if(xhr.status == 422){
            $('#errormsg').html(xhr.responseJSON.message);
          }else{
            $('#errormsg').html(error);
          }
          $('#alert').show();
          setTimeout(function() {
            $('#alert').hide();
          }, 5000);
        }
      },
      success:function(data){
        active = false;
        $("#loader").hide();
        $("#stable").show();
        $("#tdstate").html(data.data.state);
        $("#tddistrict").html(data.data.district);
      }
    })
  })
</script>