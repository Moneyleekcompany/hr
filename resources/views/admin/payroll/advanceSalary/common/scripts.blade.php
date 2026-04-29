<script src="{{asset('assets/js/imageuploadify.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<script>
    $(document).ready(function (e) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true
        });

        $("#image-uploadify").imageuploadify();

        let initialStatus = $('#status').val();
        let status = initialStatus == '' ? 'processing' : initialStatus
        manipulateDomBasedOnStatus(status);

        $('body').on('click', '.delete', function (event) {
            event.preventDefault();
            let href = $(this).data('href');
            Swal.fire({
                title: `{{ __('index.delete_confirmation') }}`,
                showDenyButton: true,
                confirmButtonText: `{{ __('index.yes') }}`,
                denyButtonText: `{{ __('index.no') }}`,
                padding:'10px 50px 10px 50px',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            })
        })

        $('#status').change(function(){
           let status = $(this).val();
           manipulateDomBasedOnStatus(status)
        });

        function manipulateDomBasedOnStatus(status){
            if(status == 'approved'){
                $('.releaseAmount').show();
                $('.document').show();
                $('.amountReleased').prop('required', 'true');
                $('.attachment').prop('required', 'true');
                $('.reason').hide();
                $('.remark').removeAttr('required');
            }

            if(status == 'rejected'){
                $('.reason').show();
                $('.remark').prop('required', 'true');
                $('.releaseAmount').hide();
                $('.document').hide();
                $('.amountReleased').removeAttr('required');
                $('.attachment').removeAttr('required');
            }

            if(status == 'processing'){
                $('.releaseAmount').hide();
                $('.reason').hide();
                $('.document').hide();
                $('.remark').removeAttr('required');
                $('.amountReleased').removeAttr('required');
                $('.attachment').removeAttr('required');
            }
        }

    });

</script>
