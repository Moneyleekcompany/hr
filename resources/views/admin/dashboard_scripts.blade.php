<script>
    $('document').ready(function(){

        $('.errorStartWorking').hide();

        $('.errorStopWorking').hide();

        $('.successStartWorking').hide();

        $('.successStopWorking').hide();

        function showLoader() {
            $('#loader').show();
        }

        function hideLoader() {
            $("#loader").hide();
        }

        setInterval(drawClock, 1000);

        function drawClock(){
            let now = new Date();
            let hr = now.getHours();
            let min = now.getMinutes();
            let sec = now.getSeconds();
            let hr_rotation = 30 * hr + min / 2;
            let min_rotation = 6 * min;
            let sec_rotation = 6 * sec;
            hour.style.transform = `rotate(${hr_rotation}deg)`;
            minute.style.transform = `rotate(${min_rotation}deg)`;
            second.style.transform = `rotate(${sec_rotation}deg)`;

            // display weekday and date
            // const weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            // const weekday = weekdays[now.getDay()];
            // const date = now.toLocaleDateString();
            //
            // const dateDiv = document.getElementById('date');
            // dateDiv.innerText = `${weekday}, ${date}`;
        }

        let tasksChart = new Chart(document.getElementById("tasksChart"), {
            type: 'pie',
            data: {
                labels: [ translatedStrings.pending,
                    translatedStrings.on_hold,
                    translatedStrings.in_progress,
                    translatedStrings.completed,
                    translatedStrings.cancelled
                ],
                datasets: [{
                    label: 'Task state',
                    type: 'doughnut',
                    backgroundColor: ["#7ee5e5","#f77eb9","#4d8af0","#00ff00","#FF0000"],
                    borderColor: [
                        'rgba(256, 256, 256, 1)',
                        'rgba(256, 256, 256, 1)',
                        'rgba(256, 256, 256, 1)',
                        'rgba(256, 256, 256, 1)',
                        'rgba(256, 256, 256, 1)'
                    ],

                    data: [
                        {{$taskPieChartData['not_started']}},
                        {{$taskPieChartData['on_hold']}},
                        {{$taskPieChartData['in_progress']}},
                        {{$taskPieChartData['completed']}},
                        {{$taskPieChartData['cancelled']}}
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'Task Pie Chart'
                    }
                }
            }
        });

        let ctx = document.getElementById('projectChart')?.getContext('2d');
        let labels = [
            translatedStrings.pending,
            translatedStrings.on_hold,
            translatedStrings.in_progress,
            translatedStrings.completed,
            translatedStrings.cancelled
        ];
        let barColors = ["#7ee5e5","#f77eb9","#4d8af0","green",'red'];
        let barData = [
            {{$projectCardDetail['not_started']}},
            {{$projectCardDetail['on_hold']}},
            {{$projectCardDetail['in_progress']}},
            {{$projectCardDetail['completed']}},
            {{$projectCardDetail['cancelled']}}
        ];
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels ,
                datasets: [{
                    label: 'Project',
                    backgroundColor: barColors,
                    data: barData,
                    borderWidth: 1,
                    borderRadius: 10,
                    borderSkipped: true,
                }],

            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                },
                plugins: {
                    legend: {
                        position: 'none',
                    },
                    title: {
                        display: false,
                        text: 'Project Bar Chart'
                    }
                },
                barThickness: 50,

            }
        });

    let currentAttendanceType = '';
    let currentUrl = '';
    let currentAudio = '';
    let currentLat = null;
    let currentLng = null;
    let videoStream = null;
    let isMockLocationDetected = true;

        $("#startWorkingBtn").click(function(e) {
            e.preventDefault();
        currentAttendanceType = 'checkIn';
        currentUrl = $(this).attr('href');
        currentAudio = $(this).data('audio');
        startAttendanceProcess();
    });

    $("#stopWorkingBtn").click(function(e){
        e.preventDefault();
        currentAttendanceType = 'checkOut';
        currentUrl = $(this).attr('href');
        currentAudio = $(this).data('audio');
        startAttendanceProcess();
    });

    function startAttendanceProcess() {
            showLoader();
            getLocation().then(function (position) {
            currentLat = position.latitude;
            currentLng = position.longitude;
            hideLoader();
            openCameraModal();
            }).catch(function (error) {
                hideLoader();
                $('#flashAttendanceMessage').removeClass('d-none');
                $('.errorStartWorking').show();
                $('.errorStartWorkingMessage').text("Error occurred while retrieving location: "+error.message);
                $('div.alert.alert-danger').not('.alert-important').delay(5000).slideUp(900);
            });
    }

    function openCameraModal() {
        $('#cameraModal').modal('show');
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
            .then(function(stream) {
                videoStream = stream;
                let video = document.getElementById('cameraVideo');
                video.srcObject = stream;
                video.play();
            })
            .catch(function(err) {
                $('#cameraModal').modal('hide');
                Swal.fire('تنبيه', 'يجب السماح بالوصول للكاميرا لالتقاط صورة الحضور.', 'warning');
            });
    }

    $('#cameraModal').on('hidden.bs.modal', function () {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
        }
    });

    $('#captureBtn').click(function() {
        let video = document.getElementById('cameraVideo');
        let canvas = document.getElementById('cameraCanvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        let imageData = canvas.toDataURL('image/png'); 

        $('#cameraModal').modal('hide');
        submitAttendance(imageData);
    });

    function submitAttendance(imageData) {
        showLoader();
        $.ajax({
            type: "POST",
            url: currentUrl,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                lat: currentLat,
                long: currentLng,
                image: imageData,
                is_mock: isMockLocationDetected
            },
            success: function(response){
                let audio = new Audio(currentAudio);
                audio.play();
                if(currentAttendanceType === 'checkIn') {
                    $('#startWorkingBtn').addClass('d-none');
                    $('#stopWorkingBtn').removeClass('d-none'); // إظهار زر الانصراف
                    $('#checkInTime').text(response.data.check_in_at);
                    $('.successStartWorking').show();
                    $('.successStartWorkingMessage').text(response.message);
                } else {
                    $('#stopWorkingBtn').addClass('d-none');
                    // قد تحتاج لإظهار زر الحضور مجدداً لو كان هناك تسجيلات متعددة
                    $('#checkOutTime').text(response.data.check_out_at);
                    $('.successStopWorking').show();
                    $('.successStopWorkingMessage').text(response.message);
                }
                $('#flashAttendanceMessage').removeClass('d-none');
                $('div.alert.alert-success').not('.alert-important').delay(3000).slideUp(900);
                // location.reload(); // تم إلغاء إعادة التحميل لتحسين تجربة المستخدم
            },
            error: function(jqXHR, textStatus, errorThrown) {
                let errorObj = jqXHR.responseJSON || JSON.parse(jqXHR.responseText);
                let errorMessage = errorObj.message || errorThrown;
                $('#flashAttendanceMessage').removeClass('d-none');
                $('.errorStopWorking').show();
                $('.errorStopWorkingMessage').text(errorMessage);
                $('div.alert.alert-danger').not('.alert-important').delay(5000).slideUp(900);
                // location.reload();
            },
            complete: function() {
                hideLoader();
            }
        });
    }


        function getLocation() {
            if (navigator.geolocation) {
                return new Promise(function(resolve, reject) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        let latitude = position.coords.latitude;
                        let longitude = position.coords.longitude;
                        
                        // التحقق مما إذا كان الموقع مزيفاً عبر اتصال جسر الموبايل (مثال: Android)
                        if (window.Android && typeof window.Android.isMockLocation === 'function') {
                            isMockLocationDetected = window.Android.isMockLocation();
                        } else if (position.coords.accuracy > 500) {
                            // دقة ضعيفة جداً قد تشير إلى تلاعب أو ضعف إشارة شديد
                        }

                        resolve({ latitude: latitude, longitude: longitude });
                    }, function(error) {
                        reject(error);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
                });
            } else {
                hideLoader();
                $('#flashAttendanceMessage').removeClass('d-none');
                $('.errorStartWorking').show();
                $('.errorStartWorkingMessage').text('Geolocation is not supported by this browser.');
                $('div.alert.alert-danger').not('.alert-important').delay(5000).slideUp(900);
            }
        }
    });



</script>
