$('.close-panel').click(function() {
  $(this).closest('.card').remove();
});

$('.bookmark-panel').click(function() {
  $(this).find('i').toggleClass('fa-bookmark-o');
  $(this).find('i').toggleClass('fa-bookmark');
});