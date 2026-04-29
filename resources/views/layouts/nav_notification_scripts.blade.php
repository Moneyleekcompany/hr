
<script>
    $('document').ready(function (){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        $('#notificationsNavBar').click(function(event){
            event.preventDefault();
            let url = $(this).data('href');
            $.get(url, function (data) {
                let len = 0;
                if(data.data != null){
                    len = data.data.length;
                }
                if(len > 0) {
                    $(".check").remove();
                    for (let i = 0; i < len; i++) {
                        let title = data.data[i].title
                        let publishDate = data.data[i].publish_date;
                        let notification =
                            "<span class='dropdown-item d-flex align-items-center py-3 border-bottom check' style='cursor: pointer; transition: background-color 0.2s ease;'>"+
                            "<div class='d-flex align-items-center justify-content-center rounded-circle shadow-sm me-3' style='background-color: #8a0c51; width: 38px; height: 38px; min-width: 38px;'>"+
                            "<i class='ti ti-bell text-white fs-5'></i>"+
                            "</div>"+
                            "<div class='flex-grow-1 me-2 text-wrap'>"+
                            "<p class='mb-1 fw-bold text-dark' style='font-size: 14.5px; line-height: 1.4;'>" +title+ "</p>"+
                            "<p class='mb-0 text-muted publish_date d-flex align-items-center' style='font-size: 12px;'><i class='ti ti-clock me-1' style='font-size: 14px;'></i>" +publishDate+ "</p>"+
                            "</div>"+
                            "</span>";
                        $("#notifications-detail").append(notification);
                    }
                }

            });
        })

        $('#navAdminNotificationCreate').on('click',function(event){
            let href = $(this).data('href');
            window.location.href = href;
        });

        $('#navAdminNotificationList').on('click',function(event){
            let url = $(this).data('href');
            window.location.href = url;
        });




    });
</script>
