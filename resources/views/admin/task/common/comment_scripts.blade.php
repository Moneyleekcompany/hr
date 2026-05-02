<script src="{{asset('assets/vendors/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('assets/js/tinymce.js')}}"></script>
<script src="{{asset('assets/js/imageuploadify.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<script>
    $(document).ready(function (e) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.commentForm').hide();

        $('.error').hide();

        $('.list').hide();

        $('.replyicon').hide();

        $("#mention").select2({
            placeholder: "mention"
        });

        $('#createComment').on('click',function(e){

            $('.d-none').removeClass('d-none');
            $('.commentListing').removeClass('d-none');
            // $('#cmntReply').hide();
            $('#description').val('');
            $('#mention').select2('destroy').find('option').prop('selected', false).end().select2();
            $("#mention").select2({
                placeholder: "@lang('index.mention')"
            });
            let text = $(this).text();
            (text === 'Comment') ? $(this).text('Close') : $(this).text("@lang('index.comment')");

            $('.commentForm').toggle(500);
            $('.list').toggle(500);
        })

        $('.showComments').click(function(e){
            $('.d-none').removeClass('d-none');
            $('.commentListing').removeClass('d-none');
            // $('#cmntReply').hide();
            $('#description').val('');
            $('#mention').select2('destroy').find('option').prop('selected', false).end().select2();
            $("#mention").select2({
                placeholder: "@lang('index.mention')"
            });

            $('.commentForm').toggle(500);
            $('.list').toggle(500);
        })

        $('body').on('click','#showReply',function(e){
            e.preventDefault();
            let id = $(this).data('id')
            $('.reply'+id+'').toggle(500);
            $('.reply'+id+'').removeClass('d-none');
        });

        $('body').on('click','.replyCreate',function(e){
            e.preventDefault();
            let commentId = $(this).data('comment');
            $('#commentId').val(commentId);
            $('#description').attr("placeholder", "@lang('index.reply')");
            $('.replyicon').show();
            $('html,body').animate({
                scrollTop: $("#replyForm").offset().top - 100
            }, 300);
        });

        $('.replyicon').click(function(e){
            e.preventDefault();
            $('#commentId').val('');
            $('#description').val("");
            $('#description').attr("placeholder", "@lang('index.write_comment')");
            $(this).hide();
        });

        // الإرسال السريع بالضغط على Enter
        $('body').on('keydown', '#description', function(e) {
            // إذا تم الضغط على Enter ولم يتم الضغط على Shift (لكتابة سطر جديد)
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                $('#commentSubmit').click();
            }
        });

        $('body').on('click','#commentSubmit',function(e){
            e.preventDefault()
            let url = $('#taskCommentForm').attr('action');;
            let formData =  $('#taskCommentForm').serialize();
            $.ajax({
                type: "POST",
                url: url,
                data: formData
            }).done(function(response) {
                if(response.status_code == 200 && response.data != ''){
                    let commentDetail = response.data;
                    let id = commentDetail.id;
                    let commentId = commentDetail.comment_id;
                    let avatar= commentDetail.avatar;
                    let createdBy= commentDetail.created_by;
                    let createdAt= commentDetail.created_at;
                    let description = commentDetail.description;
                    let commentDeleteRoute = "{{ url('admin/task-comment/delete') }}" + '/' + id
                    let replyDeleteRoute = "{{url('admin/task-comment/reply/delete')}}" + '/' + id;
                    let mentioned = commentDetail.mentioned;

                    $('#description').val('');
                    $('#mention').select2('destroy').find('option').prop('selected', false).end().select2();
                    $("#mention").select2({
                        placeholder: "@lang('index.mention')"
                    });
                    $('#commentId').val('');
                    if(commentDetail.comment_id == ''){
                        let spanId = 'comment'+id;
                        let commentCount = $('.commentsCount').text();
                        let totalComments = parseInt(commentCount) + 1;
                        $('.commentsCount').text(totalComments)
                        let count = 0;

                        $('<div class="comment-box parentComment'+id+' d-flex align-items-start gap-3 mb-4">'+
                            '<div class="comment-image flex-shrink-0 mt-1">'+
                                '<img class="rounded-circle shadow-sm" style="width: 45px; height: 45px; object-fit: cover;" title="'+createdBy+'" src="'+avatar+'" alt="profile">'+
                            '</div>'+
                            '<div class="comment-content bg-light p-4 rounded-4 w-100 position-relative shadow-sm">'
                                +'<div class="d-flex justify-content-between align-items-center mb-2">'
                                    +'<h6 class="mb-0 fw-bold text-primary">'+ createdBy +'</h6>'
                                    +'<small class="text-muted" style="font-size: 12px;">'+createdAt +'</small>'
                                +'</div>'
                                +'<p class="comment mb-3 text-dark" id="'+spanId+'" style="line-height: 1.6;">'
                                   + description +
                                '</p>'+
                                '<div class="comment-reply position-relative commentReply'+id+' border-top border-light pt-3 mt-2">'+
                                    '<div class="row number-reply d-flex align-items-center justify-content-between">'+
                                        '<div class="col-lg-6">'+
                                            '<p class="text-muted mb-0 small cursor-pointer" id="showReply" data-id="'+id+'">'+
                                                '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle me-1"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>'
                                                +'<span class="replyCount'+id+' fw-bold">'+count+ '</span> '+ '@lang('index.reply')'+
                                            '</p>'+
                                        '</div>'+
                                        '<div class="col-lg-6 text-end">'+
                                            '<button data-mention="'+createdBy+'" data-comment="'+id+'" class="replyCreate btn btn-outline-primary btn-sm rounded-pill px-3 py-1">'+
                                            '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-up-left me-1"><polyline points="9 14 4 9 9 4"></polyline><path d="M20 20v-7a4 4 0 0 0-4-4H4"></path></svg>'
                                            +'@lang('index.reply')</button>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="reply'+id+' mt-3" id="cmntReply"></div>'+
                                '</div>'+
                                '<a class="commentDelete position-absolute top-0 end-0 mt-3 me-3 text-danger opacity-75" data-comment="'+id+'" id="deleteComment" data-id="'+id+'" data-title="Comment" href="'+commentDeleteRoute+'" style="cursor: pointer; transition: 0.2s;">'+
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>'+
                                '</a>'+
                            '</div>'+
                        '</div>')
                            .appendTo(".commentsAdd");

                        if(mentioned.length > 0){
                            mentioned.forEach(function(data) {
                                let name = "@"+data.name+ " " ;
                                $('#comment'+id+'').prepend('<span">'+ '<a href="#">'+ name +'</a>'+ '</span>');
                            });
                        }

                        $('html,body').animate({
                            scrollTop: $('#comment'+id+'').offset().top - 100
                        }, 300);

                    }else{
                        let spanReplyId = 'reply'+id;
                        let repliesCount = $('.replyCount'+commentId+'').text();
                        let totalReplies = parseInt(repliesCount) + 1;
                        $('.replyCount'+commentId+'').text(totalReplies);
                        $(
                            '<div class="comment-box d-flex align-items-start gap-2 mt-3 singleReply'+id+'">'+
                                '<div class="comment-image flex-shrink-0 mt-1">'+
                                    '<img class="rounded-circle shadow-sm" style="width: 35px; height: 35px; object-fit: cover;" title="'+createdBy+'" src="'+avatar+'"  alt="profile">'+
                                '</div>'+
                                '<div class="comment-content bg-white border border-light p-3 rounded-4 w-100 position-relative shadow-sm">'+
                                    '<div class="d-flex justify-content-between align-items-center mb-1">'+
                                       ' <h6 class="mb-0 fw-bold text-secondary" style="font-size: 14px;">'+ createdBy +' </h6>'+
                                        '<small class="text-muted" style="font-size: 11px;">'+createdAt +'</small>'+
                                    '</div>'+
                                    '<p class="comment mb-0 text-dark" id="'+spanReplyId+'" style="line-height: 1.5; font-size: 14px;">'
                                        + description +
                                    '</p>'+
                                    '<a class="replyDelete position-absolute top-0 end-0 mt-2 me-2 text-danger opacity-75" id="deleteComment" data-title="Reply" data-comment="'+commentId+'" data-id="'+id+'" href="'+replyDeleteRoute+'" style="cursor: pointer; transition: 0.2s;">'+
                                        '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>'+
                                    '</a>'+
                                '</div>'+
                            '</div>'
                        ).appendTo(".reply"+commentId+"");

                        if(mentioned.length > 0){
                            mentioned.forEach(function(data) {
                                let name = "@"+data.name+ " " ;
                                $('#reply'+id+'').prepend('<span">'+ '<a href="#">'+ name +'</a>'+ '</span>');
                            });
                        }

                        $('html,body').animate({
                            scrollTop: $('.replyCount'+commentId+'').offset().top - 60
                        }, 300);
                    }
                    $('#commentId').val('');
                    $('#description').val("");
                    $('#description').attr("placeholder", "@lang('index.write_comment')");
                    $('.replyicon').hide();
                }
            }).error(function(error){
                let errorMessage = error.responseJSON.message;
                $('html,body').animate({
                    scrollTop: $("#showFlashMessageResponse").offset().top - 70
                }, 300);
                $('#errorMessageDelete').removeClass('d-none');
                $('.error').show();
                $('.errorMessageDelete').text(errorMessage);
                $('div.alert.alert-danger').not('.alert-important').delay(1000).slideUp(900);
            });
        });

        $('body').on('click', '#deleteComment', function (event) {
            event.preventDefault();
            let title = $(this).data('title');
            let id = $(this).data('id');
            let url = $(this).attr('href');
            let commentId = $(this).data('comment');
            Swal.fire({
                title: @json(__('index.delete_confirm', ['title' => ''])) + ' ' + title,
                showDenyButton: true,
                confirmButtonText: '@lang('index.yes')',
                denyButtonText: '@lang('index.no')',
                padding:'10px 50px 10px 50px',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: url ,
                    }).done(function(response) {
                        if(response.status_code == 200){
                            if(title == 'Reply'){
                                let repliesCount = $('.replyCount'+commentId+'').text();
                                let totalReplies = parseInt(repliesCount) - 1;
                                $('.replyCount'+commentId+'').text(totalReplies);
                                $('.singleReply'+id+'').remove();
                            }else{
                                let commentCount = $('.commentsCount').text();
                                let totalComments = parseInt(commentCount) - 1;
                                $('.commentsCount').text(totalComments)
                                $('.parentComment'+id+'').remove();
                            }
                        }
                    }).error(function(error){
                        let errorMessage = error.responseJSON.message;
                        $('html,body').animate({
                            scrollTop: $("#showFlashMessageResponse").offset().top - 70
                        }, 300);
                        $('#errorMessageDelete').removeClass('d-none');
                        $('.error').show();
                        $('.errorMessageDelete').text(errorMessage);
                        $('div.alert.alert-danger').not('.alert-important').delay(1000).slideUp(900);
                    });
                }
            })
        })
    });

</script>
