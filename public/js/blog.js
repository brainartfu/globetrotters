let editor;
$(document).ready(function () {

  getPostInfo(areaId);

  $(document).on('click', '.open-send-form', function () {
    $('.comment-form').remove();
    let html = `
      <div class="my-1 comment-form">
        <textarea pid="0" class="form-control post-comment" placeholder="Leave a comment" ></textarea>
        <div class="d-flex justify-content-end pt-1">
          <button class="btn btn-primary btn-sm mr-1" id="commit-comment">
              Save
          </button>
          <button class="btn btn-danger btn-sm" id="commit-cancel">
              Cancel
          </button>
        </div>
      </div>`;
    $(this).closest('li').append(html);
    $('.post-comment').attr('pid', $(this).attr('comment-id'));
    $('.post-comment').attr('post-id', $(this).attr('post-id'));
  });

  $(document).on('click', '#commit-cancel', function () {
    $('.comment-form').remove();
  });

  editor = $('#editor').summernote({
    dialogsInBody: true,
    callbacks: {
      onInit: () => {
        alert
        $('.note-modal').on('hidden.bs.modal', () => {
          $('body').addClass('modal-open');
        });
      },
      onImageUpload: function (files) {
        if (!files.length) return;
        var file = files[0];
        var reader = new FileReader();
        reader.onloadend = function () {
          console.log("img", $("<img>"));
          var img = $("<img>").attr({ src: reader.result, width: "100%" }); // << Add here img attributes !
          console.log("var img", img);
          $('#editor').summernote("insertNode", img[0]);
        }

        if (file) {
          reader.readAsDataURL(file);
        }
      }
    }
  });
  $("#post-tags").select2({
    placeholder: 'Select the tags',
    tags: true,
    tokenSeparators: [',', ' ']
  });

  $("#post-categories").select2({
    placeholder: 'Select the categories',
  });

  $('#post-commit').click(function () {
    if ($('#post-title').val() === '') {
      alert('Enter the title'); return;
    } else if ($('#post-categories').val() === '') {
      alert('Please select the categories');
    } else if ($('#editor').summernote('code') === '') {
      alert('Please edit post content');
    } else if ($('#post-tags').val() === '') {
      alert('Please select the tags');
    } else {
      $.ajax({
        url: APP_URL + 'blog',
        method: 'POST',
        data: {
          area_id: areaId,
          title: $('#post-title').val(),
          categories: $('#post-categories').val(),
          content: $('#editor').summernote('code'),
          tags: $('#post-tags').val(),
          _token: csrfToken
        },
        success: (response) => {
          $('#new-post-modal').modal('hide');
          getPostInfo(areaId);
        }
      });
    }
  });

  $(document).on('click', '#commit-comment', () => {
    if (loggedUserId > 0) {
      $.ajax({
        url: `${APP_URL}blog/leave-comment`,
        method: 'POST',
        data: {
          postId: $('.post-comment').attr('post-id'),
          pid: $('.post-comment').attr('pid'),
          comment: $('.post-comment').val(),
          _token: csrfToken
        },
        success: res => {
          if (res === 'OK') {
            $('.comment-form').remove();
            getPostInfo(areaId);
          }
        }
      });
    } else {
      alert('Please login');
      location.href = `${APP_URL}login`;
    }
  });
});

const getPostInfo = id => {
  $('.post-content .title').text('');
  $('.post-content .content').html('');
  $('.view-count').text('0');
  $('.like-count').text('0');
  $('.dislike-count').text('0');

  $.ajax({
    url: `${APP_URL}blog/${id}`,
    method: 'GET',
    data: {
      pageNum: 1,
      pageViewCount
    },
    success: res => {
      pageNum = 1;
      let htmlStr = ``;

      res.forEach(post => {
        let categoriesStr = ``;
        post.categories &&
          post.categories.forEach(item => {
            categoriesStr += `<span class="btn btn-white btn-sm text-secondary"><i class="fa fa-tag"></i> ${item}</span>`;
          });

        let postStr = `
              <div class="post pb-5 border-top">
                <div class="post-header d-flex justify-content-between py-2">
                  <div class="d-flex align-items-center">
                    <img src="${APP_URL}img/users/10.jfif" class="avatar">
                    <p class="mb-0 categories d-flex">${categoriesStr}</p>
                  </div>
                  <div class="d-flex post-status">
                    <a class="view-count btn btn-sm text-secondary d-flex align-items-center">
                      <i class="fa fa-eye fa-lg mr-1"></i>
                      <span class="count">${post.view_count}</span>
                    </a>
                    <a class="btn btn-sm text-secondary d-flex align-items-center" post-id="${post.id}" onClick="like(this)">
                      <i class="fa fa-thumbs-up fa-lg mr-1"></i>
                      <span class="count">${post.like_count}</span>
                    </a>
                    <a class="dislike-count btn btn-sm text-secondary d-flex align-items-center" post-id="${post.id}" onClick="dislike(this)">
                      <i class="fa fa-thumbs-down fa-lg mr-1"></i>
                      <span class="count">${post.dislike_count}</span>
                    </a>
                  </div>
                </div>
                <div class="post-content">
                  <h3>${post.title}</h3>
                  <div class="content">
                    ${post.content}
                    <li style='list-style-type: none'>
                      <div class="d-flex justify-content-between align-items-center">
                        <h5>Comments</h5>
                        <button comment-id='0' post-id=${post.id} class='open-send-form btn mb-1 btn-primary btn-sm'>
                          Add Comment
                        </button>
                      </div>
                    </li>
                  </div>
                  <div class="post-comments-container">
                    <div class="comment-list">${getCommentsHtml(post.comments, post.id)}</div>
                  </div>
                </div>
              </div>
            `;
        htmlStr += postStr;
      });
      $('.post-blog').html(htmlStr);
    }
  })
}
const getCommentsHtml = (comments, postId) => {
  var html = '<ul>';
  for (var c in comments) {
    html += '<li>';
    html += '<p>' + comments[c]['content'] + '</p>';
    html += '<p class="text-right mb-0"><a comment-id="' + comments[c]['id'] + '" post-id="' + postId + '" class="text-secondary open-send-form"><i class="fa fa-send"></i></a></p>';
    html += '</li>';
    if (comments[c]['childs']) {
      html += getCommentsHtml(comments[c]['childs'], postId);
    }
  }
  html += '</ul>';
  return html;
}

