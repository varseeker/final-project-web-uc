$(document).ready(function(){
  
    
    $( "input" ).on( "change", function() {
        $("#commit-button").attr('disabled', false);
        $("#commit-button").removeClass('btn-secondary');
        $("#commit-button").addClass('btn-success');
      } );

    

});