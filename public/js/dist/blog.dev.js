"use strict";

$(document).ready(function () {
  var postContent = CKEDITOR.replace('post-content');
  $("#post-tags").select2({
    placeholder: 'Select the tags',
    tags: true,
    tokenSeparators: [',', ' ']
  });
  $("#post-categories").select2({
    placeholder: 'Select the categories'
  });
  $('#post-commit').click(function () {
    if ($('#post-title').val() === '') {
      alert('Enter the title');
      return;
    } else if ($('#post-categories').val() === '') {
      alert('Please select the categories');
    } else if (editor.getData() === '') {
      alert('Please edit post content');
    } else if ($('#post-tags').val() === '') {
      alert('Please select the tags');
    } else {
      $.ajax({
        url: BASE_APP_URL + 'blog',
        method: 'POST',
        data: {
          author_id: 1,
          area_id: areaId,
          title: $('#post-title').val(),
          categories: $('#post-categories').val(),
          content: editor.getData(),
          tags: $('#post-tags').val(),
          _token: csrfToken
        },
        success: function success(response) {
          $('#new-post-modal').modal('hide');
        }
      });
    }
  });
});