const like = obj => {
  let postId = obj.getAttribute('post-id');
  let count = obj.children[1].innerHTML * 1 + 1;
  $.ajax({
    url: `${APP_URL}blog/like`,
    method: 'POST',
    data: {
      postId,
      count,
      _token: csrfToken
    },
    success: res => {
      if (res === 'OK') {
        obj.children[1].innerHTML = count;
      }
    }
  });
};

const dislike = (obj) => {
  let postId = obj.getAttribute('post-id');
  let count = obj.children[1].innerHTML * 1 + 1;
  $.ajax({
    url: `${APP_URL}blog/dislike`,
    method: 'POST',
    data: {
      postId,
      count,
      _token: csrfToken
    },
    success: res => {
      if (res === 'OK') {
        obj.children[1].innerHTML = count;
      }
    }
  });
};

$('.post-group').scroll(function () {
  let lastPostObj = $('.post').last();

  if (lastPostObj.length > 0) {
    if (lastPostObj.offset().top + lastPostObj.outerHeight() < window.innerHeight) {
      $.ajax({
        url: `${APP_URL}blog/${areaId}`,
        method: 'GET',
        data: {
          pageNum: pageNum + 1,
          pageViewCount
        },
        success: res => {
          let htmlStr = ``;

          res.forEach((post) => {
            let categoriesStr = ``;
            post.categories &&
              post.categories.forEach(item => {
                categoriesStr += `<span class="btn btn-white btn-sm text-secondary"><i class="fa fa-tag"></i> ${item}</span>`;
              });

            let postStr = `
                <div class="post pb-5 border-top">
                  <div class="post-header d-flex justify-content-between py-2">
                    <div class="d-flex align-items-center">
                      <img src="${APP_URL}img/users/10.jfif" class="avatar">
                      <p class="mb-0 categories d-flex">${categoriesStr}</p>
                    </div>
                    <div class="d-flex post-status">
                      <a class="view-count btn btn-sm text-secondary d-flex align-items-center">
                        <i class="fa fa-eye fa-lg mr-1"></i>
                        <span class="count">${post.view_count}</span>
                      </a>
                      <a class="btn btn-sm text-secondary d-flex align-items-center" post-id="${post.id}" onClick="like(this)">
                        <i class="fa fa-thumbs-up fa-lg mr-1"></i>
                        <span class="count">${post.like_count}</span>
                      </a>
                      <a class="dislike-count btn btn-sm text-secondary d-flex align-items-center" post-id="${post.id}" onClick="dislike(this)">
                        <i class="fa fa-thumbs-down fa-lg mr-1"></i>
                        <span class="count">${post.dislike_count}</span>
                      </a>
                    </div>
                  </div>
                  <div class="post-content">
                    <div class="title">${post.title}</div>
                    <div class="content">
                      ${post.content}
                      <li style='list-style-type: none' class='text-align:right'>
                        <button comment-id='0' post-id=${post.id} class='open-send-form btn mb-1 btn-primary btn-sm'>
                          Add Comment
                        </button>
                      </li>
                    </div>
                  </div>
                  <div class="post-comments-container">
                    <h5 class="mt-4">Comments</h5>
                    <div class="comment-list">${getCommentsHtml(post.comments, post.id)}</div>
                  </div>
                </div>
              `;
            htmlStr += postStr;
          });
          $('.post-blog').append(htmlStr);
          pageNum++;
        }
      })
    }
  }
});