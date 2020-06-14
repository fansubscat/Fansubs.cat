$(document).ready(function() {

  //We setup magnific popups for all fansub images
  $(".article-content").each(function(){
    $(this).magnificPopup({
      delegate: "img",
      type: 'image',
      gallery: {
        enabled: true
      },
      callbacks: {
        elementParse: function(item) { item.src = item.el.attr('src'); }
      },
	zoom: {
          enabled: true,
          duration: 300
      }
    });
  });

  //Setup filter toggle clicking
  //Yes, it's hacky because it has the string literals here.
  //No, I won't change it for now :D
  $("#filter-toggle").click(function(){
    if ($("#filter-toggle").text()=='↓ Canvia els teus preferits ↓'){
      $("#filter-toggle").text("↑ Amaga els teus preferits ↑");
      $("#filter-details").slideDown(200);
    }
    else if ($("#filter-toggle").text()=='↑ Aplica els canvis ↑'){
      $("#filter-toggle").text("↓ Canvia els teus preferits ↓");
      $("#filter-details").slideUp(200);

      var selectedChildrenAsText = "";
      if ($("#filter-details .filter-selected").length!=$("#filter-details .filter-fansub").length){
        $("#filter-details .filter-selected").each(function(index, element){
          if (selectedChildrenAsText!=""){
            selectedChildrenAsText += ",";
          }
          selectedChildrenAsText+=$(element).attr('id').substring(14);
        });
      }
      
      //Set to a user cookie with 10 years of expiry time
      //Just hoping users don't complain in 2026.
      Cookies.set('favorite_fansubs', selectedChildrenAsText, { expires: 3650, path: '/', domain: 'fansubs.cat' });
      location.href='/';
    }
    else if ($("#filter-toggle").text()=='Torna a la pàgina principal'){
      location.href='/';
    }
    else{
      $("#filter-toggle").text("↓ Canvia els teus preferits ↓");
      $("#filter-details").slideUp(200);
    }
  });

  //Setup fansub selection coloring via CSS classes (for red/green colors)
  $(".filter-fansub").click(function(){
    if ($(this).hasClass('filter-selected')){
      $(this).removeClass('filter-selected');
    }
    else{
      $(this).addClass('filter-selected');
    }
    $("#filter-toggle").text('↑ Aplica els canvis ↑');
  });

  //Setup close button for the first time welcome message
  //Also using a cookie with 10 years expiration time
  $("#close").click(function(){
    Cookies.set('welcome_closed', '1', { expires: 3650, path: '/', domain: 'fansubs.cat' });
    $("#welcome").hide();
  });

  //Same for the close button for the app message
  $("#appclose").click(function(){
    Cookies.set('app_closed', '1', { expires: 3650, path: '/', domain: 'fansubs.cat' });
    $("#app").hide();
  });

  //Select all button
  $(".filter-select-all").click(function(){
    $(".filter-fansub").addClass('filter-selected');
    $("#filter-toggle").text('↑ Aplica els canvis ↑');
  });

  //Unselect all button
  $(".filter-select-none").click(function(){
    $(".filter-fansub").removeClass('filter-selected');
    $("#filter-toggle").text('↑ Aplica els canvis ↑');
  });

  //Specific for the contact us page: hide certain elements depending on
  //contact reason (and disable them so required fields are not evaluated)
  $('select[name="reason"]').on('change', function() {
    if (this.value=="add_news"){
      $("tr.add_news").show();
      $("tr.add_news input, tr.add_news textarea").prop('disabled', false);
      $("tr.new_fansub").hide();
      $("tr.new_fansub input, tr.new_fansub textarea").prop('disabled', true);
    }
    else if (this.value=="new_fansub"){
      $("tr.add_news").hide();
      $("tr.add_news input, tr.add_news textarea").prop('disabled', true);
      $("tr.new_fansub").show();
      $("tr.new_fansub input, tr.new_fansub textarea").prop('disabled', false);
    }
    else{
      $("tr.add_news").hide();
      $("tr.add_news input, tr.add_news textarea").prop('disabled', true);
      $("tr.new_fansub").hide();
      $("tr.new_fansub input, tr.new_fansub textarea").prop('disabled', true);
    }
  });

  //Fix for back button after sending the form in contact us page
  $('select[name="reason"]').trigger("change");

  var form = document.getElementById("search_form");
  if (form!=null){
    $(form).submit(function () {
      if (document.getElementById('search_query').value!=''){
        window.location.href='/cerca/' + document.getElementById('search_query').value;
      }
      return false;
    });
  }
});

//Google Analytics
window.dataLayer = window.dataLayer || [];
function gtag(){
  dataLayer.push(arguments)
}
gtag('js', new Date());
gtag('config', 'UA-628107-13');
