<script>
    $('document').ready(function(){

        $('.errorStartWorking').hide();

        $('.errorStopWorking').hide();

        $('.successStartWorking').hide();

        $('.successStopWorking').hide();

        function showLoader() {
            Swal.fire({
                title: 'جاري جلب البيانات...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function hideLoader() {
            Swal.close();
        }

        setInterval(drawClock, 1000);

        // منطق الساعة الرقمية
        function drawClock(){
            let now = new Date();
            let hr = now.getHours();
            let min = now.getMinutes();
            let sec = now.getSeconds();
            let ampm = hr >= 12 ? 'PM' : 'AM';
            
            hr = hr % 12;
            hr = hr ? hr : 12; 
            
            hr = hr < 10 ? '0' + hr : hr;
            min = min < 10 ? '0' + min : min;
            sec = sec < 10 ? '0' + sec : sec;
            
            if(document.getElementById('d-hour')) document.getElementById('d-hour').innerText = hr;
            if(document.getElementById('d-minute')) document.getElementById('d-minute').innerText = min;
            if(document.getElementById('d-second')) document.getElementById('d-second').innerText = sec;
            if(document.getElementById('d-ampm')) document.getElementById('d-ampm').innerText = ampm;
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
                    backgroundColor: ["#93c5fd", "#fde047", "#60a5fa", "#34d399", "#f87171"], // ألوان باستيل هادئة
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
        let barColors = ["#93c5fd", "#fde047", "#60a5fa", "#34d399", "#f87171"]; // نفس ألوان الباستيل
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
                    x: {
                        grid: { display: false, drawBorder: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], drawBorder: false, color: '#f1f5f9' }
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
                barThickness: 30,

            }
        });

    let currentAttendanceType = '';
    let currentUrl = '';
    let currentAudio = '';
    let currentLat = null;
    let currentLng = null;
    let videoStream = null;
    let isMockLocationDetected = true;

    // إعداد الإشعارات الجانبية (Toast)
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

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
                Toast.fire({
                    icon: 'error',
                    title: "خطأ في تحديد الموقع: " + error.message
                });
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
        let btn = $(this);
        let originalText = btn.html();
        btn.html('<i class="spinner-border spinner-border-sm me-2"></i> جاري التسجيل...').prop('disabled', true);

        let video = document.getElementById('cameraVideo');
        let canvas = document.getElementById('cameraCanvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        let imageData = canvas.toDataURL('image/png'); 

        submitAttendance(imageData, btn, originalText);
    });

    function submitAttendance(imageData, btn, originalText) {
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
                $('#cameraModal').modal('hide');
                btn.html(originalText).prop('disabled', false); // إعادة الزر لحالته

                let audio = new Audio(currentAudio);
                audio.play();
                if(currentAttendanceType === 'checkIn') {
                    $('#startWorkingBtn').addClass('d-none');
                    $('#stopWorkingBtn').removeClass('d-none'); // إظهار زر الانصراف
                    $('#checkInTime').text(response.data.check_in_at);
                } else {
                    $('#stopWorkingBtn').addClass('d-none');
                    $('#checkOutTime').text(response.data.check_out_at);
                }
                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#cameraModal').modal('hide');
                btn.html(originalText).prop('disabled', false);

                let errorObj = jqXHR.responseJSON || JSON.parse(jqXHR.responseText);
                let errorMessage = errorObj.message || errorThrown;
                Toast.fire({
                    icon: 'error',
                    title: errorMessage
                });
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
                Toast.fire({
                    icon: 'error',
                    title: 'متصفحك لا يدعم تحديد الموقع (Geolocation).'
                });
            }
        }
    });



</script>
